<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');
$shipping_cost_per_unit = filter_input(INPUT_GET, 'shipping_cost_per_unit');
$result = array();

if(empty($id)) {
    $result['error'] = "Не указан ID расчёта";
}
elseif($shipping_cost_per_unit === null || $shipping_cost_per_unit === '') {
    $result['error'] = "Не указана отгрузочная стоимость за единицу";
}
else {
    $sql = "update calculation_result set shipping_cost_per_unit = $shipping_cost_per_unit where calculation_id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation c inner join calculation_result cr on c.id = cr.calculation_id set c.extracharge = (((cr.shipping_cost_per_unit * (select sum(quantity) from calculation_quantity where calculation_id = $id)) - cr.cost) / cr.cost) * 100 where c.id = $id and c.work_type_id = ".CalculationBase::WORK_TYPE_SELF_ADHESIVE;
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty ($error_message)) {
        $sql = "update calculation c inner join calculation_result cr on c.id = cr.calculation_id set c.extracharge = (((cr.shipping_cost_per_unit * c.quantity) - cr.cost) / cr.cost) * 100 where c.id = $id and c.work_type_id <> ".CalculationBase::WORK_TYPE_SELF_ADHESIVE;
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
        $sql = "select c.extracharge, cr.shipping_cost, cr.shipping_cost_per_unit, cr.income, cr.income_per_unit, cr.income_cliche, cr.income_knife from calculation_result cr inner join calculation c on cr.calculation_id = c.id where c.id = $id";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        // Значения shipping_cost, income, income_total дополнительно корректируем, чтобы не было "разницы в 1 рубль"
        if($row = $fetcher->Fetch()) {
            $result['extracharge'] = $row['extracharge'];
            $result['shipping_cost_per_unit'] = CalculationBase::Display(floatval($row['shipping_cost_per_unit']), 3);
            $result['shipping_cost'] = CalculationBase::Display(round(floatval($row['shipping_cost_per_unit']), 3) * $calculation->quantity, 0);
            $result['income_per_unit'] = CalculationBase::Display(floatval($row['income_per_unit']), 3);
            $result['income'] = CalculationBase::Display(round(floatval($row['income_per_unit']), 3) * $calculation->quantity, 0);
            $result['income_total'] = CalculationBase::Display(round(round(floatval($row['income_per_unit']), 3) * $calculation->quantity, 0) + round(floatval($row['income_cliche']), 0) + round(floatval($row['income_knife']), 0), 0);
        }
    }
    
    $result['error'] = $error_message;
}

echo json_encode($result);
?>