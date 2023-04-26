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
    $sql = "";
    
    if($row['shift'] == 'day') {
        $sql = "update plan_edition set shift = 'night' where id = ".$row['id'];
    }
    elseif ($row['shift'] == 'night') {
        $sql = "update plan_edition set shift = 'day', date = date_add(date, interval 1 day) where id = ".$row['id'];
    }
    
    $executer = new Executer($sql);
    $error = $executer->error;
}

$sql = "";

if($shift == 'day') {
    $sql = "select pc.id, pc.date, pc.shift "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "inner join calculation c on pe.calculation_id = c.id "
            . "where c.machine_id = $machine_id and pc.date >= '$date'";
}
elseif($shift == 'night') {
    $sql = "select pc.id, pc.date, pc.shift "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "inner join calculation c on pe.calculation_id = c.id "
            . "where c.machine_id = $machine_id and pc.date = '$date' and pc.shift = 'night' "
            . "union "
            . "select pc.id, pc.date, pc.shift "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "inner join calculation c on pe.calculation_id = c.id "
            . "where c.machine_id = $machine_id and pc.date > '$date'";
}

$grabber = new Grabber($sql);
$rows = $grabber->result;
$error = $grabber->error;

foreach($rows as $row) {
    $sql = "";
    
    if($row['shift'] == 'day') {
        $sql = "update plan_continuation set shift = 'night' where id = ".$row['id'];
    }
    elseif($row['shift'] == 'night') {
        $sql = "update plan_continuation set shift = 'day', date = date_add(date, interval 1 day) where id = ".$row['id'];
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
    $sql = "";
    
    if($row['shift'] == 'day') {
        $sql = "update plan_event set shift = 'night' where id = ".$row['id'];
    }
    elseif($row['shift'] == 'night') {
        $sql = "update plan_event set shift = 'day', date = date_add(date, interval 1 day) where id = ".$row['id'];
    }
    
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array("error" => $error));
?>