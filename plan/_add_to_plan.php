<?php
require_once '../include/topscripts.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$before = filter_input(INPUT_GET, 'before');
$error = '';

class Edition {
    public $Timespan;
    public $Position;
    public $Date;
    public $Shift;
}

$editions = array();

// Определяем размер расчёта
$work_time_1 = '';

$sql = "select cr.work_time_1 "
        . "from calculation_result cr "
        . "where cr.calculation_id = $calculation_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $work_time_1 = round($row['work_time_1'], 2);
    echo $calculation_id."<br />";
    echo $work_time_1."<br />";
}

// Если не указываем следующий расчёт, то размер смены равен 12 минус сумма всех расчётов данной смены
// Если указываем следующий расчёт, то размер смены равен 12 минус сумма всех расчётов смены, кроме тех, у кого position меньше, чем position следующего расчёта
$sql = "";
if(empty($before)) {
    $sql = "select sum(e.timespan) timespan1, max(position) position "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift'";
}
else {
    $sql = "select sum(e.timespan) timespan1, max(position) position "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift' "
            . "and e.position < "
            . "(select min(position) "
            . "from plan_edition "
            . "where calculation_id = $before and machine_id = $machine_id and date='$date' and shift = '$shift')";
}
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $position = $row['position'];
    $edition = new Edition();
    if(empty($row['position'])) {
        $edition->Position = 1;
    }
    else {
        $edition->Position = $row['position'] + 1;
    }
    $edition->Timespan = 12 - round($row['timespan1'], 2);
    array_push($editions, $edition);
}

// Определяем следующие смены
$sum_timespans = $editions[0]->Timespan;
while ($sum_timespans < $work_time_1) {
    $edition = new Edition();
    $edition->Timespan = min(12, $work_time_1 - $sum_timespans);
    $edition->Position = 1;
    $sum_timespans += $edition->Timespan;
    array_push($editions, $edition);
}

print_r($editions); echo "<br />";

echo json_encode(array('machine_id' => $machine_id, 'from' => $from, 'error' => $error));
?>