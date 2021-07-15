<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение streams_count, возвращаемся на первую страницу
$streams_count = filter_input(INPUT_GET, 'streams_count');
if(empty($streams_count)) {
    header('Location: '.APPLICATION.'/cutter/');
}

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$length_valid = '';
$length_message = "Число, макс. 30000";
$radius_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $length = filter_input(INPUT_POST, 'length');
    if(empty($length)) {
        $length_valid = ISINVALID;
        $form_valid = false;
    }
    
    $length = preg_replace("/\D/", "", filter_input(INPUT_POST, 'length'));
    if($length > 30000) {
        $length_valid = ISINVALID;
        $form_valid = false;
    }
    
    $radius = filter_input(INPUT_POST, 'radius');
    if(empty($radius)) {
        $radius_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($radius > 999) {
        $radius_valid = ISINVALID;
        $form_valid = false;
    }
    
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
    $thickness = filter_input(INPUT_POST, 'thickness');
    $width = filter_input(INPUT_POST, 'width');
    
    $id_from_supplier = "Из раскроя";
    $user_id = GetUserId();
    
    // Определяем удельный вес
    $ud_ves = null;
    $sql = "select weight from film_brand_variation where film_brand_id=$film_brand_id and thickness=$thickness";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $ud_ves = $row[0];
    }
    
    $net_weight = floatval($ud_ves) * floatval($length) * floatval($width) / 1000.0 / 1000.0;
    $cell = "Цех";
    $comment = "";
    
    // Данные, вычисленные по радиусу
    $normal_length = filter_input(INPUT_POST, 'normal_length');
    $net_weight = filter_input(INPUT_POST, 'net_weight');
    
    // Валидация длины
    $max_length = $normal_length * 1.2;
    $min_length = $normal_length * 0.8;
    
    if($length > $max_length || $length < $min_length) {
        $length_valid = ISINVALID;
        $length_message = "Длина не соответствует радиусу";
        $form_valid = false;
    }
    
    if($form_valid) {
        $sql = "insert into cut (supplier_id, film_brand_id, thickness, width) values($supplier_id, $film_brand_id, $thickness, $width)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $cut_id = $executer->insert_id;
        
        for($i=1; $i<=19; $i++) {
            if(key_exists('stream_'.$i, $_POST) && empty($error_message)) {
                $width = filter_input(INPUT_POST, 'stream_'.$i);
                $sql = "insert into cut_stream (cut_id, width) values($cut_id, $width)";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
        
        if(empty($error_message)) {
            $sql = "insert into cut_wind (cut_id, length, radius) values($cut_id, $length, $radius)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            $cut_wind_id = $executer->insert_id;
            
            // Создание рулона на каждый ручей
            for($i=1; $i<19; $i++) {
                if(key_exists('stream_'.$i, $_POST) && empty($error_message)) {
                    $width = filter_input(INPUT_POST, 'stream_'.$i);
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
            
            if(empty($error_message)) {
                header('Location: '.APPLICATION.'/cutter/print.php?cut_wind_id='.$cut_wind_id);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '../include/style_mobile.php';
        ?>
    </head>
    <body>
        <form method="post" action="cut.php" id="back_form">
            <?php foreach ($_REQUEST as $key=>$value): ?>
            <input type="hidden" name="<?=$key ?>" value="<?=$value ?>" />
            <?php endforeach; ?>
            <div class="container-fluid header">
                <nav class="navbar navbar-expand-sm justify-content-start">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="javascript: $('form#back_form').submit();"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </form>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1>Нарезка / <?=date('d.m.Y') ?></h1>
            <p class="mb-3 mt-3" style="font-size: xx-large;">Намотка 1</p>
            <?php
            for($i=1; $i<=19; $i++):
            if(isset($_REQUEST['stream_'.$i])):
            ?>
            <p>Ручей <?=$i ?> - <?=$_REQUEST['stream_'.$i] ?> мм</p>
            <?php
            endif;
            endfor;
            ?>
            <form method="post" class="mt-3">
                <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$_REQUEST['supplier_id'] ?>" />
                <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$_REQUEST['film_brand_id'] ?>" />
                <input type="hidden" id="thickness" name="thickness" value="<?=$_REQUEST['thickness'] ?>" />
                <input type="hidden" id="width" name="width" value="<?=$_REQUEST['width'] ?>" />
                <input type="hidden" id="streams_count" name="streams_count" value="<?=$_REQUEST['streams_count'] ?>" />
                <input type="hidden" id="spool" name="spool" value="76" />
                <input type="hidden" id="net_weight" name="net_weight" />
                <input type="hidden" id="normal_length" name="normal_length" />
                <?php
                for($i=1; $i<=19; $i++):
                if(key_exists('stream_'.$i, $_REQUEST)):
                ?>
                <input type="hidden" name="stream_<?=$i ?>" value="<?=$_REQUEST['stream_'.$i] ?>" />
                <?php
                endif;
                endfor;
                ?>
                <div class="form-group">
                    <label for="length">Длина, м</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only int-format<?=$length_valid ?>" id="length" name="length" value="<?= filter_input(INPUT_POST, 'length') ?>" required="required" />
                        <div class="input-group-append"><span class="input-group-text">м</span></div>
                        <div class="invalid-feedback"><?=$length_message ?></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="radius">Радиус от вала, мм</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only<?=$radius_valid ?>" id="radius" name="radius" value="<?= filter_input(INPUT_POST, 'radius') ?>" required="required" />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback">Число, макс. 999</div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-outline-dark form-control mt-3" id="next-submit" name="next-submit">След. намотка</button>
                </div>
            </form>
            <?php
            include '../include/footer.php';
            include '../include/footer_mobile.php';
            ?>
            <script src="<?=APPLICATION ?>/js/calculation.js"></script>
            <script>
                // В поле "Длина" ограничиваем значения: целые числа от 1 до 30000
                $('#length').keydown(function(e) {
                    if(!KeyDownLimitIntValue($(e.target), e, 30000)) {
                        $(this).addClass('is-invalid');
                    
                        return false;
                    }
                    else {
                        $(this).removeClass('is-invalid');
                    }
                });
    
                $("#length").change(function(){
                    val = $(this).val().replace(' ', '');
                    iVal = parseInt(val);
                    
                    if(iVal > 30000) {
                        $(this).addClass('is-invalid');
                    }
                    else {
                        $(this).removeClass('is-invalid');
                    }
                
                    ChangeLimitIntValue($(this), 30000);
                    IntFormat($(this));
                });
                
                // В поле "Радиус" ограничиваем значения: целые числа от 1 до 999
                $('#radius').keydown(function(e) {
                    if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                        $(this).addClass('is-invalid');
                    
                        return false;
                    }
                    else {
                        $(this).removeClass('is-invalid');
                    }
                });
    
                $("#radius").change(function(){
                    if($(this).val() > 999) {
                        $(this).addClass('is-invalid');
                    }
                    else {
                        $(this).removeClass('is-invalid');
                    }
                
                    ChangeLimitIntValue($(this), 999);
                });
                
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
                
                    if(!isNaN(spool) && !isNaN(thickness) && !isNaN(radius) && !isNaN(width) 
                            && spool != '' && thickness != '' && radius != '' && width != '') {
                        density = films.get(parseInt($('#film_brand_id').val())).get(parseInt(thickness));
                        
                        result = GetFilmLengthWeightBySpoolThicknessRadiusWidth(spool, thickness, radius, width, density);
                        
                        $('#normal_length').val(result.length.toFixed(2));
                        $('#net_weight').val(result.weight.toFixed(2));
                    }
                }
            
                $(document).ready(CalculateByRadius);
            
                // Рассчитываем ширину и массу плёнки при изменении значений каждого поля, участвующего в вычислении
                $('#radius').keypress(CalculateByRadius);
            
                $('#radius').keyup(CalculateByRadius);
            
                $('#radius').change(CalculateByRadius);
            </script>
        </div>
    </body>
</html>
