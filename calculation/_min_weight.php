<?php
include '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');
$result = 0;

$sql = "select min_weight from norm_machine where machine_id = $machine_id order by id desc limit 1";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $result = $row[0];
}

echo $result;
?>