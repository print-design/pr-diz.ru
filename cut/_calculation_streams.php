<?php
require_once '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);

if(empty($take_id)) {
    $take_id = filter_input(INPUT_GET, 'take_id', FILTER_VALIDATE_INT);
}

// Если объекты заказа ещё не переданы вызывающей страницей (это происходит при AJAX-обновлении
// списка ручьёв без полной перезагрузки страницы -- см. cut/take.php) -- определяем calculation_id
// по take_id отдельным лёгким запросом. Сам id заказа может быть уже известен вызывающей странице
// либо как $calculation_id, либо как $id (так называется в cut/take.php).
if(!isset($calculation_id) && isset($id)) {
    $calculation_id = $id;
}

if(!isset($calculation_id)) {
    $sql = "select calculation_id from calculation_take where id = ?";
    $fetcher = new Fetcher($sql, [$take_id]);
    if($row = $fetcher->Fetch()) {
        $calculation_id = $row['calculation_id'];
    }
}

// Толщина, плотность плёнки (всех трёх слоёв) и spool -- одинаковы для всех ручьёв заказа,
// поэтому определяются один раз, а не при каждой итерации цикла.
// Если объекты $calculation/$calculation_result уже созданы вызывающей страницей -- берём готовые
// значения оттуда. Если нет (AJAX-случай) -- создавать эти объекты заново не стоит: внутри их
// конструкторов выполняются десятки запросов ради всего расчёта стоимости заказа, а нужны нам
// всего несколько чисел, поэтому в этом случае забираем их отдельным, узким запросом.
if(isset($calculation) && isset($calculation_result)) {
    $thickness1 = $calculation->thickness_1;
    $density1 = $calculation->density_1;
    $thickness2 = $calculation->thickness_2;
    $density2 = $calculation->density_2;
    $thickness3 = $calculation->thickness_3;
    $density3 = $calculation->density_3;
    $spool = $calculation_result->spool;
}
else {
    $sql = "select c.individual_thickness, fv1.thickness thickness1, c.lamination1_individual_thickness, fv2.thickness thickness2, c.lamination2_individual_thickness, fv3.thickness thickness3, "
            . "c.individual_density, fv1.weight density1, c.lamination1_individual_density, fv2.weight density2, c.lamination2_individual_density, fv3.weight density3, tm.spool "
            . "from calculation c "
            . "inner join techmap tm on tm.calculation_id = c.id "
            . "left join film_variation fv1 on c.film_variation_id = fv1.id "
            . "left join film_variation fv2 on c.lamination1_film_variation_id = fv2.id "
            . "left join film_variation fv3 on c.lamination2_film_variation_id = fv3.id "
            . "where c.id = ?";
    $fetcher = new Fetcher($sql, [$calculation_id]);
    
    $thickness1 = 0;
    $density1 = 0;
    $thickness2 = 0;
    $density2 = 0;
    $thickness3 = 0;
    $density3 = 0;
    $spool = 0;
    
    if($row = $fetcher->Fetch()) {
        $thickness1 = !empty($row['individual_thickness']) ? $row['individual_thickness'] : ($row['thickness1'] ?? 0);
        $density1 = !empty($row['individual_density']) ? $row['individual_density'] : ($row['density1'] ?? 0);
        $thickness2 = !empty($row['lamination1_individual_thickness']) ? $row['lamination1_individual_thickness'] : ($row['thickness2'] ?? 0);
        $density2 = !empty($row['lamination1_individual_density']) ? $row['lamination1_individual_density'] : ($row['density2'] ?? 0);
        $thickness3 = !empty($row['lamination2_individual_thickness']) ? $row['lamination2_individual_thickness'] : ($row['thickness3'] ?? 0);
        $density3 = !empty($row['lamination2_individual_density']) ? $row['lamination2_individual_density'] : ($row['density3'] ?? 0);
        $spool = $row['spool'];
    }
}

// Список ручьёв заказа (id, наименование, ширина) -- если $calculation_rolls уже загружен
// и в нём уже есть сводка по ручьям, берём оттуда, не запрашивая повторно
if(isset($calculation_rolls) && !empty($calculation_rolls->streams)) {
    $stream_list = $calculation_rolls->streams;
}
else {
    $sql = "select id, name, width from calculation_stream where calculation_id = ? order by position";
    $grabber = new Grabber($sql, [$calculation_id]);
    $stream_list = $grabber->result;
}

// Данные конкретно этого съёма для каждого ручья -- то, что резчик уже ввёл (или ещё не ввёл)
// именно в этом съёме. Единственное, что действительно нельзя получить из существующих
// объектов, поскольку это как раз то, что вводится прямо сейчас, в реальном времени
$sql = "select cs.id stream_id, cts.weight, cts.length, cts.radius, cts.printed "
        . "from calculation_stream cs "
        . "left join calculation_take_stream cts on cts.calculation_take_id = ? and cts.calculation_stream_id = cs.id "
        . "where cs.calculation_id = ? "
        . "order by cs.position";
$grabber = new Grabber($sql, [$take_id, $calculation_id]);

$take_stream_data = array();
foreach($grabber->result as $row) {
    $take_stream_data[$row['stream_id']] = $row;
}

$is_first = count($stream_list) > 0;

foreach($stream_list as $stream):
    $stream_id = $stream['id'];
    $stream_name = $stream['name'];
    $stream_width = $stream['width'];
    
    $data = $take_stream_data[$stream_id] ?? array('weight' => null, 'length' => null, 'radius' => null, 'printed' => null);
    $stream_weight = $data['weight'];
    $stream_length = $data['length'];
    $stream_radius = $data['radius'];
    $stream_printed = $data['printed'];
    
    if(null !== filter_input(INPUT_POST, 'stream_print_submit') && $stream_id == filter_input(INPUT_POST, 'stream_id', FILTER_VALIDATE_INT)) {
        $stream_weight = filter_input(INPUT_POST, 'weight');
        $stream_length = filter_input(INPUT_POST, 'length');
        $stream_radius = filter_input(INPUT_POST, 'radius');
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
        <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
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
