<?php
include '../include/topscripts.php';

$error_message = '';
$machineId = filter_input(INPUT_GET, 'machine_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$workshift_id = filter_input(INPUT_GET, 'workshift_id');

$direction_get = filter_input(INPUT_GET, 'direction');
$position_get = filter_input(INPUT_GET, 'position');

if(empty($workshift_id)) {
    $sql = "insert into workshift (date, machine_id, shift) values ('$date', $machineId, '$shift')";
    $ws_executer = new Executer($sql);
    $error_message = $ws_executer->error;
    $workshift_id = $ws_executer->insert_id;
}

$sql = "select 	name, organization, length, status_id, lamination_id, coloring, roller, manager_id, comment, origin_id, origin_name from clipboard order by id desc";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;

if($row = $fetcher->Fetch()) {
    // Вставляем тираж в смену
    $name = addslashes($row['name']);
    $organization = addslashes($row['organization']);
    $length = $row['length'] == null ? 'NULL' : $row['length'];
    $status_id = $row['status_id'] == null ? 'NULL' : $row['status_id'];
    $lamination_id = $row['lamination_id'] == null ? 'NULL' : $row['lamination_id'];
    $coloring = $row['coloring'] == null ? 'NULL' : $row['coloring'];
    $roller = addslashes($row['roller']);
    $manager_id = $row['manager_id'] == null ? 'NULL' : $row['manager_id'];
    $comment = addslashes($row['comment']);
    $origin_id = $row['origin_id'];
    $origin_name = $row['origin_name'];
    
    $position = 1;
    
    if($direction_get !== null && $position_get !== null) {
        if($direction_get == 'up') {
            $error_message = (new Executer("update edition set position = position - 1 where workshift_id = $workshift_id and position < $position_get"))->error;
            $position = intval($position_get) - 1;
        }
        
        if($direction_get == 'down') {
            $error_message = (new Executer("update edition set position = position + 1 where workshift_id = $workshift_id and position > $position_get"))->error;
            $position = intval($position_get) + 1;
        }
    }
    
    $sql = "insert into edition (name, organization, length, status_id, lamination_id, coloring, roller_id, manager_id, comment, workshift_id, position) "
            . "values ('$name', '$organization', $length, $status_id, $lamination_id, $coloring, (select id from roller where name='$roller' and machine_id=$machineId limit 1), $manager_id, '$comment', $workshift_id, $position)";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    $insert_id = $executer->insert_id;
    
    // Очищаем буфер обмена
    $sql = "delete from clipboard";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    // Удаление пустых тиражей в конечной смене
    $sql = "delete from edition where workshift_id = $workshift_id "
            . "and (name is null or name = '') "
            . "and (organization is null or organization = '') "
            . "and length is null "
            . "and status_id is null "
            . "and lamination_id is null "
            . "and coloring is null "
            . "and roller_id is null "
            . "and manager_id is null "
            . "and (comment is null or comment = '')";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    // Возвращаем ID оригинального тиража, чтобы решить вопрос, удалять его или нет
    if(!empty($origin_id)) {
        echo json_encode(array("id" => $origin_id, "name" => $origin_name));
    }
}

if(!empty($error_message)) {
    echo $error_message;
}
?>