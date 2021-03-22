<?php
include '../include/topscripts.php';

// Если не задано значение id, перенаправляем на список
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/roll/');
}

// Получение данных
$sql = "select r.date, r.storekeeper_id, u.last_name, u.first_name, r.supplier_id, s.name supplier, r.id_from_supplier, "
        . "r.film_brand_id, fb.name film_brand, r.width, r.thickness, r.length, "
        . "r.net_weight, r.cell, "
        . "(select rs.name status from roll_status_history rsh left join roll_status rs on rsh.status_id = rs.id where rsh.roll_id = r.id order by rsh.id desc limit 0, 1) status, "
        . "r.comment "
        . "from roll r "
        . "left join user u on r.storekeeper_id = u.id "
        . "left join supplier s on r.supplier_id = s.id "
        . "left join film_brand fb on r.film_brand_id = fb.id "
        . "where r.id=$id";

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
        <div class="w-100" style="height: 1400px;">
            <div style="margin-bottom: 20px; margin-top: 30px;">
                <a href="<?=APPLICATION ?>/roll/new.php"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <table class="table table-bordered print w-100" style="writing-mode: vertical-rl; margin-left: 50px;">
                <tbody>
                    <tr>
                        <td colspan="2" class="font-weight-bold font-italic text-center">ООО &laquo;Принт-дизайн&raquo;</td>
                        <td class="text-center text-nowrap">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></td>
                    </tr>
                    <tr>
                        <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                        <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                        <td rowspan="6" class="qr" style="height: 20%;">
                            <?php
                            include '../qr/qrlib.php';
                            $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                            $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/roll/roll.php?id='.$id;
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
                        <td>Масса нетто<br /><strong><?=$net_weight ?> кг</strong></td>
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
        
        <div style="height: 150px;"></div>
        
        <div class="w-100" style="height: 1400px;">
            <table class="table table-bordered print w-100" style="writing-mode: vertical-rl; margin-left: 50px;">
                <tbody>
                    <tr>
                        <td colspan="2" class="font-weight-bold font-italic text-center">ООО &laquo;Принт-дизайн&raquo;</td>
                        <td class="text-center text-nowrap">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></td>
                    </tr>
                    <tr>
                        <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                        <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                        <td rowspan="6" class="qr" style="height: 20%;">
                            <?php
                            //include '../qr/qrlib.php';
                            $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                            $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/roll/roll.php?id='.$id;
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
                        <td>Масса нетто<br /><strong><?=$net_weight ?> кг</strong></td>
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
    </body>
</html>