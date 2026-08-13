<?php
include '../include/left_bar.php';

$all_status = '';
$production_status = '';
$pack_status = '';
$ship_status = '';
$shipped_status = '';
$paid_status = '';

if(empty($status_id) && !empty($calculation)) {
    $status_id = $calculation->status_id;
}

if($folder == "buh") {
    if($production) {
        $production_status = ' disabled';
    }
    elseif($paid) {
        $paid_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_PACK_READY) {
        $pack_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_SHIP_READY) {
        $ship_status = ' disabled';
    }
    elseif($status_id == ORDER_STATUS_SHIPPED) {
        $shipped_status = ' disabled';
    }
    else {
        $all_status = ' disabled';
    }
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item<?=$all_status ?>">
                <a class="nav-link<?=$all_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryRemoveArray(["production", "paid", "status", "page", "order", "from", "to"]) ?>">Все</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$production_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("production", 1, ["paid", "status", "page", "order", "from", "to"]) ?>">Производят</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$pack_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_PACK_READY, ["production", "paid", "page", "order", "from", "to"]) ?>">Упаковка</a>
            </li>
            <li class="nav-item text-nowrap">
                <a class="nav-link<?=$ship_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIP_READY, ["production", "paid", "page", "order", "from", "to"]) ?>">Ждёт отгрузки</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$shipped_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, ["production", "paid", "page", "order", "from", "to"]) ?>">Отгружено</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$paid_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("paid", 1, ["production", "status", "page", "order", "from", "to"]) ?>">Оплачено</a>
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