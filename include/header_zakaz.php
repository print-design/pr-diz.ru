<?php
$php_self = $_SERVER['PHP_SELF'];
$substrings = mb_split("/", $php_self);
$count = count($substrings);
$folder = '';
$file = '';

if($count > 1) {
    $folder = $substrings[$count - 2];
    $file = $substrings[$count - 1];
}

$shipped_status = '';
$ship_ready_status = '';
$production_status = '';
$calculation_status = '';
$not_in_work_status = '';
$draft_status = '';
$trash_status = '';

if(empty($status_id) && !empty($calculation)) {
    $status_id = $calculation->status_id;
} 

if($folder == 'calculation') {
    if($status_id == ORDER_STATUS_TRASH) {
        $trash_status = ' flexim-header-menu__item--active';
    }
    elseif($status_id == ORDER_STATUS_DRAFT) {
        $draft_status = ' flexim-header-menu__item--active';
    }
    elseif(in_array ($status_id, ORDER_STATUSES_NOT_IN_WORK) || $status_id == ORDER_STATUS_NOT_IN_WORK) {
        $not_in_work_status = ' flexim-header-menu__item--active';
    }
    elseif(in_array($status_id, ORDER_STATUSES_IN_PRODUCTION) || $status_id == ORDER_STATUS_IN_PRODUCTION) {
        $production_status = ' flexim-header-menu__item--active';
    }
    elseif($status_id == ORDER_STATUS_SHIP_READY) {
        $ship_ready_status = ' flexim-header-menu__item--active';
    }
    elseif($status_id == ORDER_STATUS_SHIPPED) {
        $shipped_status = ' flexim-header-menu__item--active';
    }
    else {
        $calculation_status = ' flexim-header-menu__item--active';
    }
}
?>
<div class="app-topbar">
    <div class="flexim-header-menu">
        <nav class="flexim-header-menu__nav">
            <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))): ?>
            <a class="flexim-header-menu__item<?=$shipped_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_SHIPPED] ?></a>
            <a class="flexim-header-menu__item<?=$ship_ready_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIP_READY, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_SHIP_READY] ?></a>
            <a class="flexim-header-menu__item<?=$production_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_IN_PRODUCTION, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_IN_PRODUCTION] ?></a>
            <a class="flexim-header-menu__item<?=$calculation_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryRemoveArray(array("status", "page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_IN_WORK] ?></a>
            <a class="flexim-header-menu__item<?=$not_in_work_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_NOT_IN_WORK, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_NOT_IN_WORK] ?></a>
            <a class="flexim-header-menu__item<?=$draft_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_DRAFT, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_DRAFT] ?></a>
            <a class="flexim-header-menu__item<?=$trash_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_TRASH, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_TRASH] ?></a>
            <?php endif; ?>
        </nav>
        <div class="app-topbar__right">
            <?php
            if(file_exists('find.php')) {
                include 'find.php';
            }
            else {
                echo "<div class='ml-auto'></div>";
            }
            
            include 'header_right.php';
            ?>
        </div>
    </div>
</div>