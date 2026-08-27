<?php
include '../include/topscripts.php';

$printing_id = filter_input(INPUT_GET, 'printing_id', FILTER_VALIDATE_INT);
$sequence = filter_input(INPUT_GET, 'sequence');
$cliche = filter_input(INPUT_GET, 'cliche');
$machine_coeff = filter_input(INPUT_GET, 'machine_coeff');
$repeat_from = filter_input(INPUT_GET, 'repeat_from');

$result = array();
$result['error'] = '';

$sql = "select id from calculation_cliche where calculation_quantity_id = ? and sequence = ?";
$fetcher = new Fetcher($sql, [$printing_id, $sequence]);
$error_message = $fetcher->error;

if(empty($error_message)) {
    if($row = $fetcher->Fetch()) {
        $id = $row[0];
        if(empty($cliche)) {
            $sql = "delete from calculation_cliche where id = ?";
            $params = [$id];
        }
        elseif($cliche == CLICHE_REPEAT) {
            $sql = "update calculation_cliche set name = ?, repeat_from = ? where id = ?";
            $params = [$cliche, $repeat_from, $id];
        }
        else {
            $sql = "update calculation_cliche set name = ?, repeat_from = NULL where id = ?";
            $params = [$cliche, $id];
        }
    }
    elseif(!empty ($repeat_from)) {
        $sql = "insert into calculation_cliche(calculation_quantity_id, sequence, name, repeat_from) values(?, ?, ?, ?)";
        $params = [$printing_id, $sequence, $cliche, $repeat_from];
    }
    else {
        $sql = "insert into calculation_cliche(calculation_quantity_id, sequence, name, repeat_from) values(?, ?, ?, NULL)";
        $params = [$printing_id, $sequence, $cliche];
    }
    
    $executer = new Executer($sql, $params);
    $error_message = $executer->error;
}

if(empty($error_message)) {
    $sql = "select"
            . " (select count(id) FROM calculation_cliche WHERE name = '".CLICHE_FLINT."' and calculation_quantity_id in (select id from calculation_quantity where calculation_id = (select calculation_id from calculation_quantity where id = ?))) flint_used,"
            . " (select count(id) FROM calculation_cliche WHERE name = '".CLICHE_KODAK."' and calculation_quantity_id in (select id from calculation_quantity where calculation_id = (select calculation_id from calculation_quantity where id = ?))) kodak_used,"
            . " (select count(id) FROM calculation_cliche WHERE name = '".CLICHE_OLD."' and calculation_quantity_id in (select id from calculation_quantity where calculation_id = (select calculation_id from calculation_quantity where id = ?))) old_used";
    $fetcher = new Fetcher($sql, [$printing_id, $printing_id, $printing_id]);
    $error_message = $fetcher->error;
    
    if($row = $fetcher->Fetch()) {
        $result['printing_id'] = $printing_id;
        $result['sequence'] = $sequence;
        $result['cliche'] = $cliche;
        $result['machine_coeff'] = $machine_coeff;
        $result['repeat_from'] = $repeat_from;
        
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