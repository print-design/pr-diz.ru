<?php
require_once '../include/topscripts.php';
$source_id = filter_input(INPUT_GET, 'source_id');
$target_id = filter_input(INPUT_GET, 'target_id');
$error = 'Ошибка при перетаскивании ручьёв';

$source_position = 0;
$target_position = 0;

$sql = "select position from calculation_stream where id = $source_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $source_position = $row[0];
}

$sql = "select position from calculation_stream where id = $target_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $target_position = $row[0];
}

if($source_position < $target_position) {
    $sql = "update calculation_stream set position = position - 1 "
            . "where calculation_id = (select calculation_id from calculation_stream where id = $source_id) "
            . "and calculation_id = (select calculation_id from calculation_stream where id = $target_id) "
            . "and position > $source_position "
            . "and position < $target_position";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update calculation_stream set position = $target_position - 1 "
                . "where id = $source_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

if($source_position > $target_position) {
    $sql = "update calculation_stream set position = position + 1 "
            . "where calculation_id = (select calculation_id from calculation_stream where id = $source_id) "
            . "and calculation_id = (select calculation_id from calculation_stream where id = $target_id) "
            . "and position >= $target_position "
            . "and position < $source_position";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update calculation_stream set position = $target_position "
                . "where id = $source_id";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

echo json_encode(array('error' => $error));
?>