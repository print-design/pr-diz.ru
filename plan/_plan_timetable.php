<?php
require_once '../include/topscripts.php';
require_once './_plan_date.php';

class PlanTimetable {
    public $dateFrom;
    public $dateTo;
    public $work_id;
    public $machine_id;
    public $plan_dates = array();
    public $employees = array();
    public $workshifts1 = array();
    public $workshifts2 = array();
    public $editions = array();

    public function __construct($work_id, $machine_id, $dateFrom, $dateTo) {
        $this->work_id = $work_id;
        $this->machine_id = $machine_id;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        
        // Работники
        $sql = "select id, first_name, last_name, role_id, active from plan_employee order by last_name, first_name";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()) {
            $this->employees[$row['id']] = array("first_name" => mb_substr($row['first_name'], 0, 1).'.', "last_name" => $row['last_name'], "role_id" => $row['role_id'], "active" => $row['active']);
        }
        
        // Работники1
        $sql = "select ws.date, ws.shift, e.id, e.first_name, e.last_name "
                . "from plan_workshift1 ws "
                . "left join plan_employee e on ws.employee1_id = e.id "
                . "where ws.work_id = ".$this->work_id." and ws.machine_id = ".$this->machine_id
                ." and ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."'";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $this->workshifts1[$this->work_id.'_'.$this->machine_id.'_'.$row['date'].'_'.$row['shift']] = $row['id'];
        }
        
        // Работники2
        if($this->work_id == WORK_PRINTING && $this->machine_id == PRINTER_COMIFLEX) {
            $sql = "select ws.date, ws.shift, e.id, e.first_name, e.last_name "
                    . "from plan_workshift2 ws "
                    . "left join plan_employee e on ws.employee2_id = e.id "
                    . "where ws.work_id = ".$this->work_id." and ws.machine_id = ".$this->machine_id
                    ." and ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."'";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                $this->workshifts2[$this->work_id.'_'.$this->machine_id.'_'.$row['date'].'_'.$row['shift']] = $row['id'];
            }
        }
        
        // Тиражи
        $sql = "select e.id id, e.date, e.shift, ".PLAN_TYPE_EDITION." as type, if(isnull(e.worktime_continued), 0, 1) as has_continuation, ifnull(e.worktime_continued, e.worktime) worktime, e.position, c.id calculation_id, c.name calculation, c.raport, c.ink_number, c.status_id, cr.length_pure_1 as length_total, "
                . "if(isnull(e.worktime_continued), round(cr.length_pure_1), round(cr.length_pure_1) / e.worktime * e.worktime_continued) as length_pure_1, "
                . "if(isnull(e.worktime_continued), round(cr.length_pure_2), round(cr.length_pure_2) / e.worktime * e.worktime_continued) as length_pure_2, "
                . "if(isnull(e.worktime_continued), round(cr.length_pure_3), round(cr.length_pure_3) / e.worktime * e.worktime_continued) as length_pure_3, "
                . "if(isnull(e.worktime_continued), round(cr.length_dirty_1), round(cr.length_dirty_1) / e.worktime * e.worktime_continued) as length_dirty_1, "
                . "if(isnull(e.worktime_continued), round(cr.length_dirty_2), round(cr.length_dirty_2) / e.worktime * e.worktime_continued) as length_dirty_2, "
                . "if(isnull(e.worktime_continued), round(cr.length_dirty_3), round(cr.length_dirty_3) / e.worktime * e.worktime_continued) as length_dirty_3, "
                . "cr.width_1, cr.width_2, cr.width_3, "
                . "c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "f.id film_id, f.name film_name, fv.thickness, c.individual_film_name, c.individual_thickness, "
                . "f1.id lamination1_film_id, c.lamination1_film_variation_id, f1.name lamination1_film_name, fv1.thickness lamination1_thickness, c.lamination1_individual_film_name, c.lamination1_individual_thickness, "
                . "f2.id lamination2_film_id, c.lamination2_film_variation_id, f2.name lamination2_film_name, fv2.thickness lamination2_thickness, c.lamination2_individual_film_name, c.lamination2_individual_thickness, "
                . "c.requirement1, c.requirement2, c.requirement3, "
                . "e.lamination, e.comment, (select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, "
                . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cutted "
                . "from plan_edition e "
                . "inner join calculation c on e.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "left join film_variation fv on c.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "left join film_variation fv1 on c.lamination1_film_variation_id = fv1.id "
                . "left join film f1 on fv1.film_id = f1.id "
                . "left join film_variation fv2 on c.lamination2_film_variation_id = fv2.id "
                . "left join film f2 on fv2.film_id = f2.id "
                . "where e.work_id = ".$this->work_id." and e.machine_id = ".$this->machine_id." and e.date >= '".$this->dateFrom->format('Y-m-d')."' and e.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "union "
                . "select ev.id, ev.date, ev.shift, ".PLAN_TYPE_EVENT." as type, 0 as has_continuation, ev.worktime, ev.position, ev.id calculation_id, ev.text calculation, 0 as raport, 0 as ink_number, 0 as status_id, 0 as length_total, "
                . "0 as length_pure_1, "
                . "0 as length_pure_2, "
                . "0 as length_pure_3, "
                . "0 as length_dirty_1, "
                . "0 as length_dirty_2, "
                . "0 as length_dirty_3, "
                . "0 as width_1, 0 as width_2, 0 as width_3, "
                . "0 as work_type_id, 0 as customer_id, '' as customer, 0 as manager_id, '' as first_name, '' as last_name, "
                . "0 as film_id, '' as film_name, 0 as thickness, '' as individual_film_name, 0 as individual_thickness, "
                . "0 as lamination1_film_id, 0 as lamination1_film_variation_id, '' as lamination1_film_name, 0 as lamination1_thickness, '' as lamination1_individual_film_name, 0 as lamination1_individual_thickness, "
                . "0 as lamination2_film_id, 0 as lamination2_film_variation_id, '' as lamination2_film_name, 0 as lamination2_thickness, '' as lamination2_individual_film_name, 0 as lamination2_individual_thickness, "
                . "'' as requirement1, '' as requirement2, '' as requirement3, "
                . "0 as lamination, ev.comment, 0 as num_for_customer, "
                . "0 as length_cutted "
                . "from plan_event ev where ev.in_plan = 1 and ev.work_id = ".$this->work_id." and ev.machine_id = ".$this->machine_id." and ev.date >= '".$this->dateFrom->format('Y-m-d')."' and ev.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "union "
                . "select pc.id, pc.date, pc.shift, ".PLAN_TYPE_CONTINUATION." as type, pc.has_continuation, pc.worktime, 1 as position, c.id calculation_id, c.name calculation, c.raport, c.ink_number, 0 as status_id, cr.length_pure_1 as length_total, "
                . "round(cr.length_pure_1) / e.worktime * pc.worktime as length_pure_1, "
                . "round(cr.length_pure_2) / e.worktime * pc.worktime as length_pure_2, "
                . "round(cr.length_pure_3) / e.worktime * pc.worktime as length_pure_3, "
                . "round(cr.length_dirty_1) / e.worktime * pc.worktime as length_dirty_1, "
                . "round(cr.length_dirty_2) / e.worktime * pc.worktime as length_dirty_2, "
                . "round(cr.length_dirty_3) / e.worktime * pc.worktime as length_dirty_3, "
                . "cr.width_1, cr.width_2, cr.width_3, "
                . "c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "f.id film_id, f.name film_name, fv.thickness, c.individual_film_name, c.individual_thickness, "
                . "f1.id lamination1_film_id, c.lamination1_film_variation_id, f1.name lamination1_film_name, fv1.thickness lamination1_thickness, c.lamination1_individual_film_name, c.lamination1_individual_thickness, "
                . "f2.id lamination2_film_id, c.lamination2_film_variation_id, f2.name lamination2_film_name, fv2.thickness lamination2_thickness, c.lamination2_individual_film_name, c.lamination2_individual_thickness, "
                . "c.requirement1, c.requirement2, c.requirement3, "
                . "e.lamination, pc.comment, (select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, "
                . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cutted "
                . "from plan_continuation pc "
                . "inner join plan_edition e on pc.plan_edition_id = e.id "
                . "inner join calculation c on e.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "left join film_variation fv on c.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "left join film_variation fv1 on c.lamination1_film_variation_id = fv1.id "
                . "left join film f1 on fv1.film_id = f1.id "
                . "left join film_variation fv2 on c.lamination2_film_variation_id = fv2.id "
                . "left join film f2 on fv2.film_id = f2.id "
                . "where e.work_id = ".$this->work_id." and e.machine_id = ".$this->machine_id." and pc.date >= '".$this->dateFrom->format('Y-m-d')."' and pc.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "union "
                . "select pp.id, pp.date, pp.shift, ".PLAN_TYPE_PART." as type, if(isnull(pp.worktime_continued), 0, 1) as has_continuation, ifnull(pp.worktime_continued, pp.worktime) worktime, pp.position, c.id calculation_id, c.name calculation, c.raport, c.ink_number, c.status_id, cr.length_pure_1 as length_total, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_pure_1, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_pure_2, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_pure_3, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_dirty_1, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_dirty_2, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_dirty_3, "
                . "cr.width_1, cr.width_2, cr.width_3, "
                . "c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "f.id film_id, f.name film_name, fv.thickness, c.individual_film_name, c.individual_thickness, "
                . "f1.id lamination1_film_id, c.lamination1_film_variation_id, f1.name lamination1_film_name, fv1.thickness lamination1_thickness, c.lamination1_individual_film_name, c.lamination1_individual_thickness, "
                . "f2.id lamination2_film_id, c.lamination2_film_variation_id, f2.name lamination2_film_name, fv2.thickness lamination2_thickness, c.lamination2_individual_film_name, c.lamination2_individual_thickness, "
                . "c.requirement1, c.requirement2, c.requirement3, "
                . "pp.lamination, pp.comment, (select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, "
                . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cutted "
                . "from plan_part pp "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "left join film_variation fv on c.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "left join film_variation fv1 on c.lamination1_film_variation_id = fv1.id "
                . "left join film f1 on fv1.film_id = f1.id "
                . "left join film_variation fv2 on c.lamination2_film_variation_id = fv2.id "
                . "left join film f2 on fv2.film_id = f2.id "
                . "where pp.in_plan = 1 and pp.work_id = ".$this->work_id." and pp.machine_id = ".$this->machine_id." and pp.date >= '".$this->dateFrom->format('Y-m-d')."' and pp.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "union "
                . "select ppc.id, ppc.date, ppc.shift, ".PLAN_TYPE_PART_CONTINUATION." as type, ppc.has_continuation, ppc.worktime, 1 as position, c.id calculation_id, c.name calculation, c.raport, c.ink_number, 0 as status_id, cr.length_pure_1 as length_total, "
                . "round(pp.length) / pp.worktime * ppc.worktime as length_pure_1, "
                . "round(pp.length) / pp.worktime * ppc.worktime as length_pure_2, "
                . "round(pp.length) / pp.worktime * ppc.worktime as length_pure_3, "
                . "round(pp.length) / pp.worktime * ppc.worktime as length_dirty_1, "
                . "round(pp.length) / pp.worktime * ppc.worktime as length_dirty_2, "
                . "round(pp.length) / pp.worktime * ppc.worktime as length_dirty_3, "
                . "cr.width_1, cr.width_2, cr.width_3, "
                . "c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "f.id film_id, f.name film_name, fv.thickness, c.individual_film_name, c.individual_thickness, "
                . "f1.id lamination1_film_id, c.lamination1_film_variation_id, f1.name lamination1_film_name, fv1.thickness lamination1_thickness, c.lamination1_individual_film_name, c.lamination1_individual_thickness, "
                . "f2.id lamination2_film_id, c.lamination2_film_variation_id, f2.name lamination2_film_name, fv2.thickness lamination2_thickness, c.lamination2_individual_film_name, c.lamination2_individual_thickness, "
                . "c.requirement1, c.requirement2, c.requirement3, "
                . "pp.lamination, ppc.comment, (select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, "
                . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cutted "
                . "from plan_part_continuation ppc "
                . "inner join plan_part pp on ppc.plan_part_id = pp.id "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "left join film_variation fv on c.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "left join film_variation fv1 on c.lamination1_film_variation_id = fv1.id "
                . "left join film f1 on fv1.film_id = f1.id "
                . "left join film_variation fv2 on c.lamination2_film_variation_id = fv2.id "
                . "left join film f2 on fv2.film_id = f2.id "
                . "where pp.work_id = ".$this->work_id." and pp.machine_id = ".$this->machine_id." and ppc.date >= '".$this->dateFrom->format('Y-m-d')."' and ppc.date <= '".$this->dateTo->format('Y-m-d')."' "
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
            
            $row['laminations'] = $laminations;
            $row['manager'] = $row['last_name'].' '. mb_substr($row['first_name'], 0, 1).'.';
            $row['samples_count'] = '';
            
            if($this->work_id == WORK_PRINTING) {
                // Вычисление количества образцов
                $AN = 0;
                
                if($row['type'] != PLAN_TYPE_EVENT) {
                    $thickness = 0;
                    
                    if(!empty($row['film_name'])) {
                        $thickness = $row['thickness'];
                    }
                    elseif(!empty ($row['individual_film_name'])) {
                        $thickness = $row['individual_thickness'];
                    }
                    
                    $AN = (3.744 * pow($thickness, 2)) - (488.4578 * $thickness) + 19401;
                }
                
                if($row['type'] == PLAN_TYPE_EDITION || $row['type'] == PLAN_TYPE_PART) {
                    $row['samples_count'] = ceil(($row['length_pure_1'] / $AN) + 1);
                }
                elseif($row['type'] == PLAN_TYPE_CONTINUATION || $row['type'] == PLAN_TYPE_PART_CONTINUATION) {
                    $row['samples_count'] = floor(($row['length_pure_1'] / $AN) + 1);
                }
                
                // Если статус не изменился на "в плане печати", а остался "ожидание постановки в план",
                // меняем принудительно.
                if($row['type'] == PLAN_TYPE_EDITION) {
                    if($row['status_id'] == ORDER_STATUS_CONFIRMED) {
                        $sql1 = "update calculation set status_id = ".ORDER_STATUS_PLAN_PRINT." where id = ".$row['calculation_id'];
                        $executer = new Executer($sql1);
                        $error = $executer->error;
                    }
                }
            }
            elseif($this->work_id == WORK_LAMINATION) {
                // Если статус не изменился на "в плане ламинации", а остался "ожидание постановки в план" или "в плане печати",
                // меняем принудительно.
                if($row['type'] == PLAN_TYPE_EDITION) {
                    if($row['status_id'] == ORDER_STATUS_CONFIRMED || $row['status_id'] == ORDER_STATUS_PLAN_PRINT) {
                        $sql1 = "update calculation set status_id = ".ORDER_STATUS_PLAN_LAMINATE." where id = ".$row['calculation_id'];
                        $executer = new Executer($sql1);
                        $error = $executer->error;
                    }
                }
            }
            elseif($this->work_id == WORK_CUTTING) {
                // Если статус не изменился на "в плане резки", а остался "ожидание постановки в план", "в плане печати" или "в плане ламинации",
                // меняем принудительно.
                if($row['type'] == PLAN_TYPE_EDITION) {
                    if($row['status_id'] == ORDER_STATUS_CONFIRMED || $row['status_id'] == ORDER_STATUS_PLAN_PRINT || $row['status_id'] == ORDER_STATUS_PLAN_LAMINATE) {
                        $sql1 = "update calculation set status_id = ".ORDER_STATUS_PLAN_CUT." where id = ".$row['calculation_id'];
                        $executer = new Executer($sql1);
                        $error = $executer->error;
                    }
                }
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
        
        foreach ($period as $date) {
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
    
    public function Print() {
        include './_plan_timetable_print.php';
    }
}
?>