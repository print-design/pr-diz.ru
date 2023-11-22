<?php
require_once '../include/topscripts.php';
$source_id = filter_input(INPUT_GET, 'source_id');
$target_id = filter_input(INPUT_GET, 'target_id');
$error = 'Ошибка при перетаскивании ручьёв';

echo json_encode(array('error' => $error));
?>