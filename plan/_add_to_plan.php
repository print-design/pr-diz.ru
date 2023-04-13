<?php
require_once '../include/topscripts.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$before = filter_input(INPUT_GET, 'before');
$error = '';

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

// Определяем, сколько места в нужной смене
$position = 0;
$timespans = array();

// Если не указываем следующий расчёт, то размер смены равен 12 минус сумма всех расчётов данной смены
// Если указываем следующий расчёт, то размер смены равен 12 минус сумма всех расчётов смены, кроме тех, у кого position меньше, чем position следующего расчёта
if(empty($before)) {
    $sql = "select 12 - sum(e.timespan) timespan1, max(position) position "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $position = $row['position'];
        array_push($timespans, round($row['timespan1'], 2));
    }
}
else {
    $sql = "select 12 - sum(e.timespan) timespan1, max(position) position "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $position = $row['position'];
        array_push($timespans, round($row['timespan1'], 2));
    }
}

echo $position."<br />";
print_r($timespans); echo "<br />";

echo json_encode(array('machine_id' => $machine_id, 'from' => $from, 'error' => $error));
?>