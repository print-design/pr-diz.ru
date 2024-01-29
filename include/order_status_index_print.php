<?php
if(!empty($status_id)) {
    echo ORDER_STATUS_NAMES[$status_id];
    
    if(in_array($status_id, ORDER_STATUSES_WITH_METERS)) {
        echo "<div style='font-size: smaller;'><span class='text-nowrap'>".rtrim(rtrim(DisplayNumber(floatval($length_cut), 2), '0'), ',')."</span> м из <span class='text-nowrap'>".DisplayNumber(floatval($length_total), 0)."</span></div>";
    }
    elseif($status_id == ORDER_STATUS_CUT_REMOVED) {
        echo "<div style='font-size: smaller;'>".$this->edition['cut_remove_cause']."</div>";
    }
}
?>