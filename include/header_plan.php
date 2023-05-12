<?php
include '../include/left_bar.php';
require_once '../include/machines.php';

$php_self = $_SERVER['PHP_SELF'];
$substrings = mb_split("/", $php_self);
$count = count($substrings);
$folder = '';
$file = '';

if($count > 1) {
    $folder = $substrings[$count - 2];
    $file = $substrings[$count - 1];
}

$print_class = '';
$laminate_class = '';
$cut_class = '';

if ($file == 'print.php') {
    $print_class = ' disabled';
}
elseif ($file == 'laminate.php') {
    $laminate_class = ' disabled';
}
elseif ($file == 'cut.php') {
    $cut_class = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link<?=$print_class ?>" href="print.php?id=<?=PRINTER_COMIFLEX ?>">Печать</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$laminate_class ?>" href="laminate.php?id=<?=LAMINATOR_SOLVENT ?>">Ламинация</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cut_class ?>" href="cut.php?id=<?=CUTTER_ATLAS ?>">Резка</a>
            </li>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>