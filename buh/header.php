<?php
include '../include/left_bar.php';

$waiting_status = '';
$paying_status = '';
$paid_status = '';
$receipt_status = '';
$production_status = '';
$pack_status = '';
$ship_status = '';
$shipped_status = '';

if(!empty($calculation)) {
    $status_id = $calculation->status_id;
}

if($folder == "buh") {
    if($file == 'income.php') {
        $receipt_status = ' disabled';
    }
    elseif($paying) {
        $paying_status = ' disabled';
    }
    elseif($paid) {
        $paid_status = ' disabled';
    }
    elseif($production) {
        $production_status = ' disabled';
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
        $waiting_status = ' disabled';
    }
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link<?=$waiting_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryRemoveArray(["paying", "paid", "production", "status", "page", "order", "from", "to", "id"]) ?>">Ждёт оплату</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$paying_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("paying", 1, ["paid", "production", "status", "page", "order", "from", "to", "id"]) ?>">Частично оплачены</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$paid_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("paid", 1, ["paying", "production", "status", "page", "order", "from", "to", "id"]) ?>">Оплачены</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$receipt_status ?>" href="<?= APPLICATION ?>/buh/income.php">Поступления</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$production_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("production", 1, ["paying", "paid", "status", "page", "order", "from", "to", "id"]) ?>">Производят</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$pack_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_PACK_READY, ["production", "paid", "page", "order", "from", "to", "id"]) ?>">Упаковка</a>
            </li>
            <li class="nav-item text-nowrap">
                <a class="nav-link<?=$ship_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIP_READY, ["production", "paid", "page", "order", "from", "to", "id"]) ?>">Ждёт отгрузки</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$shipped_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, ["production", "paid", "page", "order", "from", "to", "id"]) ?>">Отгружено</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= APPLICATION ?>/shipment/">Отгрузки</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= APPLICATION ?>/shipment/?draft=1">Черновики</a>
            </li>
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