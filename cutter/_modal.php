<!-- The Modal -->
<?php
$supplier = '';
$film_brand = '';
$thickness = '';
$ud_ves = '';
$width = '';

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
?>
<div class="modal fade" id="infoModal">
    <div class="modal-dialog" style="width: 100%; height: 100%; margin: 0;">
        <div class="modal-content" style="border: 0; border-radius: 0; height: 100%;">
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
                    for($i=1; $i<=19; $i++):
                    if(null !== filter_input(INPUT_GET, 'stream_'.$i)):
                    ?>
                    <p>Ручей <?=$i ?> &ndash; <?= filter_input(INPUT_GET, 'stream_'.$i) ?> метров</p>
                    <?php
                    endif;
                    endfor;
                    ?>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer d-none"></div>
            </form>
        </div>
    </div>
</div>