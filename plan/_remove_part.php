<?php
require_once '../include/topscripts.php';

$part_id = filter_input(INPUT_GET, 'part_id');
$work_id = filter_input(INPUT_GET, 'work_id');
$error = '';

// Получаем данные по расчёту.
$calculation_id = null;
$work_type_id = 0;
$has_lamination = false;
$two_laminations = false;

$sql = "select c.id, c.work_type_id, c.lamination1_film_variation_id, c.lamination1_individual_film_name, c.lamination2_film_variation_id, c.lamination2_individual_film_name "
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
    if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
        $two_laminations = true;
    }
}

// Если этот же заказ стоит в плане в последующих разделах,
// запрещаем удалять его из плана.
if(empty($error) && $work_id == WORK_PRINTING) {
    $in_lamination = 0;
    $in_cutting = 0;
    $parts_in_lamination = 0;
    $parts_in_cutting = 0;
    
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
    
    $sql = "select count(id) from plan_part where in_plan = 1 and calculation_id = $calculation_id and work_id = ".WORK_LAMINATION;
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $parts_in_lamination = $row[0];
    }
    
    $sql = "select count(id) from plan_part where in_plan = 1 and calculation_id = $calculation_id and work_id = ".WORK_CUTTING;
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $parts_in_cutting = $row[0];
    }
    
    if(($in_lamination > 0 || $parts_in_lamination > 0) && $in_cutting == 0 && $parts_in_cutting == 0) {
        $error = "Этот заказ стоит в плане в ламинации.";
    }
    elseif(($in_cutting > 0 || $parts_in_cutting > 0) && $in_lamination == 0 && $parts_in_lamination == 0) {
        $error = "Этот заказ стоит в плане в резке.";
    }
    elseif(($in_lamination > 0 || $parts_in_lamination > 0) || ($in_cutting > 0 || $parts_in_cutting > 0)) {
        $error = "Этот заказ стоит в плане в ламинации и в резке.";
    }
}
elseif(empty ($error) && $work_id == WORK_LAMINATION) {
    $in_cutting = 0;
    $parts_in_cutting = 0;
    
    $sql = "select count(id) from plan_edition where calculation_id = $calculation_id and work_id = ".WORK_CUTTING;
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $in_cutting = $row[0];
    }
    
    $sql = "select count(id) from plan_part where in_plan = 1 and calculation_id = $calculation_id and work_id = ".WORK_CUTTING;
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $parts_in_cutting = $row[0];
    }
    
    if($in_cutting > 0 || $parts_in_cutting > 0) {
        $error = "Этот заказ стоит в плане в резке.";
    }
}

// Устанавливаем признак "не в плане".
if(empty($error)) {
    $sql = "update plan_part set in_plan = 0, machine_id = null, date = null, shift = null, position = null where id = $part_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

// Меняем статусы и закрепляем наверху очереди.
if(empty($error) && $work_id == WORK_PRINTING) {
    // 1. Тип работы "печать".
    // Если половинки этого заказа есть вне плана в разделах "ламинация" и "резка", удаляем их.
    // Статус устанавливаем "ожидание постановки в план".
    $sql = "delete from plan_part where in_plan = 0 and calculation_id = $calculation_id and work_id in (".WORK_LAMINATION.", ".WORK_CUTTING.")";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    $sql = "update calculation set status_id = ".ORDER_STATUS_CONFIRMED.", queue_top = 1 where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_LAMINATION && $work_type_id == WORK_TYPE_NOPRINT) {
    // 2. Тип работы "ламинация", ламинация одна, тип заказа "плёнка без печати".
    // Если половинки этого заказа есть вне плана в разделе "резка", удаляем их.
    // Статус устанавливаем "ожидание постановки в план".
    $sql = "delete from plan_part where in_plan = 0 and calculation_id = $calculation_id and work_id = ".WORK_CUTTING;
    $executer = new Executer($sql);
    $error = $executer->error;
    
    $sql = "update calculation set status_id = ".ORDER_STATUS_CONFIRMED.", queue_top = 1 where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_LAMINATION && $work_type_id == WORK_TYPE_PRINT) {
    // 3. Тип работы "ламинация", ламинация одна, тип заказа "плёнка с печатью".
    // Если половинки этого заказа есть вне плана в разделе "резка", удаляем их.
    // Статус устанавливаем "в плане печати".
    $sql = "delete from plan_part where in_plan = 0 and calculation_id = $calculation_id and work_id = ".WORK_CUTTING;
    $executer = new Executer($sql);
    $error = $executer->error;
    
    $sql = "update calculation set status_id = ".ORDER_STATUS_PLAN_PRINT.", queue_top = 1 where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_CUTTING && !$has_lamination && $work_type_id == WORK_TYPE_NOPRINT) {
    // 6. Тип работы "резка", ламинации нет, тип заказа "плёнка без печати".
    // Статус устанавливаем "ожидание постановки в план".
    $sql = "update calculation set status_id = ".ORDER_STATUS_CONFIRMED.", queue_top = 1 where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_CUTTING && !$has_lamination && $work_type_id == WORK_TYPE_PRINT) {
    // 7. Тип работы "резка", ламинации нет, тип заказа "плёнка с печатью".
    // Статус устанавливаем "в плане печати".
    $sql = "update calculation set status_id = ".ORDER_STATUS_PLAN_PRINT.", queue_top = 1 where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_CUTTING && $has_lamination) {
    // 8. Тип работы "резка", ламинация есть.
    // Статус устанавливаем "в плане ламинации".
    $sql = "update calculation set status_id = ".ORDER_STATUS_PLAN_LAMINATE.", queue_top = 1 where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $work_id == WORK_CUTTING && $work_type_id == WORK_TYPE_SELF_ADHESIVE) {
    // 9. Тип работы "резка", тип заказа "самоклеящиеся материалы".
    // Статус устанавливаем "в плане печати".
    $sql = "update calculation set status_id = ".ORDER_STATUS_PLAN_PRINT.", queue_top = 1 where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>