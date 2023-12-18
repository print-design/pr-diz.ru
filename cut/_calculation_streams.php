<?php
require_once '../include/topscripts.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
if(empty($calculation_id)) {
    $calculation_id = $id;
}
$sql = "select id, name, weight, length, radius, printed from calculation_stream where calculation_id = $calculation_id order by position";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
    $stream_weight = $row['weight'];
    $stream_length = $row['length'];
    $stream_radius = $row['radius'];
    
    if(null !== filter_input(INPUT_POST, 'stream_print_submit') && $row['id'] == filter_input(INPUT_POST, 'stream_id')) {
        $stream_weight = filter_input(INPUT_POST, 'weight');
        $stream_length = filter_input(INPUT_POST, 'length');
        $stream_radius = filter_input(INPUT_POST, 'radius');
    }
?>
<div class="calculation_stream" data-id="<?=$row['id'] ?>" ondragover="DragOver(event);" ondrop="Drop(event);">
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex justify-content-sm-start">
            <div class="mr-3" draggable="true" data-id="<?=$row['id'] ?>" ondragstart="DragStart(event);" ondragend="DragEnd();">
                <img src="../images/icons/double-vertical-dots.svg" draggable="false" />
            </div>
            <div class="font-weight-bold"><?=$row['name'] ?></div>
        </div>
        <?php if(!empty($row['printed'])): ?>
        <div style="background-color: #0A9D4E0D; padding-left: 5px; padding-right: 5px; border-radius: 8px;"><span style="font-size: x-small; vertical-align: middle; color: #0A9D4E;">&#9679;</span>&nbsp;&nbsp;&nbsp;Распечатано<?=$row['printed'] ?></div>
        <?php endif; ?>
        <?php if(isset($invalid_stream) && $invalid_stream == $row['id']): ?>
        <div style="background-color: mistyrose; padding-left: 5px; padding-right: 5px; border-radius: 8px;"><span style="font-size: x-small; vertical-align: middle; color: red;">&#9679;</span>&nbsp;&nbsp;&nbsp;Не валидные данные</div>
        <?php endif; ?>
    </div>
    <form method="post">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="machine_id" value="<?= filter_input(INPUT_GET, 'machine_id') ?>" />
        <input type="hidden" name="stream_id" value="<?=$row['id'] ?>" />
        <input type="hidden" name="scroll" />
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label for="weight">Масса катушки</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="weight" value="<?=$stream_weight ?>" required="required" onkeydown="return KeyDownIntValue(event);" onkeyup="KeyUpIntValue(event);" onchange="ChangeIntValue(event);" />
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
                        <input type="text" class="form-control" name="length" value="<?=$stream_length ?>" required="required" onkeydown="return KeyDownIntValue(event);" onkeyup="KeyUpIntValue(event);" onchange="ChangeIntValue(event);" />
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
                        <input type="text" class="form-control" name="radius" value="<?=$stream_radius ?>" required="required" onkeydown="return KeyDownIntValue(event);" onkeyup="KeyUpIntValue(event);" onchange="ChangeIntValue(event);" />
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