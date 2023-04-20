<?php
require_once '../include/topscripts.php';
require_once '../calculation/status_ids.php';
require_once '../calculation/calculation.php';

class Queue {
    private $machine_id = null;
    private $machine = '';
    
    public function __construct($machine_id, $machine) {
        $this->machine_id = $machine_id;
        $this->machine = $machine;
    }
    
    public function Show() {
        $sql = "select 1 is_event, id, text calculation, '' customer, 0 length_dirty_1, 0 ink_number, 0.0 raport, "
                . "0 lamination1_film_variation_id, '' lamination1_individual_film_name, "
                . "0 lamination2_film_variation_id, '' lamination2_individual_film_name, "
                . "'' first_name, '' last_name "
                . "from plan_event where in_plan = 0 and machine_id = ".$this->machine_id
                . " union "
                . "select 0 is_event, c.id, c.name calculation, cus.name customer, cr.length_dirty_1, c.ink_number, c.raport, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                . "u.first_name, u.last_name "
                . "from calculation c "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "where c.status_id = ".CONFIRMED." ";
        if($this->machine == CalculationBase::ATLAS) {
            $sql .= "and false ";
        }
        else {
            $sql .= "and c.machine_id = $this->machine_id and c.work_type_id = ".CalculationBase::WORK_TYPE_PRINT." ";
        }
        $sql .= "order by is_event desc, id desc";
        $fetcher = new Fetcher($sql);
                    
        while($row = $fetcher->Fetch()) {
            $laminations_number = 0;
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations_number = 2;
            }
            elseif(!empty($row['lamination1_film_variation_id']) || !empty($row['lamination1_individual_film_name'])) {
                $laminations_number = 1;
            }
            
            if($row['is_event']) {
                require './_event_view.php';
            }
            else {
                require './_queue_view.php';
            }
        }
    }
}
?>