<?php
include '../include/topscripts.php';
include './calculation.php';

$work_type_id = filter_input(INPUT_GET, 'work_type_id');
$extracharge_cliche = filter_input(INPUT_GET, 'extracharge_cliche');
$result = array();

if(empty($work_type_id)) {
    $result['error'] = "Не указан тип работы";
}
elseif(empty ($extracharge_cliche)) {
    $result['error'] = "Не указан размер наценки";
}
else {
    $result['error'] = '';
    $result['shipping_cliche_cost'] = $extracharge_cliche." ".$work_type_id;
}

echo json_encode($result);
?>