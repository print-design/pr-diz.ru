<?php
$user_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/user')) == APPLICATION.'/user' ? " class='active'" : "";
$supplier_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/supplier')) == APPLICATION.'/supplier' ? " class='active'" : "";
?>
<div class="col-6">
    <a<?=$user_class ?> href="<?=APPLICATION ?>/user/">Сотрудники</a>
</div>
<div class="col-6">
    <a<?=$supplier_class ?> href="<?=APPLICATION ?>/supplier/">Поставщики</a>    
</div>