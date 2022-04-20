<?php
include '../include/topscripts.php';
header('Location: ../calculation/details.php'. BuildQuery('id', filter_input(INPUT_GET, 'id')));
?>