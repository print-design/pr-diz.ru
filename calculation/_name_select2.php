<?php
include '../include/topscripts.php';

$result = array();
$q = filter_input(INPUT_GET, 'q');

$filter_manager = "";

if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    $manager = GetUserId();
    $filter_manager = " and manager_id = $manager";
}

$sql = "select distinct trim(name) from calculation where name like ?$filter_manager order by name";
$fetcher = new Fetcher($sql, ['%'.$q.'%']);

while($row = $fetcher->Fetch()) {
    array_push($result, array('id' => $row[0], 'text' => $row[0]));
}

echo json_encode($result);
?>