<?php
include '../include/topscripts.php';

$id = 0;
$result = 0;
$error_message = '';

$sql = "select c.id from calculation c where duplicate_status_id <> "
        . "(select status_id from calculation_status_history where calculation_id = c.id order by id desc limit 1)";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $id = $row[0];
}

if($id > 0) {
    $sql = "update calculation c set c.duplicate_status_id = "
            . "(select status_id from calculation_status_history where calculation_id = c.id order by id desc limit 1) "
            . "where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

if(empty($error_message)) {
    $sql = "select count(c.id) from calculation c where duplicate_status_id <> "
            . "(select status_id from calculation_status_history where calculation_id = c.id order by id desc limit 1)";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result = $row[0];
    }
}
echo $result;
?>