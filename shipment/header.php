<?php
include '../include/left_bar.php';

$production_status = '';
$pack_status = '';
$ship_status = '';
$shipped_status = '';
$document_status = '';
$draft_status = '';

if($folder == 'shipment') {
    if($file == 'document.php' || $file == 'details_doc.php') {
        if($draft) {
            $draft_status = ' disabled';
        }
        else {
            $document_status = ' disabled';
        }
    }
    elseif($file == 'details.php') {
        if(!empty($calculation) && is_a($calculation, CalculationBase::class)) {
            switch($calculation->status_id) {
                case ORDER_STATUS_PACK_READY:
                    $pack_status = ' disabled';
                    break;
                case ORDER_STATUS_SHIP_READY:
                    $ship_status = ' disabled';
                    break;
                case ORDER_STATUS_SHIPPED:
                    $shipped_status = ' disabled';
                    break;
                default :
                    $production_status = ' disabled';
                    break;
            }
        }
    }
    elseif($status == ORDER_STATUS_PACK_READY){
        $pack_status = ' disabled';
    }
    elseif($status == ORDER_STATUS_SHIP_READY) {
        $ship_status = ' disabled';
    }
    elseif($status == ORDER_STATUS_SHIPPED) {
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
                <a class="nav-link<?=$production_status ?>" href="<?= APPLICATION ?>/shipment/<?= BuildQueryRemoveArray(["status", "page", "order", "from", "to", "id", "draft"]) ?>">Производят</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$pack_status ?>" href="<?= APPLICATION ?>/shipment/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_PACK_READY, ["page", "order", "from", "to", "id", "draft"]) ?>">Упаковка</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$ship_status ?>" href="<?= APPLICATION ?>/shipment/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIP_READY, ["page", "order", "from", "to", "id", "draft"]) ?>">Ждёт отгрузки</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$shipped_status ?>" href="<?= APPLICATION ?>/shipment/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, ["page", "order", "from", "to", "id", "draft"]) ?>">Отгружено</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$document_status ?>" href="document.php">Документы</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$draft_status ?>" href="document.php?draft=1">Черновики</a>
            </li>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>
