<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$user_id = GetUserId();
$sql = "update user set request_uri='$request_uri' where id=$user_id";
$error_message = (new Executer($sql))->error;

if(!empty($error_message)) {
    exit($error_message);
}

$id = filter_input(INPUT_GET, 'id');
$cut_id = filter_input(INPUT_GET, 'cut_id');

// Получение данных
$sql = "select DATE_FORMAT(r.date, '%d.%m.%Y') date, r.storekeeper_id, u.last_name, u.first_name, r.supplier_id, s.name supplier, r.id_from_supplier, "
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

// Текущее время
$current_date_time = date("dmYHis");

$class_attr = " class='d-none'";
if(isset($_COOKIE['remain_id'.$id]) && $_COOKIE['remain_id'.$id] == 1) {
    $class_attr = "";
}
?>
<div style="font-size: 50px; float: left;">
    <a href="javascript:void(0);" id="sharelink"><i class="fas fa-share-alt"></i></a>
</div>
<div id="new_wind_link"<?=$class_attr ?> style="float: right;">
    <button id="print_submit" type="button" class="btn btn-dark" style="font-size: 20px;">Закрыть заявку</button>
</div>

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
                $filename = "../temp/$current_date_time.png";
                            
                do {
                    QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);
                } while (!file_exists($filename));
                ?>
                <img src='<?=$filename ?>' style='height: 200px; width: 200px;' />
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
            <td class="text-nowrap">Марка пленки<br /><strong><?=$film_brand ?></strong></td>
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
$sticker_top = 1700;
?>
<table class="table table-bordered compact" style="writing-mode: vertical-rl;">
    <tbody>
        <tr>
            <td colspan="2" class="font-weight-bold font-italic text-center">ООО &laquo;Принт-дизайн&raquo;</td>
            <td class="text-center text-nowrap">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?= $date ?></td>
        </tr>
        <tr>
            <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
            <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
            <td rowspan="6" class="qr">
                <?php
                include_once '../qr/qrlib.php';
                $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/roll/roll.php?id='.$id;
                $filename = "../temp/$current_date_time.png";
                            
                do {
                    QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);
                } while (!file_exists($filename));
                ?>
                <img src='<?=$filename ?>' style='height: 200px; width: 200px;' />
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
    <?php
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
                !is_dir($file)) {
            unlink("../temp/$file");
        }
    }
?>
<script>
    $(document).ready(function (){
        let shareData = {
            url: '<?=APPLICATION ?>/cutter/print_remain.php?id=<?=$id ?>'
        }
        
        const sharelink = document.getElementById("sharelink");
        sharelink.addEventListener('click', () => {
            navigator.share(shareData)
        });
        
        setTimeout(function() { 
            document.getElementById('new_wind_link').removeAttribute('class');
            document.cookie = '<?='remain_id'.$id ?>=1; Path=/;';
        }, 30000);
    });
    
    function Submit() {
        OpenAjaxPage("_finish.php?cut_id=<?=$cut_id ?>");
        submit = true;
    }
    
    $('#print_submit').click(function() {
        $.ajax({ url: "_check_db_uri.php?uri=<?= urlencode($request_uri) ?>" })
                .done(function(data) {
                    if(data == "OK") {
                        Submit();  
                    }
                    else {
                        OpenAjaxPage(data);
                    }
                })
                .fail(function() {
                    alert('Ошибка при переходе на страницу.');
                });
    });
</script>