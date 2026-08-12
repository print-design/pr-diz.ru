<?php
require_once '../include/topscripts.php';

$event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
$error = '';

$sql = "update plan_event set in_plan = 0, date = null, shift = null, position = null where id = $event_id";
$executer = new Executer($sql);
$error = $executer->error;

echo json_encode(array('error' => $error));
?>