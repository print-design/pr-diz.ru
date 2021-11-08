<?php
if(empty(filter_input(INPUT_GET, 'machine_id'))) {
    header('Location: '.APPLICATION);
}
?>
<hr class="pb-0 mb-0" />
<div class="d-flex justify-content-start">
    <div class="p-1">
        <div class="text-nowrap nav2">
            <?php
            $sql = "select id, name from machine";
            $fetcher = new Fetcher($sql);
            
            while ($row = $fetcher->Fetch()):
            $machine_id_class = $row['id'] == filter_input(INPUT_GET, 'machine_id') ? " active" : "";
            ?>
            <a href="<?= BuildQuery('machine_id', $row['id']) ?>" class="mr-4<?=$machine_id_class ?>"><?=$row['name'] ?></a>
            <?php
            endwhile;
            
            $currency_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/currency.php')) == APPLICATION.'/admin/currency.php' ? " active" : "";
            $extracharge_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/extracharge.php')) == APPLICATION.'/admin/extracharge.php' ? " active" : "";
            $paint_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/paint.php')) == APPLICATION.'/admin/paint.php' ? " active" : "";
            ?>
            <a href="currency.php" class="mr-4<?=$currency_class ?>">Курсы валют</a>
            <a href="extracharge.php" class="mr-4<?=$extracharge_class ?>">Наценка</a>
            <a href="paint.php" class="mr-4<?=$paint_class ?>">Стоимость краски</a>
        </div>
    </div>
</div>
<?php
$colorfulness_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/characteristics.php')) == APPLICATION.'/admin/characteristics.php' ? " active" : "";
$form_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/form.php')) == APPLICATION.'/admin/form.php' ? " active" : "";
$glue_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/glue.php')) == APPLICATION.'/admin/glue.php' ? " active" : "";
$machine_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/machine.php')) == APPLICATION.'/admin/machine.php' ? " active" : "";
$fitting_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/fitting.php')) == APPLICATION.'/admin/fitting.php' ? " active" : "";
$raport_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/raport.php')) == APPLICATION.'/admin/raport.php' ? " active" : "";
$roller_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/roller.php')) == APPLICATION.'/admin/roller.php' ? " active" : "";
?>
<hr class="pb-0 mb-0" />
<div class="d-flex justify-content-start">
    <div class="p-1">
        <div class="text-nowrap nav2">
            <?php
            $machine_id = filter_input(INPUT_GET, 'machine_id');
            if($machine_id != MACHINE_LAMINATOR):
            ?>
            <a href="<?=APPLICATION ?>/admin/characteristics.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$colorfulness_class ?>">Характеристики</a>
            <a href="<?=APPLICATION ?>/admin/form.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$form_class ?>">Стоимость форм</a>
            
            <?php
            endif;
            if($machine_id == MACHINE_LAMINATOR):
            ?>
            <a href="<?=APPLICATION ?>/admin/glue.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$glue_class ?>">Стоимость клея</a>
            <?php
            endif;
            ?>
            <a href="<?=APPLICATION ?>/admin/machine.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$machine_class ?>">Нормы работы оборудования</a>
            <a href="<?=APPLICATION ?>/admin/fitting.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$fitting_class ?>">Приладка</a>
            <?php
            if($machine_id != MACHINE_LAMINATOR):
            ?>
            <a href="<?=APPLICATION ?>/admin/raport.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$raport_class ?>">Рапорт</a>
            <?php
            endif;
            if($machine_id == MACHINE_LAMINATOR):
            ?>
            <a href="<?=APPLICATION ?>/admin/roller.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$roller_class ?>">Ширина вала</a>
            <?php
            endif;
            ?>
        </div>
    </div>
</div>