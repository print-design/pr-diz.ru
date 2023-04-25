<?php
require_once '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$error = '';

$sql = "";

if($shift == 'day') {
    $sql = "select pe.id, pe.date, pe.shift "
            . "from plan_edition pe "
            . "inner join calculation c on pe.calculation_id = c.id "
            . "where c.machine_id = $machine_id and pe.date >= '$date'";
}
elseif($shift == 'night') {
    $sql = "select pe.id, pe.date, pe.shift "
            . "from plan_edition pe "
            . "inner join calculation c on pe.calculation_id = c.id "
            . "where c.machine_id = $machine_id and pe.date = '$date' and pe.shift = 'night' "
            . "union "
            . "select pe.id, pe.date, pe.shift "
            . "from plan_edition pe "
            . "inner join calculation c on pe.calculation_id = c.id "
            . "where c.machine_id = $machine_id and pe.date > '$date'";
}

$grabber = new Grabber($sql);
$rows = $grabber->result;
$error = $grabber->error;

foreach($rows as $row) {
    $id = $row['id'];
    $shift = $row['shift'];
    $sql = "";
    
    if($shift == 'day') {
        $sql = "update plan_edition set shift = 'night' where id = $id";
    }
    elseif ($shift == 'night') {
        $sql = "update plan_edition set shift = 'day', date = date_add(date, interval 1 day) where id = $id";
    }
    
    $executer = new Executer($sql);
    $error = $executer->error;
}

$sql = "";

if($shift == 'day') {
    $sql = "select id, date, shift from plan_event where machine_id = $machine_id and date >= '$date'";
}
elseif($shift == 'night') {
    $sql = "select id, date, shift from plan_event where machine_id = $machine_id and date = '$date' and shift = 'night' "
            . "union "
            . "select id, date, shift from plan_event where machine_id = $machine_id and date > '$date'";
}

$grabber = new Grabber($sql);
$rows = $grabber->result;
$error = $grabber->error;

foreach($rows as $row) {
    $id = $row['id'];
    $shift = $row['shift'];
    $sql = "";
    
    if($shift == 'day') {
        $sql = "update plan_event set shift = 'night' where id = $id";
    }
    elseif($shift == 'night') {
        $sql = "update plan_event set shift = 'day', date = date_add(date, interval 1 day) where id = $id";
    }
    
    $executer = new Executer($sql);
    $error = $executer->error;
}
    
echo json_encode(array("error" => $error));
?>