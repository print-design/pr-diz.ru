<?php
require_once '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$error = "";

$sql = "update plan_edition set worktime_continued = null where id = ?";
$executer = new Executer($sql, [$id]);
$error = $executer->error;

if(empty($error)) {
    $sql = "delete from plan_continuation where plan_edition_id = ?";
    $executer = new Executer($sql, [$id]);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>