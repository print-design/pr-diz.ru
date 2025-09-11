<?php
include '../include/topscripts.php';

const STREAM = "stream";
const PRINTING = "printing";

$object = filter_input(INPUT_GET, 'object');
$id = filter_input(INPUT_GET, 'id');
$image = filter_input(INPUT_GET, 'image');

$result = "";
?>
<button type="button">Миру мир! Нет войне!</button>