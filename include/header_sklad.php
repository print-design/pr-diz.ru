<?php
include 'left_bar.php';

$php_self = $_SERVER['PHP_SELF'];
$substrings = mb_split("/", $php_self);
$count = count($substrings);
$folder = '';
$file = '';

if($count > 1) {
    $folder = $substrings[$count - 2];
    $file = $substrings[$count - 1];
}

$rolls_status = '';
$pallets_status = '';
$cut_sources_status = '';
$utilized_status = '';
$rational_cut_status = '';

if($folder == 'roll') {
    $rolls_status = ' disabled';
}
elseif($folder == 'pallet') {
    $pallets_status = ' disabled';
}
elseif($folder == 'cut_source') {
    $cut_sources_status = ' disabled';
}
elseif($folder == 'utilized') {
    $utilized_status = ' disabled';
}
elseif($folder == 'rational_cut') {
    $rational_cut_status = ' disabled';
}
            
// На странице рулона:
// Если он сработан, то выделяем пункт меню "Сработанная плёнка",
// Если он раскроен, то выделяем пункт меню "Раскроили"
// Иначе выделяем пункт меню "Рулоны"
if($folder == 'roll' && $file == 'roll.php') {
    if(isset($status_id) && $status_id == ROLL_STATUS_UTILIZED) {
        $rolls_status = '';
        $cut_sources_status = '';
        $utilized_status = ' disabled';
    }
    elseif(isset ($status_id) && $status_id == ROLL_STATUS_CUT) {
        $rolls_status = '';
        $cut_sources_status = ' disabled';
        $utilized_status = '';
    }
}
            
// На странице рулона из паллета:
// Если паллет сработан, то выделяем пункт меню "Сработанная плёнка",
// Если он раскроен, то выделяем пункт меню "Раскроили"
// Иначе выделяем пункт меню "Паллеты".
if($folder == 'pallet' && $file == 'roll.php') {
    if(isset($status_id) && $status_id == ROLL_STATUS_UTILIZED) {
        $pallets_status = '';
        $cut_sources_status = '';
        $utilized_status = ' disabled';
    }
    elseif(isset ($status_id) && $status_id == ROLL_STATUS_CUT) {
        $pallets_status = '';
        $cut_sources_status = ' disabled';
        $utilized_status = '';
    }
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            if(IsInRole(array('technologist', 'dev', 'storekeeper', 'manager', 'administrator'))):
            ?>
            <li class='nav-item'>
                <a class="nav-link<?=$rolls_status ?>" href="<?=APPLICATION ?>/roll/<?= BuildQueryRemoveArray(array('page', 'id')) ?>">Рулоны</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$pallets_status ?>" href="<?=APPLICATION ?>/pallet/<?= BuildQueryRemoveArray(array('page', 'id')) ?>">Паллеты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cut_sources_status ?>" href="<?=APPLICATION ?>/cut_source/<?= BuildQueryRemoveArray(array('page', 'id')) ?>">Раскроили</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$utilized_status ?> text-nowrap" href="<?=APPLICATION ?>/utilized/<?= BuildQueryRemoveArray(array('page', 'id')) ?>">Сработанная пленка</a>
            </li>
            <?php
            endif;
            
            if(IsInRole(array('technologist', 'dev', 'manager'))):
            ?>
            <li class="nav-item d-none">
                <a class="nav-link<?=$rational_cut_status ?> text-nowrap" href="<?=APPLICATION ?>/rational_cut">Рациональный раскрой</a>
            </li>
            <?php
            endif;
            
            if($_SERVER['HTTP_HOST'] == "pr-diz-test.ru") {
                echo "<li style='font-weight: bold; font-size: large; margin-left: 50px;'>ТЕСТОВАЯ</li>";
            }
            else if($_SERVER['HTTP_HOST'] == "pr-diz-develop.ru") {
                echo "<li style='font-weight: bold; font-size: large; margin-left: 50px;'>РАЗРАБОТКА</li>";
            }
            ?>
        </ul>
        <?php
        if(file_exists('find.php')) {
            include 'find.php';
        }
        else {
            echo "<div class='ml-auto'></div>";
        }
        
        include 'header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>