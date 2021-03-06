<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Проверяем, имеются ли незакрытые нарезки
include '_check_cuts.php';
CheckCuts($user_id);

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$length_valid = '';
$length_message = 'Обязательно, не более 30 000';
$radius_valid = '';
$radius_message = 'Обязательно, не более 999';

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $length = preg_replace("/\D/", "", filter_input(INPUT_POST, 'length'));
    if(empty($length) || intval($length) > 30000) {
        $length_valid = ISINVALID;
        $form_valid = false;
    }
    
    $radius = filter_input(INPUT_POST, 'radius');
    if(empty($radius) || intval($radius) > 999) {
        $radius_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Валидация длины
        $normal_length = filter_input(INPUT_POST, 'normal_length');
        $max_length = floatval($normal_length) * 1.2;
        $min_length = floatval($normal_length) * 0.8;
        $my_length = floatval($length);
        
        if($my_length > $max_length || $my_length < $min_length) {
            $length_valid = ISINVALID;
            $length_message = "Длина не соответствует радиусу";
            $radius_valid = ISINVALID;
            $radius_message = "Длина не соответствует радиусу";
            $form_valid = false;
        }
    }
    
    if($form_valid) {
        // Создание намотки
        $cut_id = filter_input(INPUT_POST, 'cut_id');
        $net_weight = filter_input(INPUT_POST, 'net_weight');
        $cell = "Цех";
        
        $sql = "insert into cut_wind (cut_id, length, radius) values($cut_id, $length, $radius)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $cut_wind_id = $executer->insert_id;
        
        // Получение данных о материале
        $supplier_id = 0;
        $film_brand_id = 0;
        $thickness = 0;
        
        if(empty($error_message)) {
            $sql = "select supplier_id, film_brand_id, thickness from cut where id=$cut_id";
            $fetcher = new Fetcher($sql);
            $error_message = $fetcher->error;
            
            if($row = $fetcher->Fetch()) {
                $supplier_id = $row['supplier_id'];
                $film_brand_id = $row['film_brand_id'];
                $thickness = $row['thickness'];
            }
        }
    
        // Создание рулона на каждый ручей
        $id_from_supplier = "Из раскроя";
        
        if(empty($error_message)) {
            for($i=1; $i<=19; $i++) {
                if(key_exists('stream_'.$i, $_POST)) {
                    $width = filter_input(INPUT_POST, 'stream_'.$i);
                    $comment = filter_input(INPUT_POST, 'comment_'.$i);
                    $net_weight = filter_input(INPUT_POST, 'net_weight_'.$i);
        
                    $sql = "insert into roll (supplier_id, id_from_supplier, film_brand_id, width, thickness, length, net_weight, cell, comment, storekeeper_id, cut_wind_id) "
                            . "values ($supplier_id, '$id_from_supplier', $film_brand_id, $width, $thickness, $length, $net_weight, '$cell', '$comment', '$user_id', $cut_wind_id)";
                    $executer = new Executer($sql);
                    $error_message = $executer->error;
                    $roll_id = $executer->insert_id;
    
                    if(empty($error_message)) {
                        $sql = "insert into roll_status_history (roll_id, status_id, user_id) values($roll_id, $free_status_id, $user_id)";
                        $executer = new Executer($sql);
                        $error_message = $executer->error;
                    }
                }
            }
        }
        
        // Переход на страницу печати рулонов
        if(empty($error_message)) {
            header("Location: print.php");
        }
    }
}

// Получение объекта
$cut_id = null;
$date = '';
$supplier_id = null;
$film_brand_id = null;
$thickness = null;
$width = null;
$winds_count = 0;
$sql = "select c.id, DATE_FORMAT(c.date, '%d.%m.%Y') date, c.supplier_id, c.film_brand_id, c.thickness, c.width, (select count(id) from cut_wind where cut_id = c.id) winds_count from cut c where c.cutter_id=$user_id and c.id not in (select cut_id from cut_source)";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $cut_id = $row['id'];
    $date = $row['date'];
    $supplier_id = $row['supplier_id'];
    $film_brand_id = $row['film_brand_id'];
    $thickness = $row['thickness'];
    $width = $row['width'];
    $winds_count = $row['winds_count'];
}

