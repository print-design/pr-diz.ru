<?php
include '../include/topscripts.php';

require '../vendor/autoload.php';
use chillerlan\QRCode\QRCode;

// Если не задано значение id, перенаправляем на список
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/pallet/');
}

// Получение данных
$sql = "select DATE_FORMAT(p.date, '%d.%m.%Y') date, p.storekeeper_id, u.last_name, u.first_name, p.supplier_id, s.name supplier, "
        . "p.film_variation_id, f.name film, p.width, fv.thickness, fv.weight, "
        . "(select sum(pr1.length) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) length, "
        . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) net_weight, "
        . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) rolls_number, "
        . "p.comment "
        . "from pallet p "
        . "left join user u on p.storekeeper_id = u.id "
        . "left join supplier s on p.supplier_id = s.id "
        . "left join film_variation fv on p.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "where p.id=$id";

$row = (new Fetcher($sql))->Fetch();
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
$rolls_number = $row['rolls_number'];
$comment = $row['comment'];
$status_id = ROLL_STATUS_FREE;
$status = ROLL_STATUS_NAMES[$status_id];

// Вертикальное положение бирки
$sticker_top = 0;

// Текущее время
$current_date_time = date("dmYHis");
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
        <div style="position: absolute; top: 0; left: 0; z-index: 2000;">
            <a href="<?=APPLICATION ?>/pallet/new.php"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
        </div>
        <div style="position: absolute; top: 850px; right: 770px; font-size: 150px; z-index: 2000;">
            <a href="javascript:void(0);" id="sharelink"><i class="fas fa-share-alt"></i></a>
        </div>
        <div class="w-100" style="height: 1400px; position: absolute; top: <?=$sticker_top ?>px;">
            <table class="table table-bordered print w-100" style="writing-mode: vertical-rl; margin-top: 30px;">
                <tbody>
                    <tr>
                        <td colspan="2" class="font-weight-bold font-italic text-center">ООО &laquo;Принт-дизайн&raquo;</td>
                        <td class="text-center text-nowrap" style="font-size: 60px;">Паллет <span class="font-weight-bold"><?="П".$id ?></span> от <?=$date ?></td>
                    </tr>
                    <tr>
                        <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                        <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                        <td rowspan="6" class="qr" style="height: 20%; white-space: normal;">
                            <?php
                            $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/pallet/pallet.php?id='.$id;
                            $qrcode = (new QRCode)->render($data);
                            ?>
                            <img src="<?=$qrcode ?>" alt="QR Code" style="height: 800px; width: 800px;" />
                            <br /><br />
                            <div class="text-nowrap" style="font-size: 60px;">Паллет <span class="font-weight-bold"><?="П".$id ?></span> от <?=$date ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="pb-5">Марка пленки<br /><strong><?=$film ?></strong></td>
                        <td class="text-nowrap pb-5">Толщина, уд.вес<br /><span class="text-nowrap font-weight-bold"><?=$thickness ?> мкм,<br /> <?=$ud_ves ?> г/м<sup style="top:2px;">2</sup></span></td>
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
                        <td class="text-nowrap pb-5">Количество рулонов<br /><strong><?=$rolls_number ?></strong></td>
                        <td class="text-nowrap pb-5"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="white-space: normal;">Комментарий<br /><strong><?= $comment ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php
        $sticker_top = 1700;
        ?>
        
        <div class="w-100" style="height: 1400px; position: absolute; top: <?=$sticker_top ?>px;">
            <table class="table table-bordered print w-100" style="writing-mode: vertical-rl;">
                <tbody>
                    <tr>
                        <td colspan="2" class="font-weight-bold font-italic text-center">ООО &laquo;Принт-дизайн&raquo;</td>
                        <td class="text-center text-nowrap" style="font-size: 60px;">Паллет <span class="font-weight-bold"><?="П".$id ?></span> от <?=$date ?></td>
                    </tr>
                    <tr>
                        <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                        <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                        <td rowspan="6" class="qr" style="height: 20%; white-space: normal;">
                            <?php
                            $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/pallet/pallet.php?id='.$id;
                            $qrcode = (new QRCode)->render($data);
                            ?>
                            <img src="<?=$qrcode ?>" alt="QR Code" style="height: 800px; width: 800px;" />
                            <br /><br />
                            <div class="text-nowrap" style="font-size: 60px;">Паллет <span class="font-weight-bold"><?="П".$id ?></span> от <?=$date ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="pb-5">Марка пленки<br /><strong><?=$film ?></strong></td>
                        <td class="text-nowrap pb-5">Толщина, уд.вес<br /><span class="text-nowrap font-weight-bold"><?=$thickness ?> мкм,<br /> <?=$ud_ves ?> г/м<sup style="top:2px;">2</sup></span></td>
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
                        <td class="text-nowrap pb-5">Количество рулонов<br /><strong><?=$rolls_number ?></strong></td>
                        <td class="text-nowrap pb-5"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="white-space: normal;">Комментарий<br /><strong><?= $comment ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php
        $sql = "select pr.id pallet_roll_id, pr.weight, pr.length, pr.ordinal, ifnull(prsh.status_id, ".ROLL_STATUS_FREE.") status_id "
                . "from pallet_roll pr left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                . "where pr.pallet_id = ". filter_input(INPUT_GET, 'id')." and (prsh.status_id is null or prsh.status_id = ".ROLL_STATUS_FREE.")";
        $pallet_rolls = (new Grabber($sql))->result;
        $current_roll = 0;
        
        foreach ($pallet_rolls as $pallet_roll):
        $pallet_roll_id = $pallet_roll['pallet_roll_id'];
        $weight = $pallet_roll['weight'];
        $length = $pallet_roll['length'];
        $ordinal = $pallet_roll['ordinal'];
        $status_id = $pallet_roll['status_id'];
        $status = ROLL_STATUS_NAMES[$status_id];
        
        $current_roll++;
        
        switch ($current_roll) {
            case 1:
                $sticker_top = 3300;
                break;
            
            case 2:
                $sticker_top = 4950;
                break;
                
            case 3:
                $sticker_top = 6600;
                break;
            
            case 4:
                $sticker_top = 8200;
                break;
                
            case 5:
                $sticker_top = 9900;
                break;
                
            case 6:
                $sticker_top = 11500;
                break;
            
            case 7:
                $sticker_top = 13150;
                break;
            
            case 8:
                $sticker_top = 14800;
                break;
            
            case 9:
                $sticker_top = 16450;
                break;
            
            case 10:
                $sticker_top = 18100;
                break;

            default:
                break;
        }
        ?>
        <div class="w-100" style="height: 1400px; position: absolute; top: <?=$sticker_top ?>px;">
            <table class="table table-bordered print w-100" style="writing-mode: vertical-rl;">
                <tbody>
                    <tr>
                        <td colspan="2" class="font-weight-bold font-italic text-center">ООО &laquo;Принт-дизайн&raquo;</td>
                        <td class="text-center text-nowrap" style="font-size: 60px;">Рулон <span class="font-weight-bold"><?="П".$id ?></span> от <?=$date ?></td>
                    </tr>
                    <tr>
                        <td>Поставщик<br /><strong><?=$supplier ?></strong></td>
                        <td>Ширина<br /><strong><?=$width ?> мм</strong></td>
                        <td rowspan="6" class="qr" style="height: 20%; white-space: normal;">
                            <?php
                            $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/pallet/roll.php?id='.$pallet_roll_id;
                            $qrcode = (new QRCode)->render($data);
                            ?>
                            <img src="<?=$qrcode ?>" alt="QR Code" style="height: 800px; width: 800px;" />
                            <br /><br />
                            <div class="text-nowrap" style="font-size: 60px;">Рулон <span class="font-weight-bold"><?="П".$id ?></span> от <?=$date ?></div>
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
                        <td class="text-nowrap pb-5">Масса нетто<br /><strong><?=$weight ?> кг</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="white-space: normal;">Комментарий<br /><strong><?= $comment ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
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