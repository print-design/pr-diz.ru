<?php
include '../include/topscripts.php';

$result = array();

array_push($result, 'Modern Talking');
array_push($result, 'Joy');
array_push($result, 'Fancy');

array_push($result, $_GET['q']);

echo json_encode($result);
?>