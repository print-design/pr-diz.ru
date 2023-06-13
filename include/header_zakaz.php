<?php
include 'left_bar.php';

$php_self = $_SERVER['PHP_SELF'];
$substrings = mb_split("/", $php_self);
$count = count($substrings);
$folder = '';
$file = '';

if($count > 1) {
    $folder = $substrings[$count - 2];
    $file = $substrings[$count - 1];
}

$calculation_status = '';
$plan_status = '';
$draft_status = '';
$trash_status = '';

if($folder == 'calculation') {
    if(!isset($status_id)) {
        $status_id = filter_input(INPUT_GET, 'status');
    }
    
    if($status_id == ORDER_STATUS_TRASH) {
        $trash_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_DRAFT) {
        $draft_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_PLAN) {
        $plan_status = ' disabled';
    }
    else {
        $calculation_status = ' disabled';
    }
}
            
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            if(IsInRole(array('technologist', 'dev', 'manager', 'administrator', 'designer'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$calculation_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryRemoveArray(array("status", "page")) ?>">В работе</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$plan_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemove("status", ORDER_STATUS_PLAN, "page") ?>">Расчеты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$draft_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemove("status", ORDER_STATUS_DRAFT, "page") ?>">Черновики</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$trash_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemove("status", ORDER_STATUS_TRASH, "page") ?>">Корзина</a>
            </li>
            <?php endif; ?>
        </ul>
        <?php
        if(file_exists('find.php')) {
            include 'find.php';
        }
        else {
            echo "<div class='ml-auto'></div>";
        }
        
        include 'header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>