<?php
include '../include/topscripts.php';

$id = null;
$printed = null;
$machine_id = null;

$sql = "select cts.id, cts.printed, pe.machine_id, cs.calculation_id "
        . "from calculation_not_take_stream cts "
        . "inner join calculation_stream cs on cts.calculation_stream_id = cs.id "
        . "inner join plan_edition pe on pe.calculation_id = cs.calculation_id "
        . "where cts.plan_employee_id is null and cts.plan_employee_tested = false and pe.work_id = ". WORK_CUTTING
        . " order by cts.id desc";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $id = $row['id'];
    $printed = $row['printed'];
    $machine_id = $row['machine_id'];
    $calculation_id = $row['calculation_id'];
    $printed_date = DateTime::createFromFormat("Y-m-d H:i:s", $printed);
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
    
    $employee_id = null;
    $sql = "select employee1_id from plan_workshift1 where date_format(date, '%d-%m-%Y')='".$working_date->format('d-m-Y')."' and shift = '$working_shift' and work_id = ". WORK_CUTTING." and machine_id = $machine_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $employee_id = $row[0];
    }
    
    if(!empty($employee_id)) {
        $sql = "update calculation_not_take_stream set plan_employee_id = $employee_id where id = $id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    
    $sql = "update calculation_not_take_stream set plan_employee_tested = true where id = $id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

$result = 0;

$sql = "select count(id) from calculation_not_take_stream where plan_employee_id is not null and plan_employee_tested = true";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $result = $row[0];
}

echo $result;
?>