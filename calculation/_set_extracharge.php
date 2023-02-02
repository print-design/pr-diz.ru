<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');
$work_type_id = filter_input(INPUT_GET, 'work_type_id');
$extracharge = filter_input(INPUT_GET, 'extracharge');
$result = array();

if(empty($id)) {
    $result['error'] = "Не указан ID расчёта";
}
elseif(empty($work_type_id)) {
    $result['error'] = "Не указан тип работы";
}
elseif($extracharge === null || $extracharge === '') {
    $result['error'] = "Не указан размер наценки";
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
    
    $sql = "update calculation set extracharge = $extracharge where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost = cr.cost + (cr.cost * c.extracharge / 100) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE && $quantity > 0 && empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost_per_unit = cr.shipping_cost / $quantity where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    elseif(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost_per_unit = cr.shipping_cost / c.quantity where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE && $quantity > 0 && empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.income = cr.shipping_cost - cr.cost - (c.extra_expense * $quantity) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    elseif(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.income = cr.shipping_cost - cr.cost - (c.extra_expense * c.quantity) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.income_per_unit = cr.shipping_cost_per_unit - cr.cost_per_unit - c.extra_expense where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select shipping_cost, shipping_cost_per_unit, income, income_per_unit, income_cliche, income_knife from calculation_result where calculation_id = $id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $result['shipping_cost'] = CalculationBase::Display(floatval($row['shipping_cost']), 0);
            $result['shipping_cost_per_unit'] = CalculationBase::Display(floatval($row['shipping_cost_per_unit']), 3);
            $result['input_shipping_cost_per_unit'] = floatval($row['shipping_cost_per_unit']);
            $result['income'] = CalculationBase::Display(floatval($row['income']), 0);
            $result['income_per_unit'] = CalculationBase::Display(floatval($row['income_per_unit']), 3);
            $result['income_total'] = CalculationBase::Display(floatval($row['income']) + floatval($row['income_cliche']) + floatval($row['income_knife']), 0);
        }
    }
    
    $result['error'] = $error_message;
}

echo json_encode($result);
?>