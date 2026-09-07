<?php
require_once '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$error = "";

$plan_edition_id = 0;

$sql = "select plan_edition_id from plan_continuation where id = ?";
$fetcher = new Fetcher($sql, [$id]);
$error = $fetcher->error;
if($row = $fetcher->Fetch()) {
    $plan_edition_id = $row[0];
}

$sum_worktime = 0;

if(empty($error)) {
    $sql = "select sum(worktime) from plan_continuation where plan_edition_id = ? and id > ?";
    $fetcher = new Fetcher($sql, [$plan_edition_id, $id]);
    $error = $fetcher->error;
    if($row = $fetcher->Fetch()) {
        $sum_worktime = $row[0];
    }
}

if(empty($error)) {
    $sql = "update plan_continuation set has_continuation = 0, worktime = worktime + ? where id = ?";
    $executer = new Executer($sql, [$sum_worktime, $id]);
    $error = $executer->error;
}

if(empty($error)) {
    $sql = "delete from plan_continuation where plan_edition_id = ? and id > ?";
    $executer = new Executer($sql, [$plan_edition_id, $id]);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>