<?php
$php_self = $_SERVER['PHP_SELF'];
$substrings = mb_split("/", $php_self);
$count = count($substrings);
$folder = '';
$file = '';

if($count > 1) {
    $folder = $substrings[$count - 2];
    $file = $substrings[$count - 1];
}

$zakaz_class = '';
$sklad_class = '';
$grafik_class = '';

if($folder == "calculation") {
    $zakaz_class = " active";
}
else if($folder == "pallet" || $folder == "roll" || $folder == "cut_source" || $folder == "utilized") {
    $sklad_class = " active";
}
else if($file == "grafik.php") {
    $grafik_class = " active";
}
?>
<div id="left_bar">
    <a href="<?=APPLICATION ?>/" class="left_bar_item logo" title="На главную" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/logo.svg" /></a>
    <?php
    if(IsInRole(array('technologist', 'dev'))):
    ?>
    <a href="<?=APPLICATION ?>/calculation/" class="left_bar_item<?=$zakaz_class ?>" title="Заказы"><img src="<?=APPLICATION ?>/images/nav_clock.svg" /></a>
    <?php
    endif;
    if(IsInRole(array('technologist', 'storekeeper', 'dev', 'manager'))):
    ?>
    <a href="<?=APPLICATION ?>/pallet/" class="left_bar_item<?=$sklad_class ?>" title="Склад" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_sklad.svg" /></a>
    <a href="<?=APPLICATION ?>/grafik.php?id=1" class="left_bar_item<?=$grafik_class ?>" title="График" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_grafik.svg" /></a>
    <?php
    endif;
    ?>
</div>