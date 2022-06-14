<?php
include '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');

$sql = "select gap_raport from norm_gap where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $gap_raport = $row['gap_raport'];
    echo "Зазор между этикетками $gap_raport мм";
}
?>