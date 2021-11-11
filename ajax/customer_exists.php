<?php
include '../include/topscripts.php';

$name = addslashes(filter_input(INPUT_GET, 'name'));
$manager_id = GetUserId();

$sql = "select count(id) from customer where name = '$name' and manager_id = $manager_id";
$row = (new Fetcher($sql))->Fetch();
echo $row[0];
?>