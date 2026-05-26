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
$cut_class = '';
$pack_class = '';
$admin_class = '';
$improvement_class = '';
$okto_class = '';

if($folder == "calculation" || $folder == "techmap" || $folder == "schedule") {
    $zakaz_class = " flexim-nav-rail__tab--active";
}
elseif($folder == "pallet" || $folder == "roll" || $folder == "cut_source" || $folder == "utilized") {
    $sklad_class = " flexim-nav-rail__tab--active";
}
elseif($folder == "plan") {
    $plan_class = " flexim-nav-rail__tab--active";
}
elseif ($folder == "cut") {
    $cut_class = " flexim-nav-rail__tab--active";
}
elseif($folder == "pack") {
    $pack_class = " flexim-nav-rail__tab--active";
}
elseif($folder == "user" || $folder == "supplier" || $folder == 'admin') {
    $admin_class = " flexim-nav-rail__tab--active";
}
elseif($folder == "improvement") {
    $improvement_class = " flexim-nav-rail__tab--active";
}
elseif($folder == "okto") {
    $okto_class = " flexim-nav-rail__tab--active";
}

?>
<aside class="flexim-nav-rail" aria-label="Левое меню">
    <span class="flexim-logo__mark" aria-hidden="true"></span>
    <nav class="flexim-nav-rail__tabs">
    <?php
    // Расчёты
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))):
    ?>
    <button type="button" class="flexim-nav-rail__tab<?=$zakaz_class ?>" aria-label="Заказы"><span data-flexim-icon="time" data-size="24" aria-hidden="true"></span></button>
    <a href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, array("page", "order")) ?>" class="left_bar_item ui_tooltip right<?=$zakaz_class ?>" title="Заказы"><img src="<?=APPLICATION ?>/images/nav_clock.svg" /></a>
    <?php
    endif;
    // Склад
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER]))):
    ?>
    <button type="button" class="flexim-nav-rail__tab<?=$sklad_class ?>" aria-label="Склад"><span data-flexim-icon="box" data-size="24" aria-hidden="true"></span></button>
    <a href="<?=APPLICATION ?>/roll/" class="left_bar_item ui_tooltip right<?=$sklad_class ?>" title="Склад"><img src="<?=APPLICATION ?>/images/nav_sklad.svg" /></a>
    <?php
    endif;
    // План
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT], ROLE_NAMES[ROLE_COLORIST]))):
    ?>
    <button type="button" class="flexim-nav-rail__tab<?=$plan_class ?>" aria-label="План"><span data-flexim-icon="calendar" data-size="24" aria-hidden="true"></span></button>
    <a href="<?=APPLICATION ?>/plan/" class="left_bar_item ui_tooltip right<?=$plan_class ?>" title="План"><img src="<?=APPLICATION ?>/images/nav_grafik.svg" /></a>
    <?php
    endif;
    
    /*-----------------*/
    /*ВРЕМЕННО*/
    if(GetUserId() == CUTTER_SOMA):
    ?>
    <button type="button" class="flexim-nav-rail__tab<?=$plan_class ?>" aria-label="План"><span data-flexim-icon="calendar" data-size="24" aria-hidden="true"></span></button>
    <a href="<?=APPLICATION ?>/plan/<?= BuildQueryAddRemoveArray("work_id", WORK_CUTTING, array("page", "order")) ?>" class="left_bar_item ui_tooltip right<?=$plan_class ?>" title="План"><img src="<?=APPLICATION ?>/images/nav_grafik.svg" /></a>
    <?php
    endif;
    /*ВРЕМЕННО*/
    
    // Резка
    if(IsInRole(CUTTER_USERS) || IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_LAM_HEAD]))):
    ?>
    <button type="button" class="flexim-nav-rail__tab<?=$cut_class ?>" aria-label="Резка"><span data-flexim-icon="factory" data-size="24" aria-hidden="true"></span></button>
    <a href="<?=APPLICATION ?>/cut/<?= IsInRole(CUTTER_USERS) ? "" : "?machine_id=".CUTTER_1 ?>" class="left_bar_item ui_tooltip right<?=$cut_class ?>" title="Резка"><img src="<?=APPLICATION ?>/images/icons/factory.svg" /></a>
    <?php
    endif;
    // Упаковка
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT]))):
    ?>
    <button type="button" class="flexim-nav-rail__tab<?=$pack_class ?>" aria-label="Упаковка"><span data-flexim-icon="loader-machine" data-size="24" aria-hidden="true"></span></button>
    <a href="<?=APPLICATION ?>/pack/" class="left_bar_item ui_tooltip right<?=$pack_class ?>" title="Упаковка"><img src="<?=APPLICATION ?>/images/icons/loader_machine.svg" /></a>
    <?php
    endif;
    // Админка
    if(IsInRole(ROLE_NAMES[ROLE_TECHNOLOGIST])):
    ?>
    <button type="button" class="flexim-nav-rail__tab<?=$admin_class ?>" aria-label="Админка"><span data-flexim-icon="settings" data-size="24" aria-hidden="true"></span></button>
    <a href="<?=APPLICATION ?>/user/" class="left_bar_item ui_tooltip right<?=$admin_class ?>" title="Админка"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php
    elseif(IsInRole(array(ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD]))):
    ?>
    <button type="button" class="flexim-nav-rail__tab<?=$admin_class ?>" aria-label="Админка"><span data-flexim-icon="settings" data-size="24" aria-hidden="true"></span></button>
    <a href="<?=APPLICATION ?>/admin/plan_employees.php" class="left_bar_item ui_tooltip right<?=$admin_class ?>" title="Админка"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php
    endif;
    ?>
    
    <!-- Старший менеджер может редактировать константы -->
    <?php if(IsInRole(ROLE_NAMES[ROLE_MANAGER_SENIOR])): ?>
    <button type="button" class="flexim-nav-rail__tab<?=$admin_class ?>" aria-label="Админка"><span data-flexim-icon="settings" data-size="24" aria-hidden="true"></span></button>
    <a href="<?=APPLICATION ?>/supplier/film.php" class="left_bar_item ui_tooltip right<?=$admin_class ?>" title="Админка"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php endif; ?>
    
    <!-- Предложения по улучшению -->
    <?php if(IsInRole(ROLE_NAMES[ROLE_MANAGER_SENIOR]) || IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD]))): ?>
    <button type="button" class="flexim-nav-rail__tab<?=$improvement_class ?>" aria-label="Предложения по улучшению"><span data-flexim-icon="like" data-size="24" aria-hidden="true"></span></button>
    <a href="<?= APPLICATION ?>/improvement/" class="left_bar_item ui_tooltip right<?=$improvement_class ?>" title="Предложения по улучшению"><i class="far fa-thumbs-up" style="font-size: 1.3rem;"></i></a>
    <?php endif; ?>
    <button type="button" class="flexim-nav-rail__tab<?=$okto_class ?>" aria-label="Octopus"><span data-flexim-icon="settings" data-size="24" aria-hidden="true"></span></button>
    <a href="<?= APPLICATION ?>/okto/" class="left_bar_item ui_tooltip right<?=$okto_class ?>" title="Octopus"><i class="far fa-comments" style="font-size: 1.3rem;"></i></a>
    </nav>
</aside>