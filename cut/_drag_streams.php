<?php
require_once '../include/topscripts.php';
$source_id = filter_input(INPUT_GET, 'source_id', FILTER_VALIDATE_INT);
$target_id = filter_input(INPUT_GET, 'target_id', FILTER_VALIDATE_INT);
$error = 'Ошибка при перетаскивании ручьёв';

$source_calculation_id = 0;
$source_position = 0;

$sql = "select calculation_id, position from calculation_stream where id = ?";
$fetcher = new Fetcher($sql, [$source_id]);
if($row = $fetcher->Fetch()) {
    $source_calculation_id = $row['calculation_id'];
    $source_position = $row['position'];
}

$target_calculation_id = 0;
$target_position = 0;

$sql = "select calculation_id, position from calculation_stream where id = ?";
$fetcher = new Fetcher($sql, [$target_id]);
if($row = $fetcher->Fetch()) {
    $target_calculation_id = $row['calculation_id'];
    $target_position = $row['position'];
}

if($source_position < $target_position) {
    $sql = "update calculation_stream set position = position - 1 "
            . "where calculation_id = ? "
            . "and calculation_id = ? "
            . "and position > ? "
            . "and position < ?";
    $executer = new Executer($sql, [$source_calculation_id, $target_calculation_id, $source_position, $target_position]);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update calculation_stream set position = ? where id = ?";
        $executer = new Executer($sql, [$target_position - 1, $source_id]);
        $error = $executer->error;
    }
}

if($source_position > $target_position) {
    $sql = "update calculation_stream set position = position + 1 "
            . "where calculation_id = ? "
            . "and calculation_id = ? "
            . "and position >= ? "
            . "and position < ?";
    $executer = new Executer($sql, [$source_calculation_id, $target_calculation_id, $target_position, $source_position]);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update calculation_stream set position = ? where id = ?";
        $executer = new Executer($sql, [$target_position, $source_id]);
        $error = $executer->error;
    }
}

echo json_encode(array('error' => $error));
?>