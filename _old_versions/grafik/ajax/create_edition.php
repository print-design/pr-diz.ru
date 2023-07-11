<?php
include '../include/topscripts.php';

$workshift_id = filter_input(INPUT_GET, 'workshift_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$machineId = filter_input(INPUT_GET, 'machine_id');

$direction_get = filter_input(INPUT_GET, 'direction');
$position_get = filter_input(INPUT_GET, 'position');

$position = 1;

if(empty($workshift_id)) {
    $sql = "insert into workshift (date, machine_id, shift) values ('$date', $machineId, '$shift')";
    $ws_executer = new Executer($sql);
    $error_message = $ws_executer->error;
    $workshift_id = $ws_executer->insert_id;
}

if($direction_get !== null && $position_get !== null) {
    if($direction_get == 'up') {
        $error_message = (new Executer("update edition set position = position - 1 where workshift_id = $workshift_id and position < $position_get"))->error;
        $position = intval($position_get) - 1;
    }
    
    if($direction_get == 'down') {
        $error_message = (new Executer("update edition set position = position + 1 where workshift_id = $workshift_id and position > $position_get"))->error;
        $position = intval($position_get) + 1;
    }
}

$error_message = (new Executer("insert into edition (workshift_id, position) values ($workshift_id, $position)"))->error;
?>