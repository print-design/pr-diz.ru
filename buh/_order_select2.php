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

$sql = "select id, trim(name) name, customer_id, duplicate_num_for_customer, duplicate_status_id from calculation where name like ?$filter and duplicate_status_id not in (?, ?, ?, ?) order by name";
$params = array_merge($params, [ORDER_STATUS_DRAFT, ORDER_STATUS_CALCULATION, ORDER_STATUS_REJECTED, ORDER_STATUS_TRASH]);
$fetcher = new Fetcher($sql, $params);

while($row = $fetcher->Fetch()) {
    array_push($result, array('id' => $row['id'], 'text' => $row['name']." (".$row['customer_id']."-".$row['duplicate_num_for_customer'].', '. ORDER_STATUS_NAMES[$row['duplicate_status_id']].")"));
}

echo json_encode($result);
?>