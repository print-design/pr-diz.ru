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
    <nav class="flexim-header-menu__nav">
        <?php
        if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))):
        ?>
        <button type="button" class="flexim-header-menu__item<?=$shipped_status ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_SHIPPED] ?></button>
        <button type="button" class="flexim-header-menu__item<?=$ship_ready_status ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_SHIP_READY] ?></button>
        <button type="button" class="flexim-header-menu__item<?=$production_status ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_IN_PRODUCTION] ?></button>
        <button type="button" class="flexim-header-menu__item<?=$calculation_status ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_IN_WORK] ?></button>
        <button type="button" class="flexim-header-menu__item<?=$not_in_work_status ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_NOT_IN_WORK] ?></button>
        <button type="button" class="flexim-header-menu__item<?=$draft_status ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_DRAFT] ?></button>
        <button type="button" class="flexim-header-menu__item<?=$trash_status ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_TRASH] ?></button>
        <?php endif; ?>
    </nav>
        <?php
        if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))):
        ?>
            <div class="nav-item">
                <a class="nav-link<?=$shipped_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_SHIPPED] ?></a>
            </div>
            <div class="nav-item">
                <a class="nav-link<?=$ship_ready_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIP_READY, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_SHIP_READY] ?></a>
            </div>
            <div class="nav-item">
                <a class="nav-link<?=$production_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_IN_PRODUCTION, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_IN_PRODUCTION] ?></a>
            </div>
            <div class="nav-item">
                <a class="nav-link<?=$calculation_status ?> text-nowrap" href="<?=APPLICATION ?>/calculation/<?= BuildQueryRemoveArray(array("status", "page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_IN_WORK] ?></a>
            </div>
            <div class="nav-item">
                <a class="nav-link<?=$not_in_work_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_NOT_IN_WORK, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_NOT_IN_WORK] ?></a>
            </div>
            <div class="nav-item">
                <a class="nav-link<?=$draft_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_DRAFT, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_DRAFT] ?></a>
            </div>
            <div class="nav-item">
                <a class="nav-link<?=$trash_status ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_TRASH, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_TRASH] ?></a>
            </div>
            <?php endif; ?>
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
<div id="topmost"></div>