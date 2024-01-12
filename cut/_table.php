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
    <div class="subtitle">Всего: катушек <?=$bobbins ?> шт., <?=$weight ?> кг, <?=$length ?> м, этикеток <?= floor($calculation->streams_number * $length * 1000 / $calculation->length) ?> шт.</div>
    <table class="table">
        <tr>
            <th style="border-top-width: 0;">Наименование</th>
            <th style="border-top-width: 0;">Катушек</th>
            <th style="border-top-width: 0;">Масса</th>
            <th style="border-top-width: 0;">Метраж</th>
            <th style="border-top-width: 0;">Этикеток</th>
        </tr>
        <?php
        $sql = "select cs.name, count(cts.id) bobbins, sum(cts.weight) weight, sum(cts.length) length "
                . "from calculation_take_stream cts "
                . "left join calculation_stream cs on cts.calculation_stream_id = cs.id "
                . "where cs.calculation_id = $id "
                . "group by cts.calculation_stream_id";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()):
        ?>
        <tr>
            <td><?=$row['name'] ?></td>
            <td><?=$row['bobbins'] ?></td>
            <td><?=$row['weight'] ?? 0 ?> кг</td>
            <td><?=$row['length'] ?? 0 ?> м</td>
            <td><?= floor($calculation->streams_number * $row['length'] * 1000 / $calculation->length) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>