<?php
require_once '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$error = "";

$sql = "update plan_continuation set has_continuation = 0, worktime = worktime + (select sum(worktime) from plan_continuation where plan_edition_id = (select plan_edition_id from plan_continuation where id = $id) and id > $id) where id = $id";
$executer = new Executer($sql);
$error = $executer->error;

if(empty($error)) {
    $sql = "delete from plan_continuation where plan_edition_id = (select plan_edition_id from plan_continuation where id = $id) and id > $id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>