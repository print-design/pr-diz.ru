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
    elseif($paying) {
        $paying_status = ' disabled';
    }
    elseif($paid) {
        $paid_status = ' disabled';
    }
    elseif($income) {
        $income_status = ' disabled';
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
                <a class="nav-link<?=$waiting_status ?>" href="<?= APPLICATION ?>/buh/<?= BuildQueryRemoveArray(["paying", "paid", "income", "production", "status", "page", "order", "from", "to", "id"]) ?>">Ждёт оплату</a>
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