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

// Проверяем, точно ли у этой допечатки нет дочерних допечаток
$sql = "select has_continuation from plan_part_continuation where id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    if($row[0] == 1) {
        echo json_encode(array('error' => 'Сначала удалите все дочерние допечатки'));
        exit();
    }
}

// Получаем данные по допечатке
$date = '';
$shift = '';
$worktime = 0;
$plan_part_id = 0;
$machine_id = 0;

$sql = "select ppc.date, ppc.shift, ppc.worktime, ppc.plan_part_id, c.machine_id "
        . "from plan_part_continuation ppc "
        . "inner join plan_part pp on ppc.plan_part_id = pp.id "
        . "inner join calculation c on pp.calculation_id = c.id "
        . "where ppc.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $shift = $row['shift'];
    $worktime = $row['worktime'];
    $plan_part_id = $row['plan_part_id'];
    $machine_id = $row['machine_id'];
}

// Указываем, что у данной допечатки есть дочерняя
$sql = "update plan_part_continuation set worktime = 12, has_continuation = 1 where id = $id";
$executer = new Executer($sql);
$error = $executer->error;

// Вычисляем, сколько времени ещё нужно для допечаток
$continuation_time = $worktime - 12;

if($continuation_time > 0) {
    $next_date_shift = GetNextDateShift($date, $shift);
    $has_continuation = 0;
    $plan_part_continuation = new PlanPartContinuation($next_date_shift->date, $next_date_shift->shift, $plan_part_id, $continuation_time, $has_continuation);
    
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
            . "where pp.date = '".$plan_part_continuation->date."' and pp.shift = '".$plan_part_continuation->shift."' and c.machine_id = $machine_id";
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