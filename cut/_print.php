<?php
$stream_id = null;
$stream_name = '';
$stream_weight = '';
$stream_length = '';
$stream_printed = '';
$dt_printed = '';

if(null !== filter_input(INPUT_GET, 'stream_id')) {
    $stream_id = filter_input(INPUT_GET, 'stream_id');
    
    $sql = "select cts.id, cs.name, cts.weight, cts.length, cts.printed "
            . "from calculation_take_stream cts "
            . "inner join calculation_stream cs on cts.calculation_stream_id = cs.id "
            . "where cts.calculation_stream_id = $stream_id and cts.calculation_take_id = (select max(id) from calculation_take where calculation_id = cs.calculation_id)";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $stream_id = $row['id'];
        $stream_name = $row['name'];
        $stream_weight = $row['weight'];
        $stream_length = $row['length'];
        $stream_printed = $row['printed'];
        $dt_printed = DateTime::createFromFormat('Y-m-d H:i:s', $stream_printed);
    }
}
elseif(null !== filter_input(INPUT_GET, 'take_stream_id')) {
    $take_stream_id = filter_input(INPUT_GET, 'take_stream_id');
    
    $sql = "select cts.id, cs.name, cts.weight, cts.length, cts.printed "
            . "from calculation_take_stream cts "
            . "inner join calculation_stream cs on cts.calculation_stream_id = cs.id "
            . "where cts.id = $take_stream_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $stream_id = $row['id'];
        $stream_name = $row['name'];
        $stream_weight = $row['weight'];
        $stream_length = $row['length'];
        $stream_printed = $row['printed'];
        $dt_printed = DateTime::createFromFormat('Y-m-d H:i:s', $stream_printed);
    }
}
elseif(null !== filter_input(INPUT_GET, 'not_take_stream_id')) {
    $not_take_stream_id = filter_input(INPUT_GET, 'not_take_stream_id');
    
    $sql = "select cnts.id, cs.name, cnts.weight, cnts.length, cnts.printed "
            . "from calculation_not_take_stream cnts "
            . "inner join calculation_stream cs on cnts.calculation_stream_id = cs.id "
            . "where cnts.id = $not_take_stream_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $stream_id = $row['id'];
        $stream_name = $row['name'];
        $stream_weight = $row['weight'];
        $stream_length = $row['length'];
        $stream_printed = $row['printed'];
        $dt_printed = DateTime::createFromFormat('Y-m-d H:i:s', $stream_printed);
    }
}
            
$stream_date = $dt_printed->format('d-m-Y');
$stream_hour = $dt_printed->format('G');
$stream_shift = 'day';
if($stream_hour < 8 || $stream_hour > 19) {
    $stream_shift = 'night';
}
            
$sql = "select pe.last_name, pe.first_name "
        . "from plan_workshift1 pw inner join plan_employee pe on pw.employee1_id = pe.id "
        . "where date_format(pw.date, '%d-%m-%Y') = '$stream_date' and pw.shift = '$stream_shift' and pw.work_id = ".WORK_CUTTING." and pw.machine_id = $machine_id";
$stream_cutter = '';

if($calculation_result->labels == CalculationResult::LABEL_PRINT_DESIGN) {
    $fetcher = new Fetcher($sql);

    while($row = $fetcher->Fetch()) {
        $stream_cutter .= $row['last_name'].(mb_strlen($row['first_name']) == 0 ? '' : ' '.mb_substr($row['first_name'], 0, 1).'.');
    }

    if(empty($stream_cutter)) {
        $stream_cutter = "ВЫХОДНОЙ ДЕНЬ";
    }
}

if($calculation_result->labels == CalculationResult::LABEL_PRINT_DESIGN):
?>
<div class="d-flex justify-content-start mb-1">
    <div class="mr-2"><img src="<?=APPLICATION ?>/images/logo.svg" style="width: 20px; height: 20px;" class="mt-1" /></div>
    <div>
        <strong>ООО Принт-Дизайн</strong><span style="margin-left: 10px; font-size: small;">+7(4822)781-780</span><br />
        170006, г. Тверь, ул. Учительская д. 54<br />
    </div>
</div>
<?php endif; ?>
<div class="mb-2"><strong><?=$calculation->customer_id.'-'.$calculation->num_for_customer ?>.</strong> <?=$calculation_result->labels == CalculationResult::LABEL_PRINT_DESIGN ? $calculation->customer : '' ?></div>
<table>
    <tr>
        <td style="width: 50px;">Рулон</td>
        <td class="pl-1 font-weight-bold"><?= sprintf('%03d', $stream_id) ?></td>
    </tr>
    <tr>
        <td>Дата</td>
        <td class="pl-1 font-weight-bold"><?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y H:i') ?></td>
    </tr>
    <?php if($calculation_result->labels == CalculationResult::LABEL_PRINT_DESIGN): ?>
    <tr>
        <td>Заказ</td>
        <td class="pl-1 font-weight-bold"><?=$calculation->name ?></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td class="pb-2">Ручей</td>
        <td class="pl-1 pb-2 font-weight-bold"><?=$stream_name ?></td>
    </tr>
    <tr>
        <td>Масса</td>
        <td class="pl-1 font-weight-bold"><?= rtrim(rtrim(DisplayNumber(floatval($stream_weight), 2), '0'), ',')  ?> кг</td>
    </tr>
    <tr>
        <td class="pb-2">Кол-во</td>
        <td class="pl-1 pb-2 font-weight-bold"><?= DisplayNumber(floor($stream_length * $calculation->number_in_meter), 0) ?> шт. &#177;2% (<?= rtrim(rtrim(DisplayNumber(floatval($stream_length), 2), '0'), ',')  ?> м)</td>
    </tr>
    <tr>
        <td colspan="2" class="font-weight-bold">
            <?php
            echo $calculation->film_1.' '.$calculation->thickness_1;
                
            if(!empty($calculation->film_2) && !empty($calculation->thickness_2)) {
                echo " + ".$calculation->film_2." ".$calculation->thickness_2;
            }
                
            if(!empty($calculation->film_3) && !empty($calculation->thickness_3)) {
                echo " + ".$calculation->film_3." ".$calculation->thickness_3;
            }
            ?>
        </td>
    </tr>
    <tr>
        <td class="pb-2">Резка</td>
        <td class="pb-2"><?= $stream_cutter.' '.$dt_printed->format('d.m.Y H:i') ?></td>
    </tr>
</table>
<div class="d-flex justify-content-between">
    <div class="mb-2" style="font-size: small;">
        Гарантия хранения 12 мес.<br />ТУ 2245-001-218273282-2003
    </div>
    <div class="d-flex justify-content-end">
        <div class="mr-1 position-relative" style="width: 23px; height: 22px;"><img src="<?=APPLICATION ?>/images/package.png" style="position: absolute; top: -93px; left: -23px; width: 150px; clip: rect(93px, 43px, 113px, 23px);" /></div>
        <div class="mr-1 position-relative" style="width: 21px; height: 22px;"><img src="<?=APPLICATION ?>/images/package.png" style="position: absolute; top: -68px; left: -23px; width: 150px; clip: rect(68px, 46px, 85px, 23px);" /></div>
        <div class="position-relative" style="width: 21px; height: 22px;"><img src="<?=APPLICATION ?>/images/package.png" style="position: absolute; top: -93px; left: -50px; width: 150px; clip: rect(93px, 73px, 113px, 50px);" /></div>
    </div>
</div>