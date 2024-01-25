<?php
if(!empty($status_id)):
?>
<i class="fas fa-circle" style="color: <?=ORDER_STATUS_COLORS[$status_id] ?>;"></i>&nbsp;&nbsp;<?=ORDER_STATUS_NAMES[$status_id] ?>
<?php
if(in_array($status_id, ORDER_STATUSES_WITH_METERS)) {
    echo "<div style='font-size: smaller;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".DisplayNumber(floatval($length_cut), 0)." м из ".DisplayNumber(floatval($length_total), 0)."</div>";
}
elseif($status_id == ORDER_STATUS_CUT_REMOVED) {
    echo "<div style='font-size: smaller;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$cut_remove_cause."</div>";
}
endif;
?>