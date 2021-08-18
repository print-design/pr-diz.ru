<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

include_once '_redirects.php';

// Параметры нарезаемого материала и количество ручьёв
$supplier_id = filter_input(INPUT_GET, 'supplier_id');
$film_brand_id = filter_input(INPUT_GET, 'film_brand_id');
$thickness = filter_input(INPUT_GET, 'thickness');
$width = filter_input(INPUT_GET, 'width');
$streams_count = filter_input(INPUT_GET, 'streams_count');

// Проверяем, чтобы были переданы все параметры материала и количество ручьёв
if(empty($supplier_id) || empty($film_brand_id) || empty($thickness) || empty($width) || empty($streams_count)) {
    header("Location: index.php");
}

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

// Текущий пользователь
$user_id = GetUserId();

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
        // Создание нарезки
        $sql = "insert into cut (supplier_id, film_brand_id, thickness, width, cutter_id) values($supplier_id, $film_brand_id, $thickness, $width, $user_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $cut_id = $executer->insert_id;
        
        // Создание ручьёв
        if(empty($error_message)) {
            for($i=1; $i<=19; $i++) {
                if(key_exists('stream_'.$i, $_GET)) {
                    $width = filter_input(INPUT_GET, 'stream_'.$i);
                    $sql = "insert into cut_stream (cut_id, width) values($cut_id, $width)";
                    $executer = new Executer($sql);
                    $error_message = $executer->error;
                }    
            }
        }
        
        // Создание намотки
        if(empty($error_message)) {
            $net_weight = filter_input(INPUT_GET, 'net_weight');
            $cell = "Цех";
            $comment = "";
            
            $sql = "insert into cut_wind (cut_id, length, radius) values($cut_id, $length, $radius)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            $cut_wind_id = $executer->insert_id;
        }
        
        // Создание рулона на каждый ручей
        if(empty($error_message)) {
            $id_from_supplier = "Из раскроя";
            
            for($i=1; $i<=19; $i++) {
                if(key_exists('stream_'.$i, $_POST)) {
                    $width = filter_input(INPUT_POST, 'stream_'.$i);
                    $net_weight = filter_input(INPUT_POST, 'net_weight_'.$i);
    
                    $sql = "insert into roll (supplier_id, id_from_supplier, film_brand_id, width, thickness, length, net_weight, cell, comment, storekeeper_id, cut_wind_id) "
                            . "values ($supplier_id, '$id_from_supplier', $film_brand_id, $width, $thickness, $length, $net_weight, '$cell', '$comment', '$user_id', $cut_wind_id)";
                    $executer = new Executer($sql);
                    $error_message = $executer->error;
                    $roll_id = $executer->insert_id;
                    
                    if(empty($error_message)) {
                        $sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($roll_id, $free_status_id, $user_id)";
                        $executer = new Executer($sql);
                        $error_message = $executer->error;
                    }
                }    
            }
        }
        
        // Переход на страницу печати рулонов
        if(empty($error_message)) {
            //
        }
    }
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
            <nav class="navbar navbar-expand-sm justify-content-between">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <?php
                        $backlink = "cut.php?supplier_id=$supplier_id&film_brand_id=$film_brand_id&thickness=$thickness&width=$width&streams_count=$streams_count";
                        for($i=1; $i<=19; $i++) {
                            if(!empty(filter_input(INPUT_GET, 'stream_'.$i))) {
                                $backlink .= '&stream_'.$i.'='.filter_input(INPUT_GET, 'stream_'.$i);
                            }
                        }
                        ?>
                        <a class="nav-link" href="<?=$backlink ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" href="javascript: void(0);" data-toggle="modal" data-target="#infoModal"><img src="<?=APPLICATION ?>/images/icons/info.svg" /></a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1>Нарезка / <?=date('d.m.Y') ?></h1>
            <p class="mb-3 mt-3" style="font-size: xx-large;">Намотка 1</p>
                <?php
                for($i=1; $i<=19; $i++):
                if(isset($_GET['stream_'.$i])):
                ?>
            <p>Ручей <?=$i ?> - <?=$_GET['stream_'.$i] ?> мм</p>
                <?php
                endif;
                endfor;
                ?>
            <form method="post" class="mt-3">
                <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$_GET['supplier_id'] ?>" />
                <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$_GET['film_brand_id'] ?>" />
                <input type="hidden" id="thickness" name="thickness" value="<?=$_GET['thickness'] ?>" />
                <input type="hidden" id="width" name="width" value="<?=$_GET['width'] ?>" />
                <input type="hidden" id="streams_count" name="streams_count" value="<?=$_GET['streams_count'] ?>" />
                <input type="hidden" id="spool" name="spool" value="76" />
                <input type="hidden" id="net_weight" name="net_weight" />
                <input type="hidden" id="normal_length" name="normal_length" />
                    <?php
                    for($i=1; $i<=19; $i++):
                    if(key_exists('stream_'.$i, $_GET)):
                    ?>
                <input type="hidden" id="stream_<?=$i ?>" name="stream_<?=$i ?>" value="<?=$_GET['stream_'.$i] ?>" />
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
                    <?php
                    $data_sources = "";
                    for($i=1; $i<=19; $i++) {
                        if(!empty(filter_input(INPUT_GET, 'stream_'.$i))) {
                            $data_sources .= " data-stream".$i."=".filter_input(INPUT_GET, 'stream_'.$i);
                        }
                    }
                    ?>
                    <button type="submit" class="btn btn-outline-dark form-control mt-3" id="next-submit" name="next-submit" data-supplier_id="<?= filter_input(INPUT_GET, 'supplier_id') ?>" data-film_brand_id="<?= filter_input(INPUT_GET, 'film_brand_id') ?>" data-thickness="<?= filter_input(INPUT_GET, 'thickness') ?>" data-width="<?= filter_input(INPUT_GET, 'width') ?>" data-streams-count="<?= filter_input(INPUT_GET, 'streams_count') ?>"<?=$data_sources ?>>След. намотка</button>
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