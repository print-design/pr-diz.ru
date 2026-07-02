<?php
include '../include/topscripts.php';

$id = 0;
$calculation_id = 0;
$result = 0;
$error_message = '';

$sql = "select csh.id, csh.calculation_id from calculation_status_history csh where csh.status_id = ". ORDER_STATUS_PLAN_LAMINATE
        ." and (select count(id) from plan_edition where calculation_id = csh.calculation_id and work_id = ". WORK_LAMINATION.") = 0";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $id = $row['id'];
    $calculation_id = $row['calculation_id'];
}

if($id > 0) {
    $sql = "delete from calculation_status_history where status_id = ". ORDER_STATUS_PLAN_LAMINATE." and calculation_id = $calculation_id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

if(empty($error_message)) {
    $sql = "select count(csh.id) from calculation_status_history csh where csh.status_id = ". ORDER_STATUS_PLAN_LAMINATE
            ." and (select count(id) from plan_edition where calculation_id = csh.calculation_id and work_id = ". WORK_LAMINATION.") = 0";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result = $row[0];
    }
}

echo $result;
?>