<?php
include '../include/topscripts.php';
include './calculation.php';

$ink_id = filter_input(INPUT_GET, 'ink_id');
$ink_sequence = filter_input(INPUT_GET, 'ink_sequence');
$cliche = filter_input(INPUT_GET, 'cliche');
$machine_coeff = filter_input(INPUT_GET, 'machine_coeff');

$result = array();

$result['error'] = '';
$result['ink_id'] = $ink_id;
$result['ink_sequence'] = $ink_sequence;
$result['cliche'] = $cliche;
$result['machine_coeff'] = $machine_coeff;

$result['flint_used'] = 2;
$result['kodak_used'] = 25;
$result['old_used'] = 5;

echo json_encode($result);
?>