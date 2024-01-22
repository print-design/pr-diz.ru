<?php
include '../include/topscripts.php';

if(null !== filter_input(INPUT_POST, 'add_not_take_stream_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $calculation_stream_id = filter_input(INPUT_POST, 'calculation_stream_id');
    $weight = filter_input(INPUT_POST, 'weight');
    $length = 0;
    
    $sql = "select weight, length from calculation_take_stream "
            . "where calculation_stream_id = $calculation_stream_id "
            . "and calculation_take_id = (select max(id) from calculation_take where calculation_id = $id)";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $length = $weight * ($row['length'] / $row['weight']);
    }
    
    $sql = "insert into calculation_not_take_stream (calculation_stream_id, weight, length, printed) values ($calculation_stream_id, $weight, $length, now())";
    $executer = new Executer($sql);
    $not_take_stream_id = $executer->insert_id;
    
    $location = filter_input(INPUT_POST, 'php_self');
    $location_get = array();
    
    foreach($_POST as $key=>$value) {
        if(mb_substr($key, 0, 4) == 'get_' && mb_strlen($key) > 4) {
            $location_get[mb_substr($key, 4)] = $value;
        }
    }
    
    unset($location_get['stream_id']);
    unset($location_get['take_stream_id']);
    unset($location_get['take_id']);
    $location_get['not_take_stream_id'] = $not_take_stream_id;
    
    header('Location: '.$location."?".http_build_query($location_get).'#not_take');
}
?>