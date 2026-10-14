<?php
include '../include/topscripts.php';

$result = array();
$q = filter_input(INPUT_GET, 'q');

$filter_manager = "";

if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    $manager = GetUserId();
    $filter_manager = " and c.manager_id = $manager";
}

$sql = "select distinct cus.id, cus.name from calculation c inner join customer cus on c.customer_id = cus.id where cus.name like ?$filter_manager order by cus.name";
$fetcher = new Fetcher($sql, ['%'.$q.'%']);

while ($row = $fetcher->Fetch()) {
    array_push($result, array('id' => $row['id'], 'text' => $row['name']));
}

echo json_encode($result);
?>