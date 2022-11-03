<?php
include '../include/topscripts.php';
include './calculation.php';

$machine_coeff = filter_input(INPUT_GET, 'machine_coeff');

$result = array();

$result['error'] = '';
$result['ink_id'] = 180;
$result['ink_sequence'] = 3;
$result['cliche'] = CalculationBase::KODAK." ".$machine_coeff;
$result['flint_hidden'] = 0;
$result['kodak_hidden'] = 1;
$result['old_hidden'] = 0;

echo json_encode($result);
?>