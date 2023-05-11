<?php
require_once '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$error = '';

if($shift == 'day') {
    $max_value = 0;
    
    $sql = "select greatest(ifnull((select max(position) from plan_edition where machine_id = $machine_id and shift = 'night' and date = date_add('$date', interval -1 day)), 0), "
            . "ifnull((select count(pc.id) from plan_continuation pc inner join plan_edition pe on pc.plan_edition_id = pe.id where pe.machine_id = $machine_id and pc.shift = 'night' and pc.date = date_add('$date', interval -1 day)), 0), "
            . "ifnull((select max(position) from plan_event where in_plan = 1 and machine_id = $machine_id and shift = 'night' and date = date_add('$date', interval -1 day)), 0), "
            . "ifnull((select max(position) from plan_part where in_plan = 1 and machine_id = $machine_id and shift = 'night' and date = date_add('$date', interval -1 day)), 0), "
            . "ifnull((select count(ppc.id) from plan_part_continuation ppc inner join plan_part pp on ppc.plan_part_id = pp.id where pp.machine_id = $machine_id and ppc.shift = 'night' and ppc.date = date_add('$date', interval -1 day)), 0))";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $max_value = $row[0];
    }
    
    $sql = "update plan_edition set position = ifnull(position, 0) + $max_value where machine_id = $machine_id and date = '$date' and shift = '$shift'";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update plan_event set position = ifnull(position, 0) + $max_value where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift'";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    
    if(empty($error)) {
        $sql = "update plan_part set position = ifnull(position, 0) + $max_value where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift'";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}
elseif($shift == 'night') {
    $max_value = 0;
    
    $sql = "select greatest(ifnull((select max(position) from plan_edition where machine_id = $machine_id and shift = 'day' and date = '$date'), 0), "
            . "ifnull((select count(pc.id) from plan_continuation pc inner join plan_edition pe on pc.plan_edition_id = pe.id where pe.machine_id = $machine_id and pc.shift = 'day' and pc.date = '$date'), 0), "
            . "ifnull((select max(position) from plan_event where in_plan = 1 and machine_id = $machine_id and shift = 'day' and date = '$date'), 0), "
            . "ifnull((select max(position) from plan_part where in_plan = 1 and machine_id = $machine_id and shift = 'day' ahd date = '$date'), 0), "
            . "ifnull((select count(ppc.id) from plan_part_continuation ppc inner join plan_part pp on ppc.plan_part_id = pp.id where pp.machine_id = $machine_id and ppc.shift = 'day' and ppc.date = '$date'), 0))";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $max_value = $row[0];
    }
    
    $sql = "update plan_edition set position = ifnull(position, 0) + $max_value where machine_id = $machine_id and date = '$date' and shift = '$shift'";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update plan_event set position = ifnull(position, 0) + $max_value where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift'";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    
    if(empty($error)) {
        $sql = "update plan_part set position = ifnull(position, 0) + $max_value where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift'";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

$sql = "";

if($shift == 'day') {
    $sql = "select id, date, shift from plan_edition where machine_id = $machine_id and date >= '$date'";
}
elseif($shift == 'night') {
    $sql = "select id, date, shift from plan_edition where machine_id = $machine_id and date = '$date' and shift = 'night' "
            . "union "
            . "select id, date, shift from plan_edition where machine_id = $machine_id and date > '$date'";
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

$sql = "";

if($shift == 'day') {
    $sql = "select pc.id, pc.date, pc.shift "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "where pe.machine_id = $machine_id and pc.date >= '$date'";
}
elseif($shift == 'night') {
    $sql = "select pc.id, pc.date, pc.shift "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "where pe.machine_id = $machine_id and pc.date = '$date' and pc.shift = 'night' "
            . "union "
            . "select pc.id, pc.date, pc.shift "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "where pe.machine_id = $machine_id and pc.date > '$date'";
}

$grabber = new Grabber($sql);
$rows = $grabber->result;
$error = $grabber->error;

foreach($rows as $row) {
    $sql = "";
    
    if($row['shift'] == 'day') {
        $sql = "update plan_continuation set shift = 'night', date = date_add(date, interval -1 day) where id = ".$row['id'];
    }
    elseif($row['shift'] == 'night') {
        $sql = "update plan_continuation set shift = 'day' where id = ".$row['id'];
    }
    
    $executer = new Executer($sql);
    $error = $executer->error;
}

$sql = '';

if($shift == 'day') {
    $sql = "select id, date, shift from plan_event where in_plan = 1 and machine_id = $machine_id and date >= '$date'";
}
elseif($shift == 'night') {
    $sql = "select id, date, shift from plan_event where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = 'night' "
            . "union "
            . "select id, date, shift from plan_event where in_plan = 1 and machine_id = $machine_id and date > '$date'";
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

$sql = '';

if($shift == 'day') {
    $sql = "select id, date, shift from plan_part where in_plan = 1 and machine_id = $machine_id and date >= '$date'";
}
elseif($shift == 'night') {
    $sql = "select id, date, shift from plan_part where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = 'night' "
            . "union "
            . "select id, date, shift from plan_part where in_plan = 1 and machine_id = $machine_id and date > '$date'";
}

$grabber = new Grabber($sql);
$rows = $grabber->result;
$error = $grabber->error;

foreach($rows as $row) {
    $sql = "";
    
    if($row['shift'] == 'day') {
        $sql = "update plan_part set shift = 'night', date = date_add(date, interval -1 day) where id = ".$row['id'];
    }
    elseif($row['shift'] == 'night') {
        $sql = "update plan_part set shift = 'day' where id = ".$row['id'] ;
    }
    
    $executer = new Executer($sql);
    $error = $executer->error;
}

$sql = "";

if($shift == 'day') {
    $sql = "select ppc.id, ppc.date, ppc.shift "
            . "from plan_part_continuation ppc "
            . "inner join plan_part pp on ppc.plan_part_id = pp.id "
            . "where pp.machine_id = $machine_id and ppc.date >= '$date'";
}
elseif($shift == 'night') {
    $sql = "select ppc.id, ppc.date, ppc.shift "
            . "from plan_part_continuation ppc "
            . "inner join plan_part pp on ppc.plan_part_id = pp.id "
            . "where pp.machine_id = $machine_id and ppc.date = '$date' and ppc.shift = 'night' "
            . "union "
            . "select ppc.id, ppc.date, ppc.shift "
            . "from plan_part_continuation ppc "
            . "inner join plan_part pp on ppc.plan_part_id = pp.id "
            . "where pp.machine_id = $machine_id and ppc.date > '$date'";
}

$grabber = new Grabber($sql);
$rows = $grabber->result;
$error = $grabber->error;

foreach($rows as $row) {
    $sql = "";
    
    if($row['shift'] == 'day') {
        $sql = "update plan_part_continuation set shift = 'night', date = date_add(date, interval -1 day) where id = ".$row['id'];
    }
    elseif($row['shift'] == 'night') {
        $sql = "update plan_part_continuation set shift = 'day' where id = ".$row['id'];
    }
    
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array("error" => $error));
?>