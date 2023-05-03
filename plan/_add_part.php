<?php
require_once '../include/topscripts.php';

$part_id = filter_input(INPUT_GET, 'part_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$before = filter_input(INPUT_GET, 'before');
$error = '';

class Part {
    public $Date;
    public $Shift;
    public $Position;
}

// Определяем расчёт и машину
$calculation_id = null;
$machine_id = null;

$sql = "select c.id, c.machine_id "
        . "from calculation c "
        . "inner join plan_part pp on pp.calculation_id = c.id "
        . "where pp.id = $part_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $calculation_id = $row['id'];
    $machine_id = $row['machine_id'];
}

$part = new Part();
$part->Date = $date;
$part->Shift = $shift;

if(empty($before) && $before !== 0 && $before !== '0') {
    $max_edition = 0;
    $max_continuation = 0;
    $max_event = 0;
    $max_part = 0;
    $max_part_continuation = 0;
    
    $sql = "select max(ifnull(e.position, 0)) "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_edition = $row[0];
    
    $sql = "select count(pc.id) "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "inner join calculation c on pe.calculation_id = c.id "
            . "where c.machine_id = $machine_id and pc.date = '$date' and pc.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_continuation = $row[0];
    
    $sql = "select max(ifnull(position, 0)) "
            . "from plan_event "
            . "where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции события";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_event = $row[0];
    
    $sql = "select max(ifnull(pp.position, 0)) "
            . "from plan_part pp "
            . "inner join calculation c on pp.calculation_id = c.id "
            . "where pp.in_plan = 1 and c.machine_id = $machine_id and pp.date = '$date' and pp.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции разделённого тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_part = $row[0];
    
    $part->Position = max($max_edition, $max_continuation, $max_event, $max_part, $max_part_continuation) + 1;
}
else {
    $sql = "update plan_edition set position = ifnull(position, 0) + 1 "
            . "where date = '$date' and shift = '$shift' and calculation_id in (select id from calculation where machine_id = $machine_id) "
            . "and position >= $before";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $sql = "update plan_event set position = ifnull(position, 0) + 1 "
            . "where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position >= $before";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $sql = "update plan_part set position = ifnull(position, 0) + 1 "
            . "where in_plan = 1 and date = '$date' and shift = '$shift' and calculation_id in (select id from calculation where machine_id = $machine_id) "
            . "and position >= $before";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $max_edition = 0;
    $max_continuation = 0;
    $max_event = 0;
    $max_part = 0;
    $max_part_continuation = 0;
    
    $sql = "select max(ifnull(e.position, 0)) "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift' "
            . "and e.position < $before";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_edition = $row[0];
    
    $sql = "select count(pc.id) "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "inner join calculation c on pe.calculation_id = c.id "
            . "where c.machine_id = $machine_id and pc.date = '$date' and pc.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_continuation = $row[0];
    
    $sql = "select max(ifnull(position, 0)) "
            . "from plan_event "
            . "where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position < $before";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_event = $row[0];
    
    $sql = "select max(ifnull(pp.position, 0)) "
            . "from plan_part pp "
            . "inner join calculation c on pp.calculation_id = c.id "
            . "where pp.in_plan = 1 and c.machine_id = $machine_id and pp.date = '$date' and pp.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_part = $row[0];
    
    $part->Position = max($max_edition, $max_continuation, $max_event, $max_part, $max_part_continuation) + 1;
}

$sql = "update plan_part set in_plan = 1, date = '".$part->Date."', shift = '".$part->Shift."', position = ".$part->Position." where id = $part_id";
$executer = new Executer($sql);
$error = $executer->error;

if(empty($error)) {
    $parts_in_plan = 0;
    $parts_not_in_plan = 0;

    if($calculation_id > 0) {
        $sql = "select count(id) from plan_part where in_plan = 1 and calculation_id = $calculation_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $parts_in_plan = $row[0];
        }
    
        $sql = "select count(id) from plan_part where in_plan = 0 and calculation_id = $calculation_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $parts_not_in_plan = $row[0];
        }
    }

    if($parts_in_plan > 0 && $parts_not_in_plan == 0) {
        $sql = "update calculation set status_id = ".PLAN." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    else {
        $sql = "update calculation set status_id = ".CONFIRMED." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

echo json_encode(array('error' => $error));
?>