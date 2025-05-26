<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_CUTTER]))) {
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
        <style>
            .title1 {
                font-size: 12px;
                font-style: italic;
                margin-left: 5px;
            }
            
            .title2 {
                font-size: 12px;
                margin-left: 5px;
            }
            
            table.label tr td {
                height: 16px;
                font-size: 10px;
                padding-left: 5px;
            }
            
            @media print {
                .screen-only, #new_wind_link {
                    display: none;
                }
            }
            
            @media screen {
                .print-only {
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        <?php
        $class_attr = " class='d-none'";
        if(isset($_COOKIE['cutting_wind_id_'.$last_wind]) && $_COOKIE['cutting_wind_id_'.$last_wind] == 1) {
            $class_attr = "";
        }
        ?>
        <div class="screen-only d-flex justify-content-between">
            <div class="screen-only" style="font-size: 50px; z-index: 10;">
                <a href="?print=1" class="btn btn-dark"><i class="fa fa-print"></i></a>
            </div>
            <div id="new_wind_link"<?=$class_attr ?>>
                <a href="wind.php" class="btn btn-dark" style="font-size: 20px; z-index: 10;">Новая намотка</a>
            </div>
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
        $sticker_top = 0;
        
        switch($current_roll) {
            case 1:
                $sticker_top = 0;
                break;
            
            case 2:
                $sticker_top = 255;
                break;
            
            // Остальные расстояния надо проверить
            
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
            
            default :
                break;
        }
        ?>
        <div style="position: absolute; top: <?=$sticker_top ?>px;">
            <div style="border-bottom: solid 1px lightgray;">
                <div class="title1"><strong>ООО &laquo;Принт-дизайн&raquo;</strong></div>
                <div class="title2">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></div>
            </div>
            <table class="label">
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
                        <td><strong><?=$thickness ?> мкм, <?=$ud_ves ?> г/м<sup style="top: 2px;">2</sup></strong></td>
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
        <script>
            $(document).ready(function (){
                setTimeout(function() { 
                    document.getElementById('new_wind_link').removeAttribute('class');
                    document.cookie = '<?='cutting_wind_id_'.$last_wind ?>=1; Path=/;';
                }, 30000);
            });
            
            <?php if(filter_input(INPUT_GET, 'print') == 1): ?>
                var css = '@page { size: portrait; margin: 2mm; }',
                        head = document.head || document.getElementsByTagName('head')[0],
                        style = document.createElement('style');
            
                style.type = 'text/css';
                style.media = 'print';
            
                if (style.styleSheet){
                    style.styleSheet.cssText = css;
                } else {
                    style.appendChild(document.createTextNode(css));
                }
            
                head.appendChild(style);
            
                window.print();
            <?php endif; ?>
        </script>
    </body>
</html>