<?php
include '../include/topscripts.php';
$result = array();

$q = addslashes(filter_input(INPUT_GET, 'q'));
$sql = "select distinct cus.id, cus.name from calculation c inner join customer cus on c.customer_id = cus.id order by cus.name";

echo json_encode($result);
?>