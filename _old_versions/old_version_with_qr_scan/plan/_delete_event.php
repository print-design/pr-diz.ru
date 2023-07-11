<?php
require_once '../include/topscripts.php';

$event_id = filter_input(INPUT_GET, 'event_id');
$error = '';

$sql = "delete from plan_event where id = $event_id";
$executer = new Executer($sql);
$error = $executer->error;

echo json_encode(array('error' => $error));
?>
