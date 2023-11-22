<?php
require_once '../include/topscripts.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');
if(empty($calculation_id)) {
    $calculation_id = $id;
}
$sql = "select id, name from calculation_stream where calculation_id = $calculation_id order by position";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
?>
<div class="calculation_stream" data-id="<?=$row['id'] ?>" ondragover="DragOver(event);" ondragleave="DragLeave(event);" ondrop="Drop(event);">
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex justify-content-sm-start">
            <div class="mr-3" draggable="true" data-id="<?=$row['id'] ?>" ondragstart="DragStart(event);">
                <img src="../images/icons/double-vertical-dots.svg" draggable="false" />
            </div>
            <div class="font-weight-bold"><?=$row['name'] ?></div>
        </div>
        <div style="background-color: #0A9D4E0D; padding-left: 5px; padding-right: 5px; border-radius: 8px;"><span style="font-size: x-small; vertical-align: middle; color: #0A9D4E;">&#9679;</span>&nbsp;&nbsp;&nbsp;Распечатано</div>
    </div>
    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <label for="weight">Масса катушки</label>
                <div class="input-group">
                    <input type="text" class="form-control int-only" name="weight" value="22" />
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
                    <input type="text" class="form-control int-only" name="length" value="80" />
                    <div class="input-group-append">
                        <span class="input-group-text">м</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label for="diameter">Диаметр от вала</label>
                <div class="input-group">
                    <input type="text" class="form-control int-only" name="diameter" value="120" />
                    <div class="input-group-append">
                        <span class="input-group-text">мм</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label for="print_label">&nbsp;</label>
                <button type="button" class="btn btn-light w-100" name="print_label"><img src="../images/icons/print.svg" class="mr-2" />Распечатать бирку</button>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>