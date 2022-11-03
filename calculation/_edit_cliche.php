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

switch($cliche) {
    case CalculationBase::FLINT:
        $result['cliche'] = "Новая Flint $machine_coeff";
        break;
    case CalculationBase::KODAK:
        $result['cliche'] = "Новая Kodak $machine_coeff";
        break;
    case CalculationBase::OLD:
        $result['cliche'] = "Старая";
        break;
    default :
        $result['cliche'] = $cliche;
}

$result['flint_used'] = 111;
$result['kodak_used'] = 222;
$result['old_used'] = 333;

$result['flint_hidden'] = 0;
$result['kodak_hidden'] = 1;
$result['old_hidden'] = 0;

echo json_encode($result);
?>