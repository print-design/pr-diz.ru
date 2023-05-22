<?php
require_once '../include/topscripts.php';
require_once '../calculation/calculation.php';
require_once '../calculation/status_ids.php';
require_once '../include/works.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$lamination = filter_input(INPUT_GET, 'lamination');
$work_id = filter_input(INPUT_GET, 'work_id');
$error = '';

// Получаем данные по расчёту
$work_type_id = 0;
$has_lamination = false;
$two_laminations = false;

$sql = "select work_type_id, lamination1_film_variation_id, lamination1_individual_film_name, lamination2_film_variation_id, lamination2_individual_film_name from calculation where id = $calculation_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $work_type_id = $row['work_type_id'];
    
    if(!empty($row['lamination1_film_variation_id']) || !empty($row['lamination1_individual_film_name'])) {
        $has_lamination = true;
    }
    
    if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
        $two_laminations = true;
    }
}

$sql = "delete from plan_edition where calculation_id = $calculation_id and lamination = $lamination and work_id = $work_id";
$executer = new Executer($sql);
$error = $executer->error;

if(empty($error) && $work_id == WORK_PRINTING) {
    // 1. Тип работы "печать".
    // Удаляем все половинки этого заказа во всех разделах (ВРЕМЕННО - только вне плана).
    // Статус устанавливаем "ожидание постановки в план".
    $sql = "delete from plan_part where in_plan = 0 and calculation_id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    $sql = "update calculation set status_id = ".CONFIRMED." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_LAMINATION && $work_type_id == CalculationBase::WORK_TYPE_NOPRINT) {
    // 2. Тип работы "ламинация", тип заказа "плёнка без печати".
    // Удаляем все половинки этого заказа в разделе "резка" (ВРЕМЕННО - только вне плана)
    // Статус устанавливаем "ожидание постановки в план".
    $sql = "delete from plan_part where in_plan = 0 and calculation_id = $calculation_id and work_id = ".WORK_CUTTING;
    $executer = new Executer($sql);
    $error = $executer->error;
    
    $sql = "update calculation set status_id = ".CONFIRMED." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_LAMINATION && $work_type_id == CalculationBase::WORK_TYPE_PRINT) {
    // 3. Тип работы "ламинация", тип заказа "плёнка с печатью".
    // Удаляем все половинки этого заказа в разделе "резка" (ВРЕМЕННО - только вне плана)
    // Статус устанавливаем "в плане печати".
    $sql = "delete from plan_part where in_plan = 0 and calculation_id = $calculation_id and work_id = ".WORK_CUTTING;
    $executer = new Executer($sql);
    $error = $executer->error;
    
    $sql = "update calculation set status_id = ".PLAN_PRINT." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_CUTTING && !$has_lamination && $work_type_id == CalculationBase::WORK_TYPE_NOPRINT) {
    // 6. Тип работы "резка", ламинации нет, тип заказа "плёнка без печати".
    // Статус устанавливаем "ожидание постановки в план".
    $sql = "update calculation set status_id = ".CONFIRMED." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_CUTTING && !$has_lamination && $work_type_id == CalculationBase::WORK_TYPE_PRINT) {
    // 7. Тип работы "резка", ламинации нет, тип заказа "плёнка с печатью".
    // Статус устанавливаем "в плане печати".
    $sql = "update calculation set status_id = ".PLAN_PRINT." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_CUTTING && $has_lamination) {
    // 8. Тип работы "резка", ламинация есть.
    // Статус устанавливаем "в плане ламинации".
    $sql = "update calculation set status_id = ".PLAN_LAMINATE." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_CUTTING && $work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE) {
    // 9. Тип работы "резка", тип заказа "самоклеящиеся материалы".
    // Статус устанавливаем "в плане печати".
    $sql = "update calculation set status_id = ".PLAN_PRINT." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>