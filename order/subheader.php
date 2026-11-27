<?php
$details_status = '';
$techmap_status = '';
$cut_status = '';

switch($file) {
    case 'details.php':
        $details_status = ' active';
        break;
    case 'techmap.php':
        $techmap_status = ' active';
        break;
    case 'cut.php':
        $cut_status = ' active';
        break;
}
?>
<a href="details.php?<?= http_build_query($_GET) ?>" class="mr-4<?=$details_status ?>">Расчёт</a>
<?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))): ?>
<a href="techmap.php?<?= http_build_query($_GET) ?>" class="mr-4<?=$techmap_status ?>">Тех. карта</a>
<?php endif; ?>
<?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD], ROLE_NAMES[ROLE_STOREKEEPER])) && in_array($calculation->status_id, ORDER_STATUSES_IN_CUT)): ?>
<a href="cut.php?<?= http_build_query($_GET) ?>" class="mr-4<?=$cut_status ?>">Результаты</a>
<?php endif; ?>
<?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER])) && $has_techmap): ?>
<a href="<?= APPLICATION ?>/buh/details.php?<?= http_build_query($_GET) ?>" class="mr-4">Поступления</a>
<?php endif; ?>