<?php
$form_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/form.php')) == APPLICATION.'/norm/form.php' ? " class='active'" : "";
$paint_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/paint.php')) == APPLICATION.'/norm/paint.php' ? " class='active'" : "";
$glue_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/glue.php')) == APPLICATION.'/norm/glue.php' ? " class='active'" : "";
$machine_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/machine.php')) == APPLICATION.'/norm/machine.php' ? " class='active'" : "";
$fitting_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/fitting.php')) == APPLICATION.'/norm/fitting.php' ? " class='active'" : "";
?>
<hr />
<div class="d-flex justify-content-start nav2">
    <div class="p-1 row">
        <div class="col-2 text-nowrap">
            <a<?=$form_class ?> href="<?=APPLICATION ?>/norm/form.php">Стоимость форм</a>
        </div>
        <div class="col-2 text-nowrap">
            <a<?=$paint_class ?> href="<?=APPLICATION ?>/norm/paint.php">Стоимость краски</a>
        </div>
        <div class="col-2 text-nowrap">
            <a<?=$glue_class ?> href="<?=APPLICATION ?>/norm/glue.php">Стоимость клея</a>
        </div>
        <div class="col-2 text-nowrap">
            <a<?=$machine_class ?> href="<?=APPLICATION ?>/norm/machine.php">Нормы работы оборудования</a>
        </div>
        <div class="col-2 text-nowrap">
            <a<?=$fitting_class ?> href="<?=APPLICATION ?>/norm/fitting.php">Приладка</a>
        </div>
    </div>
</div>