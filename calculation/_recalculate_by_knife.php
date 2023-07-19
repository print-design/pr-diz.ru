<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');
$knife_in_price = filter_input(INPUT_GET, 'knife_in_price');
$customer_pays_for_knife = filter_input(INPUT_GET, 'customer_pays_for_knife');
$result = array();

if(empty($id)) {
    $result['error'] = "Не указан ID расчёта";
}
elseif($knife_in_price === null || $knife_in_price === '') {
    $result['error'] = "Не указан параметр Включить нож в себестоимость";
}
elseif($customer_pays_for_knife === null || $customer_pays_for_knife === '') {
    $result['error'] = "Не указан параметр Заказчик платит за нож";
}
else {
    $sql = "update calculation set knife_in_price = $knife_in_price, customer_pays_for_knife = $customer_pays_for_knife where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    $cost = 0;
    $cost_per_unit = 0;
    $shipping_cost = 0;
    $shipping_cost_per_unit = 0;
    $income = 0;
    $income_per_unit = 0;
    $shipping_knife_cost = 0;
    $income_knife = 0;
    
    if(empty($error_message)) {
        $calculation = CalculationBase::Create($id);
        
        if($calculation instanceof CalculationBase) {
            $cost = $calculation->cost;
            $cost_per_unit = $calculation->cost_per_unit;
            $shipping_cost = $calculation->shipping_cost;
            $shipping_cost_per_unit = $calculation->shipping_cost_per_unit;
            $income = $calculation->income;
            $income_per_unit = $calculation->income_per_unit;
            $shipping_knife_cost = $calculation->shipping_knife_cost;
            $income_knife = $calculation->income_knife;
        }
        else {
            $error_message = $calculation;
        }
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set cost = $cost, cost_per_unit = $cost_per_unit, shipping_cost = $shipping_cost, shipping_cost_per_unit = $shipping_cost_per_unit, income = $income, income_per_unit = $income_per_unit, shipping_knife_cost = $shipping_knife_cost, income_knife = $income_knife where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select c.knife_in_price, c.customer_pays_for_knife, c.extracharge, "
                . "cr.cost, cr.cost_per_unit, cr.shipping_cost, cr.shipping_cost_per_unit, cr.income, cr.income_per_unit, cr.shipping_knife_cost, cr.income_cliche, cr.income_knife "
                . "from calculation c inner join calculation_result cr on cr.calculation_id = c.id "
                . "where c.id = $id "
                . "order by c.id desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $result['knife_in_price'] = $row['knife_in_price'];
            $result['customer_pays_for_knife'] = $row['customer_pays_for_knife'];
            $result['extracharge'] = $row['extracharge'];
            $result['cost'] = DisplayNumber(floatval($row['cost']), 0);
            $result['cost_per_unit'] = DisplayNumber(floatval($row['cost_per_unit']), 3);
            $result['shipping_cost'] = DisplayNumber(floatval($row['shipping_cost']), 0);
            $result['shipping_cost_per_unit'] = DisplayNumber(floatval($row['shipping_cost_per_unit']), 3);
            $result['input_shipping_cost_per_unit'] = round(floatval($row['shipping_cost_per_unit']), 3);
            $result['income'] = DisplayNumber(floatval($row['income']), 0);
            $result['income_per_unit'] = DisplayNumber(floatval($row['income_per_unit']), 3);
            $result['shipping_knife_cost'] = DisplayNumber(floatval($row['shipping_knife_cost']), 0);
            $result['income_knife'] = DisplayNumber(floatval($row['income_knife']), 0);
            $result['income_total'] = DisplayNumber(round(floatval($row['income']), 0) + round(floatval($row['income_cliche']), 0) + round(floatval($row['income_knife']), 0), 0);
        }
    }
    
    $result['error'] = $error_message;
}
            
echo json_encode($result);
?>
