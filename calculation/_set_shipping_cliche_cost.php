<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');
$shipping_cliche_cost = filter_input(INPUT_GET, 'shipping_cliche_cost');
$result = array();

if(empty($id)) {
    $result['error'] = "Не указан ID расчёта";
}
elseif($shipping_cliche_cost === null || $shipping_cliche_cost === '') {
    $result['error'] = "Не указана отгрузочная стоимость ПФ";
}
else {
    $sql = "update calculation_result set shipping_cliche_cost = $shipping_cliche_cost where calculation_id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    $cliche_cost = 0;
    
    if(empty($error_message)) {
        $sql = "select cliche_cost from calculation_result where calculation_id = $id";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        if($row = $fetcher->Fetch()) {
            $cliche_cost = $row['cliche_cost'];
        }
    }
    
    // Коэффициент НулеваяСебестоимостьПФ
    $uk_costpf = 1;
    
    if($cliche_cost > 0) {
        $uk_costpf = 0;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation c inner join calculation_result cr on c.id = cr.calculation_id set c.extracharge_cliche = (100 * ($shipping_cliche_cost - $cliche_cost) / ($cliche_cost + $uk_costpf))";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    $shipping_cost = 0;
    $income = 0;
    $income_per_unit = 0;
    
    if(empty($error_message)) {
        $calculation = CalculationBase::Create($id);
        
        if($calculation instanceof CalculationBase) {
            $shipping_cost = $calculation->shipping_cost;
            $income = $calculation->income;
            $income_per_unit = $calculation->income_per_unit;
        }
        else {
            $error_message = $calculation;
        }
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set shipping_cost = $shipping_cost, income = $income, income_per_unit = $income_per_unit where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select c.extracharge_cliche, cr.shipping_cliche_cost, cr.income, cr.income_per_unit, cr.income_cliche, cr.income_knife from calculation_result cr inner join calculation c on cr.calculation_id = c.id where c.id = $id ";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $result['extracharge_cliche'] = $row['extracharge_cliche'];
            $result['shipping_cliche_cost'] = DisplayNumber(floatval($row['shipping_cliche_cost']), 0);
            $result['income_cliche'] = DisplayNumber(floatval($row['income_cliche']), 0);
            $result['income_total'] = DisplayNumber(round(floatval($row['income']), 0) + round(floatval($row['income_cliche']), 0) + round(floatval($row['income_knife']), 0), 0);
        }
    }
    
    $result['error'] = $error_message;
}

echo json_encode($result);
?>