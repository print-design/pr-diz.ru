<?php
require_once '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$error = "";

$sql = "update plan_part set worktime_continued = null where id = $id";
$executer = new Executer($sql);
$error = $executer->error;

if(empty($error)) {
    $sql = "delete from plan_part_continuation where plan_part_id = $id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>