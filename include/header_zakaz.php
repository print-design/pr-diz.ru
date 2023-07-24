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
$not_in_work_status = '';
$draft_status = '';
$trash_status = '';

if($folder == 'calculation') {
    if($status_id == ORDER_STATUS_TRASH) {
        $trash_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_DRAFT) {
        $draft_status = ' disabled';
    }
    elseif(in_array ($status_id, array(ORDER_STATUS_CALCULATION, ORDER_STATUS_TECHMAP, ORDER_STATUS_NOT_IN_WORK))) {
        $not_in_work_status = ' disabled';
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
            if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$calculation_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?=  BuildQueryRemoveArray(array("status", "page", "order")) ?>">В работе</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$not_in_work_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_NOT_IN_WORK, array("page", "order")) ?>">Расчеты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$draft_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_DRAFT, array("page", "order")) ?>">Черновики</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$trash_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_TRASH, array("page", "order")) ?>">Корзина</a>
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