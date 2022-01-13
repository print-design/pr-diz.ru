<?php
function CheckOpenedRolls($user_id) {
    // ID незакрытой нарезки
    $id = null;
        
    // Материал - из паллета или свободный
    $is_from_pallet = null;
    
    // Материал - ID плёнки
    $roll_id = null;
    
    // Исходный ролик без ручьёв
    $no_streams_source = null;
    
    // Последний исходный ролик с ручьями
    $last_source = null;
    
    $sql = "select c.id, cs.is_from_pallet, cs.roll_id, "
            . "(select cs1.id from cutting_source cs1 where cs1.cutting_id=c.id and cs1.id not in (select cutting_source_id from cutting_stream) limit 1) no_streams_source, "
            . "(select cs2.id from cutting_source cs2 where cs2.cutting_id=c.id and cs2.id in (select cutting_source_id from cutting_stream) order by cs2.id desc limit 1) last_source "
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
        $last_source = $row['last_source'];
    }
    
    $result = array();
    $result['id'] = $id;
    $result['is_from_pallet'] = $is_from_pallet;
    $result['roll_id'] = $roll_id;
    $result['no_streams_source'] = $no_streams_source;
    $result['last_source'] = $last_source;
    
    return $result;
}
?>