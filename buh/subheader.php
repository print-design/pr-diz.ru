<a href="<?= APPLICATION ?>/order/details.php?<?= http_build_query($_GET) ?>" class="mr-4">Расчёт</a>
<?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))): ?>
<a href="<?= APPLICATION ?>/order/techmap.php?<?= http_build_query($_GET) ?>" class="mr-4">Тех. карта</a>
<?php endif; ?>
<?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD], ROLE_NAMES[ROLE_STOREKEEPER])) && in_array($calculation->status_id, ORDER_STATUSES_IN_CUT)): ?>
<a href="<?= APPLICATION ?>/order/cut.php?<?= http_build_query($_GET) ?>" class="mr-4">Результаты</a>
<?php endif; ?>
<?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER])) && $has_techmap): ?>
<a href="income.php?<?= http_build_query($_GET) ?>" class="mr-4 active">Поступления</a>
<?php endif; ?>