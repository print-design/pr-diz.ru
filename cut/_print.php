<div class="d-flex justify-content-start mb-1">
    <div class="mr-2"><img src="<?=APPLICATION ?>/images/logo.svg" style="width: 20px; height: 20px;" class="mt-1" /></div>
    <div>
        <strong>ООО Принт-Дизайн</strong><br />
        170006, г. Тверь, ул. Учительская д. 54<br />
        +7(4822)781-780
    </div>
</div>
<div class="mb-2"><strong><?=$customer_id.'-'.$num_for_customer ?>.</strong> <?=$customer ?></div>
<table>
    <tr>
        <td>Дата</td>
        <td class="pl-1 font-weight-bold"><?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y H:i') ?></td>
    </tr>
    <tr>
        <td>Заказ</td>
        <td class="pl-1 font-weight-bold"><?=$name ?></td>
    </tr>
    <tr>
        <td class="pb-2">Ручей</td>
        <td class="pl-1 pb-2 font-weight-bold"><?=$stream_name ?></td>
    </tr>
    <tr>
        <td>Масса</td>
        <td class="pl-1 font-weight-bold"><?=$stream_weight ?> кг</td>
    </tr>
    <tr>
        <td class="pb-2">Метраж</td>
        <td class="pl-1 pb-2 font-weight-bold"><?=$stream_length ?> м</td>
    </tr>
    <tr>
        <td colspan="2" class="font-weight-bold">
            <?php
            echo $film1.' '.$density1;
                
            if(!empty($film2) && !empty($density2)) {
                echo " + $film2 $density2";
            }
                
            if(!empty($film3) && !empty($density3)) {
                echo " + $film3 $density3";
            }
            ?>
        </td>
    </tr>
    <tr>
        <td class="pb-2">Резка</td>
        <td class="pb-2"><?= $stream_cutter.' '.$dt_printed->format('d.m.Y H:i') ?></td>
    </tr>
</table>
<div class="mb-3">
    Гарантия хранения 12 мес.<br />ТУ 2245-001-218273282-2003
</div>
<div class="d-flex justify-content-start">
    <div class="mr-1 position-relative" style="width: 23px; height: 22px;"><img src="<?=APPLICATION ?>/images/package.png" style="position: absolute; top: -93px; left: -23px; width: 150px; clip: rect(93px, 43px, 113px, 23px);" /></div>
    <div class="mr-1 position-relative" style="width: 21px; height: 22px;"><img src="<?=APPLICATION ?>/images/package.png" style="position: absolute; top: -68px; left: -23px; width: 150px; clip: rect(68px, 46px, 85px, 23px);" /></div>
    <div class="position-relative" style="width: 21px; height: 22px;"><img src="<?=APPLICATION ?>/images/package.png" style="position: absolute; top: -93px; left: -50px; width: 150px; clip: rect(93px, 73px, 113px, 50px);" /></div>
</div>