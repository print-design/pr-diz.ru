<?php
include '../include/left_bar.php';

$shipments_status = '';
$drafts_status = '';

if(filter_input(INPUT_GET, 'draft', FILTER_VALIDATE_INT) == 1) {
    $drafts_status = ' disabled';
}
else {
    $shipments_status = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?= APPLICATION ?>/buh/<?= BuildQueryRemoveArray(["production", "paid", "status", "page", "order", "from", "to", "id"]) ?>">Все</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("production", 1, ["paid", "status", "page", "order", "from", "to", "id"]) ?>">Производят</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_PACK_READY, ["production", "paid", "page", "order", "from", "to", "id"]) ?>">Упаковка</a>
            </li>
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIP_READY, ["production", "paid", "page", "order", "from", "to", "id"]) ?>">Ждёт отгрузки</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, ["production", "paid", "page", "order", "from", "to", "id"]) ?>">Отгружено</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("paid", 1, ["production", "status", "page", "order", "from", "to", "id"]) ?>">Оплачено</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$shipments_status ?>" href="<?= APPLICATION ?>/shipment/">Отгрузки</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$drafts_status ?>" href="<?= APPLICATION ?>/shipment/?draft=1">Черновики</a>
            </li>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>
