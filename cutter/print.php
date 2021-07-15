<?php
include '../include/topscripts.php';
include '../qr/qrlib.php';

// Если не задано значение cut_wind_id, перенаправляем на список
$cut_wind_id = filter_input(INPUT_GET, 'cut_wind_id');
if(empty($cut_wind_id)) {
    header('Location: '.APPLICATION.'/cutter/');
}

// Текущее время
$current_date_time = date("dmYHis");

// Находим id раскроя
$cut_id = 0;
$sql = "select cut_id from cut_wind where id=$cut_wind_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $cut_id = $row[0];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table.print tr td {
                font-size: 42px;
                line-height: 48px;
                vertical-align: top;
                white-space: pre-wrap;
                padding: 0;
                padding-right: 10px;
            }
        </style>
    </head>
    <body class="print">
        <div id="new_wind_link" class="d-none" style="position: absolute; top: 550px; left: 50px; font-size: 40px; z-index: 2000;">
            <a class="btn btn-dark" style="font-size: 20px;" href="<?=APPLICATION ?>/cutter/next.php?cut_id=<?=$cut_id ?>">Новая намотка</a>
        </div>
        <div style="position: absolute; top: 600px; right: 600px; font-size: 150px; z-index: 2000;">
            <a href="javascript:void(0);" id="sharelink"><i class="fas fa-share-alt"></i></a>
        </div>
        <?php
        // Получение данных
        $sql = "select r.id, DATE_FORMAT(r.date, '%d.%m.%Y') date, r.storekeeper_id, u.last_name, u.first_name, r.supplier_id, s.name supplier, r.id_from_supplier, "
                . "r.film_brand_id, fb.name film_brand, r.width, r.thickness, r.length, "
                . "r.net_weight, r.cell, "
                . "(select rs.name status from roll_status_history rsh left join roll_status rs on rsh.status_id = rs.id where rsh.roll_id = r.id order by rsh.id desc limit 0, 1) status, "
                . "r.comment "
                . "from roll r "
                . "left join user u on r.storekeeper_id = u.id "
                . "left join supplier s on r.supplier_id = s.id "
                . "left join film_brand fb on r.film_brand_id = fb.id "
                . "where r.cut_wind_id=$cut_wind_id";
        $current_roll = 0;
        $fetcher = new Fetcher($sql);

        while($row = $fetcher->Fetch()):
        $id = $row['id'];
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
        $cell = $row['cell'];
        $status = $row['status'];
        $comment = $row['comment'];
        
        // Определяем удельный вес
        $ud_ves = null;
        $ud_ves_sql = "select weight from film_brand_variation where film_brand_id=$film_brand_id and thickness=$thickness";
        $ud_ves_fetcher = new Fetcher($ud_ves_sql);
        if($ud_ves_row = $ud_ves_fetcher->Fetch()) {
            $ud_ves = $ud_ves_row[0];
        }

        // Вертикальное положение бирки
        $current_roll++;
        $sticker_top = 0;
        
        switch ($current_roll) {
            case 1:
                $sticker_top = 0;
                break;
            
            case 2:
                $sticker_top = 1700;
                break;
                
            case 3:
                $sticker_top = 3300;
                break;
            
            case 4:
                $sticker_top = 4950;
                break;
                
            case 5:
                $sticker_top = 6600;
                break;
                
            case 6:
                $sticker_top = 8200;
                break;
            
            case 7:
                $sticker_top = 9900;
                break;
            
            case 8:
                $sticker_top = 11500;
                break;
            
            // Остальные расстояния надо проверить
            
            case 9:
                $sticker_top = 13150;
                break;
            
            case 10:
                $sticker_top = 14800;
                break;
            
            case 11:
                $sticker_top = 16450;
                break;
            
            case 12:
                $sticker_top = 18100;
                break;
            
            case 13:
                $sticker_top = 19750;
                break;
            
            case 14:
                $sticker_top = 21400;
                break;
            
            case 15:
                $sticker_top = 23050;
                break;
            
            case 16:
                $sticker_top = 24700;
                break;
            
            case 17:
                $sticker_top = 26350;
                break;
            
            case 18:
                $sticker_top = 28000;
                break;
            
            case 19:
                $sticker_top = 29650;
                break;

            default:
                break;
        }
        ?>
        <div class="w-100" style="height: 1400px; position: absolute; top: <?=$sticker_top ?>px;">
            <table class="table table-bordered print w-100" style="writing-mode: vertical-rl; margin-top: 30px;">
                <tbody>
                    <tr>
                        <td colspan="2" class="font-weight-bold font-italic text-left">ООО &laquo;Принт-дизайн&raquo;</td>
                        <td class="text-center text-nowrap" style="font-size: 60px;">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></td>
                    </tr>
                    <tr>
                        <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                        <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                        <td rowspan="6" class="qr" style="height: 20%; white-space: normal;">
                            <?php
                            $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                            $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/roll/roll.php?id='.$id;
                            $filename = "../temp/".$current_roll."_".$current_date_time.".png";
                            
                            do {
                                QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);
                            } while (!file_exists($filename));
                            ?>
                            <img src='<?=$filename ?>' style='height: 800px; width: 800px;' />
                            <br /><br />
                            <div class="text-nowrap" style="font-size: 60px;">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap pb-5">ID от поставщика<br /><span class="text-nowrap font-weight-bold"><?=$id_from_supplier ?></span></td>
                        <td class="text-nowrap pb-5">Толщина, уд.вес<br /><span class="text-nowrap font-weight-bold"><?=$thickness ?> мкм,<br /> <?=$ud_ves ?> г/м<sup style="top: 2px;">2</sup></span></td>
                    </tr>
                    <tr>
                        <td>Кладовщик<br /><strong><?=$storekeeper ?></strong></td>
                        <td>Длина<br /><strong><?=$length ?> м</strong></td>
                    </tr>
                    <tr>
                        <td class="text-nowrap pb-5">Марка пленки<br /><strong><?=$film_brand ?></strong></td>
                        <td class="text-nowrap pb-5">Масса нетто<br /><strong><?=$net_weight ?> кг</strong></td>
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
        endwhile;
        
        // Удаление всех файлов, кроме текущих (чтобы диск не переполнился).
        $files = scandir("../temp/");
        foreach ($files as $file) {
            $created = filemtime("../temp/".$file);
            $now = time();
            $diff = $now - $created;
            
            if($diff > 20 &&
                    $file != "$current_date_time.png" &&
                    $file != "1_"."$current_date_time.png" &&
                    $file != "2_"."$current_date_time.png" &&
                    $file != "3_"."$current_date_time.png" &&
                    $file != "4_"."$current_date_time.png" &&
                    $file != "5_"."$current_date_time.png" &&
                    $file != "6_"."$current_date_time.png" &&
                    $file != "7_"."$current_date_time.png" &&
                    $file != "8_"."$current_date_time.png" &&
                    $file != "9_"."$current_date_time.png" &&
                    $file != "10_"."$current_date_time.png" &&
                    $file != "11_"."$current_date_time.png" &&
                    $file != "12_"."$current_date_time.png" &&
                    $file != "13_"."$current_date_time.png" &&
                    $file != "14_"."$current_date_time.png" &&
                    $file != "15_"."$current_date_time.png" &&
                    $file != "16_"."$current_date_time.png" &&
                    $file != "17_"."$current_date_time.png" &&
                    $file != "18_"."$current_date_time.png" &&
                    $file != "19_"."$current_date_time.png" &&
                    !is_dir($file)) {
                unlink("../temp/$file");
            }
        }
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
        
        setTimeout(function() { 
            document.getElementById('new_wind_link').removeAttribute('class');
        }, 30000);
    </script>
</html>