<?php
include '../include/topscripts.php';

$id = null;
$printed = null;

$sql = "select id, printed from calculation_take_stream where plan_employee_id is null";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $id = $row['id'];
    $printed = $row['printed'];
    $printed_date = DateTime::createFromFormat('Y-m-d H:i:s', $printed);
    $working_date = clone $printed_date;
    $working_hour = $working_date->format('G');
    $working_shift = 'day';
    
    if($working_hour > 19 && $working_hour < 24) {
        $working_shift = 'night';
    }
    elseif ($working_hour >= 0 && $working_hour < 8) {
        $working_shift = 'night';
        $working_date->modify("-1 day");
    }
    
    echo $id."<br />";
    echo $printed."<br />";
    echo $working_hour."<br />";
}

$result = 0;

$sql = "select count(id) from calculation_take_stream where plan_employee_id is not null";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $result = $row[0];
}

echo $result;
?>