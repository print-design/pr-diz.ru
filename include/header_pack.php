<?php
include '../include/left_bar.php';

$production_status = '';
$pack_status = '';
$ship_status = '';
$shipped_status = '';

if(empty($status_id) && !empty($calculation)) {
    $status_id = $calculation->status_id;
}

if($folder == "pack") {
    if($status_id == ORDER_STATUS_PACK_READY) {
        $pack_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_SHIP_READY) {
        $ship_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_SHIPPED) {
        $shipped_status = ' disabled';
    }
    else {
        $production_status = ' disabled';
    }
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link<?=$production_status ?>" href="<?= APPLICATION ?>/pack/<?= BuildQueryRemoveArray(array("status", "page", "order", "waiting", "from", "to")) ?>"><?= ORDER_STATUS_TITLES[ORDER_STATUS_IN_PRODUCTION] ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$pack_status ?>" href="<?= APPLICATION ?>/pack/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_PACK_READY, array("page", "order", "waiting", "from", "to")) ?>">Упаковка</a>
            </li>
            <li class="nav-item text-nowrap">
                <a class="nav-link<?=$ship_status ?>" href="<?= APPLICATION ?>/pack/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIP_READY, array("page", "order", "waiting", "from", "to")) ?>"><?= ORDER_STATUS_TITLES[ORDER_STATUS_SHIP_READY] ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$shipped_status ?>" href="<?= APPLICATION ?>/pack/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, array("page", "order", "waiting", "from", "to")) ?>"><?= ORDER_STATUS_TITLES[ORDER_STATUS_SHIPPED] ?></a>
            </li>
        </ul>
        <div class="ml-auto"></div>
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