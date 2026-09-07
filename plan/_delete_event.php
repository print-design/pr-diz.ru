<?php
require_once '../include/topscripts.php';

$event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
$error = '';

$sql = "delete from plan_event where id = ?";
$executer = new Executer($sql, [$event_id]);
$error = $executer->error;

echo json_encode(array('error' => $error));
?>
