<?php
require_once '../include/topscripts.php';
require_once '../calculation/status_ids.php';

$part_id = filter_input(INPUT_GET, 'part_id');
$error = '';

$sql = "update plan_part set in_plan = 0, work_id = null, machine_id = null, date = null, shift = null, position = null where id = $part_id";
$executer = new Executer($sql);
$error = $executer->error;

$calculation_id = 0;

if(empty($error)) {
    $sql = "select calculation_id from plan_part where id = $part_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $calculation_id = $row[0];
    }
    
    $parts_in_plan = 0;
    $parts_not_in_plan = 0;

    if($calculation_id > 0) {
        $sql = "select count(id) from plan_part where in_plan = 1 and calculation_id = $calculation_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $parts_in_plan = $row[0];
        }
    
        $sql = "select count(id) from plan_part where in_plan = 0 and calculation_id = $calculation_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $parts_not_in_plan = $row[0];
        }
    }

    if($parts_in_plan > 0 && $parts_not_in_plan == 0) {
        $sql = "update calculation set status_id = ".PLAN." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    else {
        $sql = "update calculation set status_id = ".CONFIRMED." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

echo json_encode(array('error' => $error));
?>