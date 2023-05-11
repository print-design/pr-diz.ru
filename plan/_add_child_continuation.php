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

// Проверяем, точно ли у этой допечатки нет дочерних допечаток
$sql = "select has_continuation from plan_continuation where id = $id";
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
$plan_edition_id = 0;
$machine_id = 0;

$sql = "select pc.date, pc.shift, pc.worktime, pc.plan_edition_id, pe.machine_id "
        . "from plan_continuation pc "
        . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
        . "where pc.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $shift = $row['shift'];
    $worktime = $row['worktime'];
    $plan_edition_id = $row['plan_edition_id'];
    $machine_id = $row['machine_id'];
}

// Указываем, что у данной допечатки есть дочерняя
$sql = "update plan_continuation set worktime = 12, has_continuation = 1 where id = $id";
$executer = new Executer($sql);
$error = $executer->error;

// Вычисляем, сколько времени ещё нужно для допечаток
$continuation_time = $worktime - 12;

if($continuation_time > 0) {
    $next_date_shift = GetNextDateShift($date, $shift);
    $has_continuation = 0;
    $plan_continuation = new PlanContinuation($next_date_shift->date, $next_date_shift->shift, $plan_edition_id, $continuation_time, $has_continuation);
    
    // Увеличиваем position у всех тиражей данной смены
    $sql = "update plan_edition set position = ifnull(position, 1) + 1 "
            . "where date = '".$plan_continuation->date."' and shift = '".$plan_continuation->shift."' and machine_id = $machine_id";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    // Увеличиваем position у всех событий данной смены
    $sql = "update plan_event set position = ifnull(position, 1) + 1 "
            . "where in_plan = 1 and date = '".$plan_continuation->date."' and shift = '".$plan_continuation->shift."' and machine_id = $machine_id";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    // Увеличиваем position у всех разделённых тиражей данной смены
    $sql = "update plan_part set position = ifnull(position, 1) + 1 "
            . "where in_plan = 1 and date = '".$plan_continuation->date."' and shift = '".$plan_continuation->shift."' and machine_id = $machine_id";
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