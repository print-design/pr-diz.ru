<div class="calculation_stream">
    <div class="name" style="font-size: 33px;"><?=CUTTER_NAMES[$machine_id] ?></div>
    <div class="name">Результаты резки</div>
    <?php
    $bobbins = 0;
    $weight = 0;
    $length = 0;
    
    $sql = "select count(id) bobbins, sum(weight) weight, sum(length) length from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id)";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $bobbins = $row['bobbins'];
        $weight = $row['weight'];
        $length = $row['length'];
    }
    ?>
    <div class="subtitle">Всего: катушек <?= DisplayNumber(intval($bobbins), 0) ?> шт., <?= DisplayNumber(intval($weight), 0) ?> кг, <?= DisplayNumber(intval($length), 0) ?> м<?= $calculation->work_type_id == WORK_TYPE_NOPRINT ? "." : ", этикеток ".DisplayNumber(floor($length * 1000 / $calculation->length), 0)." шт." ?></div>
    <table class="table">
        <tr>
            <th style="border-top-width: 0; font-weight: bold;">Наименование</th>
            <th style="border-top-width: 0; font-weight: bold;">Катушек</th>
            <th style="border-top-width: 0; font-weight: bold;">Масса</th>
            <th style="border-top-width: 0; font-weight: bold;">Метраж</th>
            <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
            <th style="border-top-width: 0; font-weight: bold;">Этикеток</th>
            <?php endif; ?>
        </tr>
        <?php
        $sql = "select cs.id, cs.name, count(cts.id) bobbins, sum(cts.weight) weight, sum(cts.length) length "
                . "from calculation_take_stream cts "
                . "left join calculation_stream cs on cts.calculation_stream_id = cs.id "
                . "where cs.calculation_id = $id "
                . "group by cts.calculation_stream_id "
                . "order by cs.position";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()):
        ?>
        <tr>
            <td style="text-align: left;"><?=$row['name'] ?></td>
            <td style="text-align: left;"><?=$row['bobbins'] ?></td>
            <td style="text-align: left;"><?=$row['weight'] ?? 0 ?> кг</td>
            <td style="text-align: left;"><?=$row['length'] ?? 0 ?> м</td>
            <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
            <td style="text-align: left;"><?= floor($row['length'] * 1000 / $calculation->length) ?> шт.</td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php
    $total_length = 0;
    $sql = "select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id)";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $total_length = $row[0];
    }
    ?>
    <div class="name">Готовые съёмы</div>
    <div class="subtitle">Общий метраж съёмов: <?= DisplayNumber(intval($total_length), 0) ?> м</div>
    <?php
    $sql = "select ct.id, ct.timestamp, sum(cts.weight) weight, sum(cts.length) length "
                . "from calculation_take_stream cts "
                . "left join calculation_take ct on cts.calculation_take_id = ct.id "
                . "where ct.calculation_id = $id "
                . "group by cts.calculation_take_id";
    $grabber = new Grabber($sql);
    $takes = $grabber->result;
    $take_ordinal = 0;
    
    foreach($takes as $take):
    $take_date = DateTime::createFromFormat('Y-m-d H:i:s', $take['timestamp']);
    $take_hour = $take_date->format('G');
    $take_shift = 'day';
    if($take_hour < 8 || $take_hour > 19) {
        $take_shift = 'night';
    }
    
    $take_last_name = '';
    $take_first_name = '';
    
    $sql = "select pe.last_name, pe.first_name "
            . "from plan_workshift1 pw inner join plan_employee pe on pw.employee1_id = pe.id "
            . "where date_format(pw.date, '%d-%m-%Y') = '".$take_date->format('d-m-Y')."' "
            . "and pw.shift = '$take_shift' and pw.work_id = ".WORK_CUTTING." and pw.machine_id = $machine_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $take_last_name = $row['last_name'];
        $take_first_name = $row['first_name'];
    }
    ?>
    <div style="padding-left: 10px; padding-right: 10px; border: solid 1px #e3e3e3; border-radius: 15px; margin-top: 15px; margin-bottom: 5px;">
        <div style="padding-top: 15px; padding-bottom: 15px;">
            <a href="javascript: void(0);" class="show_table" data-id="<?=$take['id'] ?>" onclick="javascript: ShowTakeTable(<?=$take['id'] ?>);"><i class="fa fa-chevron-down" style="color: #EC3A7A; margin-left: 15px; margin-right: 15px;"></i></a>
            <a href="javascript: void(0);" class="hide_table d-none" data-id="<?=$take['id'] ?>" onclick="javascript: HideTakeTable(<?=$take['id'] ?>);"><i class="fa fa-chevron-up" style="color: #EC3A7A; margin-left: 15px; margin-right: 15px;"></i></a>
            <strong>Съём <?=(++$take_ordinal).'. '.$take_date->format('j').' '.mb_substr($months_genitive[$take_date->format('n')], 0, 3).' '.$take_date->format('Y') ?>, <?=$take_last_name.' '. mb_substr($take_first_name, 0, 1).'. ' ?></strong> <?= DisplayNumber(intval($take['weight']), 0) ?> кг, <?= DisplayNumber(intval($take['length']), 0) ?> м<?=$calculation->work_type_id == WORK_TYPE_NOPRINT ? "." : ", ".DisplayNumber(floor($take['length'] * 1000 / $calculation->length), 0)." шт." ?>
        </div>
        <table class="table take_table d-none" data-id="<?=$take['id'] ?>" style="border-bottom: 0;">
            <tr>
                <th style="font-weight: bold;">ID</th>
                <th style="font-weight: bold;">Наименование</th>
                <th style="font-weight: bold;">Время</th>
                <th style="font-weight: bold;">Масса</th>
                <th style="font-weight: bold;">Метраж</th>
                <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
                <th style="font-weight: bold;">Этикеток</th>
                <?php endif; ?>
                <th style="font-weight: bold;"></th>
            </tr>
            <?php 
            $sql = "select cts.id, cs.name, date_format(cts.printed, '%H:%i') printed, cts.weight, cts.length "
                    . "from calculation_take_stream cts "
                    . "inner join calculation_stream cs on cts.calculation_stream_id = cs.id "
                    . "where cts.calculation_take_id = ".$take['id']
                    . " order by cs.position";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
            ?>
            <tr style="border-bottom: 0;">
                <td style="text-align: left;"><?=$row['id'] ?></td>
                <td style="text-align: left;"><?=$row['name'] ?></td>
                <td style="text-align: left;"><?=$row['printed'] ?></td>
                <td style="text-align: left;"><?=$row['weight'] ?> кг</td>
                <td style="text-align: left;"><?=$row['length'] ?> м</td>
                <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
                <td style="text-align: left;"><?= DisplayNumber(floor($row['length'] * 1000 / $calculation->length), 0) ?> шт.</td>
                <?php endif; ?>
                <td style="text-align: left;"><a href="javascript: void(0);" title="Редактировать"><img src="../images/icons/edit1.svg" /></a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php endforeach; ?>
</div>