<?php
require_once '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$error = "";

$plan_part_id = 0;

$sql = "select plan_part_id from plan_part_continuation where id = $id";
$fetcher = new Fetcher($sql);
$error = $fetcher->error;
if($row = $fetcher->Fetch()) {
    $plan_part_id = $row[0];
}

$sum_worktime = 0;

if(empty($error)) {
    $sql = "select sum(worktime) from plan_part_continuation where plan_part_id = $plan_part_id and id > $id";
    $fetcher = new Fetcher($sql);
    $error = $fetcher->error;
    if($row = $fetcher->Fetch()) {
        $sum_worktime = $row[0];
    }
}

if(empty($error)) {
    $sql = "update plan_part_continuation set has_continuation = 0, worktime = worktime + $sum_worktime where id = $id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

if(empty($error)) {
    $sql = "delete from plan_part_continuation where plan_part_id = $plan_part_id and id > $id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>