<?php
require_once '../include/topscripts.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$lamination = filter_input(INPUT_GET, 'lamination');
$work_id = filter_input(INPUT_GET, 'work_id');
$error = '';

// Получаем данные по расчёту.
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

// Если этот же заказ стоит в плане в последующих разделах,
// запрещаем удалять его из плана.
if(empty($error) && $work_id == WORK_PRINTING) {
    $in_lamination = 0;
    $in_cutting = 0;
    
    $sql = "select count(id) from plan_edition where calculation_id = $calculation_id and work_id = ".WORK_LAMINATION;
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $in_lamination = $row[0];
    }
    
    $sql = "select count(id) from plan_edition where calculation_id = $calculation_id and work_id = ".WORK_CUTTING;
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $in_cutting = $row[0];
    }
    
    if($in_lamination > 0 && $in_cutting == 0) {
        $error = "Этот заказ стоит в плане в ламинации.";
    }
    elseif($in_cutting > 0 && $in_lamination == 0) {
        $error = "Этот заказ стоит в плане в резке.";
    }
    elseif($in_lamination > 0 || $in_cutting > 0) {
        $error = "Этот заказ стоит в плане в ламинации и в резке.";
    }
}
elseif (empty ($error) && $work_id == WORK_LAMINATION) {
    $in_cutting = 0;
    
    $sql = "select count(id) from plan_edition where calculation_id = $calculation_id and work_id = ".WORK_CUTTING;
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $in_cutting = $row[0];
    }
    
    if($in_cutting > 0) {
        $error = "Этот заказ стоит в плане в резке.";
    }
}

// Удаляем из плана.
if(empty($error)) {
    $sql = "delete from plan_edition where calculation_id = $calculation_id and lamination = $lamination and work_id = $work_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

// Меняем статус и закрепляем наверзху очереди.
if(empty($error) && $work_id == WORK_PRINTING) {
    // 1. Тип работы "печать".
    // Статус устанавливаем "ожидание постановки в план".
    $error = SetCalculationStatus($calculation_id, ORDER_STATUS_CONFIRMED, '');
    
    if(empty($error)) {
        $sql = "update calculation set queue_top = 1 where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}
elseif(empty ($error) && $work_id == WORK_LAMINATION && $work_type_id == WORK_TYPE_NOPRINT) {
    // 2. Тип работы "ламинация", тип заказа "плёнка без печати".
    // Статус устанавливаем "ожидание постановки в план".
    $error = SetCalculationStatus($calculation_id, ORDER_STATUS_CONFIRMED, '');
    
    if(empty($error)) {
        $sql = "update calculation set queue_top = 1 where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}
elseif(empty ($error) && $work_id == WORK_LAMINATION && $work_type_id == WORK_TYPE_PRINT) {
    // 3. Тип работы "ламинация", тип заказа "плёнка с печатью".
    // Статус устанавливаем "в плане печати".
    $error = SetCalculationStatus($calculation_id, ORDER_STATUS_PLAN_PRINT, '');
    
    if(empty($error)) {
        $sql = "update calculation set queue_top = 1 where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}
elseif(empty ($error) && $work_id == WORK_CUTTING && !$has_lamination && $work_type_id == WORK_TYPE_NOPRINT) {
    // 6. Тип работы "резка", ламинации нет, тип заказа "плёнка без печати".
    // Статус устанавливаем "ожидание постановки в план".
    $error = SetCalculationStatus($calculation_id, ORDER_STATUS_CONFIRMED, '');
    
    if(empty($error)) {
        $sql = "update calculation set queue_top = 1 where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}
elseif(empty ($error) && $work_id == WORK_CUTTING && !$has_lamination && $work_type_id == WORK_TYPE_PRINT) {
    // 7. Тип работы "резка", ламинации нет, тип заказа "плёнка с печатью".
    // Статус устанавливаем "в плане печати".
    $error = SetCalculationStatus($calculation_id, ORDER_STATUS_PLAN_PRINT, '');
    
    if(empty($error)) {
        $sql = "update calculation set queue_top = 1 where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}
elseif(empty ($error) && $work_id == WORK_CUTTING && $has_lamination) {
    // 8. Тип работы "резка", ламинация есть.
    // Статус устанавливаем "в плане ламинации".
    $error = SetCalculationStatus($calculation_id, ORDER_STATUS_PLAN_LAMINATE, '');
    
    if(empty($error)) {
        $sql = "update calculation set queue_top = 1 where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}
elseif(empty ($error) && $work_id == WORK_CUTTING && $work_type_id == WORK_TYPE_SELF_ADHESIVE) {
    // 9. Тип работы "резка", тип заказа "самоклеящиеся материалы".
    // Статус устанавливаем "в плане печати".
    $error = SetCalculationStatus($calculation_id, ORDER_STATUS_PLAN_PRINT, '');
    
    if(empty($error)) {
        $sql = "update calculation set queue_top = 1 where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

echo json_encode(array('error' => $error));
?>