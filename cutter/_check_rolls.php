<?php
function CheckOpenedRolls($user_id) {
    $id = null;
    $is_from_pallet = null;
    $roll_id = null;
    $no_streams_source = null;

    $sql = "select c.id, cs.is_from_pallet, cs.roll_id, "
            . "(select cs1.id from cutting_source cs1 where cs1.cutting_id=c.id and cs1.id not in (select cutting_source_id from cutting_stream) limit 1) no_streams_source "
            . "from cutting c "
            . "left join cutting_source cs on cs.cutting_id = c.id "
            . "where c.date is null and c.cutter_id = $user_id "
            . "order by cs.id desc";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $id = $row['id'];
        $is_from_pallet = $row['is_from_pallet'];
        $roll_id = $row['roll_id'];
        $no_streams_source = $row['no_streams_source'];
    }
    
    $result = array();
    $result['id'] = $id;
    $result['is_from_pallet'] = $is_from_pallet;
    $result['roll_id'] = $roll_id;
    $result['no_streams_source'] = $no_streams_source;
    
    return $result;
}
?>