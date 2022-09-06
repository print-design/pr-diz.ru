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
    
    if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE) {
        $sql = "select sum(quantity) from calculation_quantity where calculation_id = $id order by id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $quantity = $row[0];
        }
    }
    
    $sql = "update calculation_result set shipping_cost_per_unit = $shipping_cost_per_unit where calculation_id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE && $quantity > 0 && empty($error_message)) {
        $sql = "update calculation c inner join calculation_result cr on c.id = cr.calculation_id set c.extracharge = (((cr.shipping_cost_per_unit * $quantity) - cr.cost) / cr.cost) * 100 where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    elseif(empty($error_message)) {
        $sql = "update calculation c inner join calculation_result cr on c.id = cr.calculation_id set c.extracharge = (((cr.shipping_cost_per_unit * c.quantity) - cr.cost) / cr.cost) * 100 where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select c.extracharge, cr.shipping_cost_per_unit from calculation_result cr inner join calculation c on cr.calculation_id = c.id where c.id = $id";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $result['extracharge'] = $row['extracharge'];
            $result['shipping_cost_per_unit'] = CalculationBase::Display(floatval($row['shipping_cost_per_unit']), 3);
        }
    }
    
    $result['error'] = $error_message;
}

echo json_encode($result);
?>