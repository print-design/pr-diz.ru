<?php
$user_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/user')) == APPLICATION.'/user' ? " active" : "";
$supplier_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/supplier')) == APPLICATION.'/supplier' ? " active" : "";
$norm_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm')) == APPLICATION.'/norm' ? " active" : "";
$extracharge_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/extracharge')) == APPLICATION.'/extracharge' ? " active" : "";
?>
<div class="text-nowrap nav2">
    <a class="mr-4<?=$user_class ?>" href="<?=APPLICATION ?>/user/">Сотрудники</a>
    <a class="mr-4<?=$supplier_class ?>" href="<?=APPLICATION ?>/supplier/">Поставщики</a>    
    <a class="mr-4<?=$norm_class ?>" href="<?=APPLICATION ?>/norm/form.php">Нормы</a>
    <a class="mr-4<?=$extracharge_class ?>" href="<?=APPLICATION ?>/extracharge">Наценка</a>
</div>