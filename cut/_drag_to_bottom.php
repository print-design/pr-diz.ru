<?php
require_once '../include/topscripts.php';
$source_id = filter_input(INPUT_GET, 'source_id', FILTER_VALIDATE_INT);
$error = 'Ошибка при перетаскивании ручьёв';

$source_calculation_id = 0;
$source_position = 0;

$sql = "select calculation_id, position from calculation_stream where id = ?";
$fetcher = new Fetcher($sql, [$source_id]);
if($row = $fetcher->Fetch()) {
    $source_calculation_id = $row['calculation_id'];
    $source_position = $row['position'];
}

$sql = "update calculation_stream set position = position - 1 "
        . "where calculation_id = ? "
        . "and position > ?";
$executer = new Executer($sql, [$source_calculation_id, $source_position]);
$error = $executer->error;

if(empty($error)) {
    $max_position = 0;
    
    $sql = "select max(position) from calculation_stream where calculation_id = ? and id <> ?";
    $fetcher = new Fetcher($sql, [$source_calculation_id, $source_id]);
    if($row = $fetcher->Fetch()) {
        $max_position = $row[0];
    }
    
    $sql = "update calculation_stream set position = ? where id = ?";
    $executer = new Executer($sql, [$max_position + 1, $source_id]);
    $error = $executer->error;
}

echo json_encode(array('error' => $error));
?>