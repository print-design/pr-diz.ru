<?php
require_once '../include/topscripts.php';
require_once './_functions.php';

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
        // Получаем список валов и красочность для текущей машины
        $sql = "select value from raport where active = 1 and machine_id = ".$this->machine_id;
        $grabber = new Grabber($sql);
        $result = $grabber->result;
        $raports = array();
        foreach($result as $item) {
            array_push($raports, $item['value']);
        }
        
        if(count($raports) == 0) {
            array_push($raports, 0);
        }
            
        $str_raports = implode(', ', $raports);
        $colorfulness = PRINTER_COLORFULLNESSES[$this->machine_id];
        
        // В список расчётов для каждой машины
        // добавляем также расчёты для других машин,
        // у которых есть вал, указанный в данном заказе,
        // и красочность не меньше, чем красочность заказа
        $sql = "select ".PLAN_TYPE_EVENT." as type, 1 as position, id, 0 as calculation_id, text calculation, '' customer, 0 length, 0 ink_number, 0.0 raport, 0 as status_id, now() as status_date, 0 as queue_top, "
                . "0 lamination1_film_variation_id, '' lamination1_individual_film_name, "
                . "0 lamination2_film_variation_id, '' lamination2_individual_film_name, "
                . "0 as lamination, "
                . "'' first_name, '' last_name, "
                . "'' as stream_image1, '' as stream_image2, '' as printing_image1, '' as printing_image2 "
                . "from plan_event "
                . "where in_plan = 0"
                . " and work_id = ".$this->work_id
                . " and machine_id = ".$this->machine_id
                . " union "
                . "select ".PLAN_TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name calculation, cus.name customer, cr.length_dirty_1 as length, c.ink_number, c.raport, c.status_id, c.status_date, c.queue_top, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                . "0 as lamination, "
                . "u.first_name, u.last_name, "
                . "(select image1 from calculation_stream where calculation_id = c.id and image1 <> '' limit 1) as stream_image1, "
                . "(select image2 from calculation_stream where calculation_id = c.id and image2 <> '' limit 1) as stream_image2, "
                . "(select image1 from calculation_quantity where calculation_id = c.id and image1 <> '' limit 1) as printing_image1, "
                . "(select image2 from calculation_quantity where calculation_id = c.id and image2 <> '' limit 1) as printing_image2 "
                . "from calculation c "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id.")"
                . " and c.work_type_id <> ".WORK_TYPE_NOPRINT
                . " and c.status_id = ".ORDER_STATUS_CONFIRMED
                . " and ((c.raport in ($str_raports) and c.ink_number <= $colorfulness) or c.machine_id = ".$this->machine_id.")"
                . " order by position, queue_top desc, status_date";
        $fetcher = new Fetcher($sql);
                    
        while($row = $fetcher->Fetch()) {
            $laminations_number = 0;
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations_number = 2;
            }
            elseif(!empty($row['lamination1_film_variation_id']) || !empty($row['lamination1_individual_film_name'])) {
                $laminations_number = 1;
            }
            
            $row['image'] = "";
            $row['image_object'] = "";
            
            if(!empty($row['stream_image1'])) {
                $row['image'] = $row['stream_image1'];
                $row['image_object'] = "stream";
            }
            elseif(!empty ($row['stream_image2'])) {
                $row['image'] = $row['stream_image2'];
                $row['image_object'] = "stream";
            }
            elseif(!empty ($row['printing_image1'])) {
                $row['image'] = $row['printing_image1'];
                $row['image_object'] = "printing";
            }
            elseif(!empty ($row['printing_image2'])) {
                $row['image'] = $row['printing_image2'];
                $row['image_object'] = "printing";
            }
            
            if($row['type'] == PLAN_TYPE_EVENT) {
                require './_event_view.php';
            }
            elseif($row['type'] == PLAN_TYPE_EDITION) {
                require './_queue_view.php';
            }
        }
    }
    
    private function ShowLaminate() {
        $sql = "select ".PLAN_TYPE_EVENT." as type, 1 as position, id, 0 as calculation_id, text calculation, 0 as work_type_id, '' customer, 0 length, 0 ink_number, 0.0 raport, 0 lamination_roller_width, 0 as status_id, now() as status_date, 0 as queue_top, "
                . "'' film_name, 0 thickness, '' individual_film_name, 0 individual_thickness, "
                . "0 lamination1_film_variation_id, '' lamination1_film_name, 0 lamination1_thickness, '' lamination1_individual_film_name, 0 lamination1_individual_thickness, "
                . "0 lamination2_film_variation_id, '' lamination2_film_name, 0 lamination2_thickness, '' lamination2_individual_film_name, 0 lamination2_individual_thickness, "
                . "0 as lamination, 0 as width_1, 0 as width_2, 0 as width_3, "
                . "'' as first_name, '' as last_name, null as print_date, '' as print_shift, 0 as print_position, "
                . "'' as stream_image1, '' as stream_image2, '' as printing_image1, '' as printing_image2 "
                . "from plan_event "
                . "where in_plan = 0 and work_id = ".$this->work_id." and machine_id = ".$this->machine_id
                . " union "
                . "select ".PLAN_TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name calculation, c.work_type_id, cus.name as customer, cr.length_dirty_2 as length, c.ink_number, c.raport, c.lamination_roller_width, c.status_id, c.status_date, c.queue_top, "
                . "f.name film_name, fv.thickness, c.individual_film_name, c.individual_thickness, "
                . "c.lamination1_film_variation_id, f1.name lamination1_film_name, fv1.thickness lamination1_thickness, c.lamination1_individual_film_name, c.lamination1_individual_thickness, "
                . "c.lamination2_film_variation_id, f2.name lamination2_film_name, fv2.thickness lamination2_thickness, c.lamination2_individual_film_name, c.lamination2_individual_thickness, "
                . "1 as lamination, cr.width_1, cr.width_2, cr.width_3, "
                . "u.first_name, u.last_name, "
                . "peprint.date as print_date, peprint.shift as print_shift, peprint.position as print_position, "
                . "(select image1 from calculation_stream where calculation_id = c.id and image1 <> '' limit 1) as stream_image1, "
                . "(select image2 from calculation_stream where calculation_id = c.id and image2 <> '' limit 1) as stream_image2, "
                . "(select image1 from calculation_quantity where calculation_id = c.id and image1 <> '' limit 1) as printing_image1, "
                . "(select image2 from calculation_quantity where calculation_id = c.id and image2 <> '' limit 1) as printing_image2 "
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
                . "left join plan_edition peprint on peprint.calculation_id = c.id and peprint.work_id = ".WORK_PRINTING." "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id." and lamination = 1)"
                . " and (c.lamination1_film_variation_id is not null or (c.lamination1_individual_film_name is not null and c.lamination1_individual_film_name <> ''))"
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
                . " union "
                . "select ".PLAN_TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name calculation, c.work_type_id, cus.name as customer, cr.length_dirty_3 as length, c.ink_number, c.raport, c.lamination_roller_width, c.status_id, c.status_date, c.queue_top, "
                . "f.name film_name, fv.thickness, c.individual_film_name, c.individual_thickness, "
                . "c.lamination1_film_variation_id, f1.name lamination1_film_name, fv1.thickness lamination1_thickness, c.lamination1_individual_film_name, c.lamination1_individual_thickness, "
                . "c.lamination2_film_variation_id, f2.name lamination2_film_name, fv2.thickness lamination2_thickness, c.lamination2_individual_film_name, c.lamination2_individual_thickness, "
                . "2 as lamination, cr.width_1, cr.width_2, cr.width_3, "
                . "u.first_name, u.last_name, "
                . "peprint.date as print_date, peprint.shift as print_shift, peprint.position as print_position, "
                . "(select image1 from calculation_stream where calculation_id = c.id and image1 <> '' limit 1) as stream_image1, "
                . "(select image2 from calculation_stream where calculation_id = c.id and image2 <> '' limit 1) as stream_image2, "
                . "(select image1 from calculation_quantity where calculation_id = c.id and image1 <> '' limit 1) as printing_image1, "
                . "(select image2 from calculation_quantity where calculation_id = c.id and image2 <> '' limit 1) as printing_image2 "
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
                . "left join plan_edition peprint on peprint.calculation_id = c.id and peprint.work_id = ".WORK_PRINTING." "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id." and lamination = 2)"
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
                . " order by position, queue_top desc, work_type_id, print_date, print_shift, print_position, status_date";
        $fetcher = new Fetcher($sql);
        
        while($row = $fetcher->Fetch()) {
            $laminations_number = 0;
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations_number = 2;
            }
            elseif(!empty ($row['lamination1_film_variation_id']) || !empty ($row['lamination1_individual_film_name'])) {
                $laminations_number = 1;
            }
            
            $row['image'] = "";
            $row['image_object'] = "";
            
            if(!empty($row['stream_image1'])) {
                $row['image'] = $row['stream_image1'];
                $row['image_object'] = "stream";
            }
            elseif(!empty ($row['stream_image2'])) {
                $row['image'] = $row['stream_image2'];
                $row['image_object'] = "stream";
            }
            elseif(!empty ($row['printing_image1'])) {
                $row['image'] = $row['printing_image1'];
                $row['image_object'] = "printing";
            }
            elseif(!empty ($row['printing_image2'])) {
                $row['image'] = $row['printing_image2'];
                $row['image_object'] = "printing";
            }
            
            if($row['type'] == PLAN_TYPE_EVENT) {
                require './_event_view.php';
            }
            elseif($row['type'] == PLAN_TYPE_EDITION) {
                require './_queue_view.php';
            }
        }
    }
    
    private function ShowCut() {
        $sql = "select ".PLAN_TYPE_EVENT." as type, 1 as position, id, 0 as calculation_id, text as calculation, 0 as work_type_id, '' as customer, 0 as length, 0 as ink_number, 0.0 as raport, 0 as status_id, now() as status_date, 0 as queue_top, "
                . "0 as lamination1_film_variation_id, '' as lamination1_individual_film_name, "
                . "0 as lamination2_film_variation_id, '' as lamination2_individual_film_name, "
                . "0 as lamination, "
                . "'' as first_name, '' as last_name, null as print_date, '' as print_shift, 0 as print_position, null as lamination_date, '' as lamination_shift, 0 as lamination_position, "
                . "'' as stream_image1, '' as stream_image2, '' as printing_image1, '' as printing_image2 "
                . "from plan_event "
                . "where in_plan = 0 and work_id = ".$this->work_id." and machine_id = ".$this->machine_id;
        $sql .= " union "
                . "select ".PLAN_TYPE_EDITION." as type, 3 as position, c.id as id, c.id as calculation_id, c.name as calculation, c.work_type_id, cus.name as customer, cr.length_dirty_1 as length, c.ink_number, c.raport, c.status_id, c.status_date, c.queue_top, "
                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                . "0 as lamination, "
                . "u.first_name, u.last_name, "
                . "peprint.date as print_date, peprint.shift as print_shift, peprint.position as print_position, "
                . "pelam.date as lamination_date, pelam.shift as lamination_shift, pelam.position as lamination_position, "
                . "(select image1 from calculation_stream where calculation_id = c.id and image1 <> '' limit 1) as stream_image1, "
                . "(select image2 from calculation_stream where calculation_id = c.id and image2 <> '' limit 1) as stream_image2, "
                . "(select image1 from calculation_quantity where calculation_id = c.id and image1 <> '' limit 1) as printing_image1, "
                . "(select image2 from calculation_quantity where calculation_id = c.id and image2 <> '' limit 1) as printing_image2 "
                . "from calculation c "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "left join plan_edition peprint on peprint.calculation_id = c.id and peprint.work_id = ".WORK_PRINTING." "
                . "left join plan_edition pelam on pelam.calculation_id = c.id and pelam.work_id = ".WORK_LAMINATION." and (select count(id) from plan_edition where calculation_id = pelam.calculation_id and work_id = pelam.work_id and id < pelam.id) = 0 "
                . "where c.id not in (select calculation_id from plan_edition where work_id = ".$this->work_id.")";
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
        $sql .= " order by position, queue_top desc, work_type_id, print_date, print_shift, print_position, lamination_date, lamination_shift, lamination_position, status_date";
        $fetcher = new Fetcher($sql);
        
        while($row = $fetcher->Fetch()) {
            $laminations_number = 0;
            if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                $laminations_number = 2;
            }
            elseif(!empty ($row['lamination1_film_variation_id']) || !empty ($row['lamination1_individual_film_name'])) {
                $laminations_number = 1;
            }
            
            $row['image'] = "";
            $row['image_object'] = "";
            
            if(!empty($row['stream_image1'])) {
                $row['image'] = $row['stream_image1'];
                $row['image_object'] = "stream";
            }
            elseif(!empty ($row['stream_image2'])) {
                $row['image'] = $row['stream_image2'];
                $row['image_object'] = "stream";
            }
            elseif(!empty ($row['printing_image1'])) {
                $row['image'] = $row['printing_image1'];
                $row['image_object'] = "printing";
            }
            elseif(!empty ($row['printing_image2'])) {
                $row['image'] = $row['printing_image2'];
                $row['image_object'] = "printing";
            }
            
            if($row['type'] == PLAN_TYPE_EVENT) {
                require './_event_view.php';
            }
            elseif($row['type'] == PLAN_TYPE_EDITION) {
                require './_queue_view.php';
            }
        }
    }
}
?>