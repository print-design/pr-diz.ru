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
        $sql = "update calculation c inner join calculation_result cr on c.id = cr.calculation_id set c.extracharge_cliche = (100 * ($shipping_cliche_cost - $cliche_cost) / ($cliche_cost + $uk_costpf)) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    $income_cliche = 0;
    
    if(empty($error_message)) {
        $calculation = CalculationBase::Create($id);
        
        if($calculation instanceof CalculationBase) {
            $income_cliche = $calculation->income_cliche;
        }
        else {
            $error_message = $calculation;
        }
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set income_cliche = $income_cliche where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select c.extracharge_cliche, cr.shipping_cliche_cost, cr.income, cr.income_per_unit, cr.income_cliche, cr.income_knife from calculation_result cr inner join calculation c on cr.calculation_id = c.id where c.id = $id order by cr.id desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $result['extracharge_cliche'] = $row['extracharge_cliche'];
            $result['shipping_cliche_cost'] = DisplayNumber(floatval($row['shipping_cliche_cost']), 0);
            $result['income_cliche'] = DisplayNumber(floatval($row['income_cliche']), 0);
            $result['income_total'] = DisplayNumber(round(floatval($row['income'] ?? 0), 0) + round(floatval($row['income_cliche'] ?? 0), 0) + round(floatval($row['income_knife'] ?? 0), 0), 0);
        }
    }
    
    $result['error'] = $error_message;
}

echo json_encode($result);
?>