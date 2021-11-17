<hr class="pb-0 mb-0" />
<div class="d-flex justify-content-start">
    <div class="p-1">
        <div class="text-nowrap nav2">
            <?php
            $sql = "select id, name from machine";
            $fetcher = new Fetcher($sql);
            
            while ($row = $fetcher->Fetch()):
            $machine_id_class = (!empty($machine_id) && $row['id'] == $machine_id) ? " active" : "";
            
            $file_name = "";
            if(empty($machine_id)) {
                $file_name = "machine.php";
            }
            ?>
            <a href="<?= $file_name.BuildQuery('machine_id', $row['id']) ?>" class="mr-4<?=$machine_id_class ?>"><?=$row['name'] ?></a>
            <?php
            endwhile;
            
            $laminator_class = 
                    (substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator.php')) == APPLICATION.'/admin/laminator.php' || 
                    substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator_tuning.php')) == APPLICATION.'/admin/laminator_tuning.php' || 
                    substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator_roller.php')) == APPLICATION.'/admin/laminator_roller.php') ? 
                    " active" : "";
            $currency_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/currency.php')) == APPLICATION.'/admin/currency.php' ? " active" : "";
            $extracharge_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/extracharge.php')) == APPLICATION.'/admin/extracharge.php' ? " active" : "";
            $ink_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/paint.php')) == APPLICATION.'/admin/paint.php' ? " active" : "";
            $glue_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/glue.php')) == APPLICATION.'/admin/glue.php' ? " active" : "";
            $form_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/form.php')) == APPLICATION.'/admin/form.php' ? " active" : "";
            ?>
            <a href="laminator.php" class="mr-4<?=$laminator_class ?>">Ламинатор</a>
            <a href="currency.php" class="mr-4<?=$currency_class ?>">Курсы валют</a>
            <a href="extracharge.php" class="mr-4<?=$extracharge_class ?>">Наценка</a>
            <a href="paint.php" class="mr-4<?=$ink_class ?>">Стоимость краски</a>
            <a href="glue.php" class="mr-4<?=$glue_class ?>">Стоимость клея</a>
            <a href="form.php" class="mr-4<?=$form_class ?>">Стоимость форм</a>
        </div>
    </div>
</div>
<?php
if(!empty($machine_id)):
$machine_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/machine.php')) == APPLICATION.'/admin/machine.php' ? " active" : "";
$tuning_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/tuning.php')) == APPLICATION.'/admin/tuning.php' ? " active" : "";
$raport_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/raport.php')) == APPLICATION.'/admin/raport.php' ? " active" : "";
?>
<hr class="pb-0 mb-0" />
<div class="d-flex justify-content-start">
    <div class="p-1">
        <div class="text-nowrap nav2">
            <a href="<?=APPLICATION ?>/admin/machine.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$machine_class ?>">Нормы работы оборудования</a>
            <a href="<?=APPLICATION ?>/admin/tuning.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$tuning_class ?>">Приладка</a>
            <a href="<?=APPLICATION ?>/admin/raport.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$raport_class ?>">Рапорт</a>
        </div>
    </div>
</div>
<?php endif; ?>
<?php
if(substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator.php')) == APPLICATION.'/admin/laminator.php' ||
        substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator_tuning.php')) == APPLICATION.'/admin/laminator_tuning.php' ||
        substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator_roller.php')) == APPLICATION.'/admin/laminator_roller.php'):
$laminator_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator.php')) == APPLICATION.'/admin/laminator.php' ? " active" : "";
$laminator_tuning_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator_tuning.php')) == APPLICATION.'/admin/laminator_tuning.php' ? " active" : "";
$laminator_roller_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator_roller.php')) == APPLICATION.'/admin/laminator_roller.php' ? " active" : "";
?>
<hr class="pb-0 mb-0" />
<div class="d-flex justify-content-start">
    <div class="p-1">
        <div class="text-nowrap nav2">
            <a href="<?=APPLICATION ?>/admin/laminator.php" class="mr-4<?=$laminator_class ?>">Нормы работы оборудования</a>
            <a href="<?=APPLICATION ?>/admin/laminator_tuning.php" class="mr-4<?=$laminator_tuning_class ?>">Приладка</a>
            <a href="<?=APPLICATION ?>/admin/laminator_roller.php" class="mr-4<?=$laminator_roller_class ?>">Ширина вала</a>
        </div>
    </div>
</div>
<?php endif; ?>
