<?php
include '../include/left_bar.php';

$rolls_status = '';
$cut_sources_status = '';
$utilized_status = '';

if(filter_input(INPUT_GET, 'status', FILTER_VALIDATE_INT) == ROLL_STATUS_CUT || (isset($status_id) && $status_id == ROLL_STATUS_CUT)) {
    $cut_sources_status = ' disabled';
}
elseif(filter_input(INPUT_GET, 'status', FILTER_VALIDATE_INT) == ROLL_STATUS_UTILIZED || (isset($status_id) && $status_id == ROLL_STATUS_UTILIZED)) {
    $utilized_status = ' disabled';
}
else {
    $rolls_status = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link<?=$rolls_status ?>" href="<?= APPLICATION ?>/roll/">Рулоны</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= APPLICATION ?>/pallet/">Паллеты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cut_sources_status ?>" href="<?= APPLICATION ?>/roll/?status=<?= ROLL_STATUS_CUT ?>">Раскроили</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$utilized_status ?> text-nowrap" href="<?= APPLICATION ?>/roll/?status=<?= ROLL_STATUS_UTILIZED ?>">Сработанная пленка</a>
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