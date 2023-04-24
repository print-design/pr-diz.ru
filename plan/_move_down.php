<?php
require_once '../include/topscripts.php';

$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$error = '';

echo json_encode($error);
?>