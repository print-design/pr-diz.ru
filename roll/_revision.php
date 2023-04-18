<?php
include '../include/topscripts.php';

$roll_id = 0;

$sql = "select id from roll where id not in (select roll_id from roll_status_history where date > '2023-04-15' and date < '2023-04-17' and status_id = 2)";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $roll_id = $row[0];
}

$sql = "insert into roll_status_history (roll_id, date, status_id, user_id) values($roll_id, '2023-04-16', 2, 120)";
$executer = new Executer($sql);
$error = $executer->error;

if(!empty($error)) {
    echo $error;
    exit();
}

$result = 0;
$sql = "select count(id) from roll where id not in (select roll_id from roll_status_history where date > '2023-04-15' and date < '2023-04-17' and status_id = 2)";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $result = $row[0];
}
echo $result;
?>
