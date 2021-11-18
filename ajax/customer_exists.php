<?php
include '../include/topscripts.php';

$name = addslashes(filter_input(INPUT_GET, 'name'));

$sql = "select count(id) from customer where name = '$name'";
$row = (new Fetcher($sql))->Fetch();
echo $row[0];
?>