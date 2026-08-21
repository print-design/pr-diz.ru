<?php
include '../include/topscripts.php';

$id = 0;
$result = 0;
$error_message = '';

$sql = "select csh.id from calculation_status_history csh where csh.status_id = ". ORDER_STATUS_PLAN_LAMINATE
        ." and (select count(id) from calculation_status_history where calculation_id = csh.calculation_id and status_id = ". ORDER_STATUS_PLAN_LAMINATE." and id > csh.id) > 0";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $id = $row[0];
}

if($id > 0) {
    $sql = "delete from calculation_status_history where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

if(empty($error_message)) {
    $sql = "select count(csh.id) from calculation_status_history csh where csh.status_id = ". ORDER_STATUS_PLAN_LAMINATE
            ." and (select count(id) from calculation_status_history where calculation_id = csh.calculation_id and status_id = ". ORDER_STATUS_PLAN_LAMINATE." and id > csh.id) > 0";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result = $row[0];
    }
}

echo $result;
?>