<?php
define('PAGE', 'page');

$pager_total_count = 0;
$pager_page = 1;

if(isset($_GET[PAGE]) && $_GET[PAGE] != '' && is_numeric($_GET[PAGE])) {
    $pager_page = $_GET[PAGE];
}

if($pager_page < 1) {
    $pager_page = 1;
}

$pager_take = 20;
$pager_skip = $pager_take * ($pager_page - 1);
?>