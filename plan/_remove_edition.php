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

// Статус устанавливаем "в плане", если при наличии 2 ламинаций - 2 тиража, в других случаях - 1 тираж.
// Статус устанавливаем "ожидание постановки в план", если нет ни одного тиража по этому заказу, е если там две ламинации = если менее двух тиражей.
// Должны выполняться следующие условия:
// 1. тип работы "печать", а тип заказа "плёнка с печатью",
// 2. тип работы "печать", а тип заказа "самоклеящиеся материалы",
// 3. тип работы "ламинация", а тип заказа "плёнка без печати, но с ламинацией",
// 4. тип работы "резка", а тип заказа "плёнка без печати и без ламинации"
if((empty($error) && $work_id == WORK_PRINTING && $work_type_id == CalculationBase::WORK_TYPE_PRINT) 
        || (empty($error) && $work_id == WORK_PRINTING && $work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE) 
        || (empty($error) && $work_id == WORK_LAMINATION && $work_type_id == CalculationBase::WORK_TYPE_NOPRINT && $has_lamination) 
        || (empty($error) && $work_type_id == WORK_CUTTING && $work_type_id == CalculationBase::WORK_TYPE_NOPRINT && !$has_lamination)) {
    $editions_count = 0;
    
    $sql = "select count(id) from plan_edition where calculation_id = $calculation_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $editions_count = $row[0];
    }
    
    if(($two_laminations && $editions_count == 2) || (!$two_laminations && $editions_count == 1)) {
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