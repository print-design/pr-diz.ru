<?php
include '../include/topscripts.php';

$result = array();
$q = filter_input(INPUT_GET, 'q');
$customer = filter_input(INPUT_GET, 'customer', FILTER_VALIDATE_INT);

$filter = "";
$params = ['%'.$q.'%'];

if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    $manager = GetUserId();
    $filter .= " and manager_id = $manager";
    array_push($params, $manager);
}

if(!empty($customer)) {
    $filter .= " and customer_id = $customer";
}

$sql = "select distinct trim(name) from calculation where name like ?$filter order by name";
$fetcher = new Fetcher($sql, $params);

while($row = $fetcher->Fetch()) {
    array_push($result, array('id' => $row[0], 'text' => $row[0]));
}

echo json_encode($result);
?>