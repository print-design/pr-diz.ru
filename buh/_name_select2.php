<?php
include '../include/topscripts.php';

$result = array();
$q = filter_input(INPUT_GET, 'q');
$customer = filter_input(INPUT_GET, 'customer', FILTER_VALIDATE_INT);

$filter = '';
$params = ['%'.$q.'%'];

if(!empty($customer)) {
    $filter .= " and customer_id = ?";
    array_push($params, $customer);
}

$sql = "select distinct trim(name) from calculation where name like ?$filter order by name";
$fetcher = new Fetcher($sql, $params);

while($row = $fetcher->Fetch()) {
    array_push($result, array('id' => $row[0], 'text' => $row[0]));
}

echo json_encode($result);
?>