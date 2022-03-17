<?php
function CheckOpenedRolls($user_id) {
    // ID незакрытой нарезки
    $id = null;
    
    // Количество ручьёв
    $streams_count = 0;
    
    // Последний исходный ролик
    $last_source = null;
    
    // Последняя намотка последнего исходного ролика
    $last_wind = null;
    
    $sql = "select c.id, "
            . "(select count(id) from cutting_stream where cutting_id=c.id) streams_count, "
            . "(select cs.id from cutting_source cs where cs.cutting_id=c.id order by cs.id desc limit 1) last_source, "
            . "(select cw.id from cutting_wind cw where cw.cutting_source_id=(select id from cutting_source where cutting_id=c.id order by id desc limit 1) order by cw.id desc limit 1) last_wind "
            . "from cutting c "
            . "where c.date is null and c.cutter_id = $user_id "
            . "order by c.id desc";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $id = $row['id'];
        $streams_count = $row['streams_count'];
        $last_source = $row['last_source'];
        $last_wind = $row['last_wind'];
    }
    
    $result = array();
    $result['id'] = $id;
    $result['streams_count'] = $streams_count;
    $result['last_source'] = $last_source;
    $result['last_wind'] = $last_wind;
    
    return $result;
}
?>