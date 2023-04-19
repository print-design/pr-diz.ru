<?php
require_once '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');

if(empty($machine_id) || empty($date) || empty($shift)) {
    echo 'Недостаточно данных';
    exit();
}

$error_message = '';

if(empty($id)) {
    $sql = "delete from plan_workshift2 where date = '$date' and shift = '$shift' and machine_id = $machine_id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}
else {
    $workshift_id = null;
    
    $sql = "select id from plan_workshift2 where date = '$date' and shift = '$shift' and machine_id = $machine_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $workshift_id = $row['id'];
    }
    
    if(empty($workshift_id)) {
        $sql = "insert into plan_workshift2 (date, shift, machine_id, employee2_id) values ('$date', '$shift', $machine_id, $id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    else {
        $sql = "update plan_workshift2 set employee2_id = $id where id = $workshift_id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

$result = '';

if(empty($error_message)) {
    $sql = "select employee2_id from plan_workshift2 where date = '$date' and shift = '$shift' and machine_id = $machine_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result = $row[0];
    }
}

if(empty($error_message)) {
    echo $result;
}
else {
    echo $error_message;
}
?>