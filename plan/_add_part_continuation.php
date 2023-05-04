<?php
require_once '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$error = "";

class PlanPartContinuation {
    public $date;
    public $shift;
    public $plan_part_id;
    public $worktime;
    public $has_continuation;
    
    function __construct($date, $shift, $plan_part_id, $worktime, $has_continuation) {
        $this->date = $date;
        $this->shift = $shift;
        $this->plan_part_id = $plan_part_id;
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

// Проверяем, точно ли у этого разделённого тиража нет допечатки
$sql = "select worktime_continued from plan_part where id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    if(!empty($row[0])) {
        echo json_encode(array('error' => 'Сначала удалите все допечатки'));
        exit();
    }
}

// Получаем данные по разделённому тиражу
$date = '';
$shift = '';
$worktime = 0;
$machine_id = 0;

$sql = "select pp.date, pp.shift, pp.worktime, c.machine_id "
        . "from plan_part pp "
        . "inner join calculation c on pp.calculation_id = c.id "
        . "where pp.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $shift = $row['shift'];
    $worktime = $row['worktime'];
    $machine_id = $row['machine_id'];
}

// Вычисляем, сколько времени остаётся в текущей смене
$sum_edition = 0;
$sum_continuation = 0;
$sum_event = 0;
$sum_part = 0;
$sum_part_continuation = 0;

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

$sql = "select sum(worktime) from plan_event where date = '$date' and shift = '$shift' and machine_id = $machine_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sum_event = $row[0];
    
    if(empty($sum_event)) {
        $sum_event = 0;
    }
}

$sql = "select sum(pp.worktime) "
        . "from plan_part pp "
        . "inner join calculation c on pp.calculation_id = c.id "
        . "where pp.date = '$date' and pp.shift = '$shift' and c.machine_id = $machine_id";
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
        . "inner join calculation c on pp.calculation_id = c.id "
        . "where ppc.date = '$date' and ppc.shift = '$shift' and c.machine_id = $machine_id";
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
$sql = "update plan_part set worktime_continued = $start_time where id = $id";
$executer = new Executer($sql);
$error = $executer->error;

// Вычисляем, сколько времени нужно для допечаток
$continuation_time = $worktime - $start_time;

if($continuation_time > 0) {
    $next_date_shift = GetNextDateShift($date, $shift);
    $has_continuation = 0;
    $plan_part_continuation = new PlanPartContinuation($next_date_shift->date, $next_date_shift->shift, $id, $continuation_time, $has_continuation);
    
    // Увеличиваем position у всех тиражей данной смены
    $sql = "update plan_edition pe inner join calculation c on pe.calculation_id = c.id "
            . "set pe.position = ifnull(pe.position, 1) + 1 "
            . "where pe.date = '".$plan_part_continuation->date."' and pe.shift = '".$plan_part_continuation->shift."' and c.machine_id = $machine_id";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    // Увеличиваем position у всех событий данной смены
    $sql = "update plan_event set position = ifnull(position, 1) + 1 "
            . "where date = '".$plan_part_continuation->date."' and shift = '".$plan_part_continuation->shift."' and machine_id = $machine_id";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    // Увеличиваем position у всех разделённых тиражей данной смены
    $sql = "update plan_part pp inner join calculation c on pp.calculation_id = c.id "
            . "set pp.position = ifnull(pp.position, 1) + 1 "
            . "where pp.date = '".$plan_part_continuation->date."' and shift = '".$plan_part_continuation->shift."' and machine_id = $machine_id";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    // Создаём допечатку
    $sql = "insert into plan_part_continuation (date, shift, plan_part_id, worktime, has_continuation) "
            . "values ('".$plan_part_continuation->date."', '".$plan_part_continuation->shift."', ".$plan_part_continuation->plan_part_id.", ".$plan_part_continuation->worktime.", ".$plan_part_continuation->has_continuation.")";
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>