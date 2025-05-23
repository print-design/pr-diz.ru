<?php
include '../include/topscripts.php';

// Если не задано значение cutting_wind_id, перенаправляем на список
$cutting_wind_id = filter_input(INPUT_GET, 'cutting_wind_id');
if(empty($cutting_wind_id)) {
    header('Location: '.APPLICATION.'/cutter/');
}

// Текущее время
$current_date_time = date("dmYHis");

// Находим id раскроя
$cut_id = 0;
$sql = "select cut_id from cut_wind where id=$cutting_wind_id";
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
                font-size: 4rem;
                line-height: 4rem;
                white-space: pre-wrap;
                padding: 10px;
                vertical-align: top;
            }
        </style>
    </head>
    <body>
        <?php
        // Получение данных
        $sql = "select r.id, DATE_FORMAT(r.date, '%d.%m.%Y') date, r.storekeeper_id, u.last_name, u.first_name, r.supplier_id, s.name supplier, "
                . "r.film_variation_id, f.name film, r.width, fv.thickness, fv.weight , r.length, "
                . "r.net_weight, "
                . "(select status_id from roll_status_history where roll_id = r.id order by id desc limit 0, 1) status_id, "
                . "r.comment "
                . "from roll r "
                . "left join user u on r.storekeeper_id = u.id "
                . "left join supplier s on r.supplier_id = s.id "
                . "left join film_variation fv on r.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "where r.cutting_wind_id=$cutting_wind_id";
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

        // Вертикальное положение бирки
        $current_roll++;
        $sticker_top = 0;
        
        switch ($current_roll) {
            case 1:
                $sticker_top = 0;
                break;
            
            case 2:
                $sticker_top = 1515;
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
        <div style="position: absolute; top: <?=$sticker_top ?>px;">
            <p style="font-size: 4.2rem;" class="font-italic"><strong>ООО &laquo;Принт-дизайн&raquo;</strong></p>
            <p style="font-size: 4rem;">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></p>
            <hr />
            <table class="print">
                <tbody>
                    <tr>
                        <td>Поставщик</td>
                        <td><strong><?=$supplier ?></strong></td>
                    </tr>
                    <tr>
                        <td>Ширина</td>
                        <td><strong><?=$width ?> мм</strong></td>
                    </tr>
                    <tr>
                        <td>Марка пленки</td>
                        <td><strong><?=$film ?></strong></td>
                    </tr>
                    <tr>
                        <td>Толщина, уд.вес</td>
                        <td><span class="text-nowrap font-weight-bold"><?=$thickness ?> мкм, <?=$ud_ves ?> г/м<sup style="top: 2px;">2</sup></span></td>
                    </tr>
                    <tr>
                        <td>Кладовщик</td>
                        <td><strong><?=$storekeeper ?></strong></td>
                    </tr>
                    <tr>
                        <td>Длина</td>
                        <td><strong><?=$length ?> м</strong></td>
                    </tr>
                    <tr>
                        <td>Статус</td>
                        <td><strong><?=$status ?></strong></td>
                    </tr>
                    <tr>
                        <td>Масса нетто</td>
                        <td><strong><?=$net_weight ?> кг</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="white-space: normal;">Комментарий<br /><strong><?= $comment ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>        
        <?php endwhile; ?>
    </body>
</html>