<?php
require_once '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');

if(empty($take_id)) {
    $take_id = filter_input(INPUT_GET, 'take_id');
}

// Ручьи данного съёма
$sql = "select ct.id take_id, cs.calculation_id, cs.id stream_id, cs.name, cs.width stream_width, cts.weight, cts.length, cts.radius, cts.printed, tm.spool, "
        . "c.individual_thickness, fv1.thickness thickness1, c.lamination1_individual_thickness, fv2.thickness thickness2, c.lamination2_individual_thickness, fv3.thickness thickness3, "
        . "c.individual_density, fv1.weight density1, c.lamination1_individual_density, fv2.weight density2, c.lamination2_individual_density, fv3.weight density3 "
        . "from calculation_take ct "
        . "inner join calculation c on ct.calculation_id = c.id "
        . "inner join calculation_stream cs on cs.calculation_id = c.id "
        . "inner join techmap tm on tm.calculation_id = c.id "
        . "left join film_variation fv1 on c.film_variation_id = fv1.id "
        . "left join film_variation fv2 on c.lamination1_film_variation_id = fv2.id "
        . "left join film_variation fv3 on c.lamination2_film_variation_id = fv3.id "
        . "left join calculation_take_stream cts on cts.calculation_take_id = ct.id and calculation_stream_id = cs.id "
        . "where ct.id = $take_id "
        . "order by cs.position";
$grabber = new Grabber($sql);
$streams = $grabber->result;
$is_first = false;

if(count($streams) > 0) {
    $is_first = true;
}

foreach($streams as $row):
    $take_id = $row['take_id'];
    $calculation_id = $row['calculation_id'];
    $stream_id = $row['stream_id'];
    $stream_name = $row['name'];
    $stream_weight = $row['weight'];
    $stream_length = $row['length'];
    $stream_radius = $row['radius'];
    $stream_printed = $row['printed'];
    $stream_width = $row['stream_width'];
    $spool = $row['spool'];
    
    if(null !== filter_input(INPUT_POST, 'stream_print_submit') && $stream_id == filter_input(INPUT_POST, 'stream_id')) {
        $stream_weight = filter_input(INPUT_POST, 'weight');
        $stream_length = filter_input(INPUT_POST, 'length');
        $stream_radius = filter_input(INPUT_POST, 'radius');
    }
    
    $thickness1 = $row['individual_thickness'];
    if(empty($thickness1)) {
        $thickness1 = $row['thickness1'];
    }
    if(empty($thickness1)) {
        $thickness1 = 0;
    }
    
    $density1 = $row['individual_density'];
    if(empty($density1)) {
        $density1 = $row['density1'];
    }
    if(empty($density1)) {
        $density1 = 0;
    }
    
    $thickness2 = $row['lamination1_individual_thickness'];
    if(empty($thickness2)) {
        $thickness2 = $row['thickness2'];
    }
    if(empty($thickness2)) {
        $thickness2 = 0;
    }
    
    $density2 = $row['lamination1_individual_density'];
    if(empty($density2)) {
        $density2 = $row['density2'];
    }
    if(empty($density2)) {
        $density2 = 0;
    }
    
    $thickness3 = $row['lamination2_individual_thickness'];
    if(empty($thickness3)) {
        $thickness3 = $row['thickness3'];
    }
    if(empty($thickness3)) {
        $thickness3 = 0;
    }
    
    $density3 = $row['lamination2_individual_density'];
    if(empty($density3)) {
        $density3 = $row['density3'];
    }
    if(empty($density3)) {
        $density3 = 0;
    }
    
    $length_class = "not_first_length";
    $radius_class = "not_first_radius";
    
    if($is_first) {
        $length_class = "first_length";
        $radius_class = "first_radius";
    }
