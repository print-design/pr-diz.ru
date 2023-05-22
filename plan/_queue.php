<?php
require_once '../include/topscripts.php';
require_once '../calculation/status_ids.php';
require_once '../calculation/calculation.php';
require_once '../include/works.php';
require_once '../include/machines.php';
require_once './types.php';

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
            
            $sql = "select colorfulness from machine where id = ".$this->machine_id;
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $colorfulness = $row[0];
            }
        }
        
        // Если эта машина ZBS1, ZBS2, ZBS3, 
        // то добавляем сюда расчёты для других машин (из списка ZBS1, ZBS2, ZBS2),
        // у которых вал присутствует в списке валов для этой машины
        $sql = "select ".TYPE_EVENT." as type, 1 as position, id, 0 as calculation_id, text calculation, '' customer, 0 length, 0 ink_number, 0.0 raport, "
                . "0 lamination1_film_variation_id, '' lamination1_individual_film_name, "
                . "0 lamination2_film_variation_id, '' lamination2_individual_film_name, "
                . "0 as lamination, "
                . "'' first_name, '' last_name "
                . "from plan_event "
                . "where in_plan = 0"
                . " and work_id = ".$this->work_id
                . " and machine_id = ".$this->machine_id
                . " union "
                . "select ".TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name calculation, cus.name customer, cr.length_dirty_1 as length, c.ink_number, c.raport, "
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
                . " and c.work_type_id <> ".CalculationBase::WORK_TYPE_NOPRINT
                . " and c.status_id = ".CONFIRMED;
        if($this->work_id == WORK_PRINTING && ($this->machine_id == PRINTER_ZBS_1 || $this->machine_id == PRINTER_ZBS_2 || $this->machine_id == PRINTER_ZBS_3)) {
            $zbs_machines = PRINTER_ZBS_1.", ".PRINTER_ZBS_2.", ".PRINTER_ZBS_3;
            $sql .= " and ((c.machine_id in ($zbs_machines) and c.raport in ($str_raports) and c.ink_number <= $colorfulness) or c.machine_id = ".$this->machine_id.")";
        }
        else {
            $sql .= " and c.machine_id = ".$this->machine_id;
        }
        $sql .= " union "
                . "select ".TYPE_PART." as type, 2 as position, pp.id as id, c.id as calculation_id, c.name calculation, cus.name customer, pp.length, c.ink_number, c.raport, "
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
        
        $sql .= " order by position, id desc";
        $fetcher = new Fetcher($sql);
                    
        while($row = $fetcher->Fetch()) {
            $laminations_number = 0;
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations_number = 2;
            }
            elseif(!empty($row['lamination1_film_variation_id']) || !empty($row['lamination1_individual_film_name'])) {
                $laminations_number = 1;
            }
            
            if($row['type'] == TYPE_EVENT) {
                require './_event_view.php';
            }
            elseif($row['type'] == TYPE_EDITION || $row['type'] == TYPE_PART) {
                require './_queue_view.php';
            }
        }
    }
    
    private function ShowLaminate() {
        $sql = "select ".TYPE_EVENT." as type, 1 as position, id, 0 as calculation_id, text calculation, '' customer, 0 length, 0 ink_number, 0.0 raport, "
                . "0 lamination1_film_variation_id, '' lamination1_individual_film_name, "
                . "0 lamination2_film_variation_id, '' lamination2_individual_film_name, "
                . "0 as lamination, "
                . "'' as first_name, '' as last_name "
                . "from plan_event "
                . "where in_plan = 0 and work_id = ".$this->work_id." and machine_id = ".$this->machine_id
                . " union "
                . "select ".TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name calculation, cus.name as customer, cr.length_dirty_2 as length, c.ink_number, c.raport, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                . "1 as lamination, "
                . "u.first_name, u.last_name "
                . "from calculation c "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id." and lamination = 1)"
                . " and c.id not in (select calculation_id from plan_part where work_id = ".$this->work_id." and lamination = 1)"
                . " and (("
                . "c.work_type_id = ".CalculationBase::WORK_TYPE_PRINT
                . " and c.status_id = ".PLAN_PRINT
                . ") or ("
                . "c.work_type_id = ".CalculationBase::WORK_TYPE_NOPRINT
                . " and c.status_id = ".CONFIRMED
                . ") or ("
                . "c.work_type_id = ".CalculationBase::WORK_TYPE_NOPRINT
                . " and c.status_id = ".PLAN_PRINT
                . "))"
                . " and (c.lamination1_film_variation_id is not null or (c.lamination1_individual_film_name is not null and c.lamination1_individual_film_name <> ''))"
                . " union "
                . "select ".TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name calculation, cus.name as customer, cr.length_dirty_3 as length, c.ink_number, c.raport, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                . "2 as lamination, "
                . "u.first_name, u.last_name "
                . "from calculation c "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id." and lamination = 2)"
                . " and c.id not in (select calculation_id from plan_part where work_id = ".$this->work_id." and lamination = 2)"
                . " and (c.lamination2_film_variation_id is not null or (c.lamination2_individual_film_name is not null and c.lamination2_individual_film_name <> ''))"
                . " and (("
                . "c.work_type_id = ".CalculationBase::WORK_TYPE_PRINT
                . " and c.status_id = ".PLAN_PRINT
                . ") or ("
                . "c.work_type_id = ".CalculationBase::WORK_TYPE_NOPRINT
                . " and c.status_id = ".CONFIRMED
                . ") or ("
                . "c.work_type_id = ".CalculationBase::WORK_TYPE_NOPRINT
                . " and c.status_id = ".PLAN_PRINT
                . "))"
                . " union "
                . "select ".TYPE_PART." as type, 2 as position, pp.id as id, c.id as calculation_id, c.name as calculation, cus.name as customer, pp.length, c.ink_number, c.raport, "
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
                . "and pp.work_id = ".$this->work_id
                . " order by position, id desc";
        $fetcher = new Fetcher($sql);
        
        while($row = $fetcher->Fetch()) {
            $laminations_number = 0;
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations_number = 2;
            }
            elseif(!empty ($row['lamination1_film_variation_id']) || !empty ($row['lamination1_individual_film_name'])) {
                $laminations_number = 1;
            }
            
            if($row['type'] == TYPE_EVENT) {
                require './_event_view.php';
            }
            elseif($row['type'] == TYPE_EDITION || $row['type'] == TYPE_PART) {
                require './_queue_view.php';
            }
        }
    }
    
    private function ShowCut() {
        $sql = "select ".TYPE_EVENT." as type, 1 as position, id, 0 as calculation_id, text as calculation, '' as customer, 0 as length, 0 as ink_number, 0.0 as raport, "
                . "0 as lamination1_film_variation_id, '' as lamination1_individual_film_name, "
                . "0 as lamination2_film_variation_id, '' as lamination2_individual_film_name, "
                . "0 as lamination, "
                . "'' as first_name, '' as last_name "
                . "from plan_event "
                . "where in_plan = 0 and work_id = ".$this->work_id." and machine_id = ".$this->machine_id
                . " union "
                . "select ".TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name as calculation, cus.name as customer, cr.length_dirty_1 as length, c.ink_number, c.raport, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                . "0 as lamination, "
                . "u.first_name, u.last_name "
                . "from calculation c "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id.")"
                . " and c.id not in (select calculation_id from plan_part where work_id = ".$this->work_id.")";
        if($this->machine_id == CUTTER_ATLAS) {
            $sql .= " and c.work_type_id = ".CalculationBase::WORK_TYPE_SELF_ADHESIVE
                    . " and c.status_id = ".PLAN_PRINT;
        }
        else {
            $sql .= " and "
                    . "((c.work_type_id = ".CalculationBase::WORK_TYPE_PRINT
                    . " and c.status_id = ".PLAN_PRINT
                    . " and c.lamination1_film_variation_id is null and (c.lamination1_individual_film_name is null or c.lamination1_individual_film_name = '')"
                    . ") or ("
                    . "c.work_type_id = ".CalculationBase::WORK_TYPE_PRINT
                    . " and c.status_id = ".PLAN_LAMINATE
                    . " and (c.lamination1_film_variation_id is not null or (c.lamination1_individual_film_name is not null and c.lamination1_individual_film_name <> ''))"
                    . ") or ("
                    . "c.work_type_id = ".CalculationBase::WORK_TYPE_NOPRINT
                    . " and c.status_id = ".CONFIRMED
                    . " and c.lamination1_film_variation_id is null and (c.lamination1_individual_film_name is null or c.lamination1_individual_film_name = '')"
                    . ") or ("
                    . "c.work_type_id = ".CalculationBase::WORK_TYPE_NOPRINT
                    . " and c.status_id = ".PLAN_LAMINATE
                    . " and (c.lamination1_film_variation_id is not null or (c.lamination1_individual_film_name is not null and c.lamination1_individual_film_name <> ''))"
                    . "))";
        }
        $sql .= " union "
                . "select ".TYPE_PART." as type, 2 as position, pp.id as id, c.id as calculation_id, c.name as calculation, cus.name as customer, pp.length, c.ink_number, c.raport, "
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
        if($this->machine_id == CUTTER_ATLAS) {
            $sql .= " and c.work_type_id = ".CalculationBase::WORK_TYPE_SELF_ADHESIVE;
        }
        else {
            $sql .= " and "
                    . "(c.work_type_id = ".CalculationBase::WORK_TYPE_PRINT
                    . " or "
                    . "c.work_type_id = ".CalculationBase::WORK_TYPE_NOPRINT
                    . ")";
        }
        $sql .= " order by position, id desc";
        $fetcher = new Fetcher($sql);
        
        while($row = $fetcher->Fetch()) {
            $laminations_number = 0;
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations_number = 2;
            }
            elseif(!empty ($row['lamination1_film_variation_id']) || !empty ($row['lamination1_individual_film_name'])) {
                $laminations_number = 1;
            }
            
            if($row['type'] == TYPE_EVENT) {
                require './_event_view.php';
            }
            elseif($row['type'] == TYPE_EDITION || $row['type'] == TYPE_PART) {
                require './_queue_view.php';
            }
        }
    }
}
?>