<?php
require_once '../include/topscripts.php';
require_once '../calculation/status_ids.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$error = '';

$sql = "delete from plan_edition where calculation_id = $calculation_id";
$executer = new Executer($sql);
$error = $executer->error;

if(empty($error)) {
    $sql = "update calculation set status_id = ".CONFIRMED." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>