<?php
require_once '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$error = "";

class PlanContinuation {
    public $date;
    public $shift;
    public $plan_edition_id;
    public $worktime;
    public $has_continuation;
    
    function __construct($date, $shift, $plan_edition_id, $worktime, $has_continuation) {
        $this->date = $date;
        $this->shift = $shift;
        $this->plan_edition_id = $plan_edition_id;
        $this->worktime = $worktime;
        $this->has_continuation = $has_continuation;
    }
}

class DateShift {
    public $date;
    public $shift;
    
    function __construct($date, $shift) {
        $this->date = $date;
        $this->shift = $shift;
    }
}

function GetNextDateShift($date, $shift) {
    if($shift == 'day') {
        return new DateShift($date, 'night');
    }
    else {
        $datetime = DateTime::createFromFormat('Y-m-d', $date);
        return new DateShift($datetime->add(new DateInterval('P1D'))->format('Y-m-d'), 'day');
    }
}

// Проверяем, точно ли у этого тиража нет допечатки
$sql = "select worktime_continued from plan_edition where id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    if(!empty($row[0])) {
        echo json_encode(array('error' => 'Сначала удалите все допечатки'));
        exit();
    }
}

// Получаем данные по тиражу
$date = '';
$shift = '';
$worktime = 0;
$work_id = 0;
$machine_id = 0;

$sql = "select date, shift, worktime, work_id, machine_id from plan_edition where id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $shift = $row['shift'];
    $worktime = $row['worktime'];
    $work_id = $row['work_id'];
    $machine_id = $row['machine_id'];
}

// Вычисляем, сколько времени остаётся в текущей смене
$sum_edition = 0;
$sum_continuation = 0;
$sum_event = 0;
$sum_part = 0;
$sum_part_continuation = 0;

$sql = "select sum(worktime) from plan_edition where id <> $id and work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift'";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sum_edition = $row[0];
    
    if(empty($sum_edition)) {
        $sum_edition = 0;
    }
}

$sql = "select sum(pc.worktime) "
        . "from plan_continuation pc "
        . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
        . "where pe.work_id = $work_id and pe.machine_id = $machine_id and pc.date = '$date' and pc.shift = '$shift'";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sum_continuation = $row[0];
    
    if(empty($sum_continuation)) {
        $sum_continuation = 0;
    }
}

$sql = "select sum(worktime) from plan_event where in_plan = 1 and work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift'";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sum_event = $row[0];
    
    if(empty($sum_event)) {
        $sum_event = 0;
    }
}

$sql = "select sum(worktime) from plan_part where in_plan = 1 and work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift'";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sum_part = $row[0];
    
    if(empty($sum_part)) {
        $sum_part = 0;
    }
}

$sql = "select sum(ppc.worktime) "
        . "from plan_part_continuation ppc "
        . "inner join plan_part pp on ppc.plan_part_id = pp.id "
        . "where pp.work_id = $work_id and pp.machine_id = $machine_id and ppc.date = '$date' and ppc.shift = '$shift'";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sum_part_continuation = $row[0];
    
    if(empty($sum_part_continuation)) {
        $sum_part_continuation = 0;
    }
}

$start_time = 12 - $sum_edition - $sum_continuation - $sum_event - $sum_part - $sum_part_continuation;

if($start_time < 0) {
    $start_time = 0;
}

// Указываем оставшееся в текущей смене время для этого тиража
$sql = "update plan_edition set worktime_continued = $start_time where id = $id";
$executer = new Executer($sql);
$error = $executer->error;

// Вычисляем, сколько времени нужно для допечаток
$continuation_time = $worktime - $start_time;

if($continuation_time > 0) {
    $next_date_shift = GetNextDateShift($date, $shift);
    $has_continuation = 0;
    $plan_continuation = new PlanContinuation($next_date_shift->date, $next_date_shift->shift, $id, $continuation_time, $has_continuation);
    
    // Увеличиваем position у всех тиражей данной смены
    $sql = "update plan_edition set position = ifnull(position, 1) + 1 "
            . "where work_id = $work_id and machine_id = $machine_id and date = '".$plan_continuation->date."' and shift = '".$plan_continuation->shift."'";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    // Увеличиваем position у всех событий данной смены
    $sql = "update plan_event set position = ifnull(position, 1) + 1 "
            . "where in_plan = 1 and work_id = $work_id and machine_id = $machine_id and date = '".$plan_continuation->date."' and shift = '".$plan_continuation->shift."'";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    // Увеличиваем position у всех разделённых тиражей данной смены
    $sql = "update plan_part set position = ifnull(position, 1) + 1 "
            . "where in_plan = 1 and work_id = $work_id and machine_id = $machine_id and date = '".$plan_continuation->date."' and shift = '".$plan_continuation->shift."'";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    // Создаём допечатку
    $sql = "insert into plan_continuation (date, shift, plan_edition_id, worktime, has_continuation) "
            . "values ('".$plan_continuation->date."', '".$plan_continuation->shift."', ".$plan_continuation->plan_edition_id.", ".$plan_continuation->worktime.", ".$plan_continuation->has_continuation.")";
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>