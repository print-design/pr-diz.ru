<?php
include '../include/topscripts.php';

$error_message = '';
$clipboard = filter_input(INPUT_GET, 'clipboard');
$machineId = filter_input(INPUT_GET, 'machine_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$workshift_id = filter_input(INPUT_GET, 'workshift_id');

$direction_get = filter_input(INPUT_GET, 'direction');
$position_get = filter_input(INPUT_GET, 'position');

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

if(empty($workshift_id)) {
    $sql = "insert into workshift (date, machine_id, shift, user1_id, user2_id) values ('$date', $machineId, '$shift', $user1_id, $user2_id)";
    $ws_executer = new Executer($sql);
    $error_message = $ws_executer->error;
    $workshift_id = $ws_executer->insert_id;
    
    if($workshift_id > 0) {
        $error_message = (new Executer($sql))->error;
    }
}


if(!empty($direction_get) && !empty($position_get)) {
    if($direction_get == 'up') {
        $error_message = (new Executer("update edition e inner join workshift ws on e.workshift_id = ws.id set e.position = e.position - 1 where ws.date = '$date' and ws.shift = '$shift' and ws.machine_id = '$machineId' and position < $position_get"))->error;
        $position = intval($position_get) - 1;
    }
    
    if($direction_get == 'down') {
        $error_message = (new Executer("update edition e inner join workshift ws on e.workshift_id = ws.id set e.position = e.position + 1 where ws.date = '$date' and ws.shift = '$shift' and ws.machine_id = '$machineId' and position > $position_get"))->error;
        $position = intval($position_get) + 1;
    }
}

$sql = "insert into edition (name, organization, length, status_id, lamination_id, coloring, roller_id, manager_id, comment, workshift_id, position) "
        . "values ($name, $organization, $length, $status_id, $lamination_id, $coloring, $roller_id, $manager_id, $comment, $workshift_id, $position)";
$executer = new Executer($sql);
$error_message = $executer->error;
$insert_id = $executer->insert_id;

//echo $clipboard.' --- '.$machineId.' --- '.$date.' --- '.$shift.' --- '.$workshift_id;
?>