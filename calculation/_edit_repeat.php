<?php
include '../include/topscripts.php';

$printing_id = filter_input(INPUT_GET, 'printing_id', FILTER_VALIDATE_INT);
$sequence = filter_input(INPUT_GET, 'sequence');
$repeat_from = filter_input(INPUT_GET, 'repeat_from');

$result = array();
$result['error'] = '';

$sql = "select id from calculation_cliche where calculation_quantity_id = ? and sequence = ?";
$fetcher = new Fetcher($sql, [$printing_id, $sequence]);
$error_message = $fetcher->error;

if(empty($error_message) && $row = $fetcher->Fetch()) {
    $id = $row[0];
    $sql = "update calculation_cliche set repeat_from = ? where id = ?";
    $executer = new Executer($sql, [$repeat_from, $id]);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "select repeat_from from calculation_cliche where id = ?";
        $fetcher = new Fetcher($sql, [$id]);
        $error_message = $fetcher->error;
        
        if(empty($error_message) && $row = $fetcher->Fetch()) {
            $result['repeat_from'] = $row[0];
            $result['printing_id'] = $printing_id;
            $result['sequence'] = $sequence;
        }
        else {
            $result['error'] = "Ошибка при изменении с какого тиража использовать повторно";
        }
    }
}

echo json_encode($result);
?>