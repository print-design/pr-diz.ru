<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');
$extracharge_cliche = filter_input(INPUT_GET, 'extracharge_cliche');
$result = array();

if(empty($id)) {
    $result['error'] = "Не указан ID расчёта";
}
elseif($extracharge_cliche === null || $extracharge_cliche === '') {
    $result['error'] = "Не указан размер наценки";
}
else {
    $sql = "update calculation set extracharge_cliche = $extracharge_cliche where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    $shipping_cliche_cost = 0;
    $income_cliche = 0;
    
    if(empty($error_message)) {
        $calculation = CalculationBase::Create($id);
        
        if($calculation instanceof CalculationBase) {
            $shipping_cliche_cost = $calculation->shipping_cliche_cost;
            $income_cliche = $calculation->income_cliche;
        }
        else {
            $error_message = $calculation;
        }
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set shipping_cliche_cost = $shipping_cliche_cost, income_cliche = $income_cliche where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select shipping_cliche_cost, income, income_per_unit, income_cliche, income_knife from calculation_result where calculation_id = $id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        // Значения income_total дополнительно корректируем, чтобы не было "разницы в 1 рубль"
        if($row = $fetcher->Fetch()) {
            $result['shipping_cliche_cost'] = CalculationBase::Display(floatval($row['shipping_cliche_cost']), 0);
            $result['income_cliche'] = CalculationBase::Display(floatval($row['income_cliche']), 0);
            $result['income_total'] = CalculationBase::Display(round(floatval($row['income_per_unit']), 3) * $calculation->quantity + round(floatval($row['income_cliche']), 0) + round(floatval($row['income_knife']), 0), 0);
        }
    }
    
    $result['error'] = $error_message;    
}

echo json_encode($result);
?>