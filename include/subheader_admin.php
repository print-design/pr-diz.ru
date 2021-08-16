<?php
$user_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/user')) == APPLICATION.'/user' ? " active" : "";
$supplier_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/supplier')) == APPLICATION.'/supplier' ? " active" : "";
$history_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/history')) == APPLICATION.'/history' ? " active" : "";
?>
<div class="text-nowrap nav2">
    <a class="mr-4<?=$user_class ?>" href="<?=APPLICATION ?>/user/">Сотрудники</a>
    <a class="mr-4<?=$supplier_class ?>" href="<?=APPLICATION ?>/supplier/">Поставщики</a>
    <a class="mr-4<?=$history_class ?>" href="<?=APPLICATION ?>/history/cut.php">История</a>
</div>