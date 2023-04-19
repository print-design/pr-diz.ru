<?php
require_once '../include/topscripts.php';
require_once '../calculation/status_ids.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$from = filter_input(INPUT_GET, 'from');
$before = filter_input(INPUT_GET, 'before');
$error = '';

if($before == $calculation_id) {
    $before = null;
}

class Edition {
    public $Date;
    public $Shift;
    public $WorkTime;
    public $Position;
}

// Определяем размер расчёта и машину
$machine_id = null;
$work_time_1 = '';

$sql = "select c.machine_id, cr.work_time_1 "
        . "from calculation c "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "where c.id = $calculation_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $machine_id = $row['machine_id'];
    $work_time_1 = round($row['work_time_1'], 2);
}

// Если не указываем следующий расчёт, то position - на 1 больше, чем максимальный position данной смены.
// Если указываем следующий расчёт, то 
// увеличиваем Position на 1 у следующего расчёта и всех следующих за ним
// и устанавливаем Position текущего расчёта - на 1 больше, чем максимальный position смены, кроме тех, у кого position меньше, чем position следующего расчёта.
$edition = new Edition();
$edition->Date = $date;
$edition->Shift = $shift;
$edition->WorkTime = $work_time_1;

if(empty($before)) {
    $sql = "select max(e.position) "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции";
        echo json_encode(array('error' => $error));
        exit();
    }
    $edition->Position = $row[0] + 1;
}
else {
    $sql = "select min(position) "
            . "from plan_edition "
            . "where calculation_id = $before"; 
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции последующего тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $min_position = $row[0];
    
    $sql = "update plan_edition set position = position + 1 "
            . "where date = '$date' and shift = '$shift' and calculation_id in (select id from calculation where machine_id = $machine_id) "
            . "and position >= $min_position";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $sql = "select max(e.position) "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift' "
            . "and e.position < "
            . "(select min(position) "
            . "from plan_edition "
            . "where calculation_id = $before and machine_id = $machine_id and date = '$date' and shift = '$shift')";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $edition->Position = $row[0] + 1;
}

$sql = "insert into plan_edition (calculation_id, date, shift, worktime, position) "
        . "values ($calculation_id, '".$edition->Date."', '".$edition->Shift."', ".$edition->WorkTime.", ".$edition->Position.")";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update calculation set status_id = ".PLAN." where id = $calculation_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }

echo json_encode(array('error' => $error));
?>