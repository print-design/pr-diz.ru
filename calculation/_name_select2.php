<?php
include '../include/topscripts.php';
$result = array();

$q = addslashes(filter_input(INPUT_GET, 'q'));
$sql = "select distinct trim(name) from calculation where name like '%$q%' order by name";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    array_push($result, array('id' => $row[0], 'text' => $row[0]));
}

echo json_encode($result);
?>