<?php
require_once '../include/topscripts.php';

$event_id = filter_input(INPUT_GET, 'event_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$before = filter_input(INPUT_GET, 'before');
$error = '';

class Event {
    public $Date;
    public $Shift;
    public $Position;
}

// Определяем машину
$machine_id = null;

$sql = "select machine_id from plan_event where id = $event_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $machine_id = $row[0];
}

$event = new Event();
$event->Date = $date;
$event->Shift = $shift;

if(empty($before)) {
    $max_edition = 0;
    $max_event = 0;
    
    $sql = "select max(e.position) "
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
    
    $sql = "select max(position) "
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
    
    $event->Position = max($max_edition, $max_event) + 1;
}
else {
    $sql = "update plan_edition set position = position + 1 "
            . "where date = '$date' and shift = '$shift' and calculation_id in (select id from calculation where machine_id = $machine_id) "
            . "and position >= $before";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $sql = "update plan_event set position = position + 1 "
            . "where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position >= $before";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $max_edition = 0;
    $max_event = 0;
    
    $sql = "select max(e.position) "
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
    
    $sql = "select max(position) "
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
    
    $event->Position = max($max_edition, $max_event) + 1;
}

$sql = "update plan_event set in_plan = 1, date = '".$event->Date."', shift = '".$event->Shift."', position = ".$event->Position." where id = $event_id";
$executer = new Executer($sql);
$error = $executer->error;

echo json_encode(array('error' => $error));
?>