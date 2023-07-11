<?php
include '../../include/topscripts.php';
header("Location: ".APPLICATION."/roll/roll.php". BuildQuery('id', $_GET['id']));
?>