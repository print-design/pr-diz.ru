<?php
require_once '../include/topscripts.php';
require_once './_cut_date.php';

class CutTimetable {
    public $machine_id;
    public $dateFrom;
    public $dateTo;
    public $cut_dates = array();
    public $employees = array();
    public $workshifts = array();
    public $editions = array();
    
    // Имеется ли хоть одна работа со статусом "Приладка на резке"
    public $has_priladka = false;
    
    public function __construct($machine_id, $dateFrom, $dateTo) {
        $this->machine_id = $machine_id;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        
        // Работники
        $sql = "select id, first_name, last_name, role_id, active from plan_employee order by last_name, first_name";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $this->employees[$row['id']] = array("first_name" => mb_substr($row['first_name'], 0, 1).'.', "last_name" => $row['last_name'], "role_id" => $row['role_id'], "active" => $row['active']);
        }
        
        // Работники1
        $sql = "select ws.date, ws.shift, e.id, e.first_name, e.last_name "
                . "from plan_workshift1 ws "
                . "left join plan_employee e on ws.employee1_id = e.id "
                . "where ws.work_id = ".WORK_CUTTING." and ws.machine_id = ".$this->machine_id
                . " and ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."'";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $this->workshifts[$row['date'].'_'.$row['shift']] = $row['id'];
        }
        
        // Кнопка "Приступить" имеется только в самой верхней работе под статусом "В плане резки",
        // и только если нет ни одной работы в статусе "Приладка на резке".
        $button_start = true;
        
        // Тиражи
        $sql = "select e.id id, e.date, e.shift, ".PLAN_TYPE_EDITION." as type, if(isnull(e.worktime_continued), 0, 1) as has_continuation, ifnull(e.worktime_continued, e.worktime) worktime, e.position, c.id calculation_id, c.name calculation, c.status_id, "
                . "if(isnull(e.worktime_continued), round(cr.length_pure_1), round(cr.length_pure_1) / e.worktime * e.worktime_continued) as length_pure_1, "
                . "if(isnull(e.worktime_continued), round(cr.length_dirty_1), round(cr.length_dirty_1) / e.worktime * e.worktime_continued) as length_dirty_1, "
                . "cr.width_1, c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_edition e "
                . "inner join calculation c on e.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where e.work_id = ".WORK_CUTTING." and e.machine_id = ".$this->machine_id." and e.date >= '".$this->dateFrom->format('Y-m-d')."' and e.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
                . "union "
                . "select pc.id, pc.date, pc.shift, ".PLAN_TYPE_CONTINUATION." as type, pc.has_continuation, pc.worktime, 1 as position, c.id calculation_id, c.name calculation, c.status_id, "
                . "round(cr.length_pure_1) / e.worktime * pc.worktime as length_pure_1, "
                . "round(cr.length_dirty_1) / e.worktime * pc.worktime as length_dirty_1, "
                . "cr.width_1, c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_continuation pc "
                . "inner join plan_edition e on pc.plan_edition_id = e.id "
                . "inner join calculation c on e.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where e.work_id = ".WORK_CUTTING." and e.machine_id = ".$this->machine_id." and pc.date >= '".$this->dateFrom->format('Y-m-d')."' and pc.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
                . "union "
                . "select pp.id, pp.date, pp.shift, ".PLAN_TYPE_PART." as type, if(isnull(pp.worktime_continued), 0, 1) as has_continuation, ifnull(pp.worktime_continued, pp.worktime) worktime, pp.position, c.id calculation_id, c.name calculation, c.status_id, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_pure_1, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_dirty_1, "
                . "cr.width_1, c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_part pp "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where pp.in_plan = 1 and pp.work_id = ".WORK_CUTTING." and pp.machine_id = ".$this->machine_id." and pp.date >= '".$this->dateFrom->format('Y-m-d')."' and pp.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
                . "union "
                . "select ppc.id, ppc.date, ppc.shift, ".PLAN_TYPE_PART_CONTINUATION." as type, ppc.has_continuation, ppc.worktime, 1 as position, c.id calculation_id, c.name calculation, c.status_id, "
                . "round(pp.length) / pp.worktime * ppc.worktime as length_pure_1, "
                . "round(pp.length) / pp.worktime * ppc.worktime as length_dirty_1, "
                . "cr.width_1, c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_part_continuation ppc "
                . "inner join plan_part pp on ppc.plan_part_id = pp.id "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where pp.work_id = ".WORK_CUTTING." and pp.machine_id = ".$this->machine_id." and ppc.date >= '".$this->dateFrom->format('Y-m-d')."' and ppc.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
                . "order by date, shift, position";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            if(!array_key_exists($row['date'], $this->editions)) {
                $this->editions[$row['date']] = array();
            }
            
            if(!array_key_exists($row['shift'], $this->editions[$row['date']])) {
                $this->editions[$row['date']][$row['shift']] = array();
            }
            
            // Полное имя и фамилия менеджера
            $row['manager'] = $row['last_name'].' '.mb_substr($row['first_name'], 0, 1).'.';
            
            // Кнопка "Приступить" имеется только в самой верхней работе под статусом "В плане резки".
            if($row['status_id'] == ORDER_STATUS_PLAN_CUT && $button_start) {
                $row['button_start'] = true;
                $button_start = false;
            }
            else {
                $row['button_start'] = false;
            }
            
            // Кнопка "Продолжить" имеется у работ со статусом "Приладка на резке".
            // И если такая есть хоть одна, то кнопки "Приступить" ни у кого быть не может.
            if($row['status_id'] == ORDER_STATUS_CUT_PRILADKA) {
                $row['button_continue'] = true;
                $this->has_priladka = true;
            }
            else {
                $row['button_continue'] = false;
            }
            
            array_push($this->editions[$row['date']][$row['shift']], $row);
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
        
        // Распределение тиражей по датам и сменам
        foreach($period as $date) {
            $day_editions = array();
            if(key_exists($date->format('Y-m-d'), $this->editions) && key_exists('day', $this->editions[$date->format('Y-m-d')])) {
                $day_editions = $this->editions[$date->format('Y-m-d')]['day'];
            }
            
            $night_editions = array();
            if(key_exists($date->format('Y-m-d'), $this->editions) && key_exists('night', $this->editions[$date->format('Y-m-d')])) {
                $night_editions = $this->editions[$date->format('Y-m-d')]['night'];
            }
            
            $cut_date = new CutDate($date, $this, $day_editions, $night_editions);
            array_push($this->cut_dates, $cut_date);
        }
    }
    
    public function Show() {
        include './_cut_timetable_view.php';
    }
}
?>