<?php
include '../include/left_bar.php';

$waiting_status = '';
$paying_status = '';
$paid_status = '';
$income_status = '';

if($folder == "buh") {
    if($file == 'income.php') {
        $income_status = ' disabled';
    }
    elseif($income) {
        $income_status = ' disabled';
    }
    elseif($paying) {
        $paying_status = ' disabled';
    }
    elseif($paid) {
        $paid_status = ' disabled';
    }
    else {
        $waiting_status = ' disabled';
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
            <li class="nav-item">
                <a class="nav-link<?=$waiting_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryRemoveArray(["paying", "paid", "income", "production", "status", "page", "order", "from", "to", "id"]) ?>">Поступлений нет</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$paying_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("paying", 1, ["paid", "income", "production", "status", "page", "order", "from", "to", "id"]) ?>">Частично оплачены</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$paid_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryAddRemoveArray("paid", 1, ["paying", "income", "production", "status", "page", "order", "from", "to", "id"]) ?>">Оплачены</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$income_status ?>" href="<?= APPLICATION ?>/buh/income.php">Поступления</a>
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