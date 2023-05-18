<?php
require_once '../include/topscripts.php';
require_once '../calculation/calculation.php';
require_once '../calculation/status_ids.php';
require_once '../include/works.php';

$part_id = filter_input(INPUT_GET, 'part_id');
$work_id = filter_input(INPUT_GET, 'work_id');
$error = '';

// Получаем данные по расчёту
$calculation_id = null;
$work_type_id = 0;
$has_lamination = false;

$sql = "select c.id, c.work_type_id, c.lamination1_film_variation_id, c.lamination1_individual_film_name "
        . "from calculation c "
        . "inner join plan_part pp on pp.calculation_id = c.id "
        . "where pp.id = $part_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $calculation_id = $row['id'];
    $work_type_id = $row['work_type_id'];
    if(!empty($row['lamination1_film_variation_id']) || !empty($row['lamination1_individual_film_name'])) {
        $has_lamination = true;
    }
}

// Устанавливаем признак "не в плане"
$sql = "update plan_part set in_plan = 0, machine_id = null, date = null, shift = null, position = null where id = $part_id";
$executer = new Executer($sql);
$error = $executer->error;

// Статус устанавливаем "в плане", если есть части заказа в плане И нет частей заказа не в плане.
// Статус устанавливаем "ожидание постановки в план", если нет частей заказа в плане ИЛИ есть части заказа не в плане.
// Должны выполняться следующие условия:
// 1. тип работы "печать", а тип заказа "плёнка с печатью",
// 2. тип работы "ламинация", а тип заказа "плёнка без печати, но с ламинацией",
// 3. тип работы "резка", а тип заказа "плёнка без печати и без ламинации"
if((empty($error) && $work_id == WORK_PRINTING && $work_type_id == CalculationBase::WORK_TYPE_PRINT) 
        || (empty($error) && $work_id == WORK_LAMINATION && $work_type_id == CalculationBase::WORK_TYPE_NOPRINT && $has_lamination) 
        || (empty($error) && $work_id == WORK_CUTTING && $work_type_id == CalculationBase::WORK_TYPE_NOPRINT && !$has_lamination)) {
    $parts_in_plan = 0;
    $parts_not_in_plan = 0;

    if($calculation_id > 0) {
        $sql = "select count(id) from plan_part where in_plan = 1 and calculation_id = $calculation_id and work_id = $work_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $parts_in_plan = $row[0];
        }
    
        $sql = "select count(id) from plan_part where in_plan = 0 and calculation_id = $calculation_id and work_id = $work_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $parts_not_in_plan = $row[0];
        }
    }

    if($parts_in_plan > 0 && $parts_not_in_plan == 0) {
        $sql = "update calculation set status_id = ".PLAN." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    else {
        $sql = "update calculation set status_id = ".CONFIRMED." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

echo json_encode(array('error' => $error));
?>