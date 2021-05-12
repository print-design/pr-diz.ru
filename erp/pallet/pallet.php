<?php
include '../../include/topscripts.php';
header("Location: ".APPLICATION."/pallet/pallet.php". BuildQuery('id', $_GET['id']));
?>