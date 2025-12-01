<?php
include '../include/left_bar.php';

$pack_status = '';
$ship_status = '';
$shipped_status = '';

if(filter_input(INPUT_GET, 'status_id') == ORDER_STATUS_SHIP_READY || (!empty($calculation) && $calculation->status_id == ORDER_STATUS_SHIP_READY)) {
    $ship_status = ' disabled';
}
elseif(filter_input(INPUT_GET, 'status_id') == ORDER_STATUS_SHIPPED || (!empty ($calculation) && $calculation->status_id == ORDER_STATUS_SHIPPED)) {
    $shipped_status = ' disabled';
}
else {
    $pack_status = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link<?=$pack_status ?>" href="<?= APPLICATION."/pack/?status_id=".ORDER_STATUS_PACK_READY ?>">Упаковка</a></li>
            <li class="nav-item text-nowrap"><a class="nav-link<?=$ship_status ?>" href="<?= APPLICATION."/pack/?status_id=".ORDER_STATUS_SHIP_READY ?>">Ждёт отгрузки</a></li>
            <li class="nav-item"><a class="nav-link<?=$shipped_status ?>" href="<?= APPLICATION."/pack/?status_id=".ORDER_STATUS_SHIPPED ?>">Отгружено</a></li>
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