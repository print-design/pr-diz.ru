<?php
include '../include/topscripts.php';

$error_message = '';
$clipboard = filter_input(INPUT_GET, 'clipboard');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$machineId = filter_input(INPUT_GET, 'machine_id');

$row = json_decode($clipboard, true);

$name = $row['name'] == null ? 'NULL' : "'".addslashes($row['name'])."'";
$organization = $row['organization'] == null ? 'NULL' : "'".addslashes($row['organization'])."'";
$length = $row['length'] == null ? 'NULL' : "'".$row['length']."'";
$status_id = $row['status_id'] == null ? 'NULL' : "'".$row['status_id']."'";
$lamination_id = $row['lamination_id'] == null ? 'NULL' : "'".$row['lamination_id']."'";
$coloring = $row['coloring'] == null ? 'NULL' : "'".$row['coloring']."'";
$roller_id = $row['roller_id'] == null ? 'NULL' : "'".$row['roller_id']."'";
$manager_id = $row['manager_id'] == null ? 'NULL' : "'".$row['manager_id']."'";
$comment = $row['comment'] == null ? 'NULL' : "'".addslashes($row['comment'])."'";
$user1_id = $row['user1_id'] == null ? 'NULL' : "'".$row['user1_id']."'";
$user2_id = $row['user2_id'] == null ? 'NULL' : "'".$row['user2_id']."'";
$position = 1;

$workshift_id = filter_input(INPUT_POST, 'workshift_id');
if($workshift_id == null) {
    $sql = "insert into workshift (date, machine_id, shift, user1_id, user2_id) values ('$date', $machineId, '$shift', $user1_id, $user2_id)";
    $ws_executer = new Executer($sql);
    $error_message = $ws_executer->error;
    $workshift_id = $ws_executer->insert_id;
    
    if($workshift_id > 0) {
        $error_message = (new Executer($sql))->error;
    }
}

$direction_post = filter_input(INPUT_POST, 'direction');
$position_post = filter_input(INPUT_POST, 'position');
if($direction_post !== null && $position_post !== null) {
    if($direction_post == 'up') {
        $error_message = (new Executer("update edition e inner join workshift ws on e.workshift_id = ws.id set e.position = e.position - 1 where ws.date = '$date' and ws.shift = '$shift' and ws.machine_id = '$machineId' and position < $position_post"))->error;
        $position = intval($position_post) - 1;
    }
    
    if($direction_post == 'down') {
        $error_message = (new Executer("update edition e inner join workshift ws on e.workshift_id = ws.id set e.position = e.position + 1 where ws.date = '$date' and ws.shift = '$shift' and ws.machine_id = '$machineId' and position > $position_post"))->error;
        $position = intval($position_post) + 1;
    }
}

$sql = "insert into edition (name, organization, length, status_id, lamination_id, coloring, roller_id, manager_id, comment, workshift_id, position) "
        . "values ($name, $organization, $length, $status_id, $lamination_id, $coloring, $roller_id, $manager_id, $comment, $workshift_id, $position)";
$error_message = (new Executer($sql))->error;

if(empty($error_message)) {
    include '../include/table_row.php';
}
else {
    echo $error_message;
}
?>