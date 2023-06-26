<?php
require_once '../include/topscripts.php';
require_once '../calculation/calculation.php';

class Queue {
    private $work_id = null;
    private $machine_id = null;
    
    public function __construct($work_id, $machine_id) {
        $this->work_id = $work_id;
        $this->machine_id = $machine_id;
    }
    
    public function Show() {
        switch($this->work_id) {
            case WORK_PRINTING:
                $this->ShowPrint();
                break;
            case WORK_LAMINATION:
                $this->ShowLaminate();
                break;
            case WORK_CUTTING:
                $this->ShowCut();
                break;
        }
    }
    
    private function ShowPrint() {
        $str_raports = '';
        $colorfulness = 0;
        
        // Если эта машина ZBS1, ZBS2, ZBS3, получаем список валов и красочность
        if($this->work_id == WORK_PRINTING && ($this->machine_id == PRINTER_ZBS_1 || $this->machine_id == PRINTER_ZBS_2 || $this->machine_id == PRINTER_ZBS_3)) {
            $sql = "select value from raport where active = 1 and machine_id = ".$this->machine_id;
            $grabber = new Grabber($sql);
            $result = $grabber->result;
            $raports = array();
            foreach($result as $item) {
                array_push($raports, $item['value']);
            }
            
            $str_raports = implode(', ', $raports);
            $colorfulness = PRINTER_COLORFULLNESSES[$this->machine_id];
        }
        
        // Если эта машина ZBS1, ZBS2, ZBS3, 
        // то добавляем сюда расчёты для других машин (из списка ZBS1, ZBS2, ZBS2),
        // у которых вал присутствует в списке валов для этой машины
        $sql = "select ".PLAN_TYPE_EVENT." as type, 1 as position, id, 0 as calculation_id, text calculation, '' customer, 0 length, 0 ink_number, 0.0 raport, now() as status_date, "
                . "0 lamination1_film_variation_id, '' lamination1_individual_film_name, "
                . "0 lamination2_film_variation_id, '' lamination2_individual_film_name, "
                . "0 as lamination, "
                . "'' first_name, '' last_name "
                . "from plan_event "
                . "where in_plan = 0"
                . " and work_id = ".$this->work_id
                . " and machine_id = ".$this->machine_id
                . " union "
                . "select ".PLAN_TYPE_PART." as type, 2 as position, pp.id as id, c.id as calculation_id, c.name calculation, cus.name customer, pp.length, c.ink_number, c.raport, c.status_date, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                . "pp.lamination, "
                . "u.first_name, u.last_name "
                . "from plan_part pp "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "where pp.in_plan = 0 "
                . "and pp.work_id = ".$this->work_id;
        if($this->work_id == WORK_PRINTING && ($this->machine_id == PRINTER_ZBS_1 || $this->machine_id == PRINTER_ZBS_2 || $this->machine_id == PRINTER_ZBS_3)) {
            $zbs_machines = PRINTER_ZBS_1.", ".PRINTER_ZBS_2.", ".PRINTER_ZBS_3;
            $sql .= " and ((c.machine_id in ($zbs_machines) and c.raport in ($str_raports) and c.ink_number <= $colorfulness) or c.machine_id = ".$this->machine_id.")";
        }
        else {
            $sql .= " and c.machine_id = ".$this->machine_id;
        }
        $sql .= " union "
                . "select ".PLAN_TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name calculation, cus.name customer, cr.length_dirty_1 as length, c.ink_number, c.raport, c.status_date, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                . "0 as lamination, "
                . "u.first_name, u.last_name "
                . "from calculation c "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id.")"
                . " and c.id not in (select calculation_id from plan_part where work_id = ".$this->work_id.")"
                . " and c.work_type_id <> ".WORK_TYPE_NOPRINT
                . " and c.status_id = ".ORDER_STATUS_CONFIRMED;
        if($this->work_id == WORK_PRINTING && ($this->machine_id == PRINTER_ZBS_1 || $this->machine_id == PRINTER_ZBS_2 || $this->machine_id == PRINTER_ZBS_3)) {
            $zbs_machines = PRINTER_ZBS_1.", ".PRINTER_ZBS_2.", ".PRINTER_ZBS_3;
            $sql .= " and ((c.machine_id in ($zbs_machines) and c.raport in ($str_raports) and c.ink_number <= $colorfulness) or c.machine_id = ".$this->machine_id.")";
        }
        else {
            $sql .= " and c.machine_id = ".$this->machine_id;
        }
        
        $sql .= " order by position, status_date";
        $fetcher = new Fetcher($sql);
                    
        while($row = $fetcher->Fetch()) {
            $laminations_number = 0;
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations_number = 2;
            }
            elseif(!empty($row['lamination1_film_variation_id']) || !empty($row['lamination1_individual_film_name'])) {
                $laminations_number = 1;
            }
            
            if($row['type'] == PLAN_TYPE_EVENT) {
                require './_event_view.php';
            }
            elseif($row['type'] == PLAN_TYPE_EDITION || $row['type'] == PLAN_TYPE_PART) {
                require './_queue_view.php';
            }
        }
    }
    
    private function ShowLaminate() {
        $sql = "select ".PLAN_TYPE_EVENT." as type, 1 as position, id, 0 as calculation_id, text calculation, 0 as work_type_id, '' customer, 0 length, 0 ink_number, 0.0 raport, now() as status_date, "
                . "'' film_name, 0 thickness, '' individual_film_name, 0 individual_thickness, "
                . "0 lamination1_film_variation_id, '' lamination1_film_name, 0 lamination1_thickness, '' lamination1_individual_film_name, 0 lamination1_individual_thickness, "
                . "0 lamination2_film_variation_id, '' lamination2_film_name, 0 lamination2_thickness, '' lamination2_individual_film_name, 0 lamination2_individual_thickness, "
                . "0 as lamination, "
                . "'' as first_name, '' as last_name, null as print_date, '' as print_shift, 0 as print_position "
                . "from plan_event "
                . "where in_plan = 0 and work_id = ".$this->work_id." and machine_id = ".$this->machine_id
                . " union "
                . "select ".PLAN_TYPE_PART." as type, 2 as position, pp.id as id, c.id as calculation_id, c.name as calculation, c.work_type_id, cus.name as customer, pp.length, c.ink_number, c.raport, c.status_date, "
                . "f.name film_name, fv.thickness, c.individual_film_name, c.individual_thickness, "
                . "c.lamination1_film_variation_id, f1.name lamination1_film_name, fv1.thickness lamination1_thickness, c.lamination1_individual_film_name, c.lamination1_individual_thickness, "
                . "c.lamination2_film_variation_id, f2.name lamination2_film_name, fv2.thickness lamination2_thickness, c.lamination2_individual_film_name, c.lamination2_individual_thickness, "
                . "pp.lamination, "
                . "u.first_name, u.last_name, null as print_date, '' as print_shift, 0 as print_position "
                . "from plan_part pp "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "left join film_variation fv on c.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "left join film_variation fv1 on c.lamination1_film_variation_id = fv1.id "
                . "left join film f1 on fv1.film_id = f1.id "
                . "left join film_variation fv2 on c.lamination2_film_variation_id = fv2.id "
                . "left join film f2 on fv2.film_id = f2.id "
                . "where pp.in_plan = 0 "
                . "and pp.work_id = ".$this->work_id
                . " union "
                . "select ".PLAN_TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name calculation, c.work_type_id, cus.name as customer, cr.length_dirty_2 as length, c.ink_number, c.raport, c.status_date, "
                . "f.name film_name, fv.thickness, c.individual_film_name, c.individual_thickness, "
                . "c.lamination1_film_variation_id, f1.name lamination1_film_name, fv1.thickness lamination1_thickness, c.lamination1_individual_film_name, c.lamination1_individual_thickness, "
                . "c.lamination2_film_variation_id, f2.name lamination2_film_name, fv2.thickness lamination2_thickness, c.lamination2_individual_film_name, c.lamination2_individual_thickness, "
                . "1 as lamination, "
                . "u.first_name, u.last_name, "
                . "if(isnull(peprintpart.date), peprint.date, peprintpart.date) as print_date, if(isnull(peprintpart.date), peprint.shift, peprintpart.shift) as print_shift, if(isnull(peprintpart.date), peprint.position, peprintpart.position) as print_position "
                . "from calculation c "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "left join film_variation fv on c.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "left join film_variation fv1 on c.lamination1_film_variation_id = fv1.id "
                . "left join film f1 on fv1.film_id = f1.id "
                . "left join film_variation fv2 on c.lamination2_film_variation_id = fv2.id "
                . "left join film f2 on fv2.film_id = f2.id "
                . "left join plan_part peprintpart on peprintpart.calculation_id = c.id and peprintpart.work_id = ".WORK_PRINTING." and peprintpart.in_plan = 1 and (select count(id) from plan_part where calculation_id = peprintpart.calculation_id and work_id = peprintpart.work_id and in_plan = 1 and id < peprintpart.id) = 0 "
                . "left join plan_edition peprint on peprint.calculation_id = c.id and peprint.work_id = ".WORK_PRINTING." "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id." and lamination = 1)"
                . " and c.id not in (select calculation_id from plan_part where work_id = ".$this->work_id." and lamination = 1)"
                . " and (("
                . "c.work_type_id = ".WORK_TYPE_PRINT
                . " and c.status_id = ".ORDER_STATUS_PLAN_PRINT
                . ") or ("
                . "c.work_type_id = ".WORK_TYPE_NOPRINT
                . " and c.status_id = ".ORDER_STATUS_CONFIRMED
                . ") or ("
                . "c.work_type_id = ".WORK_TYPE_NOPRINT
                . " and c.status_id = ".ORDER_STATUS_PLAN_PRINT
                . "))"
                . " and (c.lamination1_film_variation_id is not null or (c.lamination1_individual_film_name is not null and c.lamination1_individual_film_name <> ''))"
                . " union "
                . "select ".PLAN_TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name calculation, c.work_type_id, cus.name as customer, cr.length_dirty_3 as length, c.ink_number, c.raport, c.status_date, "
                . "f.name film_name, fv.thickness, c.individual_film_name, c.individual_thickness, "
                . "c.lamination1_film_variation_id, f1.name lamination1_film_name, fv1.thickness lamination1_thickness, c.lamination1_individual_film_name, c.lamination1_individual_thickness, "
                . "c.lamination2_film_variation_id, f2.name lamination2_film_name, fv2.thickness lamination2_thickness, c.lamination2_individual_film_name, c.lamination2_individual_thickness, "
                . "2 as lamination, "
                . "u.first_name, u.last_name, "
                . "if(isnull(peprintpart.date), peprint.date, peprintpart.date) as print_date, if(isnull(peprintpart.date), peprint.shift, peprintpart.shift) as print_shift, if(isnull(peprintpart.date), peprint.position, peprintpart.position) as print_position "
                . "from calculation c "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "left join film_variation fv on c.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "left join film_variation fv1 on c.lamination1_film_variation_id = fv1.id "
                . "left join film f1 on fv1.film_id = f1.id "
                . "left join film_variation fv2 on c.lamination2_film_variation_id = fv2.id "
                . "left join film f2 on fv2.film_id = f2.id "
                . "left join plan_part peprintpart on peprintpart.calculation_id = c.id and peprintpart.work_id = ".WORK_PRINTING." and peprintpart.in_plan = 1 and (select count(id) from plan_part where calculation_id = peprintpart.calculation_id and work_id = peprintpart.work_id and in_plan = 1 and id < peprintpart.id) = 0 "
                . "left join plan_edition peprint on peprint.calculation_id = c.id and peprint.work_id = ".WORK_PRINTING." "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id." and lamination = 2)"
                . " and c.id not in (select calculation_id from plan_part where work_id = ".$this->work_id." and lamination = 2)"
                . " and (c.lamination2_film_variation_id is not null or (c.lamination2_individual_film_name is not null and c.lamination2_individual_film_name <> ''))"
                . " and (("
                . "c.work_type_id = ".WORK_TYPE_PRINT
                . " and c.status_id = ".ORDER_STATUS_PLAN_PRINT
                . ") or ("
                . "c.work_type_id = ".WORK_TYPE_NOPRINT
                . " and c.status_id = ".ORDER_STATUS_CONFIRMED
                . ") or ("
                . "c.work_type_id = ".WORK_TYPE_NOPRINT
                . " and c.status_id = ".ORDER_STATUS_PLAN_PRINT
                . "))"
                . " order by position, work_type_id, print_date, print_shift, print_position, status_date";
        $fetcher = new Fetcher($sql);
        
        while($row = $fetcher->Fetch()) {
            $laminations_number = 0;
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations_number = 2;
            }
            elseif(!empty ($row['lamination1_film_variation_id']) || !empty ($row['lamination1_individual_film_name'])) {
                $laminations_number = 1;
            }
            
            if($row['type'] == PLAN_TYPE_EVENT) {
                require './_event_view.php';
            }
            elseif($row['type'] == PLAN_TYPE_EDITION || $row['type'] == PLAN_TYPE_PART) {
                require './_queue_view.php';
            }
        }
    }
    
    private function ShowCut() {
        $sql = "select ".PLAN_TYPE_EVENT." as type, 1 as position, id, 0 as calculation_id, text as calculation, 0 as work_type_id, '' as customer, 0 as length, 0 as ink_number, 0.0 as raport, now() as status_date, "
                . "0 as lamination1_film_variation_id, '' as lamination1_individual_film_name, "
                . "0 as lamination2_film_variation_id, '' as lamination2_individual_film_name, "
                . "0 as lamination, "
                . "'' as first_name, '' as last_name, null as print_date, '' as print_shift, 0 as print_position, null as lamination_date, '' as lamination_shift, 0 as lamination_position "
                . "from plan_event "
                . "where in_plan = 0 and work_id = ".$this->work_id." and machine_id = ".$this->machine_id
                . " union "
                . "select ".PLAN_TYPE_PART." as type, 2 as position, pp.id as id, c.id as calculation_id, c.name as calculation, c.work_type_id, cus.name as customer, pp.length, c.ink_number, c.raport, c.status_date, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                . "pp.lamination, "
                . "u.first_name, u.last_name, null as print_date, '' as print_shift, 0 as print_position, null as lamination_date, '' as lamination_shift, 0 as lamination_position "
                . "from plan_part pp "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "where pp.in_plan = 0 "
                . "and pp.work_id = ".$this->work_id;
        if($this->machine_id == CUTTER_ATLAS) {
            $sql .= " and c.work_type_id = ".WORK_TYPE_SELF_ADHESIVE;
        }
        else {
            $sql .= " and "
                    . "(c.work_type_id = ".WORK_TYPE_PRINT
                    . " or "
                    . "c.work_type_id = ".WORK_TYPE_NOPRINT
                    . ")";
        }
        $sql .= " union "
                . "select ".PLAN_TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name as calculation, c.work_type_id, cus.name as customer, cr.length_dirty_1 as length, c.ink_number, c.raport, c.status_date, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                . "0 as lamination, "
                . "u.first_name, u.last_name, "
                . "if(isnull(pelam.date) and isnull(pelampart.date), if(isnull(peprintpart.date), peprint.date, peprintpart.date), null) as print_date, if(isnull(pelam.date) and isnull(pelampart.date), if(isnull(peprintpart.date), peprint.shift, peprintpart.shift), null) as print_shift, if(isnull(pelam.date) and isnull(pelampart.date), if(isnull(peprintpart.date), peprint.position, peprintpart.position), null) as print_position, "
                . "if(isnull(pelampart.date), pelam.date, pelampart.date) as lamination_date, if(isnull(pelampart.date), pelam.shift, pelampart.shift) as lamination_shift, if(isnull(pelampart.date), pelam.position, pelampart.position) as lamination_position "
                . "from calculation c "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "left join plan_part peprintpart on peprintpart.calculation_id = c.id and peprintpart.work_id = ".WORK_PRINTING." and peprintpart.in_plan = 1 and (select count(id) from plan_part where calculation_id = peprintpart.calculation_id and work_id = peprintpart.work_id and in_plan = 1 and id < peprintpart.id) = 0 "
                . "left join plan_edition peprint on peprint.calculation_id = c.id and peprint.work_id = ".WORK_PRINTING." "
                . "left join plan_part pelampart on pelampart.calculation_id = c.id and pelampart.work_id = ".WORK_LAMINATION." and pelampart.in_plan = 1 and (select count(id) from plan_part where calculation_id = pelampart.calculation_id and work_id = pelampart.work_id and in_plan = 1 and id < pelampart.id) = 0 "
                . "left join plan_edition pelam on pelam.calculation_id = c.id and pelam.work_id = ".WORK_LAMINATION." and (select count(id) from plan_edition where calculation_id = pelam.calculation_id and work_id = pelam.work_id and id < pelam.id) = 0 "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id.")"
                . " and c.id not in (select calculation_id from plan_part where work_id = ".$this->work_id.")";
        if($this->machine_id == CUTTER_ATLAS) {
            $sql .= " and c.work_type_id = ".WORK_TYPE_SELF_ADHESIVE
                    . " and c.status_id = ".ORDER_STATUS_PLAN_PRINT;
        }
        else {
            $sql .= " and "
                    . "((c.work_type_id = ".WORK_TYPE_PRINT
                    . " and c.status_id = ".ORDER_STATUS_PLAN_PRINT
                    . " and c.lamination1_film_variation_id is null and (c.lamination1_individual_film_name is null or c.lamination1_individual_film_name = '')"
                    . ") or ("
                    . "c.work_type_id = ".WORK_TYPE_PRINT
                    . " and c.status_id = ".ORDER_STATUS_PLAN_LAMINATE
                    . " and (c.lamination1_film_variation_id is not null or (c.lamination1_individual_film_name is not null and c.lamination1_individual_film_name <> ''))"
                    . ") or ("
                    . "c.work_type_id = ".WORK_TYPE_NOPRINT
                    . " and c.status_id = ".ORDER_STATUS_CONFIRMED
                    . " and c.lamination1_film_variation_id is null and (c.lamination1_individual_film_name is null or c.lamination1_individual_film_name = '')"
                    . ") or ("
                    . "c.work_type_id = ".WORK_TYPE_NOPRINT
                    . " and c.status_id = ".ORDER_STATUS_PLAN_LAMINATE
                    . " and (c.lamination1_film_variation_id is not null or (c.lamination1_individual_film_name is not null and c.lamination1_individual_film_name <> ''))"
                    . "))";
        }
        $sql .= " order by position, work_type_id, print_date, print_shift, print_position, lamination_date, lamination_shift, lamination_position, status_date";
        $fetcher = new Fetcher($sql);
        
        while($row = $fetcher->Fetch()) {
            $laminations_number = 0;
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations_number = 2;
            }
            elseif(!empty ($row['lamination1_film_variation_id']) || !empty ($row['lamination1_individual_film_name'])) {
                $laminations_number = 1;
            }
            
            if($row['type'] == PLAN_TYPE_EVENT) {
                require './_event_view.php';
            }
            elseif($row['type'] == PLAN_TYPE_EDITION || $row['type'] == PLAN_TYPE_PART) {
                require './_queue_view.php';
            }
        }
    }
}
?>