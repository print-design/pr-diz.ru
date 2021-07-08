<?php
include '../include/topscripts.php';

$error_message = '';
$machineId = filter_input(INPUT_GET, 'machine_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$workshift_id = filter_input(INPUT_GET, 'workshift_id');

$direction_get = filter_input(INPUT_GET, 'direction');
$position_get = filter_input(INPUT_GET, 'position');

if(empty($workshift_id)) {
    $sql = "insert into workshift (date, machine_id, shift) values ('$date', $machineId, '$shift')";
    $ws_executer = new Executer($sql);
    $error_message = $ws_executer->error;
    $workshift_id = $ws_executer->insert_id;
}

$sql = "select 	name, organization, length, status_id, lamination_id, coloring, roller_id, manager_id, comment from clipboard order by id desc";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;

if($row = $fetcher->Fetch()) {
    $name = addslashes($row['name']);
    $organization = addslashes($row['organization']);
    $length = $row['length'] == null ? 'NULL' : $row['length'];
    $status_id = $row['status_id'] == null ? 'NULL' : $row['status_id'];
    $lamination_id = $row['lamination_id'] == null ? 'NULL' : $row['lamination_id'];
    $coloring = $row['coloring'] == null ? 'NULL' : $row['coloring'];
    $roller_id = $row['roller_id'] == null ? 'NULL' : $row['roller_id'];
    $manager_id = $row['manager_id'] == null ? 'NULL' : $row['manager_id'];
    $comment = addslashes($row['comment']);
    
    $position = 1;
    
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
    
    $sql = "insert into edition (name, organization, length, status_id, lamination_id, coloring, roller_id, manager_id, comment, workshift_id, position) "
            . "values ('$name', '$organization', $length, $status_id, $lamination_id, $coloring, $roller_id, $manager_id, '$comment', $workshift_id, $position)";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    $insert_id = $executer->insert_id;
    
    $sql = "delete from clipboard";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

if(!empty($error_message)) {
    echo $error_message;
}
?>