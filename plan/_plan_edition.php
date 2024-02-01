<?php
class PlanEdition {
    private $plan_shift;
    private $edition_key;
    private $edition;

    public function __construct(PlanShift $plan_shift, $edition_key, $edition) {
        $this->plan_shift = $plan_shift;
        $this->edition_key = $edition_key;
        $this->edition = $edition;
    }
    
    function Show() {
        $from = $this->plan_shift->timetable->dateFrom->format('Y-m-d');
        $to = $this->plan_shift->timetable->dateTo->format('Y-m-d');
        
        include './_plan_edition_view.php';
    }
    
    function Print() {
        $from = $this->plan_shift->timetable->dateFrom->format('Y-m-d');
        $to = $this->plan_shift->timetable->dateTo->format('Y-m-d');
        
        $top = 'nottop';
        if($this->plan_shift->shift == 'day' && $this->edition_key == 0) {
            $top = 'top';
        }
        
        include './_plan_edition_print.php';
    }
    
    function ShowOrderStatus($status_id, $length_cut, $weight_cut, $quantity_sum, $quantity, $unit, $raport, $length, $gap_raport, $cut_remove_cause) {
        include '../include/order_status_index.php';
    }
    
    function ShowOrderStatusPrint($status_id, $length_cut, $weight_cut, $quantity_sum, $quantity, $unit, $raport, $length, $gap_raport, $cut_remove_cause) {
        include '../include/order_status_index_print.php';
    }
}
?>