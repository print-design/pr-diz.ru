<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';
include '../calculation/calculation_result.php';

$id = 0;
$result = 0;
$error_message = '';

$sql = $sql = "select c.id "
        . "from calculation_take_stream cts "
        . "inner join calculation_take ct on cts.calculation_take_id = ct.id "
        . "inner join calculation c on ct.calculation_id = c.id "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "where c.duplicate_shipping_cost = 0 and cts.weight > 0 and cts.length > 0 and cr.shipping_cost_per_unit <> 0 ";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $id = $row[0];
}

if($id > 0) {
    $calculation = CalculationBase::Create($id);
    $calculation_result = CalculationResult::Create($id);
    
    // Стоимость заказа
    $shipping_order_cost = 0;
    
    if($calculation->unit == KG) {
        $shipping_order_cost = round($calculation->weight_cut, 2) * round($calculation_result->shipping_cost_per_unit, 3);
    }
    else {
        $shipping_order_cost = floor($calculation->length_cut * $calculation->number_in_meter) * round($calculation_result->shipping_cost_per_unit, 3);
    }
    
    // Общая стоимость
    $shipping_cost = $shipping_order_cost + $calculation_result->shipping_cliche_cost + $calculation_result->shipping_knife_cost;
    
    // Заполняем дублирующееся поле
    $sql = "update calculation set duplicate_shipping_cost = $shipping_cost where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

if(empty($error_message)) {
    $sql = "select count(distinct c.id) "
            . "from calculation_take_stream cts "
            . "inner join calculation_take ct on cts.calculation_take_id = ct.id "
            . "inner join calculation c on ct.calculation_id = c.id "
            . "inner join calculation_result cr on cr.calculation_id = c.id "
            . "where c.duplicate_shipping_cost = 0 and cts.weight > 0 and cts.length > 0 and cr.shipping_cost_per_unit <> 0 ";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result = $row[0];
    }
}
echo $result;
?>