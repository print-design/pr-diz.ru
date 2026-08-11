<?php
include '../include/topscripts.php';

$result = array();
$q = filter_input(INPUT_GET, 'q');

$sql = "select distinct id, name from customer where name like ? order by name";
$fetcher = new Fetcher($sql, ['%'.$q.'%']);

while ($row = $fetcher->Fetch()) {
    array_push($result, array('id' => $row['id'], 'text' => $row['name']));
}

echo json_encode($result);
?>