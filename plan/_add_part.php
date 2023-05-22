<?php
require_once '../include/topscripts.php';
require_once '../calculation/calculation.php';
require_once '../calculation/status_ids.php';
require_once '../include/works.php';

$part_id = filter_input(INPUT_GET, 'part_id');
$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$before = filter_input(INPUT_GET, 'before');
$error = '';

class Part {
    public $WorkId;
    public $MachineId;
    public $Date;
    public $Shift;
    public $Position;
}

// Получаем данные по расчёту
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

$part = new Part();
$part->WorkId = $work_id;
$part->MachineId = $machine_id;
$part->Date = $date;
$part->Shift = $shift;

if(empty($before) && $before !== 0 && $before !== '0') {
    $max_edition = 0;
    $max_continuation = 0;
    $max_event = 0;
    $max_part = 0;
    $max_part_continuation = 0;
    
    $sql = "select max(ifnull(position, 0)) from plan_edition "
            . "where work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_edition = $row[0];
    
    $sql = "select count(pc.id) "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "where pe.work_id = $work_id and pe.machine_id = $machine_id and pc.date = '$date' and pc.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_continuation = $row[0];
    
    $sql = "select max(ifnull(position, 0)) from plan_event "
            . "where in_plan = 1 and work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции события";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_event = $row[0];
    
    $sql = "select max(ifnull(position, 0)) from plan_part "
            . "where in_plan = 1 and work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции разделённого тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_part = $row[0];
    
    $sql = "select count(ppc.id) "
            . "from plan_part_continuation ppc "
            . "inner join plan_part pp on ppc.plan_part_id = pp.id "
            . "where pp.work_id = $work_id and pp.machine_id = $machine_id and ppc.date = '$date' and ppc.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции разделённого тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_part_continuation = $row[0];
    
    $part->Position = max($max_edition, $max_continuation, $max_event, $max_part, $max_part_continuation) + 1;
}
else {
    $sql = "update plan_edition set position = ifnull(position, 0) + 1 "
            . "where work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position >= $before";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $sql = "update plan_event set position = ifnull(position, 0) + 1 "
            . "where in_plan = 1 and work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position >= $before";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $sql = "update plan_part set position = ifnull(position, 0) + 1 "
            . "where in_plan = 1 and work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position >= $before";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $max_edition = 0;
    $max_continuation = 0;
    $max_event = 0;
    $max_part = 0;
    $max_part_continuation = 0;
    
    $sql = "select max(ifnull(position, 0)) from plan_edition "
            . "where work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position < $before";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_edition = $row[0];
    
    $sql = "select count(pc.id) "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "where pe.work_id = $work_id and pe.machine_id = $machine_id and pc.date = '$date' and pc.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_continuation = $row[0];
    
    $sql = "select max(ifnull(position, 0)) from plan_event "
            . "where in_plan = 1 and work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position < $before";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_event = $row[0];
    
    $sql = "select max(ifnull(position, 0)) from plan_part "
            . "where in_plan = 1 and work_id = $work_id and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position < $before";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_part = $row[0];
    
    $sql = "select count(ppc.id) "
            . "from plan_part_continuation ppc "
            . "inner join plan_part pp on ppc.plan_part_id = pp.id "
            . "where pp.work_id = $work_id and pp.machine_id = $machine_id and ppc.date = '$date' and ppc.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции разделённого тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_part_continuation = $row[0];
    
    $part->Position = max($max_edition, $max_continuation, $max_event, $max_part, $max_part_continuation) + 1;
}

$sql = "update plan_part set in_plan = 1, work_id = ".$part->WorkId.", machine_id = ".$part->MachineId.", date = '".$part->Date."', shift = '".$part->Shift."', position = ".$part->Position." where id = $part_id";
$executer = new Executer($sql);
$error = $executer->error;

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

if(empty($error) && $parts_in_plan > 0 && $parts_not_in_plan == 0 && $work_id == WORK_PRINTING) {
    // 1. Тип работы "печать".
    // Статус устанавливаем "в плане печати".
    $sql = "update calculation set status_id = ".PLAN_PRINT." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $parts_in_plan > 0 && $parts_not_in_plan == 0 && $work_id == WORK_LAMINATION && !$two_laminations) {
    // 2. Тип работы "ламинация", ламинация одна.
    // Статус устанавливаем "в плане ламинации".
    $sql = "update calculation set status_id = ".PLAN_LAMINATE." where id = $calculation_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
elseif(empty ($error) && $parts_in_plan > 0 && $parts_not_in_plan == 0 && $work_id == WORK_LAMINATION && $two_laminations) {
    // 3. Тип работы "ламинация", ламинации две.
    // Статус устанавливаем "в плане ламинации", если тиражей два.
    $editions_count = 0;
        
    $sql = "select count(id) from plan_edition where calculation_id = $calculation_id and work_id = $work_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $editions_count = $row['0'];
    }
        
    if($editions_count == 2) {
        $sql = "update calculation set status_is = ".PLAN_LAMINATE." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}
elseif(empty ($error) && $parts_in_plan > 0 && $parts_not_in_plan == 0 && $work_id == WORK_CUTTING) {
    // 4. Тип работы "резка".
    // Статус устанавливаем "в плане резки".
    $sql = "update calculation set status_id = ".PLAN_CUT." where id = $calculation_id";
}

echo json_encode(array('error' => $error));
?>