<?php
include '../include/topscripts.php';
header('Location: ../calculation/csv.php'. BuildQuery('id', filter_input(INPUT_GET, 'id')));
?>