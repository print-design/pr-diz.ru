<?php
require_once '../include/topscripts.php';
require_once './_cut_date.php';

class CutTimetable {
    public $dateFrom;
    public $dateTo;
    public $cut_dates = array();
    public $workshifts = array();
    public $editions = array();
    
    public function __construct($dateFrom, $dateTo) {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        
        // Тиражи
        $sql = "select e.id id. e.date, e.shift, ".PLAN_TYPE_EDITION." as type, if(isnull(e.worktime_continued), 0, 1) as has_continuation, ifnull(e.worktime_continued, e.worktime) worktime, e.position, c.id calculation_id, c.name calculation, c.status_id, "
                . "if(isnull(e.worktime_continued), round(cr.length_pure_1), round(cr.length_pure_1) / e.worktime * e.worktime_continued) as length_pure_1, "
                . "if(isnull(e.worktime_continued), round(cr.length_dirty_1), round(cr.length_dirty_1) / e.worktime * e.worktime_continued) as length_dirty_1, "
                . "cr.width_1, c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_edition e "
                . "inner join calculation c on e.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where e.work_id = ".WORK_CUTTING." and e.machine_id = ". GetUserId()." and e.date >= '".$this->dateFrom->format('Y-m-d')."' and e.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "union "
                . "select pc.id, pc.date, pc.shift, ".PLAN_TYPE_CONTINUATION." as type, pc.has_continuation, pc.worktime, 1 as position, c.id calculation_id, c.name calculation, 0 as status_id, "
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
                . "where e.work_id = ".WORK_CUTTING." and e.machine_id = ". GetUserId()." and pc.date >= '".$this->dateFrom->format('Y-m-d')."' and pc.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "union "
                . "select pp.id, pp.date, pp.shift, ".PLAN_TYPE_PART." as type, if(isnull(pp.worktime_continued), 0, 1) as has_continuation, ifnull(pp.worktime_continued, pp.worktime) worktime, pp.position, c.id calculation_id, a.name calculation, c.status_id, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_pure_1, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_dirty_1, "
                . "cr.width_1, c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_part pp "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where pp.in_plan = 1 and pp.work_id = ".WORK_CUTTING." and pp.machine_id = ". GetUserId()." and pp.date >= '".$this->dateFrom->format('Y-m-d')."' and pp.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "union "
                . "select ppc.id, ppc.date, ppc.shift, ".PLAN_TYPE_PART_CONTINUATION." as type, ppc.has_continuation, ppc.worktime, 1 as position, c.id calculation_id, c.name calculation, 0 as status_id, "
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
                . "where pp.work_id = ".WORK_CUTTING." and pp.machine_id = ". GetUserId()." and ppc.date >= '".$this->dateFrom->format('Y-m-d')."' and ppc.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "order by position";
    }
    
    public function Show() {
        include './_cut_timetable_view.php';
    }
}
?>