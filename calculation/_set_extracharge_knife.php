<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');
$extracharge_knife = filter_input(INPUT_GET, 'extracharge_knife');
$result = array();

if(empty($id)) {
    $result['error'] = "Не указан ID расчёта";
}
elseif($extracharge_knife === null || $extracharge_knife === '') {
    $result['error'] = "Не указан размер наценки";
}
else {
    $error_message = '';
    
    $sql = "update calculation set extracharge_knife=$extracharge_knife where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_knife_cost = (cr.knife_cost + (cr.knife_cost * c.extracharge_knife / 100)) * c.customer_pays_for_knife * ((c.knife_in_price - 1) / -1) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.income_knife = (cr.shipping_knife_cost - cr.knife_cost) * ((c.knife_in_price - 1) / -1) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select shipping_knife_cost, income, income_cliche, income_knife from calculation_result where calculation_id = $id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $result['shipping_knife_cost'] = CalculationBase::Display(floatval($row['shipping_knife_cost']), 0);
            $result['income_knife'] = CalculationBase::Display(floatval($row['income_knife']), 0);
            $result['income_total'] = CalculationBase::Display(floatval($row['income']) + floatval($row['income_cliche']) + floatval($row['income_knife']), 0);
        }
    }
    
    $result['error'] = $error_message;
}

echo json_encode($result);
?>