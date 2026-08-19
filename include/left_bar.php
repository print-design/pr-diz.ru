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
$buh_class = '';
$admin_class = '';
$improvement_class = '';
$okto_class = '';

if($folder == "calculation" || $folder == "techmap" || $folder == "schedule") {
    $zakaz_class = " active";
}
elseif($folder == "pallet" || $folder == "roll" || $folder == "cut_source" || $folder == "utilized") {
    $sklad_class = " active";
}
elseif($folder == "plan") {
    $plan_class = " active";
}
elseif ($folder == "cut") {
    $cut_class = " active";
}
elseif($folder == "pack") {
    $pack_class = " active";
}
elseif($folder == "buh") {
    $buh_class = " active";
}
elseif($folder == "user" || $folder == "supplier" || $folder == 'admin') {
    $admin_class = " active";
}
elseif($folder == "improvement") {
    $improvement_class = " active";
}
elseif($folder == "okto") {
    $okto_class = " active";
}

?>
<div id="left_bar">
    <a href="<?=APPLICATION ?>/" class="left_bar_item logo ui_tooltip right" data-placement="right" title="На главную"><img src="<?=APPLICATION ?>/images/logo.svg" /></a>
    <?php
    // Расчёты
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))):
    ?>
    <a href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, array("page", "order", "id")) ?>" class="left_bar_item ui_tooltip right<?=$zakaz_class ?>" data-placement="right" title="Заказы"><img src="<?=APPLICATION ?>/images/nav_clock.svg" /></a>
    <?php
    endif;
    // Склад
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER]))):
    ?>
    <a href="<?=APPLICATION ?>/roll/" class="left_bar_item ui_tooltip right<?=$sklad_class ?>" data-placement="right" title="Склад"><img src="<?=APPLICATION ?>/images/nav_sklad.svg" /></a>
    <?php
    endif;
    // План
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT], ROLE_NAMES[ROLE_COLORIST]))):
    ?>
    <a href="<?=APPLICATION ?>/plan/" class="left_bar_item ui_tooltip right<?=$plan_class ?>" data-placement="right" title="План"><img src="<?=APPLICATION ?>/images/nav_grafik.svg" /></a>
    <?php
    endif;
    
    /*-----------------*/
    /*ВРЕМЕННО*/
    if(GetUserId() == CUTTER_SOMA):
    ?>
    <a href="<?=APPLICATION ?>/plan/<?= BuildQueryAddRemoveArray("work_id", WORK_CUTTING, array("page", "order")) ?>" class="left_bar_item ui_tooltip right<?=$plan_class ?>" data-placement="right" title="План"><img src="<?=APPLICATION ?>/images/nav_grafik.svg" /></a>
    <?php
    endif;
    /*ВРЕМЕННО*/
    
    // Резка
    if(IsInRole(CUTTER_USERS) || IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_LAM_HEAD]))):
    ?>
    <a href="<?=APPLICATION ?>/cut/<?= IsInRole(CUTTER_USERS) ? "" : "?machine_id=".CUTTER_1 ?>" class="left_bar_item ui_tooltip right<?=$cut_class ?>" data-placement="right" title="Резка"><img src="<?=APPLICATION ?>/images/icons/factory.svg" /></a>
    <?php
    endif;
    // Упаковка
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER]))):
    ?>
    <a href="<?=APPLICATION ?>/pack/" class="left_bar_item ui_tooltip right<?=$pack_class ?>" data-placement="right" title="Упаковка"><img src="<?=APPLICATION ?>/images/icons/loader_machine.svg" /></a>
    <?php
    endif;
    // Оплата заказа
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ACCOUNTANT]))):
    ?>
    <a href="<?= APPLICATION ?>/buh/" class="left_bar_item ui_tooltip right<?=$buh_class ?>" data-placement="right" title="Готовая продукция">
        <svg viewBox="0 0 24 24" width="24" height="24" data-flexim-name="rub" style="color: currentcolor; display: inline-flex; flex-shrink: 0;">
            <path d="M6.125 2V12H4V14H6.125V16H4V18H6.125V22H8.25V18H13.5625V16H8.25V14H14.625C18.1313 14 21 11.3 21 8C21 4.7 18.1313 2 14.625 2H6.125ZM8.25 4H14.625C16.9827 4 18.875 5.781 18.875 8C18.875 10.219 16.9827 12 14.625 12H8.25V4Z" fill="currentColor"></path>
        </svg>
    </a>
    <?php
    endif;
    // Админка
    if(IsInRole(ROLE_NAMES[ROLE_TECHNOLOGIST])):
    ?>
    <a href="<?=APPLICATION ?>/user/" class="left_bar_item ui_tooltip right<?=$admin_class ?>" data-placement="right" title="Админка"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php
    elseif(IsInRole(array(ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD]))):
    ?>
    <a href="<?=APPLICATION ?>/admin/plan_employees.php" class="left_bar_item ui_tooltip right<?=$admin_class ?>" data-placement="right" title="Админка"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php
    endif;
    ?>
    
    <!-- Старший менеджер может редактировать константы -->
    <?php if(IsInRole(ROLE_NAMES[ROLE_MANAGER_SENIOR])): ?>
    <a href="<?=APPLICATION ?>/supplier/film.php" class="left_bar_item ui_tooltip right<?=$admin_class ?>" data-placement="right" title="Админка"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php endif; ?>
    
    <!-- Предложения по улучшению -->
    <?php if(IsInRole(ROLE_NAMES[ROLE_MANAGER_SENIOR]) || IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD]))): ?>
    <a href="<?= APPLICATION ?>/improvement/" class="left_bar_item ui_tooltip right<?=$improvement_class ?>" data-placement="right" title="Предложения по улучшению"><i class="far fa-thumbs-up" style="font-size: 1.3rem;"></i></a>
    <?php endif; ?>
    
    <a href="<?= APPLICATION ?>/okto/" class="left_bar_item ui_tooltip right<?=$okto_class ?> d-none" data-placement="right" title="Octopus"><i class="far fa-comments" style="font-size: 1.3rem;"></i></a>
</div>