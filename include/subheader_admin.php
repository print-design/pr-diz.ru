<?php
$user_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/user')) == APPLICATION.'/user' ? " active" : "";
$supplier_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/supplier')) == APPLICATION.'/supplier' ? " active" : "";
$price_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/price')) == APPLICATION.'/price' ? " active" : "";
$norm_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin')) == APPLICATION.'/admin' ? " active" : "";
?>
<div class="text-nowrap nav2">
    <a class="mr-4<?=$user_class ?>" href="<?=APPLICATION ?>/user/">Сотрудники</a>
    <a class="mr-4<?=$supplier_class ?>" href="<?=APPLICATION ?>/supplier/">Поставщики</a>
    <a class="mr-4<?=$price_class ?>" href="<?=APPLICATION ?>/price/">Цены</a>
    <a class="mr-4<?=$norm_class ?>" href="<?=APPLICATION ?>/admin/characteristics.php<?= BuildQuery('machine_id', 1) ?>">Нормы</a>
</div>