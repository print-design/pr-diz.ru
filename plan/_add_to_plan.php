<?php
require_once '../include/topscripts.php';
require_once '../calculation/status_ids.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$from = filter_input(INPUT_GET, 'from');
$before = filter_input(INPUT_GET, 'before');
$error = '';

class Edition {
    public $Timespan;
    public $Date;
    public $Shift;
    public $Position;
}

class DateShift {
    public $Date;
    public $Shift;
    
    public function __construct($date, $shift) {
        $this->Date = $date;
        $this->Shift = $shift;
    }
}

function GetNextDateShift(DateShift $dateshift) {
    switch ($dateshift->Shift) {
        case 'day':
            return new DateShift($dateshift->Date, 'night');
        
        case 'night':
            $old_date = DateTime::createFromFormat('Y-m-d', $dateshift->Date);
            $interval = new DateInterval('P1D');
            $new_date = $old_date->add($interval);
            return new DateShift($new_date->format('Y-m-d'), 'day');
        
        default :
            return new DateShift(null, null);
    }    
}

$editions = array();

// Определяем размер расчёта и машину
$machine_id = null;
$work_time_1 = '';

$sql = "select c.machine_id, cr.work_time_1 "
        . "from calculation c "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "where c.id = $calculation_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $machine_id = $row['machine_id'];
    $work_time_1 = round($row['work_time_1'], 2);
}

// Размер смены равен 12 минус сумма всех расчётов данной смены.
// Если не указываем следующий расчёт, то position - на 1 больше, чем максимальный position данной смены.
// Если указываем следующий расчёт, то 
// увеличиваем Position на 1 у следующего расчёта и всех следу.щих за ним
// и устанавливаем Position текущего расчёта - на 1 больше, чем максимальный position смены, кроме тех, у кого position меньше, чем position следующего расчёта.
$edition = new Edition();
$edition->Date = $date;
$edition->Shift = $shift;

$sql = "select sum(e.timespan) timespan1 "
        . "from plan_edition e "
        . "inner join calculation c on e.calculation_id = c.id "
        . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift'";
$fetcher = new Fetcher($sql);
$row = $fetcher->Fetch();
if(!$row) {
    $error = "Ошибка при получении рабочего времени";
    echo json_encode(array('error' => $error));
    exit();
}
if(round($row['timespan1'], 2) >= 12) {
    $error = "В этой смене места нет";
    echo json_encode(array('error' => $error));
    exit();
}
$edition->Timespan = min(12 - round($row['timespan1'], 2), $work_time_1);

if(empty($before)) {
    $sql = "select max(e.position) position1 "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции";
        echo json_encode(array('error' => $error));
        exit();
    }
    $edition->Position = $row['position1'] + 1;
}
else {
    $sql = "update plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "set e.position = e.position + 1 "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift' "
            . "and e.position >= "
            . "(select min(position) "
            . "from plan_edition "
            . "where calculation_id = $before and machine_id = $machine_id and date = '$date' and shift = '$shift')";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $sql = "select max(e.position) position1 "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift' "
            . "and e.position < "
            . "(select min(position) "
            . "from plan_edition "
            . "where calculation_id = $before and machine_id = $machine_id and date = '$date' and shift = '$shift')";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при получении позиции";
    }
    $edition->Position = $row['position1'] + 1;
}

array_push($editions, $edition);

// Определяем следующие смены
$sum_timespans = $editions[0]->Timespan;
$old_dateshift = new DateShift($editions[0]->Date, $editions[0]->Shift);
while ($sum_timespans < $work_time_1) {
    $edition = new Edition();
    $edition->Timespan = min(12, $work_time_1 - $sum_timespans);
    $new_dateshift = GetNextDateShift($old_dateshift);
    $edition->Date = $new_dateshift->Date;
    $edition->Shift = $new_dateshift->Shift;
    $edition->Position = 0;
    
    // Устанавливаем position - на 1 меньше, чем минимальный position данной смены
    $update_sql = "select min(e.position) position1 "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '".$edition->Date."' and e.shift = '".$edition->Shift."'";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $edition->Position = $row['position1'] - 1;
    }
    
    array_push($editions, $edition);
    
    $old_dateshift = $new_dateshift;
    $sum_timespans += $edition->Timespan;
}

foreach($editions as $edition) {
    $sql = "insert into plan_edition (calculation_id, date, shift, timespan, position) "
            . "values ($calculation_id, '".$edition->Date."', '".$edition->Shift."', ".$edition->Timespan.", ".$edition->Position.")";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update calculation set status_id = ".PLAN." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

echo json_encode(array('error' => $error));
?>