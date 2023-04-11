<?php
require_once '../include/topscripts.php';
require_once './_plan_date.php';
require_once '../calculation/calculation.php';

class PlanTimetable {
    public $dateFrom;
    public $dateTo;
    public $machineId;
    public $machine;
    public $plan_dates = array();
    public $employees = array();
    public $workshifts1 = array();
    public $workshifts2 = array();

    public function __construct($machineId, $dateFrom, $dateTo) {
        $this->machineId = $machineId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        
        // Машина
        $sql = "select m.shortname machine from machine m where id = $machineId";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $this->machine = $row['machine'];
        }
        
        // Работники
        $sql = "select id, first_name, last_name, role_id, active from plan_employee order by last_name, first_name";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()) {
            array_push($this->employees, array("id" => $row['id'], "first_name" => $row['first_name'], "last_name" => $row['last_name'], "role_id" => $row['role_id'], "active" => $row['active']));
        }
        
        // Работники 2
        /*if($this->machine == CalculationBase::COMIFLEX) {
            $sql = "select id, first_name, last_name, active from plan_employee where role_id = ".ROLE_ASSISTANT." order by last_name, first_name";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                array_push($this->employees2, array("id" => $row['id'], "first_name" => $row['first_name'], "last_name" => $row['last_name'], "active" => $row['active']));
            }
        }*/
        
        // Смены1
        $sql = "select ws.date, ws.shift, e.id, e.first_name, e.last_name "
                . "from plan_workshift1 ws "
                . "left join plan_employee e on ws.employee1_id = e.id "
                . "where ws.machine_id = ".$this->machineId
                ." and ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."'";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $this->workshifts1[$this->machineId.'_'.$row['date'].'_'.$row['shift']] = $row['id'];
        }
        
        // Смены2
        if($this->machine == CalculationBase::COMIFLEX) {
            $sql = "select ws.date, ws.shift, e.id, e.first_name, e.last_name "
                    . "from plan_workshift2 ws "
                    . "left join plan_employee e on ws.employee2_id = e.id "
                    . "where ws.machine_id = ".$this->machineId
                    ." and ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."'";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                $this->workshifts2[$this->machineId.'_'.$row['date'].'_'.$row['shift']] = $row['id'];
            }
        }
        
        // Список дат и смен
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
            
            $plan_date = new PlanDate($date, $this);
            array_push($this->plan_dates, $plan_date);
        }
    }
    
    public function Show() {
        include './_plan_timetable_view.php';
    }
}
?>