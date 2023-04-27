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

$plan_continuations = array();

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
$machine_id = 0;

$sql = "select pe.date, pe.shift, pe.worktime, c.machine_id "
        . "from plan_edition pe "
        . "inner join calculation c on pe.calculation_id = c.id "
        . "where pe.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $shift = $row['shift'];
    $worktime = $row['worktime'];
    $machine_id = $row['machine_id'];
}

// Вычисляем, сколько времени остаётся в текущей смене
$sum_edition = 0;

$sql = "select sum(pe.worktime) "
        . "from plan_edition pe "
        . "inner join calculation c on pe.calculation_id = c.id "
        . "where pe.id <> $id and pe.date = '$date' and pe.shift = '$shift' and c.machine_id = $machine_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sum_edition = $row[0];
    
    if(empty($sum_edition)) {
        $sum_edition = 0;
    }
}

$sum_continuation = 0;

$sql = "select sum(pc.worktime) "
        . "from plan_continuation pc "
        . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
        . "inner join calculation c on pe.calculation_id = c.id "
        . "where pc.date = '$date' and pc.shift = '$shift' and c.machine_id = $machine_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sum_continuation = $row[0];
    
    if(empty($sum_continuation)) {
        $sum_continuation = 0;
    }
}

$sum_event = 0;

$sql = "select sum(worktime) from plan_event where date = '$date' and shift = '$shift' and machine_id = $machine_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sum_event = $row[0];
    
    if(empty($sum_event)) {
        $sum_event = 0;
    }
}

$start_time = 12 - $sum_edition - $sum_continuation - $sum_event;

if($start_time < 0) {
    $start_time = 0;
}

// Указываем оставшееся в текущей смене время для этого тиража
$sql = "update plan_edition set worktime_continued = $start_time where id = $id";
$executer = new Executer($sql);
$error = $executer->error;

// Вычисляем, сколько времени нужно для допечаток
$continuation_time = $worktime - $start_time;

while($continuation_time > 0) {
    $next_date_shift = GetNextDateShift($date, $shift);
    $next_worktime = min(12, $continuation_time);
    $has_continuation = $continuation_time > $next_worktime ? 1 : 0;
    $plan_continuation = new PlanContinuation($next_date_shift->date, $next_date_shift->shift, $id, $next_worktime, $has_continuation);
    
    // Увеличиваем position у всех тиражей данной смены
    $sql = "update plan_edition pe inner join calculation c on pe.calculation_id = c.id "
            . "set pe.position = ifnull(pe.position, 1) + 1 "
            . "where pe.date = '".$plan_continuation->date."' and pe.shift = '".$plan_continuation->shift."' and c.machine_id = $machine_id";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    // Увеличиваем position у всех событий данной смены
    $sql = "update plan_event set position = ifnull(position, 1) + 1 "
            . "where date = '".$plan_continuation->date."' and shift = '".$plan_continuation->shift."' and machine_id = $machine_id";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    // Создаём допечатку
    $sql = "insert into plan_continuation (date, shift, plan_edition_id, worktime, has_continuation) "
            . "values ('".$plan_continuation->date."', '".$plan_continuation->shift."', ".$plan_continuation->plan_edition_id.", ".$plan_continuation->worktime.", ".$plan_continuation->has_continuation.")";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    $continuation_time -= 12;
}

echo json_encode(array('error' => $error));
?>