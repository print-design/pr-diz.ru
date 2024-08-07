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
$plan_class = '';
$admin_class = '';

if($folder == "calculation" || $folder == "techmap" || $folder == "schedule") {
    $zakaz_class = " active";
}
elseif($folder == "pallet" || $folder == "roll" || $folder == "cut_source" || $folder == "utilized") {
    $sklad_class = " active";
}
elseif($folder == "plan") {
    $plan_class = " active";
}
elseif($folder == "user" || $folder == "supplier" || $folder == 'admin') {
    $admin_class = " active";
}

?>
<div id="left_bar">
    <a href="<?=APPLICATION ?>/" class="left_bar_item logo" title="На главную" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/logo.svg" /></a>
    <?php
    // Расчёты
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))):
    ?>
    <a href="<?=APPLICATION ?>/calculation/" class="left_bar_item<?=$zakaz_class ?>" title="Заказы"><img src="<?=APPLICATION ?>/images/nav_clock.svg" /></a>
    <?php
    endif;
    // Склад
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER]))):
    ?>
    <a href="<?=APPLICATION ?>/roll/" class="left_bar_item<?=$sklad_class ?>" title="Склад" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_sklad.svg" /></a>
    <?php
    endif;
    // План
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_STOREKEEPER]))):
    ?>
    <a href="<?=APPLICATION ?>/plan/" class="left_bar_item<?=$plan_class ?>" title="План" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_grafik.svg" /></a>
    <?php
    endif;
    // Админка
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST]))):
    ?>
    <a href="<?=APPLICATION ?>/user/" class="left_bar_item<?=$admin_class ?>" title="Админка" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php
    elseif(IsInRole(array(ROLE_NAMES[ROLE_SCHEDULER]))):
    ?>
    <a href="<?=APPLICATION ?>/admin/plan_employees.php" class="left_bar_item<?=$admin_class ?>" title="Админка" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php
    endif;
    ?>
    
    <!-- Старший менеджер может редактировать константы -->
    <?php if(IsInRole(ROLE_NAMES[ROLE_MANAGER_SENIOR])): ?>
    <a href="<?=APPLICATION ?>/supplier/film.php" class="left_bar_item<?=$admin_class ?>" title="Админка" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php endif; ?>
</div>