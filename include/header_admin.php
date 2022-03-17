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

if($folder == 'user') {
    $user_class = ' disabled';
}
elseif($folder == 'supplier' && $file != 'film.php') {
    $supplier_class = ' disabled';
}
elseif ($file == 'film.php') {
    $film_class = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link<?=$user_class ?>" href="<?=APPLICATION ?>/user/">Сотрудники</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$supplier_class ?>" href="<?=APPLICATION ?>/supplier/">Поставщики</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$film_class ?>" href="<?=APPLICATION ?>/supplier/film.php">Пленка</a>
            </li>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include 'header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>