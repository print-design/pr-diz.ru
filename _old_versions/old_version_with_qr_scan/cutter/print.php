<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Проверяем, имеются ли незакрытые нарезки

// Текущее время
$current_date_time = date("dmYHis");

// Проверяем, имеются ли незакрытые нарезки
include '_check_rolls.php';
$opened_roll = CheckOpenedRolls($user_id);
$cutting_id = $opened_roll['id'];
$last_source = $opened_roll['last_source'];
$streams_count = $opened_roll['streams_count'];
$last_wind = $opened_roll['last_wind'];

// Если нет незакрытой нарезки, переходим на первую страницу
if(empty($cutting_id)) {
    header("Location: ".APPLICATION.'/cutter/');
}
// Если нет исходного ролика, переходим на страницу создания исходного ролика
elseif(empty ($last_source)) {
    header("Location: source.php");
}
// Если нет ручьёв, переходим на страницу "Как режем"
elseif(empty ($streams_count)) {
    header("Location: streams.php");
}
// Если есть исходные ролики и ручьи, но у последнего исходного ролика нет намоток, переходим на страницу создания намотки
elseif (empty ($last_wind)) {
    header("Location: wind.php");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include '_head.php';
        ?>
    </head>
    <body>
        <?php
        $class_attr = " class='d-none'";
        if(isset($_COOKIE['cutting_wind_id_'.$last_wind]) && $_COOKIE['cutting_wind_id_'.$last_wind] == 1) {
            $class_attr = "";
        }
        ?>
        <div style="font-size: 50px; float: left;">
            <a href="javascript:void(0);" id="sharelink"><i class="fas fa-share-alt"></i></a>
        </div>
        <div id="new_wind_link"<?=$class_attr ?> style="float: right;">
            <a href="wind.php" class="btn btn-dark" style="font-size: 20px;">Новая намотка</a>
        </div>
        <div style="clear: both;" />
    
        <?php
        // Получение данных
        $sql = "select r.id, DATE_FORMAT(r.date, '%d.%m.%Y') date, r.storekeeper_id, u.last_name, u.first_name, r.supplier_id, s.name supplier, r.id_from_supplier, "
                . "r.film_variation_id, f.name film, r.width, fv.thickness, fv.weight, r.length, "
                . "r.net_weight, r.cell, "
                . "(select rs.name status from roll_status_history rsh left join roll_status rs on rsh.status_id = rs.id where rsh.roll_id = r.id order by rsh.id desc limit 0, 1) status, "
                . "r.comment "
                . "from roll r "
                . "left join user u on r.storekeeper_id = u.id "
                . "left join supplier s on r.supplier_id = s.id "
                . "left join film_variation fv on r.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "where r.cutting_wind_id=$last_wind";
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
        $film_variation_id = $row['film_variation_id'];
        $film = $row['film'];
        $width = $row['width'];
        $thickness = $row['thickness'];
        $ud_ves = $row['weight'];
        $length = $row['length'];
        $net_weight = $row['net_weight'];
        $cell = $row['cell'];
        $status = $row['status'];
        $comment = $row['comment'];
                
        $current_roll++;
        ?>
        <table class="table table-bordered compact" style="writing-mode: vertical-rl;">
            <tbody>
                <tr>
                    <td colspan="2" class="font-weight-bold font-italic text-left">ООО &laquo;Принт-дизайн&raquo;</td>
                    <td class="text-center text-nowrap">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></td>
                </tr>
                <tr>
                    <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                    <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                    <td rowspan="6" class="qr">
                        <?php
                        include_once '../qr/qrlib.php';
                        $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                        $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/roll/roll.php?id='.$id;
                        $filename = "../temp/".$current_roll."_".$current_date_time.".png";
                            
                        do {
                            QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);
                        } while (!file_exists($filename));
                        ?>
                        <img src='<?=$filename ?>' style="width: 200px; height: 200px;" />
                        <br /><br />
                        <div class="text-nowrap">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></div>
                    </td>
                </tr>
                <tr>
                    <td class="text-nowrap">ID от поставщика<br /><span class="text-nowrap font-weight-bold"><?=$id_from_supplier ?></span></td>
                    <td class="text-nowrap">Толщина, уд.вес<br /><span class="text-nowrap font-weight-bold"><?=$thickness ?> мкм,<br /> <?=$ud_ves ?> г/м<sup style="top: 2px;">2</sup></span></td>
                </tr>
                <tr>
                    <td>Кладовщик<br /><strong><?=$storekeeper ?></strong></td>
                    <td>Длина<br /><strong><?=$length ?> м</strong></td>
                </tr>
                <tr>
                    <td class="text-nowrap">Марка пленки<br /><strong><?=$film ?></strong></td>
                    <td class="text-nowrap">Масса нетто<br /><strong><?=$net_weight ?> кг</strong></td>
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
        <script>
            $(document).ready(function (){
                let myShareData = {
                    url: '<?=APPLICATION ?>/cutter/_print.php?cutting_wind_id=<?=$last_wind ?>'
                };
        
                const sharelink = document.getElementById("sharelink");
                sharelink.addEventListener('click', () => {
                    navigator.share(myShareData)
                });
        
                setTimeout(function() { 
                    document.getElementById('new_wind_link').removeAttribute('class');
                    document.cookie = '<?='cutting_wind_id_'.$last_wind ?>=1; Path=/;';
                }, 30000);
            });
        </script>
    </body>
</html>