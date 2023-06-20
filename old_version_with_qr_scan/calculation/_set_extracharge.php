<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');
$extracharge = filter_input(INPUT_GET, 'extracharge');
$result = array();

if(empty($id)) {
    $result['error'] = "Не указан ID расчёта";
}
elseif($extracharge === null || $extracharge === '') {
    $result['error'] = "Не указан размер наценки";
}
else {
    $sql = "update calculation set extracharge = $extracharge where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    $shipping_cost = 0;
    $shipping_cost_per_unit = 0;
    $income = 0;
    $income_per_unit = 0;
    
    if(empty($error_message)) {
        $calculation = CalculationBase::Create($id);
        
        if($calculation instanceof CalculationBase) {
            $shipping_cost = $calculation->shipping_cost;
            $shipping_cost_per_unit = $calculation->shipping_cost_per_unit;
            $income = $calculation->income;
            $income_per_unit = $calculation->income_per_unit;
        }
        else {
            $error_message = $calculation;
        }
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set shipping_cost = $shipping_cost, shipping_cost_per_unit = $shipping_cost_per_unit, income = $income, income_per_unit = $income_per_unit where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select shipping_cost, shipping_cost_per_unit, income, income_per_unit, income_cliche, income_knife from calculation_result where calculation_id = $id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $result['shipping_cost_per_unit'] = CalculationBase::Display(floatval($row['shipping_cost_per_unit']), 3);
            $result['input_shipping_cost_per_unit'] = round(floatval($row['shipping_cost_per_unit']), 3);
            $result['shipping_cost'] = CalculationBase::Display(floatval($row['shipping_cost']), 0);
            $result['income_per_unit'] = CalculationBase::Display(floatval($row['income_per_unit']), 3);
            $result['income'] = CalculationBase::Display(floatval($row['income']), 0);
            $result['income_total'] = CalculationBase::Display(round(floatval($row['income']), 0) + round(floatval($row['income_cliche']), 0) + round(floatval($row['income_knife']), 0), 0);
        }
    }
    
    $result['error'] = $error_message;
}

echo json_encode($result);
?>