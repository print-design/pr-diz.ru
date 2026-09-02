<?php
include '../include/left_bar.php';

$all_status = '';
$shipped_status = '';
$ship_ready_status = '';
$production_status = '';
$calculated_status = '';
$draft_status = '';
$trash_status = '';

if(!empty($calculation)) {
    $status_id = $calculation->status_id;
}

if($folder == 'calculation') {
    if(isset($production) && $production) {
        $production_status = ' disabled';
    }
    elseif(isset($calculated) && $calculated) {
        $calculated_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_TRASH) {
        $trash_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_DRAFT) {
        $draft_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_SHIP_READY) {
        $ship_ready_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_SHIPPED) {
        $shipped_status = ' disabled';
    }
    elseif(!empty ($status_id) || $file == "index.php") {
        $all_status = ' disabled';
    }
}

// После создания техкарты
$has_techmap = false;

if(!empty($calculation)) {
    $has_techmap = in_array($calculation->status_id, [ORDER_STATUS_TECHMAP, ORDER_STATUS_WAITING, ORDER_STATUS_CONFIRMED, ORDER_STATUS_REJECTED, ORDER_STATUS_PLAN_PRINT, ORDER_STATUS_PLAN_LAMINATE, ORDER_STATUS_PLAN_CUT, ORDER_STATUS_CUT_PRILADKA, ORDER_STATUS_CUTTING, ORDER_STATUS_CUT_REMOVED, ORDER_STATUS_PACK_READY, ORDER_STATUS_SHIP_READY, ORDER_STATUS_SHIPPED]);
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$shipped_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, array("production", "calculated", "page", "order", "id")) ?>"><?= ORDER_STATUS_NAMES[ORDER_STATUS_SHIPPED] ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$ship_ready_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIP_READY, array("production", "calculated", "page", "order", "id")) ?>"><?= ORDER_STATUS_NAMES[ORDER_STATUS_SHIP_READY] ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$production_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("production", 1, array("calculated", "status", "page", "order", "id")) ?>">Производят</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$all_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryRemoveArray(array("production", "calculated", "status", "page", "order", "id")) ?>">В работе</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$calculated_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("calculated", 1, array("production", "status", "page", "order", "id")) ?>">Расчёты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$draft_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_DRAFT, array("production", "calculated", "page", "order", "id")) ?>"><?= ORDER_STATUS_NAMES[ORDER_STATUS_DRAFT] ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$trash_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_TRASH, array("production", "calculated", "page", "order", "id")) ?>"><?= ORDER_STATUS_NAMES[ORDER_STATUS_TRASH] ?></a>
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
        
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>