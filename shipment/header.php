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
