<?php
include '../include/left_bar.php';

$pack_status = '';
$ship_status = '';

if(filter_input(INPUT_GET, 'status_id') == ORDER_STATUS_SHIP_READY) {
    $ship_status = ' disabled';
}
else {
    $pack_status = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link<?=$pack_status ?>" href="<?=APPLICATION ?>/pack/">Упаковка</a></li>
            <li class="nav-item"><a class="nav-link<?=$ship_status ?>" href="<?=APPLICATION ?>/pack/?status_id=<?=ORDER_STATUS_SHIP_READY ?>">Ждёт отгрузки</a></li>
            <li class="nav-item"><a class="nav-link" href="javascript: void();">Отгружено</a></li>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>