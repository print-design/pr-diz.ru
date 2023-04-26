<?php
require_once '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$error = '';

if($shift == 'day') {
    $max_value = 0;
    
    $sql = "select greatest(ifnull((select max(pe.position) from plan_edition pe inner join calculation c on pe.calculation_id = c.id where c.machine_id = $machine_id and pe.shift = 'night' and pe.date = date_add('$date', interval -1 day)), 0), "
            . "ifnull((select max(position) from plan_event where machine_id = $machine_id and shift = 'night' and date = date_add('$date', interval -1 day)), 0))";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $max_value = $row[0];
    }
    
    $sql = "update plan_edition pe set pe.position = ifnull(pe.position, 0) + $max_value where pe.date = '$date' and shift = '$shift'";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error)) {
        $max_value = 0;
        
        $sql = "select greatest(ifnull((select max(pe.position) from plan_edition pe inner join calculation c on calculation_id = c.id where c.machine_id = $machine_id and pe.shift = 'night' and pe.date = date_add('$date', interval -1 day)), 0), "
                . "ifnull((select max(position) from plan_event where machine_id = $machine_id and shift = 'night' and date = date_add('$date', interval -1 day)), 0))";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $max_value = $row[0];
        }
        
        $sql = "update plan_event pe set pe.position = ifnull(pe.position, 0) + $max_value where pe.date = '$date' and shift = '$shift'";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}
elseif($shift == 'night') {
    $max_value = 0;
    
    $sql = "select greatest(ifnull((select max(pe.position) from plan_edition pe inner join calculation c on pe.calculation_id = c.id where c.machine_id = $machine_id and pe.shift = 'day' and pe.date = '$date'), 0), "
            . "ifnull((select max(position) from plan_event where machine_id = $machine_id and shift = 'day' and date = '$date'), 0))";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $max_value = $row[0];
    }
    
    $sql = "update plan_edition pe set pe.position = ifnull(pe.position, 0) + $max_value where pe.date = '$date' and pe.shift = '$shift'";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error)) {
        $max_value = 0;
        
        $sql = "select greatest(ifnull((select max(pe.position) from plan_edition pe inner join calculation c on pe.calculation_id = c.id where c.machine_id = $machine_id and pe.shift = 'day' and pe.date = '$date'), 0), "
                . "ifnull((select max(position) from plan_event where machine_id = $machine_id and shift = 'day' and date = '$date'), 0))";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $max_value = $row[0];
        }
        
        $sql = "update plan_event pe set pe.position = ifnull(pe.position, 0) + $max_value where pe.date = '$date' and pe.shift = '$shift'";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

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
        $sql = "update plan_edition set shift = 'night', date = date_add(date, interval -1 day) where id = ".$row['id'];
    }
    elseif($row['shift'] == 'night') {
        $sql = "update plan_edition set shift = 'day' where id = ".$row['id'];
    }
    
    $executer = new Executer($sql);
    $error = $executer->error;
}

$sql = '';

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
        $sql = "update plan_event set shift = 'night', date = date_add(date, interval -1 day) where id = ".$row['id'];
    }
    elseif($row['shift'] == 'night') {
        $sql = "update plan_event set shift = 'day' where id = ".$row['id'];
    }
    
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array("error" => $error));
?>