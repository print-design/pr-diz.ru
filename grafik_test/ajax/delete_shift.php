<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$error_message = (new Executer("delete from workshift where id=$id"))->error;
?>