$sql = "select width, comment from cut_stream where cut_id=$cut_id order by id";
$fetcher = new Fetcher($sql);
$i = 0;
while ($row = $fetcher->Fetch()) {
    $stream = 'stream_'.++$i;
    $$stream = $row['width'];
    $comment = 'comment_'.$i;
    $$comment = $row['comment'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include '_head.php';
        include '_info.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" href="javascript: void(0);" data-toggle="modal" data-target="#infoModal"><img src="<?=APPLICATION ?>/images/icons/info.svg" /></a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1>Нарезка <?=$cut_id ?> / <?=$date ?></h1>
            <p class="mb-3 mt-3" style="font-size: xx-large;">Намотка <?=($winds_count + 1) ?></p>
                <?php
                for($i=1; $i<=19; $i++):
                    $stream = 'stream_'.$i;
                if(isset($$stream)):
                ?>
            <p>Ручей <?=$i ?> - <?=$$stream ?> мм</p>
                <?php
                endif;
                endfor;
                ?>
            <form method="post" class="mt-3">
                <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$supplier_id ?>" />
                <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand_id ?>" />
                <input type="hidden" id="thickness" name="thickness" value="<?=$thickness ?>" />
                <input type="hidden" id="width" name="width" value="<?=$width ?>" />
                <input type="hidden" id="spool" name="spool" value="76" />
                <input type="hidden" id="net_weight" name="net_weight" />
                <input type="hidden" id="normal_length" name="normal_length" />
                <input type="hidden" name="cut_id" value="<?=$cut_id ?>" />
                    <?php
                    for($i=1; $i<=19; $i++):
                    $stream = 'stream_'.$i;
                    $comment = 'comment_'.$i;
                    if(isset($$stream)):
                    ?>
                <input type="hidden" id="stream_<?=$i ?>" name="stream_<?=$i ?>" value="<?=$$stream ?>" />
                <input type="hidden" id="comment_<?=$i ?>" name="comment_<?=$i ?>" value="<?=$$comment ?>" />
                <input type="hidden" id="net_weight_<?=$i ?>" name="net_weight_<?=$i ?>" />
                    <?php
                    endif;
                    endfor;
                    ?>
                <div class="form-group">
                    <label for="length">Длина, м</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only int-format<?=$length_valid ?>" data-max="30000" id="length" name="length" value="<?= filter_input(INPUT_POST, 'length') ?>" required="required" autocomplete="off" />
                        <div class="input-group-append"><span class="input-group-text">м</span></div>
                        <div class="invalid-feedback invalid-length"><?=$length_message ?></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="radius">Радиус от вала, мм</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only<?=$radius_valid ?>" data-max="999" id="radius" name="radius" value="<?= filter_input(INPUT_POST, 'radius') ?>" required="required" autocomplete="off" />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback invalid-radius"><?=$radius_message ?></div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-outline-dark form-control mt-3" id="next-submit" name="next-submit">След. намотка</button>
                </div>
                <div class="form-group">
                    <a href="close.php" class="btn btn-dark form-control mt-3">Заявка выполнена</a>
                </div>
            </form>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Все марки плёнки с их вариациями
            var films = new Map();
            
            <?php
            $sql = "SELECT fbv.film_brand_id, fbv.thickness, fbv.weight FROM film_brand_variation fbv";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                echo "if(films.get(".$row['film_brand_id'].") == undefined) {\n";
                echo "films.set(".$row['film_brand_id'].", new Map());\n";
                echo "}\n";
                echo "films.get(".$row['film_brand_id'].").set(".$row['thickness'].", ".$row['weight'].");\n";
            }
            ?>
                
            // Расчёт длины и массы плёнки по шпуле, толщине, радиусу, ширине, удельному весу
            function CalculateByRadius() {
                $('#normal_length').val('');
                $('#net_weight').val('');
                
                film_brand_id = $('#film_brand_id').val();
                spool = $('#spool').val();
                thickness = $('#thickness').val();
                radius = $('#radius').val();
                width = $('#width').val();
                length = $('#length').val().replaceAll(/\D/g, '');
                
                if(!isNaN(spool) && !isNaN(thickness) && !isNaN(radius) && !isNaN(width) 
                        && spool != '' && thickness != '' && radius != '' && width != '') {
                    density = films.get(parseInt($('#film_brand_id').val())).get(parseInt(thickness));
                        
                    result = GetFilmLengthWeightBySpoolThicknessRadiusWidth(spool, thickness, radius, width, density);
                        
                    $('#normal_length').val(result.length.toFixed(2));
                    $('#net_weight').val(result.weight.toFixed(2));
                }
        
                for(i=1; i<=19; i++) {
                    if($('#stream_' + i).length > 0) {
                        width = $('#stream_' + i).val();
                
                        if(!isNaN(spool) && !isNaN(thickness) && !isNaN(radius) && !isNaN(width) 
                                && spool != '' && thickness != '' && radius != '' && width != '') {
                            density = films.get(parseInt($('#film_brand_id').val())).get(parseInt(thickness));
                            weight = GetFilmWeightByLengthWidth(length, width, density);
                            $('#net_weight_' + i).val(weight.toFixed(2));
                        }
                    }
                }
            }
            
            $(document).ready(CalculateByRadius);
            
            // Рассчитываем ширину и массу плёнки при изменении значений радиуса
            $('#radius').keypress(CalculateByRadius);
            
            $('#radius').keyup(CalculateByRadius);
            
            $('#radius').change(CalculateByRadius);
    
            $('#length').keypress(CalculateByRadius);
            
            $('#length').keyup(CalculateByRadius);
            
            $('#length').change(CalculateByRadius);
        </script>
    </body>
</html>