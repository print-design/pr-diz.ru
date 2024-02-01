<?php
if(!empty($status_id)) {
    $result_cut = 0;

    if($unit == CalculationBase::KG) {
        $result_cut = $weight_cut;
    }
    elseif(empty ($quantity_sum)) {
        $number_in_meter = 1 / $length * 1000;
        $result_cut = floor($length_cut * $number_in_meter);
    }
    else {
        $number_in_meter = floor($raport / ($length + $gap_raport)) / $raport * 1000;
        $result_cut = floor($length_cut * $number_in_meter);
    }
    
    echo ORDER_STATUS_NAMES[$status_id];
    
    if(in_array($status_id, ORDER_STATUSES_WITH_METERS)) {
        echo "<div style='font-size: smaller;'><span class='text-nowrap'>".rtrim(rtrim(DisplayNumber(floatval($result_cut), 2), '0'), ',')."</span> ".($unit == CalculationBase::KG ? "кг" : "шт")." из <span class='text-nowrap'>".DisplayNumber(floatval(empty($quantity_sum) ? $quantity : $quantity_sum), 0)."</span></div>";
    }
    elseif($status_id == ORDER_STATUS_CUT_REMOVED) {
        echo "<div style='font-size: smaller;'>".$this->edition['cut_remove_cause']."</div>";
    }
}
?>