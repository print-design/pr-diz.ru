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
$admin_class = '';

if($folder == "request_calc" || $folder == "techmap" || $folder == "schedule") {
    $zakaz_class = " active";
}
elseif($folder == "pallet" || $folder == "roll" || $folder == "cut_source" || $folder == "utilized") {
    $sklad_class = " active";
}
elseif($file == "grafik.php") {
    $grafik_class = " active";
}
elseif($folder == "user" || $folder == "supplier") {
    $admin_class = " active";
}
?>
<div id="left_bar">
    <a href="<?=APPLICATION ?>/" class="left_bar_item logo" title="На главную" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/logo.svg" /></a>
    <?php
    if(IsInRole(array('technologist', 'dev', 'manager', 'administrator', 'designer')) && false):
    ?>
    <a href="<?=APPLICATION ?>/request_calc/<?= IsInRole('manager') ? BuildQuery("manager", GetUserId()) : "" ?>" class="left_bar_item<?=$zakaz_class ?>" title="Заказы"><img src="<?=APPLICATION ?>/images/nav_clock.svg" /></a>
    <?php
    endif;
    if(IsInRole(array('technologist', 'storekeeper', 'dev', 'manager', 'administrator'))):
    ?>
    <a href="<?=APPLICATION ?>/roll/" class="left_bar_item<?=$sklad_class ?>" title="Склад" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_sklad.svg" /></a>
    <a href="<?=APPLICATION ?>/grafik.php?id=1" class="left_bar_item<?=$grafik_class ?>" title="График" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_grafik.svg" /></a>
    <?php
    endif;
    if(IsInRole(array('technologist', 'dev', 'administrator'))):
    ?>
    <a href="<?=APPLICATION ?>/user/" class="left_bar_item<?=$admin_class ?>" title="Админка" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php
    endif;
    ?>
</div>