<?php
include '../include/topscripts.php';

$error_message = '';

$sql = "select id, storekeeper_id, date from roll where id not in(select roll_id from roll_cell_history)";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sql1 = "insert into roll_cell_history (roll_id, date, cell, user_id) select id, date, cell, storekeeper_id from roll where id = ".$row['id'];
    $executer = new Executer($sql1);
    $error_message = $executer->error;
}

// Количество мигрированных рулонов
$ok_count = 0;

$sql = "select count(id) ok_count from roll where id in(select roll_id from roll_cell_history)";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $ok_count = $row['ok_count'];
}

if(!empty($error_message)) {
    exit(-1);
}

echo $ok_count;
?>