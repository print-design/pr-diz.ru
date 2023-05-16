<?php
require_once '../include/topscripts.php';
require_once '../calculation/status_ids.php';
require_once '../include/works.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$lamination = filter_input(INPUT_GET, 'lamination');
$work_id = filter_input(INPUT_GET, 'work_id');
$error = '';

$sql = "delete from plan_edition where calculation_id = $calculation_id and lamination = $lamination and work_id = $work_id";
$executer = new Executer($sql);
$error = $executer->error;

// Статус меняем на "ожидаем постановки в план" только если тип работы "печать"
if(empty($error) && $work_id == WORK_PRINTING) {
    $sql = "update calculation set status_id = ".CONFIRMED." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>