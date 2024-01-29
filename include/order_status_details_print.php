<div id="status">
    <i class="<?=ORDER_STATUS_ICONS[$calculation->status_id] ?>"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=ORDER_STATUS_NAMES[$calculation->status_id] ?>
    <?php
    if(in_array($calculation->status_id, ORDER_STATUSES_WITH_METERS)) {
        $length_cut = 0;
        
        $sql = "select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id)";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $length_cut = $row[0];
        }
        
        $sql = "select sum(length) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = $id)";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $length_cut += $row[0];
        }

        echo ' '.rtrim(rtrim(DisplayNumber(floatval($length_cut), 2), '0'), ',')." м из ".DisplayNumber(floatval(is_a($calculation, CalculationSelfAdhesive::class) ? $calculation->length_pure : $calculation->length_pure_1), 0);
    }
                                
    if($calculation->status_id == ORDER_STATUS_CUT_REMOVED) {
        echo " ".$calculation->cut_remove_cause;
    }
    ?>
</div>