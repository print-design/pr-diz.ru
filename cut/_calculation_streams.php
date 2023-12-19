<?php
require_once '../include/topscripts.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
if(empty($calculation_id)) {
    $calculation_id = $id;
}
$sql = "select cs.id stream_id, cs.calculation_id, cs.name, cs.weight, cs.length, cs.radius, cs.printed, c.stream_width, tm.spool, "
        . "c.individual_density, fv1.weight density1, c.lamination1_individual_density, fv2.weight density2, c.lamination2_individual_density, fv3.weight density3 "
        . "from calculation_stream cs "
        . "inner join calculation c on cs.calculation_id = c.id "
        . "inner join techmap tm on tm.calculation_id = c.id "
        . "left join film_variation fv1 on c.film_variation_id = fv1.id "
        . "left join film_variation fv2 on c.lamination1_film_variation_id = fv2.id "
        . "left join film_variation fv3 on c.lamination2_film_variation_id = fv3.id "
        . "where cs.calculation_id = $calculation_id "
        . "order by cs.position";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
    $id = $row['calculation_id'];
    $stream_id = $row['stream_id'];
    $stream_weight = $row['weight'];
    $stream_length = $row['length'];
    $stream_radius = $row['radius'];
    $printed = $row['printed'];
    $stream_width = $row['stream_width'];
    $spool = $row['spool'];
    
    $density1 = $row['individual_density'];
    if(empty($density1)) {
        $density1 = $row['density1'];
    }
    if(empty($density1)) {
        $density1 = 0;
    }
    
    $density2 = $row['lamination1_individual_density'];
    if(empty($density2)) {
        $density2 = $row['density2'];
    }
    if(empty($density2)) {
        $density2 = 0;
    }
    
    $density3 = $row['lamination2_individual_density'];
    if(empty($density3)) {
        $density3 = $row['density3'];
    }
    if(empty($density3)) {
        $density3 = 0;
    }
    
    if(null !== filter_input(INPUT_POST, 'stream_print_submit') && $stream_id == filter_input(INPUT_POST, 'stream_id')) {
        $stream_weight = filter_input(INPUT_POST, 'weight');
        $stream_length = filter_input(INPUT_POST, 'length');
        $stream_radius = filter_input(INPUT_POST, 'radius');
    }
?>
<div class="calculation_stream" data-id="<?=$stream_id ?>" ondragover="DragOver(event);" ondrop="Drop(event);">
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex justify-content-sm-start">
            <div class="mr-3" draggable="true" data-id="<?=$stream_id ?>" ondragstart="DragStart(event);" ondragend="DragEnd();">
                <img src="../images/icons/double-vertical-dots.svg" draggable="false" />
            </div>
            <div class="font-weight-bold"><?=$row['name'] ?></div>
        </div>
        <?php if(!empty($printed)): ?>
        <div style="background-color: #0A9D4E0D; padding-left: 5px; padding-right: 5px; border-radius: 8px;"><span style="font-size: x-small; vertical-align: middle; color: #0A9D4E;">&#9679;</span>&nbsp;&nbsp;&nbsp;Распечатано <?= DateTime::createFromFormat('Y-m-d H:i:s', $printed)->format('d.m.Y H:i:s') ?></div>
        <?php endif; ?>
        <?php if(isset($invalid_stream) && $invalid_stream == $stream_id): ?>
        <div style="background-color: mistyrose; padding-left: 5px; padding-right: 5px; border-radius: 8px;"><span style="font-size: x-small; vertical-align: middle; color: red;">&#9679;</span>&nbsp;&nbsp;&nbsp;Невалидные данные</div>
        <?php endif; ?>
    </div>
    <form method="post" action="<?=APPLICATION ?>/cut/take.php?id=<?=$calculation_id ?>&machine_id=<?=$machine_id ?>">
        <input type="hidden" name="id" value="<?=$calculation_id ?>" />
        <input type="hidden" name="machine_id" value="<?= $machine_id ?>" />
        <input type="hidden" name="stream_id" value="<?=$stream_id ?>" />
        <input type="hidden" name="stream_width" value="<?=$stream_width ?>" />
        <input type="hidden" name="density1" value="<?=$density1 ?>" />
        <input type="hidden" name="density2" value="<?=$density2 ?>" />
        <input type="hidden" name="density3" value="<?=$density3 ?>" />
        <input type="hidden" name="spool" value="<?=$spool ?>" />
        <input type="hidden" name="scroll" />
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label for="weight">Масса катушки</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="weight" value="<?=$stream_weight ?>" required="required" onkeydown="return KeyDownFloatValue(event);" onkeyup="KeyUpFloatValue(event);" onchange="ChangeFloatValue(event);" />
                        <div class="input-group-append">
                            <span class="input-group-text">кг</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="length">Метраж</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="length" value="<?=$stream_length ?>" required="required" onkeydown="return KeyDownFloatValue(event);" onkeyup="KeyUpFloatValue(event);" onchange="ChangeFloatValue(event);" />
                        <div class="input-group-append">
                            <span class="input-group-text">м</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="radius">Радиус от вала</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="radius" value="<?=$stream_radius ?>" required="required" onkeydown="return KeyDownFloatValue(event);" onkeyup="KeyUpFloatValue(event);" onchange="ChangeFloatValue(event);" />
                        <div class="input-group-append">
                            <span class="input-group-text">мм</span>
                        </div>
                    </div>
                </div>
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
<?php endwhile; ?>