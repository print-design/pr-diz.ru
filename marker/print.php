<?php
include_once '../include/topscripts.php';

require '../vendor/autoload.php';
use chillerlan\QRCode\QRCode;

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MARKER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Текущее время
$current_date_time = date("dmYHis");

// ID ролла
$roll_id = 0;
$sql = "select id from roll where storekeeper_id = $user_id order by id desc limit 1";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $roll_id = $row['id'];
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
        <div style="font-size: 50px; float: left;">
            <a href="javascript: void(0);" id="sharelink"><i class="fas fa-share-alt"></i></a>
        </div>
        <div id="finish_link" style="float: right;">
            <a href="<?=APPLICATION ?>/marker/roll.php" class="btn btn-dark" style="font-size: 20px;">Завершить</a>
        </div>
        <div style="clear: both;" />
    
        <?php
        // Получение данных
        $sql = "select r.id, DATE_FORMAT(r.date, '%d.%m.%Y') date, r.storekeeper_id, u.last_name, u.first_name, r.supplier_id, s.name supplier, "
                . "r.film_variation_id, f.name film, r.width, fv.thickness, fv.weight, r.length, "
                . "r.net_weight, "
                . "(select status_id from roll_status_history where roll_id = r.id order by id desc limit 0, 1) status_id, "
                . "r.comment "
                . "from roll r "
                . "left join user u on r.storekeeper_id = u.id "
                . "left join supplier s on r.supplier_id = s.id "
                . "left join film_variation fv on r.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "where r.id=$roll_id";
        $current_roll = 0;
        $fetcher = new Fetcher($sql);

        while($row = $fetcher->Fetch()):
        $id = $row['id'];
        $date = $row['date'];
        $storekeeper_id = $row['storekeeper_id'];
        $storekeeper = $row['last_name'].' '.$row['first_name'];
        $supplier_id = $row['supplier_id'];
        $supplier = $row['supplier'];
        $film_variation_id = $row['film_variation_id'];
        $film = $row['film'];
        $width = $row['width'];
        $thickness = $row['thickness'];
        $ud_ves = $row['weight'];
        $length = $row['length'];
        $net_weight = $row['net_weight'];
        $status = ROLL_STATUS_NAMES[$row['status_id']];
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
                        $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/roll/roll.php?id='.$id;
                        $qrcode = (new QRCode)->render($data);
                        ?>
                        <img src="<?=$qrcode ?>" alt="QR Code" style="height: 200px; width: 200px;" />
                        <br /><br />
                        <div class="text-nowrap">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></div>
                    </td>
                </tr>
                <tr>
                    <td class="pb-5">Марка пленки<br /><strong><?=$film ?></strong></td>
                    <td class="text-nowrap pb-5">Толщина, уд.вес<br /><span class="text-nowrap font-weight-bold"><?=$thickness ?> мкм,<br /> <?=$ud_ves ?> г/м<sup style="top: 2px;">2</sup></span></td>
                </tr>
                <tr>
                    <td class="text-nowrap pb-5">Кладовщик<br /><strong><?=$storekeeper ?></strong></td>
                    <td class="text-nowrap pb-5">Длина<br /><strong><?=$length ?> м</strong></td>
                </tr>
                <tr>
                    <td class="text-nowrap pb-5">Статус<br /><strong><?=$status ?></strong></td>
                    <td class="text-nowrap pb-5">Масса нетто<br /><strong><?=$net_weight ?> кг</strong></td>
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
                    <td colspan="2" class="font-weight-bold font-italic text-left">ООО &laquo;Принт-дизайн&raquo;</td>
                    <td class="text-center text-nowrap">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></td>
                </tr>
                <tr>
                    <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                    <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                    <td rowspan="6" class="qr">
                        <?php
                        $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/roll/roll.php?id='.$id;
                        $qrcode = (new QRCode)->render($data);
                        ?>
                        <img src="<?=$qrcode ?>" alt="QR Code" style="height: 200px; width: 200px;" />
                        <br /><br />
                        <div class="text-nowrap">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></div>
                    </td>
                </tr>
                <tr>
                    <td class="pb-5">Марка пленки<br /><strong><?=$film ?></strong></td>
                    <td class="text-nowrap pb-5">Толщина, уд.вес<br /><span class="text-nowrap font-weight-bold"><?=$thickness ?> мкм,<br /> <?=$ud_ves ?> г/м<sup style="top: 2px;">2</sup></span></td>
                </tr>
                <tr>
                    <td class="text-nowrap pb-5">Кладовщик<br /><strong><?=$storekeeper ?></strong></td>
                    <td class="text-nowrap pb-5">Длина<br /><strong><?=$length ?> м</strong></td>
                </tr>
                <tr>
                    <td class="text-nowrap pb-5">Статус<br /><strong><?=$status ?></strong></td>
                    <td class="text-nowrap pb-5">Масса нетто<br /><strong><?=$net_weight ?> кг</strong></td>
                </tr>
                <tr>
                    <td colspan="2" style="white-space: normal;">Комментарий<br /><strong><?= $comment ?></strong></td>
                </tr>
            </tbody>
        </table>
            <?php endwhile; ?>
        <script>
            $(document).ready(function (){
                let myShareData = {
                    url: '<?=APPLICATION ?>/marker/_print.php?roll_id=<?=$roll_id ?>'
                };
        
                const sharelink = document.getElementById("sharelink");
                sharelink.addEventListener('click', () => {
                    navigator.share(myShareData)
                });
            });
        </script>
    </body>
</html>