<div class="text-nowrap nav2">
    <?php
    $machines_class = empty($machine_id) ? "" : " active";
    $laminators_class = empty($laminator_id) ? "" : " active";
    $cutters_class = empty($cutter_id) ? "" : " active";
    $raw_class = (substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/extracharge.php')) == APPLICATION.'/admin/extracharge.php' || 
            substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/ink.php')) == APPLICATION.'/admin/ink.php' || 
            substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/cliche.php')) == APPLICATION.'/admin/cliche.php') ? " active" : "";
    ?>
    <a href="<?=APPLICATION ?>/admin/machine.php?machine_id=<?= PRINTER_COMIFLEX ?>" class="mr-4<?=$machines_class ?>">Печатные машины</a>
    <a href="<?=APPLICATION ?>/admin/laminator.php?laminator_id=<?= LAMINATOR_SOLVENT ?>" class="mr-4<?=$laminators_class ?>">Ламинаторы</a>
    <a href="<?=APPLICATION ?>/admin/cutter.php?cutter_id=<?= CUTTER_1 ?>" class="mr-4<?=$cutters_class ?>">Резки</a>
    <a href="<?=APPLICATION ?>/admin/extracharge.php" class="mr-4<?=$raw_class ?>">Сырье</a>
</div>
<hr class="pb-0 mb-0" />
<div class="text-nowrap nav2">
    <?php
    if(!empty($machines_class)):
    foreach (PRINTERS as $printer):
    $machine_id_class = (!empty($machine_id) && $printer == $machine_id) ? " active" : "";
            
    $file_name = "";
    if(empty($machine_id)) {
        $file_name = "machine.php";
    }
    ?>
    <a href="<?= $file_name. BuildQueryAddRemove('machine_id', $printer, 'laminator_id') ?>" class="mr-4<?=$machine_id_class ?>"><?=PRINTER_NAMES[$printer] ?></a>
    <?php
    endforeach;
    endif;
    
    if(!empty($laminators_class)):
    foreach(LAMINATOR_NAMES as $key => $value):
    $laminator_id_class = (!empty($laminator_id) && $key == $laminator_id) ? " active" : "";
    
    $file_name = "";
    if(empty($laminator_id)) {
        $file_name = "laminator.php";
    }
    ?>
    <a href="<?=$file_name. BuildQueryAddRemove('laminator_id', $key, 'machine_id') ?>" class="mr-4<?=$laminator_id_class ?>"><?=$value ?></a>
    <?php
    endforeach;
    endif;
    
    if(!empty($cutters_class)):
    foreach(CUTTER_NAMES as $key => $value):
    $cutter_id_class = (!empty($cutter_id) && $key == $cutter_id) ? " active" : "";
    ?>
    <a href="cutter.php?cutter_id=<?=$key ?>" class="mr-4<?=$cutter_id_class ?>"><?=$value ?></a>
    <?php
    endforeach;
    endif;
    
    if(!empty($raw_class)):
    $extracharge_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/extracharge.php')) == APPLICATION.'/admin/extracharge.php' ? " active" : "";
    $ink_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/ink.php')) == APPLICATION.'/admin/ink.php' ? " active" : "";
    $cliche_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/cliche.php')) == APPLICATION.'/admin/cliche.php' ? " active" : "";
    ?>
    <a href="extracharge.php" class="mr-4<?=$extracharge_class ?>">Наценка</a>
    <a href="ink.php" class="mr-4<?=$ink_class ?>">Цена краски</a>
    <a href="cliche.php" class="mr-4<?=$cliche_class ?>">Цена форм</a>
    <?php endif; ?>
</div>
<?php
if(!empty($machine_id)):
$machine_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/machine.php')) == APPLICATION.'/admin/machine.php' ? " active" : "";
$priladka_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/priladka.php')) == APPLICATION.'/admin/priladka.php' ? " active" : "";
$raport_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/raport.php')) == APPLICATION.'/admin/raport.php' ? " active" : "";
$gap_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/gap.php')) == APPLICATION.'/admin/gap.php' ? " active" : "";
?>
<hr class="pb-0 mb-0" />
<div class="text-nowrap nav2">
    <a href="<?=APPLICATION ?>/admin/machine.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$machine_class ?>">Нормы работы оборудования</a>
    <a href="<?=APPLICATION ?>/admin/priladka.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$priladka_class ?>">Приладка</a>
    <a href="<?=APPLICATION ?>/admin/raport.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$raport_class ?>">Рапорт</a>
    <?php if($machine_id == PRINTER_ATLAS): ?>
    <a href="<?=APPLICATION ?>/admin/gap.php<?= BuildQuery('machine_id', $machine_id) ?>" class="mr-4<?=$gap_class ?>">Зазор</a>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php
if(!empty($laminator_id)):
$laminator_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator.php')) == APPLICATION.'/admin/laminator.php' ? " active" : "";
$laminator_priladka_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator_priladka.php')) == APPLICATION.'/admin/laminator_priladka.php' ? " active" : "";
$laminator_roller_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/laminator_roller.php')) == APPLICATION.'/admin/laminator_roller.php' ? " active" : "";
$glue_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/glue.php')) == APPLICATION.'/admin/glue.php' ? " active" : "";
?>
<hr class="pb-0 mb-0" />
<div class="text-nowrap nav2">
    <a href="<?=APPLICATION ?>/admin/laminator.php<?= BuildQuery('laminator_id', $laminator_id) ?>" class="mr-4<?=$laminator_class ?>">Нормы работы оборудования</a>
    <a href="<?=APPLICATION ?>/admin/laminator_priladka.php<?= BuildQuery('laminator_id', $laminator_id) ?>" class="mr-4<?=$laminator_priladka_class ?>">Приладка</a>
    <a href="<?=APPLICATION ?>/admin/laminator_roller.php<?= BuildQuery('laminator_id', $laminator_id) ?>" class="mr-4<?=$laminator_roller_class ?>">Ширина вала</a>
    <a href="<?=APPLICATION ?>/admin/glue.php<?= BuildQuery('laminator_id', $laminator_id) ?>" class="mr-4<?=$glue_class ?>">Цена клея</a>
</div>
<?php endif; ?>
<hr />