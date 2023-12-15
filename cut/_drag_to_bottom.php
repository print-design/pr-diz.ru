<?php
require_once '../include/topscripts.php';
$source_id = filter_input(INPUT_GET, 'source_id');
$error = 'Ошибка при перетаскивании ручьёв';

$source_calculation_id = 0;
$source_position = 0;

$sql = "select calculation_id, position from calculation_stream where id = $source_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $source_calculation_id = $row['calculation_id'];
    $source_position = $row['position'];
}

$sql = "update calculation_stream set position = position - 1 "
        . "where calculation_id = $source_calculation_id "
        . "and position > $source_position";
$executer = new Executer($sql);
$error = $executer->error;

if(empty($error)) {
    $max_position = 0;
    
    $sql = "select max(position) from calculation_stream where calculation_id = $source_calculation_id and id <> $source_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $max_position = $row[0];
    }
    
    $sql = "update calculation_stream set position = $max_position + 1 where id = $source_id";
    $executer = new Executer($sql);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>