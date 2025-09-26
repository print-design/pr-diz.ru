<?php
$stream_id = null;
$stream_name = '';
$stream_weight = '';
$stream_length = '';
$stream_printed = '';
$dt_printed = '';

if(null !== filter_input(INPUT_GET, 'stream_id')) {
    $stream_id = filter_input(INPUT_GET, 'stream_id');
    
    $sql = "select cts.id, cs.name, cts.weight, cts.length, cts.printed, pe.last_name, pe.first_name "
            . "from calculation_take_stream cts "
            . "inner join calculation_stream cs on cts.calculation_stream_id = cs.id "
            . "left join plan_employee pe on cts.plan_employee_id = pe.id "
            . "where cts.calculation_stream_id = $stream_id and cts.calculation_take_id = (select max(id) from calculation_take where calculation_id = cs.calculation_id)";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $stream_id = $row['id'];
        $stream_name = $row['name'];
        $stream_weight = $row['weight'];
        $stream_length = $row['length'];
        $stream_printed = $row['printed'];
        $last_name = $row['last_name'];
        $first_name = $row['first_name'];
        $dt_printed = DateTime::createFromFormat('Y-m-d H:i:s', $stream_printed);
    }
}
elseif(null !== filter_input(INPUT_GET, 'take_stream_id')) {
    $take_stream_id = filter_input(INPUT_GET, 'take_stream_id');
    
    $sql = "select cts.id, cs.name, cts.weight, cts.length, cts.printed, pe.last_name, pe.first_name "
            . "from calculation_take_stream cts "
            . "inner join calculation_stream cs on cts.calculation_stream_id = cs.id "
            . "left join plan_employee pe on cts.plan_employee_id = pe.id "
            . "where cts.id = $take_stream_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $stream_id = $row['id'];
        $stream_name = $row['name'];
        $stream_weight = $row['weight'];
        $stream_length = $row['length'];
        $stream_printed = $row['printed'];
        $last_name = $row['last_name'];
        $first_name = $row['first_name'];
        $dt_printed = DateTime::createFromFormat('Y-m-d H:i:s', $stream_printed);
    }
}
elseif(null !== filter_input(INPUT_GET, 'not_take_stream_id')) {
    $not_take_stream_id = filter_input(INPUT_GET, 'not_take_stream_id');
    
    $sql = "select cnts.id, cs.name, cnts.weight, cnts.length, cnts.printed, pe.last_name, pe.first_name "
            . "from calculation_not_take_stream cnts "
            . "inner join calculation_stream cs on cnts.calculation_stream_id = cs.id "
            . "left join plan_employee pe on cnts.plan_employee_id = pe.id "
            . "where cnts.id = $not_take_stream_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $stream_id = $row['id'];
        $stream_name = $row['name'];
        $stream_weight = $row['weight'];
        $stream_length = $row['length'];
        $stream_printed = $row['printed'];
        $last_name = $row['last_name'];
        $first_name = $row['first_name'];
        $dt_printed = DateTime::createFromFormat('Y-m-d H:i:s', $stream_printed);
    }
}

if($calculation_result->labels == CalculationResult::LABEL_PRINT_DESIGN):
?>
<div class="d-flex justify-content-start mb-1">
    <div class="mr-2"><img src="<?=APPLICATION ?>/images/logo.svg" style="width: 20px; height: 20px;" class="mt-1" /></div>
    <div>
        <strong>ООО Принт-Дизайн</strong><span style="margin-left: 10px; font-size: small;">+7(4822)781-780</span><br />
        170100, г. Тверь, Московское шоссе, 20ю<br />
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
        <td>Резка</td>
        <!-- Фамилия резчика показывается только на бирках "Принт-Дизайн" -->
        <td class="pl-1 font-weight-bold"><?= ($calculation_result->labels == CalculationResult::LABEL_PRINT_DESIGN ? $last_name.(empty($first_name) ? '' : ' '.mb_substr($first_name, 0, 1).'.').' ' : '').$dt_printed->format('d.m.Y H:i') ?></td>
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
        <td class="pb-2">Дата</td>
        <td class="pb-2"><?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y H:i') ?></td>
    </tr>
</table>
<div class="d-flex justify-content-between">
    <div class="mb-2" style="font-size: small;">
        Гарантия хранения 12 мес.
        <?php if($calculation_result->labels == CalculationResult::LABEL_PRINT_DESIGN): ?>
        <br />ТУ 22.29.21-001-10785166-2025
        <?php endif; ?>
    </div>
    <div class="d-flex justify-content-end">
        <div class="mr-1 position-relative" style="width: 23px; height: 22px;"><img src="<?=APPLICATION ?>/images/package.png" style="position: absolute; top: -93px; left: -23px; width: 150px; clip: rect(93px, 43px, 113px, 23px);" /></div>
        <div class="mr-1 position-relative" style="width: 21px; height: 22px;"><img src="<?=APPLICATION ?>/images/package.png" style="position: absolute; top: -68px; left: -23px; width: 150px; clip: rect(68px, 46px, 85px, 23px);" /></div>
        <?php if($calculation->customer_id == 216): ?>
        <div class="position-relative" style="width: 21px; height: 22px;"><img src="<?=APPLICATION ?>/images/mebius5.png" style="position: absolute; top: 1px; left: 0px; width: 18px;" /></div>
        <?php else: ?>
        <div class="position-relative" style="width: 21px; height: 22px;"><img src="<?=APPLICATION ?>/images/package.png" style="position: absolute; top: -93px; left: -50px; width: 150px; clip: rect(93px, 73px, 113px, 50px);" /></div>        
        <?php endif; ?>
    </div>
</div>