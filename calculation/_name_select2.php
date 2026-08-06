<?php
include '../include/topscripts.php';

$result = array();

array_push($result, array('id' => 1, 'text' => 'Modern Talking'));
array_push($result, array('id' => 2, 'text' => 'Joy'));
array_push($result, array('id' => 3, 'text' => 'Fancy'));

$q = isset($_GET['q']) ? $_GET['q'] : '';

array_push($result, array('id' => 4, $q));

echo json_encode($result);
?>