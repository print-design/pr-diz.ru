<?php
function CheckOpenedRolls($user_id) {
    $id = null;
    $is_from_pallet = null;
    $roll_id = null;
    $sources_no_streams_count = null;

    $sql = "select c.id, cs.is_from_pallet, cs.roll_id, "
            . "(select count(cs1.id) from cutting_source cs1 where cs1.cutting_id=c.id and cs1.id not in (select cutting_source_id from cutting_stream)) sources_no_streams_count "
            . "from cutting c "
            . "left join cutting_source cs on cs.cutting_id = c.id "
            . "where c.date is null and c.cutter_id = $user_id "
            . "order by cs.id desc";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $id = $row['id'];
        $is_from_pallet = $row['is_from_pallet'];
        $roll_id = $row['roll_id'];
        $sources_no_streams_count = $row['sources_no_streams_count'];
    }
    
    $result = array();
    $result['id'] = $id;
    $result['is_from_pallet'] = $is_from_pallet;
    $result['roll_id'] = $roll_id;
    $result['sources_no_streams_count'] = $sources_no_streams_count;
    
    return $result;
}
?>