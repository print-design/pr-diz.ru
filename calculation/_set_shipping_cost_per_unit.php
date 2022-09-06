<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');
$work_type_id = filter_input(INPUT_GET, 'work_type_id');
$shipping_cost_per_unit = filter_input(INPUT_GET, 'shipping_cost_per_unit');
$result = array();

if(empty($id)) {
    $result['error'] = "Не указан ID расчёта";
}
elseif(empty($work_type_id)) {
    $result['error'] = "Не указан тип работы";
}
elseif($shipping_cost_per_unit === null || $shipping_cost_per_unit === '') {
    $result['error'] = "Не указана отгрузочная стоимость за единицу";
}
else {
    $error_message = '';
    $quantity = 0;
    
    $result['extracharge'] = $shipping_cost_per_unit;
    
    $result['error'] = $error_message;
}

echo json_encode($result);
?>