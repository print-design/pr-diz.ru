<!-- The Modal -->
<?php
$supplier = '';
$film_brand = '';
$thickness = '';
$ud_ves = '';
$width = '';

$cut_id = null;

// Для окна "Нарезка 1"
if(null !== filter_input(INPUT_GET, 'supplier_id') 
        && null !== filter_input(INPUT_GET, 'film_brand_id') 
        && null !== filter_input(INPUT_GET, 'thickness') 
        && null !== filter_input(INPUT_GET, 'width')) {
    $sql = "select fbv.weight, fb.name film_brand, s.name supplier "
            . "from film_brand_variation fbv "
            . "inner join film_brand fb on fbv.film_brand_id = fb.id "
            . "inner join supplier s on fb.supplier_id = s.id "
            . "where fbv.thickness = ". filter_input(INPUT_GET, 'thickness')." and fb.id = ". filter_input(INPUT_GET, 'film_brand_id');
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $supplier = $row['supplier'];
        $film_brand = $row['film_brand'];
        $ud_ves = $row['weight'];
        $thickness = filter_input(INPUT_GET, 'thickness');
        $width = filter_input(INPUT_GET, 'width');
    }
}
else {
    $sql = "select id from cut where cutter_id = $user_id and id not in (select cut_id from cut_source)";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $cut_id = $row[0];        
    }
    
    if(null == $cut_id) {
        $sql = "select id from cut where cutter_id = $user_id and id in (select cut_id from cut_source) order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $cut_id = $row[0];
        }
    }
}

// Для всех остальных окон
if(null !== $cut_id) {
    $sql = "select s.name supplier, fb.name film_brand, fbv.weight, c.thickness, c.width "
            . "from cut c "
            . "inner join supplier s on c.supplier_id = s.id "
            . "inner join film_brand fb on c.film_brand_id = fb.id "
            . "inner join film_brand_variation fbv on fbv.film_brand_id = fb.id "
            . "where c.id = $cut_id and fb.id = c.film_brand_id and fbv.thickness = c.thickness";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $supplier = $row['supplier'];
        $film_brand = $row['film_brand'];
        $ud_ves = $row['weight'];
        $thickness = $row['thickness'];
        $width = $row['width'];
    }
}
?>
<div class="modal fade" id="infoModal">
    <div class="modal-dialog" style="width: 100%; height: 100%; margin: 0;">
        <div class="modal-content" style="border: 0; border-radius: 0; height: 100%; overflow: auto;">
            <form method="post">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Характеристики</h4>
                    <button type="button" class="close" data-dismiss="modal"><img src="<?=APPLICATION ?>/images/icons/x.svg" /></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <p>Поставщик: <?=$supplier ?></p>
                    <p>Марка пленки: <?=$film_brand ?></p>
                    <p>Толщина: <?=$thickness ?> мкм <?=$ud_ves ?> г/м<sup>2</sup></p>
                    <p>Ширина: <?=$width ?> мм</p>
                    <p class="font-weight-bold mt-2" style="font-size: large;">Как режем?</p>
                    <?php
                    // Для окна "Нарезка 1"
                    for($i=1; $i<=19; $i++):
                    if(null !== filter_input(INPUT_GET, 'stream_'.$i)):
                    ?>
                    <p>Ручей <?=$i ?> &ndash; <?= filter_input(INPUT_GET, 'stream_'.$i) ?> мм</p>
                    <?php
                    endif;
                    endfor;
                    
                    // Для всех остальных окон
                    if(null !== $cut_id):
                    $sql = "select width from cut_stream where cut_id = $cut_id";
                    $fetcher = new Fetcher($sql);
                    $i = 0;
                    while ($row = $fetcher->Fetch()):
                    ?>
                    <p>Ручей <?=++$i ?> &ndash; <?=$row['width'] ?> мм</p>
                    <?php
                    endwhile;
                    
                    ?>
                    <p class="font-weight-bold mt-2" style="font-size: large;">Сколько нарезали?</p>
                    <?php
                    $sql = "select length from cut_wind where cut_id = $cut_id";
                    $fetcher = new Fetcher($sql);
                    $i=0;
                    while ($row = $fetcher->Fetch()):
                    ?>
                    <p>Намотка <?=++$i ?> &ndash; <?=$row['length'] ?> метров</p>
                    <?php
                    endwhile;
                    
                    $sql = "select sum(length) from cut_wind where cut_id = $cut_id";
                    $fetcher = new Fetcher($sql);
                    if($row = $fetcher->Fetch()):
                    ?>
                    <p class="font-weight-bold">Всего нарезали: <?=$row[0] ?> метров</p>
                    <?php
                    endif;
                    endif;
                    ?>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer d-none"></div>
            </form>
        </div>
    </div>
</div>