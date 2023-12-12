<?php
include '../../include/topscripts.php';
header("Location: ".APPLICATION."/pallet/roll.php". BuildQuery('id', $_GET['id']));
?>