?>
<div class="calculation_stream" data-id="<?=$stream_id ?>" ondragover="DragOver(event);" ondrop="Drop(event);" style="border-radius: 10px; margin-bottom: 3px; padding-top: 5px; padding-bottom: 5px;">
    <div class="d-flex justify-content-between mb-1">
        <div class="d-flex justify-content-sm-start">
            <div class="mr-3" draggable="true" data-id="<?=$stream_id ?>" ondragstart="DragStart(event);" ondragend="DragEnd();">
                <img src="../images/icons/double-vertical-dots.svg" draggable="false" />
            </div>
            <div class="font-weight-bold"><?=$stream_name.' '.$stream_width ?> мм</div>
        </div>
        <?php if(!empty($stream_printed)): ?>
        <div style="background-color: #0A9D4E0D; padding-left: 5px; padding-right: 5px; border-radius: 8px;"><span style="font-size: x-small; vertical-align: middle; color: #0A9D4E;">&#9679;</span>&nbsp;&nbsp;&nbsp;Распечатано <?= DateTime::createFromFormat('Y-m-d H:i:s', $stream_printed)->format('d.m.Y H:i') ?></div>
        <?php endif; ?>
        <?php if(isset($invalid_stream) && $invalid_stream == $stream_id): ?>
        <div style="background-color: mistyrose; padding-left: 5px; padding-right: 5px; border-radius: 8px;"><span style="font-size: x-small; vertical-align: middle; color: red;">&#9679;</span>&nbsp;&nbsp;&nbsp;Невалидные данные</div>
        <?php endif; ?>
    </div>
    <form method="post" action="<?=APPLICATION ?>/cut/take.php?id=<?=$calculation_id ?>&machine_id=<?=$machine_id ?>">
        <input type="hidden" name="take_id" value="<?=$take_id ?>" />
        <input type="hidden" name="calculation_id" value="<?=$calculation_id ?>" />
        <input type="hidden" name="machine_id" value="<?= $machine_id ?>" />
        <input type="hidden" name="stream_id" value="<?=$stream_id ?>" />
        <input type="hidden" name="stream_width" value="<?=$stream_width ?>" />
        <input type="hidden" name="spool" value="<?=$spool ?>" />
        <input type="hidden" name="thickness1" value="<?=$thickness1 ?>" />
        <input type="hidden" name="thickness2" value="<?=$thickness2 ?>" />
        <input type="hidden" name="thickness3" value="<?=$thickness3 ?>" />
        <input type="hidden" name="density1" value="<?=$density1 ?>" />
        <input type="hidden" name="density2" value="<?=$density2 ?>" />
        <input type="hidden" name="density3" value="<?=$density3 ?>" />
        <input type="hidden" name="scroll" />
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label for="weight">Масса катушки</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="weight" value="<?=$stream_weight ?>" required="required" autocomplete="off" onkeydown="return KeyDownFloatValue(event);" onkeyup="KeyUpFloatValue(event);" onchange="ChangeFloatValue(event);" />
                        <div class="input-group-append">
                            <span class="input-group-text">кг</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="length">Метраж</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control <?=$length_class ?>" name="length" value="<?=$stream_length ?>" required="required" autocomplete="off" onkeydown="return KeyDownFloatValue(event);" onkeyup="LengthFill(event); KeyUpFloatValue(event);" onchange="LengthFill(event); ChangeFloatValue(event);" />
                        <div class="input-group-append">
                            <span class="input-group-text">м</span>
                        </div>
                    </div>
                </div>
                <?php if($is_first): ?>
                <div class="form-check">
                    <label class="form-check-label" style="line-height: 25px;">
                        <input type="checkbox" checked='checked' class="form-check-input length_checkbox" onchange="LengthCheck(event);" />Метраж одинаковый
                    </label>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="radius">Радиус от вала</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control <?=$radius_class ?>" name="radius" value="<?=$stream_radius ?>" required="required" autocomplete="off" onkeydown="return KeyDownFloatValue(event);" onkeyup="RadiusFill(event); KeyUpFloatValue(event);" onchange="RadiusFill(event); ChangeFloatValue(event);" />
                        <div class="input-group-append">
                            <span class="input-group-text">мм</span>
                        </div>
                    </div>
                </div>
                <?php if($is_first): ?>
                <div class="form-check">
                    <label class="form-check-label" style="line-height: 25px;">
                        <input type="checkbox" checked='checked' class="form-check-input radius_checkbox" onchange="RadiusCheck(event);" />Радиус одинаковый
                    </label>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="stream_print_submit">&nbsp;</label>
                    <button type="submit" class="btn btn-light w-100" name="stream_print_submit"><img src="../images/icons/print.svg" class="mr-2" />Распечатать бирку</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
if($is_first) {
    $is_first = false;
}

endforeach;
?>