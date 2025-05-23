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
            body {
                font-size: 1rem;
            }
            .title1 {
                font-size: x-large;
                font-style: italic;
                margin-bottom: 10px;
                margin-left: 5px;
            }
            
            .title2 {
                font-size: large;
                margin-left: 5px;
            }
            
            table.label {
                margin-bottom: 2rem;
            }
            
            table.label tr td {
                padding: 5px;
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
        <div style="font-size: 50px; float: left;">
            <a href="javascript:void(0);" id="sharelink"><i class="fas fa-share-alt"></i></a>
        </div>
        <div id="new_wind_link"<?=$class_attr ?> style="float: right;">
            <a href="wind.php" class="btn btn-dark" style="font-size: 20px;">Новая намотка</a>
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
        ?>
        <div class="title1"><strong>ООО &laquo;Принт-дизайн&raquo;</strong></div>
        <div class="title2">Рулон <span class="font-weight-bold"><?="Р".$id ?></span> от <?=$date ?></div>
        <hr />
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
        <?php endwhile; ?>
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