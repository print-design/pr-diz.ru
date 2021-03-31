<?php
include '../include/topscripts.php';

// Если не задано значение id, перенаправляем на список
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/pallet/');
}

// Получение данных
$sql = "select p.date, p.storekeeper_id, u.last_name, u.first_name, p.supplier_id, s.name supplier, p.id_from_supplier, "
        . "p.film_brand_id, fb.name film_brand, p.width, p.thickness, p.length, "
        . "p.net_weight, p.rolls_number, p.cell, "
        . "(select ps.name from pallet_status_history psh left join pallet_status ps on psh.status_id = ps.id where psh.pallet_id = p.id order by psh.id desc limit 0, 1) status, "
        . "p.comment "
        . "from pallet p "
        . "left join user u on p.storekeeper_id = u.id "
        . "left join supplier s on p.supplier_id = s.id "
        . "left join film_brand fb on p.film_brand_id = fb.id "
        . "where p.id=$id";

$row = (new Fetcher($sql))->Fetch();
$date = $row['date'];
$storekeeper_id = $row['storekeeper_id'];
$storekeeper = $row['last_name'].' '.$row['first_name'];
$supplier_id = $row['supplier_id'];
$supplier = $row['supplier'];
$id_from_supplier = $row['id_from_supplier'];
$film_brand_id = $row['film_brand_id'];
$film_brand = $row['film_brand'];
$width = $row['width'];
$thickness = $row['thickness'];
$length = $row['length'];
$net_weight = $row['net_weight'];
$rolls_number = $row['rolls_number'];
$cell = $row['cell'];
$status = $row['status'];
$comment = $row['comment'];

// Определяем удельный вес
$ud_ves = null;
$sql = "select weight from film_brand_variation where film_brand_id=$film_brand_id and thickness=$thickness";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $ud_ves = $row[0];
}

