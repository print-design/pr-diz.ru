<?php
include '../include/left_bar.php';

$print_class = '';
$laminate_class = '';
$cut_class = '';

if ($work_id == WORK_PRINTING) {
    $print_class = ' disabled';
}
elseif ($work_id == WORK_LAMINATION) {
    $laminate_class = ' disabled';
}
elseif ($work_id == WORK_CUTTING) {
    $cut_class = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link<?=$print_class ?>" href="?work_id=<?=WORK_PRINTING ?>&machine_id=<?=PRINTER_COMIFLEX ?>">Печать</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$laminate_class ?>" href="?work_id=<?=WORK_LAMINATION ?>&machine_id=<?=LAMINATOR_SOLVENT ?>">Ламинация</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cut_class ?>" href="?work_id=<?=WORK_CUTTING ?>&machine_id=<?=CUTTER_ATLAS ?>">Резка</a>
            </li>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>