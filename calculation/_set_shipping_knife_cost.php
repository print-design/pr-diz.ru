<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');
$shipping_knife_cost = filter_input(INPUT_GET, 'shipping_knife_cost');
$result = array();

if(empty($id)) {
    $result['error'] = "Не указан ID расчёта";
}
elseif($shipping_knife_cost === null || $shipping_knife_cost === '') {
    $result['error'] = "Не указана отгрузочная стоимость ножа";
}
else {
    $sql = "update calculation_result set shipping_knife_cost = $shipping_knife_cost where calculation_id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    $knife_cost = 0;
    
    if(empty($error_message)) {
        $sql = "select knife_cost from calculation_result where calculation_id = $id";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        if($row = $fetcher->Fetch()) {
            $knife_cost = $row['knife_cost'];
        }
    }
    
    // Коэффициент НулеваяСебестоимостьНожа
    $uk_costknife = 1;
    
    if($knife_cost > 0) {
        $uk_costknife = 0;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation c inner join calculation_result cr on c.id = cr.calculation_id set c.extracharge_knife = (100 * ($shipping_knife_cost - $knife_cost) / ($knife_cost + $uk_costknife)) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    $income_knife = 0;
    
    if(empty($error_message)) {
        $calculation = CalculationBase::Create($id);
        
        if($calculation instanceof CalculationBase) {
            $income_knife = $calculation->income_knife;
        }
        else {
            $error_message = $calculation;
        }
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set income_knife = $income_knife where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select c.extracharge_knife, cr.shipping_knife_cost, cr.income, cr.income_per_unit, cr.income_cliche, cr.income_knife from calculation_result cr inner join calculation c on cr.calculation_id = c.id where c.id = $id order by cr.id desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $result['extracharge_knife'] = $row['extracharge_knife'];
            $result['shipping_knife_cost'] = DisplayNumber(floatval($row['shipping_knife_cost']), 0);
            $result['income_knife'] = DisplayNumber(floatval($row['income_knife']), 0);
            $result['income_total'] = DisplayNumber(round(floatval($row['income']), 0) + round(floatval($row['income_cliche']), 0) + round(floatval($row['income_knife']), 0), 0);
        }
    }
    
    $result['error'] = $error_message;
}

echo json_encode($result);
?>