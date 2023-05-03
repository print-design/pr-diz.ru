<?php
require_once '../include/topscripts.php';
require_once '../calculation/status_ids.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$before = filter_input(INPUT_GET, 'before');
$error = '';

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

// Если не указываем следующую позицию, то position - на 1 больше, чем максимальная позиция данной смены.
// Если указываем следующую позицию, то 
// увеличиваем позицию на 1 у следующего тиража или события и всех следующих за ним
// и устанавливаем текущую позицию на 1 больше, чем максимальная позиция смены из тех, что меньше следующей позиции.
$edition = new Edition();
$edition->Date = $date;
$edition->Shift = $shift;
$edition->WorkTime = $work_time_1;

if(empty($before) && $before !== 0 && $before !== '0') {
    $max_edition = 0;
    $max_event = 0;
    $count_continuation = 0;
    
    $sql = "select max(ifnull(e.position, 0)) "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_edition = $row[0];
    
    $sql = "select max(ifnull(position, 0)) "
            . "from plan_event "
            . "where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции события";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_event = $row[0];
    
    $sql = "select count(pc.id) "
            . "from plan_continuation pc "
            . "inner join plan_edition e on pc.plan_edition_id = e.id "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and pc.date = '$date' and pc.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $count_continuation = $row[0];
    
    $edition->Position = max($max_edition, $max_event, $count_continuation) + 1;
}
else {
    $sql = "update plan_edition set position = ifnull(position, 0) + 1 "
            . "where date = '$date' and shift = '$shift' and calculation_id in (select id from calculation where machine_id = $machine_id) "
            . "and position >= $before";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $sql = "update plan_event set position = ifnull(position, 0) + 1 "
            . "where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position >= $before";
    $executer = new Executer($sql);
    $error = $executer->error;
    if(!empty($error)) {
        echo json_encode(array('error' => $error));
        exit();
    }
    
    $max_edition = 0;
    $max_event = 0;
    $count_continuation = 0;
    
    $sql = "select max(ifnull(e.position, 0)) "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift' "
            . "and e.position < $before";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_edition = $row[0];
    
    $sql = "select max(ifnull(position, 0)) "
            . "from plan_event "
            . "where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift' "
            . "and position < $before";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = $fetcher->error;
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_event = $row[0];
    
    $sql = "select count(pc.id) "
            . "from plan_continuation pc "
            . "inner join plan_edition e on pc.plan_edition_id = e.id "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and pc.date = '$date' and pc.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $count_continuation = $row[0];
    
    $edition->Position = max($max_edition, $max_event, $count_continuation) + 1;
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