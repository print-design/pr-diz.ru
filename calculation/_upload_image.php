<?php
include '../include/topscripts.php';

$result = array('error' => '');
$result['info'] = "Мы только начинаем";
$result['to_plan_visible'] = true;

echo json_encode($result);
?>