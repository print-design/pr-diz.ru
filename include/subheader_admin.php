<?php
$user_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/user')) == APPLICATION.'/user' ? " class='active'" : "";
$supplier_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/supplier')) == APPLICATION.'/supplier' ? " class='active'" : "";
$norm_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm')) == APPLICATION.'/norm' ? " class='active'" : "";
?>
<div class="col-4">
    <a<?=$user_class ?> href="<?=APPLICATION ?>/user/">Сотрудники</a>
</div>
<div class="col-4">
    <a<?=$supplier_class ?> href="<?=APPLICATION ?>/supplier/">Поставщики</a>    
</div>
<div class="col-4">
    <a<?=$norm_class ?> href="<?=APPLICATION ?>/norm/form.php">Нормы</a>
</div>