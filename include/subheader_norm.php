<?php
$form_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/form.php')) == APPLICATION.'/norm/form.php' ? " active" : "";
$paint_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/paint.php')) == APPLICATION.'/norm/paint.php' ? " active" : "";
$glue_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/glue.php')) == APPLICATION.'/norm/glue.php' ? " active" : "";
$machine_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/machine.php')) == APPLICATION.'/norm/machine.php' ? " active" : "";
$fitting_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/fitting.php')) == APPLICATION.'/norm/fitting.php' ? " active" : "";
$raport_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/norm/raport.php')) == APPLICATION.'/norm/raport.php' ? " active" : "";
?>
<hr class="pb-0 mb-0" />
<div class="d-flex justify-content-start">
    <div class="p-1">
        <div class="text-nowrap nav2">
            <a href="<?=APPLICATION ?>/norm/form.php" class="mr-4<?=$form_class ?>">Стоимость форм</a>
            <a href="<?=APPLICATION ?>/norm/paint.php" class="mr-4<?=$paint_class ?>">Стоимость краски</a>
            <a href="<?=APPLICATION ?>/norm/glue.php" class="mr-4<?=$glue_class ?>">Стоимость клея</a>
            <a href="<?=APPLICATION ?>/norm/machine.php" class="mr-4<?=$machine_class ?>">Нормы работы оборудования</a>
            <a href="<?=APPLICATION ?>/norm/fitting.php" class="mr-4<?=$fitting_class ?>">Приладка</a>
            <a href="<?=APPLICATION ?>/norm/raport.php" class="mr-4<?=$raport_class ?>">Рапорт</a>
        </div>
    </div>
</div>