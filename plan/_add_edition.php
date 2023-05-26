<?php
require_once '../include/topscripts.php';
require_once '../calculation/calculation.php';
require_once '../calculation/status_ids.php';
require_once '../include/constants.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$lamination = filter_input(INPUT_GET, 'lamination');
$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$before = filter_input(INPUT_GET, 'before');
$error = '';

class Edition {
    public $WorkId;
    public $MachineId;
    public $Date;
    public $Shift;
    public $WorkTime;
    public $Position;
}

// Получаем данные расчёта
$work_time_1 = '';
$work_time_2 = '';
$work_time_3 = '';

$work_type_id = 0;
$length_dirty_1 = 0;
$has_lamination = false;
$two_laminations = false;

$sql = "select cr.work_time_1, cr.work_time_2, cr.work_time_3, c.work_type_id, cr.length_dirty_1, c.lamination1_film_variation_id, c.lamination1_individual_film_name, c.lamination2_film_variation_id, c.lamination2_individual_film_name "
        . "from calculation c "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "where c.id = $calculation_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $work_time_1 = round($row['work_time_1'], 2);
    $work_time_2 = round($row['work_time_2'], 2);
    $work_time_3 = round($row['work_time_3'], 2);
    
    $work_type_id = $row['work_type_id'];
    $length_dirty_1 = $row['length_dirty_1'];
    
    if(!empty($row['lamination1_film_variation_id']) || !empty($row['lamination1_individual_film_name'])) {
        $has_lamination = true;
    }
    
    if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
        $two_laminations = true;
    }
}

// Если не указываем следующую позицию, то position - на 1 больше, чем максимальная позиция данной смены.
// Если указываем следующую позицию, то 
// увеличиваем позицию на 1 у следующего тиража или события и всех следующих за ним
// и устанавливаем текущую позицию на 1 больше, чем максимальная позиция смены из тех, что меньше следующей позиции.
$edition = new Edition();
$edition->WorkId = $work_id;
$edition->MachineId = $machine_id;
$edition->Date = $date;
$edition->Shift = $shift;

if($work_id == WORK_PRINTING) {
    $edition->WorkTime = $work_time_1;
}
elseif($work_id == WORK_CUTTING) {
    $edition->WorkTime = $length_dirty_1 / $cutter_speeds[$edition->MachineId] / 60;
}
elseif($work_id == WORK_LAMINATION && $lamination == 1) {
    $edition->WorkTime = $work_time_2;
}
elseif($work_id == WORK_LAMINATION && $lamination == 2) {
    $edition->WorkTime = $work_time_3;
}

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
            . "inner join plan_edition e on pc.plan_edition_id = e.id "
            . "where e.work_id = $work_id and e.machine_id = $machine_id and pc.date = '$date' and pc.shift = '$shift'";
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
    
    $edition->Position = max($max_edition, $max_continuation, $max_event, $max_part, $max_part_continuation) + 1;
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
            . "inner join plan_edition e on pc.plan_edition_id = e.id "
            . "where e.work_id = $work_id and e.machine_id = $machine_id and pc.date = '$date' and pc.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_continuation = $row[0];
    
    $sql = "select max(ifnull(position, 0)) "
            . "from plan_event "
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
    if(!$row) {
        $error = "Ошибка при определении позиции разделённого тиража";
        echo json_encode(array("error" => $error));
        exit();
    }
    
    $edition->Position = max($max_edition, $max_continuation, $max_event, $max_part, $max_part_continuation) + 1;
}

$plan_edition_id = 0;

$sql = "select id from plan_edition where calculation_id = $calculation_id and lamination = $lamination and work_id = $work_id and machine_id = $machine_id" ;
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $plan_edition_id = $row[0];
}

if($plan_edition_id > 0) {
    $sql = "update plan_edition set work_id = ".$edition->WorkId.", machine_id = ".$edition->MachineId.", date = '".$edition->Date."', shift = '".$edition->Shift."', worktime = ".$edition->WorkTime.", position = ".$edition->Position
            ." where id = $plan_edition_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}
else {
    $sql = "insert into plan_edition (calculation_id, lamination, work_id, machine_id, date, shift, worktime, position) "
            . "values ($calculation_id, $lamination, ".$edition->WorkId.", ".$edition->MachineId.", '".$edition->Date."', '".$edition->Shift."', ".$edition->WorkTime.", ".$edition->Position.")";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error) && $work_id == WORK_PRINTING) {
        // 1. Тип работы "печать".
        // Статус устанавливаем "в плане печати".
        $sql = "update calculation set status_id = ".PLAN_PRINT." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    elseif(empty ($error) && $work_id == WORK_LAMINATION && !$two_laminations) {
        // 2. Тип работы "ламинация", ламинация одна.
        // Статус устанавливаем "в плане ламинации".
        $sql = "update calculation set status_id = ".PLAN_LAMINATE." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    elseif(empty ($error) && $work_id == WORK_LAMINATION && $two_laminations) {
        // 3. Тип работы "ламинация", ламинации две.
        // Статус устанавливаем "в плане ламинации":
        // - два тиража,
        // - один тираж и половинки второго тиража.
        $editions_count = 0;
        $parts_in_plan = 0;
        $parts_not_in_plan = 0;
        
        $sql = "select count(id) from plan_edition where calculation_id = $calculation_id and work_id = $work_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $editions_count = $row[0];
        }
        
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
        
        if($editions_count == 2 
                || ($editions_count == 1 && $parts_in_plan > 0 && $parts_not_in_plan == 0)) {
            $sql = "update calculation set status_id = ".PLAN_LAMINATE." where id = $calculation_id";
            $executer = new Executer($sql);
            $error = $executer->error;
        }
    }
    elseif(empty ($error) && $work_id == WORK_CUTTING) {
        // 4. Тип работы "резка".
        // Статус устанавливаем "в плане резки".
        $sql = "update calculation set status_id = ".PLAN_CUT." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

echo json_encode(array('error' => $error));
?>