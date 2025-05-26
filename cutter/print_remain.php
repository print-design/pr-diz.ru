<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_CUTTER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Текущее время
$current_date_time = date("dmYHis");

// Находим id остаточного ролика последней закрытой нарезки данного пользователя
$cutting_id = null;
$id = null;

$sql = "select id, remain from cutting where cutter_id=$user_id and date is not null and remain is not null order by id desc limit 1";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $cutting_id = $row['id'];
    $id = $row['remain'];
}

// Получение данных
$sql = "select DATE_FORMAT(r.date, '%d.%m.%Y') date, r.storekeeper_id, u.last_name, u.first_name, r.supplier_id, s.name supplier, "
        . "r.film_variation_id, f.name film, r.width, fv.thickness, fv.weight, r.length, "
        . "r.net_weight, "
        . "(select status_id from roll_status_history where roll_id = r.id order by id desc limit 0, 1) status_id, "
        . "r.comment "
        . "from roll r "
        . "left join user u on r.storekeeper_id = u.id "
        . "left join supplier s on r.supplier_id = s.id "
        . "left join film_variation fv on r.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "where r.id=$id";

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
$status = ROLL_STATUS_NAMES[$row['status_id']];
$comment = $row['comment'];
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
        if(isset($_COOKIE['remain_id'.$id]) && $_COOKIE['remain_id'.$id] == 1) {
            $class_attr = "";
        }
        ?>
        <div class="screen-only d-flex justify-content-between">
            <div class="screen-only" style="font-size: 50px; z-index: 10;">
                <a href="?print=1" class="btn btn-dark"><i class="fa fa-print"></i></a>
            </div>
            <div id="new_wind_link"<?=$class_attr ?>>
                <a class="btn btn-dark" href="finish.php?id=<?=$cutting_id ?>" style="font-size: 20px; z-index: 10;">Закрыть заявку</a>
            </div>
        </div>
        <div style="clear: both;" />

        <div style="position: absolute; top: 0px;">
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
            
        <div style="position: absolute; top: 270px;">
            <div style="border-bottom: solid 1px lightgray;">
                <div class="title1"><strong>ООО &laquo;Принт-дизайн&raquo;</strong></div>
                <div class="title2">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?= $date ?></div>
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
                        <td><strong><?=$thickness ?> мкм,<br /> <?=$ud_ves ?> г/м<sup style="top: 2px;">2</sup></strong></td>
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
        <script>
            $(document).ready(function (){
                setTimeout(function() { 
                    document.getElementById('new_wind_link').removeAttribute('class');
                    document.cookie = '<?='remain_id'.$id ?>=1; Path=/;';
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