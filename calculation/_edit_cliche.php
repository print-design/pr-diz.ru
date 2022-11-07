<?php
include '../include/topscripts.php';
include './calculation.php';

$printing_id = filter_input(INPUT_GET, 'printing_id');
$sequence = filter_input(INPUT_GET, 'sequence');
$cliche = filter_input(INPUT_GET, 'cliche');
$machine_coeff = filter_input(INPUT_GET, 'machine_coeff');

$result = array();
$result['error'] = '';

$sql = "select id from calculation_cliche where calculation_quantity_id = $printing_id and sequence = $sequence";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;

if(empty($error_message)) {
    if($row = $fetcher->Fetch()) {
        $id = $row[0];
        if(empty($cliche)) {
            $sql = "delete from calculation_cliche where id = $id";
        }
        else {
            $sql = "update calculation_cliche set name = '$cliche' where id = $id";
        }
    }
    else {
        $sql = "insert into calculation_cliche(calculation_quantity_id, sequence, name) values($printing_id, $sequence, '$cliche')";
    }
    
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

if(empty($error_message)) {
    $sql = "select(select count(id) FROM calculation_cliche WHERE name = '".CalculationBase::FLINT."' and calculation_quantity_id in (select id from calculation_quantity where calculation_id = (select calculation_id from calculation_quantity where id = $printing_id))) flint_used, (select count(id) FROM calculation_cliche WHERE name = '".CalculationBase::KODAK."' and calculation_quantity_id in (select id from calculation_quantity where calculation_id = (select calculation_id from calculation_quantity where id = $printing_id))) kodak_used, (select count(id) FROM calculation_cliche WHERE name = '".CalculationBase::OLD."' and calculation_quantity_id in (select id from calculation_quantity where calculation_id = (select calculation_id from calculation_quantity where id = $printing_id))) old_used";
    $fetcher = new Fetcher($sql);
    $error_message = $fetcher->error;
    
    if($row = $fetcher->Fetch()) {
        $result['printing_id'] = $printing_id;
        $result['sequence'] = $sequence;
        $result['cliche'] = $cliche;
        $result['machine_coeff'] = $machine_coeff;
        
        $result['flint_used'] = $row['flint_used'];
        $result['kodak_used'] = $row['kodak_used'];
        $result['old_used'] = $row['old_used'];
    }
    else {
        $result['error'] = $error_message;
    }
}

echo json_encode($result);
?>