// Вертикальное положение стикера
$sticker_top = 30;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table.print tr td {
                font-size: 48px;
                line-height: 52px;
                vertical-align: top;
                white-space: pre-wrap;
                padding: 0;
                padding-right: 10px;
            }
        </style>
    </head>
    <body class="print">
        <div class="w-100" style="height: 1400px; position: absolute; left: 30px; top: <?=$sticker_top ?>px;">
            <div style="margin-bottom: 20px; margin-top: 30px;">
                <a href="<?=APPLICATION ?>/pallet/new.php"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                <div style="display: inline; margin-left: 300px; font-size: 30px;">
                    <a href="javascript:void(0);" id="sharelink"><i class="fas fa-share-alt"></i></a>
                </div>
            </div>
            <table class="table table-bordered print w-100" style="writing-mode: vertical-rl; margin-left: 50px;">
                <tbody>
                    <tr>
                        <td colspan="2" class="font-weight-bold font-italic text-center">ООО &laquo;Принт-дизайн&raquo;</td>
                        <td class="text-center text-nowrap">Паллет <span class="font-weight-bold"><?="П".$id ?></span> от <?=$date ?></td>
                    </tr>
                    <tr>
                        <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                        <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                        <td rowspan="6" class="qr" style="height: 20%;">
                            <?php
                            include '../qr/qrlib.php';
                            $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                            $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/pallet/pallet.php?id='.$id;
                            $current_date_time = date("dmYHis");
                            $filename = "../temp/$current_date_time.png";
                            QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);
                            echo "<img src='$filename' />";

                            // Удаление всех файлов, кроме текущего (чтобы диск не переполнился).
                            $files = scandir("../temp/");
                            foreach ($files as $file) {
                                if($file != "$current_date_time.png" && !is_dir($file)) {
                                    unlink("../temp/$file");
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>ID от поставщика<br /><strong><?=$id_from_supplier ?></strong></td>
                        <td>Толщина, уд.вес<br /><strong><?=$thickness ?> мкм,<br /> <?=$ud_ves ?> г/м<sup style="top:2px;">2</sup></strong></td>
                    </tr>
                    <tr>
                        <td>Кладовщик<br /><strong><?=$storekeeper ?></strong></td>
                        <td>Длина<br /><strong><?=$length ?> м</strong></td>
                    </tr>
                    <tr>
                        <td>Марка пленки<br /><strong><?=$film_brand ?></strong></td>
                        <td>Масса нетто<br /><strong><?=$net_weight ?> кг</strong></td>
                    </tr>
                    <tr>
                        <td>Статус<br /><strong><?=$status ?></strong></td>
                        <td>Количество рулонов<br /><strong><?=$rolls_number ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="white-space: normal;">Комментарий<br /><strong><?= $comment ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php
        $sticker_top = 1930;
        ?>
        
        <div class="w-100" style="height: 1400px; position: absolute; top: <?=$sticker_top ?>px">
            <table class="table table-bordered print w-100" style="writing-mode: vertical-rl; margin-left: 50px;">
                <tbody>
                    <tr>
                        <td colspan="2" class="font-weight-bold font-italic text-center">ООО &laquo;Принт-дизайн&raquo;</td>
                        <td class="text-center text-nowrap">Паллет <span class="font-weight-bold"><?="П".$id ?></span> от <?=$date ?></td>
                    </tr>
                    <tr>
                        <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                        <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                        <td rowspan="6" class="qr" style="height: 20%;">
                            <?php
                            //include '../qr/qrlib.php';
                            $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                            $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/pallet/pallet.php?id='.$id;
                            $current_date_time = date("dmYHis");
                            $filename = "../temp/$current_date_time.png";
                            QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);
                            echo "<img src='$filename' />";
                            
                            // Удаление всех файлов, кроме текущего (чтобы диск не переполнился).
                            $files = scandir("../temp/");
                            foreach ($files as $file) {
                                if($file != "$current_date_time.png" && !is_dir($file)) {
                                    unlink("../temp/$file");
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>ID от поставщика<br /><strong><?=$id_from_supplier ?></strong></td>
                        <td>Толщина, уд.вес<br /><strong><?=$thickness ?> мкм,<br /> <?=$ud_ves ?> г/м<sup style="top:2px;">2</sup></strong></td>
                    </tr>
                    <tr>
                        <td>Кладовщик<br /><strong><?=$storekeeper ?></strong></td>
                        <td>Длина<br /><strong><?=$length ?> м</strong></td>
                    </tr>
                    <tr>
                        <td>Марка пленки<br /><strong><?=$film_brand ?></strong></td>
                        <td>Масса нетто<br /><strong><?=$net_weight ?> кг</strong></td>
                    </tr>
                    <tr>
                        <td>Статус<br /><strong><?=$status ?></strong></td>
                        <td>Количество рулонов<br /><strong><?=$rolls_number ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="white-space: normal;">Комментарий<br /><strong><?= $comment ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php
        $sql = "select id pallet_roll_id, weight, length, ordinal from pallet_roll where pallet_id = ". filter_input(INPUT_GET, 'id');
        $pallet_rolls = (new Grabber($sql))->result;
        foreach ($pallet_rolls as $pallet_roll):
        $pallet_roll_id = $pallet_roll['pallet_roll_id'];
        $weight = $pallet_roll['weight'];
        $length = $pallet_roll['length'];
        $ordinal = $pallet_roll['ordinal'];
        
        switch ($pallet_roll_id) {
            case 1:
                $sticker_top = 2830;
                break;
            
            case 2:
                $sticker_top = 5730;
                break;
                
            case 3:
                $sticker_top = 7630;
                break;
            
            case 4:
                $sticker_top = 9530;
                break;
                
            case 5:
                $sticker_top = 11430;
                break;
                
            case 6:
                $sticker_top = 13330;
                break;

            default:
                break;
        }
        ?>
        <div class="w-100" style="height: 1400px; position: absolute; left: 30px; top: <?=$sticker_top ?>px;">
            <table class="table table-bordered print w-100" style="writing-mode: vertical-rl; margin-left: 50px;">
                <tbody>
                    <tr>
                        <td colspan="2" class="font-weight-bold font-italic text-center">ООО &laquo;Принт-дизайн&raquo;</td>
                        <td class="text-center text-nowrap">Рулон <span class="font-weight-bold"><?="П".$id."Р".$pallet_roll_id ?></span> от <?=$date ?></td>
                    </tr>
                    <tr>
                        <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                        <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                        <td rowspan="6" class="qr" style="height: 20%;">
                            <?php
                            //include '../qr/qrlib.php';
                            $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                            $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/pallet/roll.php?id='.$pallet_roll_id;
                            $current_date_time = date("dmYHis");
                            $filename = "../temp/$current_date_time.png";
                            QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);
                            echo "<img src='$filename' />";
                            
                            // Удаление всех файлов, кроме текущего (чтобы диск не переполнился).
                            $files = scandir("../temp/");
                            foreach ($files as $file) {
                                if($file != "$current_date_time.png" && !is_dir($file)) {
                                    unlink("../temp/$file");
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>ID от поставщика<br /><strong><?=$id_from_supplier ?></strong></td>
                        <td>Толщина, уд.вес<br /><strong><?=$thickness ?> мкм,<br /> <?=$ud_ves ?> г/м<sup style="top: 2px;">2</sup></strong></td>
                    </tr>
                    <tr>
                        <td>Кладовщик<br /><strong><?=$storekeeper ?></strong></td>
                        <td>Длина<br /><strong><?=$length ?> м</strong></td>
                    </tr>
                    <tr>
                        <td>Марка пленки<br /><strong><?=$film_brand ?></strong></td>
                        <td>Масса нетто<br /><strong><?=$weight ?> кг</strong></td>
                    </tr>
                    <tr>
                        <td>Статус<br /><strong><?=$status ?></strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="white-space: normal;">Комментарий<br /><strong><?= $comment ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
        endforeach;
        ?>
    </body>
    <script>
        let shareData = {
            url: '<?=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>'
        }
        
        const sharelink = document.getElementById("sharelink");
        sharelink.addEventListener('click', () => {
            navigator.share(shareData)
        });
    </script>
</html>