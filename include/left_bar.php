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
    <a class="flexim-logo__mark" href="<?= APPLICATION ?>/"></a>
    <nav class="flexim-nav-rail__tabs">
    <?php
    // Расчёты
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))):
    ?>
    <a href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, array("page", "order")) ?>" type="button" class="flexim-nav-rail__tab<?=$zakaz_class ?>" aria-label="Заказы"><span data-flexim-icon="time" data-size="24" aria-hidden="true"></span></a>
    <?php
    endif;
    // Склад
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER]))):
    ?>
    <a href="<?=APPLICATION ?>/roll/" type="button" class="flexim-nav-rail__tab<?=$sklad_class ?>" aria-label="Склад"><span data-flexim-icon="box" data-size="24" aria-hidden="true"></span></a>
    <?php
    endif;
    // План
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT], ROLE_NAMES[ROLE_COLORIST]))):
    ?>
    <a href="<?=APPLICATION ?>/plan/" type="button" class="flexim-nav-rail__tab<?=$plan_class ?>" aria-label="План"><span data-flexim-icon="calendar" data-size="24" aria-hidden="true"></span></a>
    <?php
    endif;
    
    /*-----------------*/
    /*ВРЕМЕННО*/
    if(GetUserId() == CUTTER_SOMA):
    ?>
    <a href="<?=APPLICATION ?>/plan/<?= BuildQueryAddRemoveArray("work_id", WORK_CUTTING, array("page", "order")) ?>" type="button" class="flexim-nav-rail__tab<?=$plan_class ?>" aria-label="План"><span data-flexim-icon="calendar" data-size="24" aria-hidden="true"></span></a>
    <?php
    endif;
    /*ВРЕМЕННО*/
    
    // Резка
    if(IsInRole(CUTTER_USERS) || IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_LAM_HEAD]))):
    ?>
    <a href="<?=APPLICATION ?>/cut/<?= IsInRole(CUTTER_USERS) ? "" : "?machine_id=".CUTTER_1 ?>" type="button" class="flexim-nav-rail__tab<?=$cut_class ?>" aria-label="Резка"><span data-flexim-icon="factory" data-size="24" aria-hidden="true"></span></a>
    <?php
    endif;
    // Упаковка
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT]))):
    ?>
    <a href="<?=APPLICATION ?>/pack/" type="button" class="flexim-nav-rail__tab<?=$pack_class ?>" aria-label="Упаковка"><span data-flexim-icon="loader-machine" data-size="24" aria-hidden="true"></span></a>
    <?php
    endif;
    // Админка
    if(IsInRole(ROLE_NAMES[ROLE_TECHNOLOGIST])):
    ?>
    <a href="<?=APPLICATION ?>/user/" type="button" class="flexim-nav-rail__tab<?=$admin_class ?>" aria-label="Админка"><span data-flexim-icon="settings" data-size="24" aria-hidden="true"></span></a>
    <?php
    elseif(IsInRole(array(ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD]))):
    ?>
    <a href="<?=APPLICATION ?>/admin/plan_employees.php" type="button" class="flexim-nav-rail__tab<?=$admin_class ?>" aria-label="Админка"><span data-flexim-icon="settings" data-size="24" aria-hidden="true"></span></a>
    <?php
    endif;
    ?>
    
    <!-- Старший менеджер может редактировать константы -->
    <?php if(IsInRole(ROLE_NAMES[ROLE_MANAGER_SENIOR])): ?>
    <a href="<?=APPLICATION ?>/supplier/film.php" type="button" class="flexim-nav-rail__tab<?=$admin_class ?>" aria-label="Админка"><span data-flexim-icon="settings" data-size="24" aria-hidden="true"></span></a>
    <?php endif; ?>
    
    <!-- Предложения по улучшению -->
    <?php if(IsInRole(ROLE_NAMES[ROLE_MANAGER_SENIOR]) || IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD]))): ?>
    <a href="<?= APPLICATION ?>/improvement/" type="button" class="flexim-nav-rail__tab<?=$improvement_class ?>" aria-label="Предложения по улучшению"><span data-flexim-icon="like" data-size="24" aria-hidden="true"></span></a>
    <?php endif; ?>
    <a href="<?= APPLICATION ?>/okto/" type="button" class="flexim-nav-rail__tab<?=$okto_class ?>" aria-label="Octopus"><i class="far fa-comments"></i></a>
    </nav>
</aside>