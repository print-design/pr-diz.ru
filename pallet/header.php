<?php
include '../include/left_bar.php';

$pallets_status = '';
$cut_sources_status = '';
$utilized_status = '';

// На странице паллета или списка паллетов
// Выделяем пункт "паллеты".
if($file == 'pallet.php' || $file == 'new.php' || $file == 'index.php') {
    $pallets_status = ' disabled';
}

// На странице рулона из паллета:
// Если паллет сработан, то выделяем пункт меню "Сработанная плёнка",
// Если он раскроен, то выделяем пункт меню "Раскроили",
// Иначе выделяем пункт меню "Паллеты".
if($file == 'roll.php') {
    if($status_id == ROLL_STATUS_UTILIZED) {
        $utilized_status = ' disabled';
    }
    elseif($status_id == ROLL_STATUS_CUT) {
        $cut_sources_status = ' disabled';
    }
    else {
        $pallets_status = ' disabled';
    }
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?= APPLICATION ?>/roll/">Рулоны</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$pallets_status ?>" href="<?= APPLICATION ?>/pallet/">Паллеты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cut_sources_status ?>" href="<?= APPLICATION ?>/roll/?status=<?= ROLL_STATUS_CUT ?>">Раскроили</a>
            </li>
            <li class="nav-item text-nowrap">
                <a class="nav-link<?=$utilized_status ?>" href="<?= APPLICATION ?>/roll/?status=<?= ROLL_STATUS_UTILIZED ?>">Сработанная пленка</a>
            </li>
            <?php
            if($_SERVER['HTTP_HOST'] == "pr-diz-test.ru") {
                echo "<li style='font-weight: bold; font-size: large; margin-left: 50px;'>ТЕСТОВАЯ</li>";
            }
            else if($_SERVER['HTTP_HOST'] == "pr-diz-develop.ru") {
                echo "<li style='font-weight: bold; font-size: large; margin-left: 50px;'>РАЗРАБОТКА</li>";
            }
            ?>
        </ul>
        <?php
        include 'find.php';
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>