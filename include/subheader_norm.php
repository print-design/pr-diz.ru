<?php
$form_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/form.php')) == APPLICATION.'/admin/form.php' ? " active" : "";
$paint_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/paint.php')) == APPLICATION.'/admin/paint.php' ? " active" : "";
$glue_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/glue.php')) == APPLICATION.'/admin/glue.php' ? " active" : "";
$machine_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/machine.php')) == APPLICATION.'/admin/machine.php' ? " active" : "";
$fitting_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/fitting.php')) == APPLICATION.'/admin/fitting.php' ? " active" : "";
$raport_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/raport.php')) == APPLICATION.'/admin/raport.php' ? " active" : "";
$extracharge_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/extracharge.php')) == APPLICATION.'/admin/extracharge.php' ? " active" : "";
?>
<hr class="pb-0 mb-0" />
<div class="d-flex justify-content-start">
    <div class="p-1">
        <div class="text-nowrap nav2">
            <a href="<?=APPLICATION ?>/admin/form.php" class="mr-4<?=$form_class ?>">Стоимость форм</a>
            <a href="<?=APPLICATION ?>/admin/paint.php" class="mr-4<?=$paint_class ?>">Стоимость краски</a>
            <a href="<?=APPLICATION ?>/admin/glue.php" class="mr-4<?=$glue_class ?>">Стоимость клея</a>
            <a href="<?=APPLICATION ?>/admin/machine.php" class="mr-4<?=$machine_class ?>">Нормы работы оборудования</a>
            <a href="<?=APPLICATION ?>/admin/fitting.php" class="mr-4<?=$fitting_class ?>">Приладка</a>
            <a href="<?=APPLICATION ?>/admin/raport.php" class="mr-4<?=$raport_class ?>">Рапорт</a>
            <a href="<?=APPLICATION ?>/admin/extracharge.php" class="mr-4<?=$extracharge_class ?>">Наценка</a>
        </div>
    </div>
</div>