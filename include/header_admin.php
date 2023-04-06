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

$user_class = '';
$supplier_class = '';
$film_class = '';
$norm_class = '';
$currency_class = '';
$plan_class = '';

if($folder == 'user') {
    $user_class = ' disabled';
}
elseif($folder == 'supplier' && $file != 'film.php') {
    $supplier_class = ' disabled';
}
elseif ($file == 'film.php') {
    $film_class = ' disabled';
}
elseif($file == 'currency.php') {
    $currency_class = ' disabled';
}
elseif($file == 'plan_employees.php' || $file == 'plan_employees_create.php') {
    $plan_class = ' disabled';
}
elseif($folder == 'admin') {
    $norm_class = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php if(IsInRole(array('technologist', 'dev', 'administrator'))): ?>
            <li class="nav-item">
                <a class="nav-link<?=$user_class ?>" href="<?=APPLICATION ?>/user/">Сотрудники</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$supplier_class ?>" href="<?=APPLICATION ?>/supplier/">Поставщики</a>
            </li>
            <?php endif; ?>
            <?php if(IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))): ?>
            <li class="nav-item">
                <a class="nav-link<?=$film_class ?>" href="<?=APPLICATION ?>/supplier/film.php">Пленка</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$norm_class ?>" href="<?=APPLICATION ?>/admin/machine.php<?= BuildQuery('machine_id', 4) ?>">Нормы</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$currency_class ?>" href="<?=APPLICATION ?>/admin/currency.php">Курсы валют</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$plan_class ?>" href="<?=APPLICATION ?>/admin/plan_employees.php">План</a>
            </li>
            <?php endif; ?>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include 'header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>