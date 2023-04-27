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
        return new DateShift($datetime->add(new DateInterval('P1D')), 'day');
    }
}

$plan_continuations = array();

// Вычисляем, сколько времени остаётся в текущей смене
$start_time = 0;

$sql = "select 12 - sum(pe.worktime) "
        . "from plan_edition pe "
        . "inner join calculation c on pe.calculation_id = c.id "
        . "where pe.id <> $id "
        . "and pe.date = (select date from plan_edition where id = $id) "
        . "and pe.shift = (select shift from plan_edition where id = $id) "
        . "and c.machine_id = (select machine_id from calculation where id in (select calculation_id from plan_edition where id = $id))";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $start_time = $row[0];
    
    if($start_time < 0) {
        $start_time = 0;
    }
}

// Указываем оставшееся в текущей смене время для этого тиража
echo $start_time."<br />";

// Вычисляем, сколько времени нужно для допечаток
$continuation_time = 0;
$date = '';
$shift = '';

$sql = "select worktime - $start_time as continuation_time, date, shift from plan_edition where id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $continuation_time = $row['continuation_time'];
    $date = $row['date'];
    $shift = $row['shift'];
}

while($continuation_time > 0) {
    $next_date_shift = GetNextDateShift($date, $shift);
    $worktime = min(12, $continuation_time);
    $has_continuation = $continuation_time > $worktime ? 1 : 0;
    $plan_continuation = new PlanContinuation($next_date_shift->date, $next_date_shift->shift, $id, $worktime, $has_continuation);
    
    // Увеличиваем position у всех тиражей и событий данной смены
    
    // Создаём допечатку
    echo $plan_continuation->date.' -- '.$plan_continuation->shift.' -- '.$plan_continuation->plan_edition_id.' -- '.$plan_continuation->worktime.' -- '.$plan_continuation->has_continuation."<br />";
    
    $continuation_time -= 12;
}

echo json_encode(array('error' => $error));
?>