<?php
require_once '../include/topscripts.php';

$work_id = filter_input(INPUT_GET, 'work_id', FILTER_VALIDATE_INT);
$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$error = '';

if($shift == 'day') {
    $max_value = 0;
    
    $sql = "select greatest(ifnull((select max(position) from plan_edition where work_id = ? and machine_id = ? and shift = 'night' and date = date_add(?, interval -1 day)), 0), "
            . "ifnull((select count(pc.id) from plan_continuation pc inner join plan_edition pe on pc.plan_edition_id = pe.id where pe.work_id = ? and pe.machine_id = ? and pc.shift = 'night' and pc.date = date_add(?, interval -1 day)), 0), "
            . "ifnull((select max(position) from plan_event where in_plan = 1 and work_id = ? and machine_id = ? and shift = 'night' and date = date_add(?, interval -1 day)), 0))";
    $fetcher = new Fetcher($sql, [$work_id, $machine_id, $date, $work_id, $machine_id, $date, $work_id, $machine_id, $date]);
    if($row = $fetcher->Fetch()) {
        $max_value = $row[0];
    }
    
    $sql = "update plan_edition set position = ifnull(position, 0) + ? where work_id = ? and machine_id = ? and date = ? and shift = ?";
    $executer = new Executer($sql, [$max_value, $work_id, $machine_id, $date, $shift]);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update plan_event set position = ifnull(position, 0) + ? where in_plan = 1 and work_id = ? and machine_id = ? and date = ? and shift = ?";
        $executer = new Executer($sql, [$max_value, $work_id, $machine_id, $date, $shift]);
        $error = $executer->error;
    }
}
elseif($shift == 'night') {
    $max_value = 0;
    
    $sql = "select greatest(ifnull((select max(position) from plan_edition where work_id = ? and machine_id = ? and shift = 'day' and date = ?), 0), "
            . "ifnull((select count(pc.id) from plan_continuation pc inner join plan_edition pe on pc.plan_edition_id = pe.id where pe.work_id = ? and pe.machine_id = ? and pc.shift = 'day' and pc.date = ?), 0), "
            . "ifnull((select max(position) from plan_event where in_plan = 1 and work_id = ? and machine_id = ? and shift = 'day' and date = ?), 0))";
    $fetcher = new Fetcher($sql, [$work_id, $machine_id, $date, $work_id, $machine_id, $date, $work_id, $machine_id, $date]);
    if($row = $fetcher->Fetch()) {
        $max_value = $row[0];
    }
    
    $sql = "update plan_edition set position = ifnull(position, 0) + ? where work_id = ? and machine_id = ? and date = ? and shift = ?";
    $executer = new Executer($sql, [$max_value, $work_id, $machine_id, $date, $shift]);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update plan_event set position = ifnull(position, 0) + ? where in_plan = 1 and work_id = ? and machine_id = ? and date = ? and shift = ?";
        $executer = new Executer($sql, [$max_value, $work_id, $machine_id, $date, $shift]);
        $error = $executer->error;
    }
}

$sql = "";
$select_params = [];

if($shift == 'day') {
    $sql = "select id, date, shift from plan_edition where work_id = ? and machine_id = ? and date >= ?";
    $select_params = [$work_id, $machine_id, $date];
}
elseif($shift == 'night') {
    $sql = "select id, date, shift from plan_edition where work_id = ? and machine_id = ? and date = ? and shift = 'night' "
            . "union "
            . "select id, date, shift from plan_edition where work_id = ? and machine_id = ? and date > ?";
    $select_params = [$work_id, $machine_id, $date, $work_id, $machine_id, $date];
}

$grabber = new Grabber($sql, $select_params);
$rows = $grabber->result;
$error = $grabber->error;

foreach($rows as $row) {
    $sql = "";
    
    if($row['shift'] == 'day') {
        $sql = "update plan_edition set shift = 'night', date = date_add(date, interval -1 day) where id = ?";
    }
    elseif($row['shift'] == 'night') {
        $sql = "update plan_edition set shift = 'day' where id = ?";
    }
    
    $executer = new Executer($sql, [$row['id']]);
    $error = $executer->error;
}

$sql = "";
$select_params = [];

if($shift == 'day') {
    $sql = "select pc.id, pc.date, pc.shift "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "where pe.work_id = ? and pe.machine_id = ? and pc.date >= ?";
    $select_params = [$work_id, $machine_id, $date];
}
elseif($shift == 'night') {
    $sql = "select pc.id, pc.date, pc.shift "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "where pe.work_id = ? and pe.machine_id = ? and pc.date = ? and pc.shift = 'night' "
            . "union "
            . "select pc.id, pc.date, pc.shift "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "where pe.work_id = ? and pe.machine_id = ? and pc.date > ?";
    $select_params = [$work_id, $machine_id, $date, $work_id, $machine_id, $date];
}

$grabber = new Grabber($sql, $select_params);
$rows = $grabber->result;
$error = $grabber->error;

foreach($rows as $row) {
    $sql = "";
    
    if($row['shift'] == 'day') {
        $sql = "update plan_continuation set shift = 'night', date = date_add(date, interval -1 day) where id = ?";
    }
    elseif($row['shift'] == 'night') {
        $sql = "update plan_continuation set shift = 'day' where id = ?";
    }
    
    $executer = new Executer($sql, [$row['id']]);
    $error = $executer->error;
}

$sql = '';
$select_params = [];

if($shift == 'day') {
    $sql = "select id, date, shift from plan_event where in_plan = 1 and work_id = ? and machine_id = ? and date >= ?";
    $select_params = [$work_id, $machine_id, $date];
}
elseif($shift == 'night') {
    $sql = "select id, date, shift from plan_event where in_plan = 1 and work_id = ? and machine_id = ? and date = ? and shift = 'night' "
            . "union "
            . "select id, date, shift from plan_event where in_plan = 1 and work_id = ? and machine_id = ? and date > ?";
    $select_params = [$work_id, $machine_id, $date, $work_id, $machine_id, $date];
}

$grabber = new Grabber($sql, $select_params);
$rows = $grabber->result;
$error = $grabber->error;

foreach($rows as $row) {
    $sql = "";
    
    if($row['shift'] == 'day') {
        $sql = "update plan_event set shift = 'night', date = date_add(date, interval -1 day) where id = ?";
    }
    elseif($row['shift'] == 'night') {
        $sql = "update plan_event set shift = 'day' where id = ?";
    }
    
    $executer = new Executer($sql, [$row['id']]);
    $error = $executer->error;
}

echo json_encode(array("error" => $error));
?>