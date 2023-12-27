<?php
include '../include/left_bar.php';

$pack_status = '';

if($folder == 'pack') {
    $pack_status = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link<?=$pack_status ?>" href="<?=APPLICATION ?>/pack/">Упаковка</a></li>
            <li class="nav-item"><a class="nav-link" href="javascript: void();">Ждёт отгрузки</a></li>
            <li class="nav-item"><a class="nav-link" href="javascript: void();">Отгружено</a></li>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>