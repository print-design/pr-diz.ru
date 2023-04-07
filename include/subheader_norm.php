<?php
require_once '../calculation/calculation.php';
?>
<div class="text-nowrap nav2">
    <?php
    $sql = "select id, name from machine order by position";
    $fetcher = new Fetcher($sql);
            
    while ($row = $fetcher->Fetch()):
    $machine_id_class = (!empty($machine_id) && $row['id'] == $machine_id) ? " active" : "";
            
    $file_name = "";
    if(empty($machine_id)) {
        $file_name = "machine.php";
    }
    ?>
    <a href="<?= $file_name. BuildQueryAddRemove('machine_id', $row['id'], 'laminator_id') ?>" class="mr-4<?=$machine_id_class ?>"><?=$row['name'] ?></a>
    <?php
    endwhile;
    
    $extracharge_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/extracharge.php')) == APPLICATION.'/admin/extracharge.php' ? " active" : "";
    $ink_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/ink.php')) == APPLICATION.'/admin/ink.php' ? " active" : "";
    $cliche_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/cliche.php')) == APPLICATION.'/admin/cliche.php' ? " active" : "";
    
    $sql = "select id, name from laminator order by position";
    $fetcher = new Fetcher($sql);
    
    while($row = $fetcher->Fetch()):
    $laminator_id_class = (!empty($laminator_id) && $row['id'] == $laminator_id) ? " active" : "";
    
    $file_name = "";
    if(empty($laminator_id)) {
        $file_name = "laminator.php";
    }
    ?>
    <a href="<?=$file_name. BuildQueryAddRemove('laminator_id', $row['id'], 'machine_id') ?>" class="mr-4<?=$laminator_id_class ?>"><?=$row['name'] ?></a>
    <?php
    endwhile;
    ?>
    <a href="extracharge.php" class="mr-4<?=$extracharge_class ?>">Наценка</a>
    <a href="ink.php" class="mr-4<?=$ink_class ?>">Цена краски</a>
    <a href="cliche.php" class="mr-4<?=$cliche_class ?>">Цена форм</a>
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
    <?php if($machine_id == CalculationBase::ATLAS): ?>
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