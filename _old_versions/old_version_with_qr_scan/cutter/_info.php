<!-- The Modal -->
<?php
$supplier = '';
$film_brand = '';
$thickness = '';
$ud_ves = '';
$width = '';

if(null !== $cutting_id) {
    $sql = "select s.name supplier, f.name film, fv.weight, fv.thickness, c.width "
            . "from cutting c "
            . "inner join supplier s on c.supplier_id = s.id "
            . "inner join film_variation fv on c.film_variation_id = fv.id "
            . "inner join film f on fv.film_id = f.id "
            . "where c.id = $cutting_id and fv.id = c.film_variation_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $supplier = $row['supplier'];
        $film = $row['film'];
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
                    <p>Марка пленки: <?=$film ?></p>
                    <p>Толщина: <?=$thickness ?> мкм <?=$ud_ves ?> г/м<sup>2</sup></p>
                    <p>Ширина: <?=$width ?> мм</p>
                    <p class="font-weight-bold mt-2" style="font-size: large;">Как режем?</p>
                    <?php
                    // Для окна "Нарезка 1"
                    for($i=1; $i<=19; $i++):
                    if(null !== filter_input(INPUT_GET, 'stream_'.$i)):
                    ?>
                    <p>Ручей <?=$i ?> &ndash; <?= filter_input(INPUT_GET, 'stream_'.$i) ?> мм<?= empty(filter_input(INPUT_GET, 'comment_'.$i)) ? '' : ' ('. urldecode(filter_input(INPUT_GET, 'comment_'.$i)).')' ?></p>
                    <?php
                    endif;
                    endfor;
                    
                    // Для всех остальных окон
                    if(null !== $cutting_id):
                    $sql = "select width, comment from cutting_stream where cutting_id = $cutting_id";
                    $fetcher = new Fetcher($sql);
                    $i = 0;
                    while ($row = $fetcher->Fetch()):
                    ?>
                    <p>Ручей <?=++$i ?> &ndash; <?=$row['width'] ?> мм<?= empty($row['comment']) ? '' : ' ('.$row['comment'].')' ?></p>
                    <?php
                    endwhile;
                    ?>
                    <p class="font-weight-bold mt-2" style="font-size: large;">Сколько нарезали?</p>
                    <?php
                    $sql = "select cs.id, cs.roll_id, cs.is_from_pallet, concat('Р', r.id) name, r.length, r.id_from_supplier "
                            . "from cutting_source cs "
                            . "inner join roll r on cs.roll_id = r.id "
                            . "where cs.cutting_id=$cutting_id and cs.is_from_pallet = 0 "
                            . "union "
                            . "select cs.id, cs.roll_id, cs.is_from_pallet, concat('П', p.id, 'Р', pr.ordinal) name, pr.length, pr.id_from_supplier "
                            . "from cutting_source cs "
                            . "inner join pallet_roll pr on cs.roll_id = pr.id "
                            . "inner join pallet p on pr.pallet_id = p.id "
                            . "where cs.cutting_id=$cutting_id and cs.is_from_pallet = 1 "
                            . "order by id";
                    $grabber = new Grabber($sql);
                    $sources = $grabber->result;
                    
                    $i=0;
                    foreach($sources as $source):
                    ?>
                    <p class="font-weight-bold font-italic" style="color: #888888;">Исходный ролик <?=$source['name'] ?> (<?=$source['length'] ?> метров)</p>
                    <p class="font-weight-bold font-italic" style="color: #888888;">id поставщика: <?=$source['id_from_supplier'] ?></p>
                    <?php
                    $sql = "select length from cutting_wind where cutting_source_id=".$source['id'];
                    $fetcher = new Fetcher($sql);
                    
                    while ($row = $fetcher->Fetch()):
                    ?>
                    <p>Намотка <?=++$i ?> &ndash; <?=$row['length'] ?> метров</p>
                    <?php
                    endwhile;
                    endforeach;
                    
                    $sql = "select ifnull(sum(length), 0) from cutting_wind where cutting_source_id in (select id from cutting_source where cutting_id = $cutting_id)";
                    $fetcher = new Fetcher($sql);
                    if($row = $fetcher->Fetch()):
                    ?>
                    <p class="font-weight-bold" style="font-size: large;">Всего нарезали: <?=$row[0] ?> метров</p>
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