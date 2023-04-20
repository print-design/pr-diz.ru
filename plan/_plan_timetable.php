<?php
require_once '../include/topscripts.php';
require_once './_plan_date.php';
require_once '../calculation/calculation.php';
require_once '../calculation/status_ids.php';

class PlanTimetable {
    public $dateFrom;
    public $dateTo;
    public $machine_id;
    public $machine;
    public $plan_dates = array();
    public $employees = array();
    public $workshifts1 = array();
    public $workshifts2 = array();
    public $editions = array();

    public function __construct($machine_id, $dateFrom, $dateTo) {
        $this->machine_id = $machine_id;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        
        // Машина
        $sql = "select m.shortname machine from machine m where id = $machine_id";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $this->machine = $row['machine'];
        }
        
        // Работники
        $sql = "select id, first_name, last_name, role_id, active from plan_employee order by last_name, first_name";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()) {
            array_push($this->employees, array("id" => $row['id'], "first_name" => mb_substr($row['first_name'], 0, 1).'.', "last_name" => $row['last_name'], "role_id" => $row['role_id'], "active" => $row['active']));
        }
        
        // Работники1
        $sql = "select ws.date, ws.shift, e.id, e.first_name, e.last_name "
                . "from plan_workshift1 ws "
                . "left join plan_employee e on ws.employee1_id = e.id "
                . "where ws.machine_id = ".$this->machine_id
                ." and ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."'";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $this->workshifts1[$this->machine_id.'_'.$row['date'].'_'.$row['shift']] = $row['id'];
        }
        
        // Работники2
        if($this->machine == CalculationBase::COMIFLEX) {
            $sql = "select ws.date, ws.shift, e.id, e.first_name, e.last_name "
                    . "from plan_workshift2 ws "
                    . "left join plan_employee e on ws.employee2_id = e.id "
                    . "where ws.machine_id = ".$this->machine_id
                    ." and ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."'";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                $this->workshifts2[$this->machine_id.'_'.$row['date'].'_'.$row['shift']] = $row['id'];
            }
        }
        
        // Тиражи
        $sql = "select ev.date, ev.shift, 1 as is_event, 0 worktime, ev.position, ev.id calculation_id, ev.text calculation, 0 as raport, 0 as ink_number, 0 as status_id, "
                . "0 as length_dirty_1, '' as customer, '' as first_name, '' as last_name, "
                . "0 as lamination1_film_variation_id, '' as lamination1_individual_film_name, "
                . "0 as lamination2_film_variation_id, '' as lamination2_individual_film_name "
                . "from plan_event ev where in_plan = 1 and machine_id = ".$this->machine_id
                . " union "
                . "select e.date, e.shift, 0 as is_event, e.worktime, e.position, c.id calculation_id, c.name calculation, c.raport, c.ink_number, c.status_id, "
                . "cr.length_dirty_1, cus.name customer, u.first_name, u.last_name, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name "
                . "from plan_edition e "
                . "inner join calculation c on e.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where c.machine_id = ".$this->machine_id." "
                . "and c.id in (select calculation_id from plan_edition where date >= '".$this->dateFrom->format('Y-m-d')."' and date <= '".$this->dateTo->format('Y-m-d')."') "
                . "order by position";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            if(!array_key_exists($row['date'], $this->editions)) {
                $this->editions[$row['date']] = array();
            }
            
            if(!array_key_exists($row['shift'], $this->editions[$row['date']])) {
                $this->editions[$row['date']][$row['shift']] = array();
            }
            
            $laminations = '-';
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations = '2';
            }
            elseif(!empty($row['lamination1_film_variation_id']) || !empty($row['lamination1_individual_film_name'])) {
                $laminations = '1';
            }
            
            array_push($this->editions[$row['date']][$row['shift']], array('is_event' => $row['is_event'], 
                'worktime' => $row['worktime'], 
                'position' => $row['position'],
                'calculation_id' => $row['calculation_id'], 
                'calculation' => $row['calculation'], 
                'raport' => $row['raport'], 
                'ink_number' => $row['ink_number'], 
                'length_dirty_1' => $row['length_dirty_1'], 
                'customer' => $row['customer'], 
                'laminations' => $laminations, 
                'manager' => $row['last_name'].' '. mb_substr($row['first_name'], 0, 1).'.'));
            
            // Если расчёт в плане, но статус его не "в плане", меняем статус на "в плане"
            if($row['status_id'] != PLAN) {
                $sql = "update calculation set status_id = ".PLAN." where id = ".$row['calculation_id'];
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
        
        // Даты и смены
        if($this->dateFrom < $this->dateTo) {
            $date_diff = $this->dateFrom->diff($this->dateTo);
            $interval = DateInterval::createFromDateString("1 day");
            $period = new DatePeriod($this->dateFrom, $interval, $date_diff->days);
        }
        else {
            $period = array();
            array_push($period, $this->dateFrom);
        }
        
        foreach ($period as $date) {
            $str_date = $date->format('Y-m-d');
            
            $day_editions = array();
            if(key_exists($date->format('Y-m-d'), $this->editions) && key_exists('day', $this->editions[$date->format('Y-m-d')])) {
                $day_editions = $this->editions[$date->format('Y-m-d')]['day'];
            }
            
            $night_editions = array();
            if(key_exists($date->format('Y-m-d'), $this->editions) && key_exists('night', $this->editions[$date->format('Y-m-d')])) {
                $night_editions = $this->editions[$date->format('Y-m-d')]['night'];
            }
            
            $plan_date = new PlanDate($date, $this, $day_editions, $night_editions);
            array_push($this->plan_dates, $plan_date);
        }
    }
    
    public function Show() {
        include './_plan_timetable_view.php';
    }
}
?>