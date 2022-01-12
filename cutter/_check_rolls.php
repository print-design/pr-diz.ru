<?php
function CheckOpenedRolls($user_id) {
    $id = null;
    $is_from_pallet = null;
    $roll_id = null;

    $sql = "select c.id, cs.is_from_pallet, cs.roll_id "
            . "from cutting c "
            . "left join cutting_source cs on cs.cutting_id = c.id "
            . "where c.date is null and c.cutter_id = $user_id "
            . "order by cs.id desc";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $id = $row['id'];
        $is_from_pallet = $row['is_from_pallet'];
        $roll_id = $row['roll_id'];
    }
    
    $result = array();
    $result['id'] = $id;
    $result['is_from_pallet'] = $is_from_pallet;
    $result['roll_id'] = $roll_id;
    
    return $result;
}
?>