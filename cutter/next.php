<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение cut_id, возвращаемся на первую страницу
$cut_id = $_REQUEST['cut_id'];
if(empty($cut_id)) {
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
    
    $cut_id = filter_input(INPUT_POST, 'cut_id');
    $id_from_supplier = "Из раскроя";
    $user_id = GetUserId();
    
    $supplier_id = 0;
    $film_brand_id = 0;
    $thickness = 0;
    $width = 0;
    $ud_ves = 0;
    
    $sql = "select c.supplier_id, c.film_brand_id, c.thickness, c.width, "
            . "(select weight from film_brand_variation where film_brand_id = c.film_brand_id and thickness = c.thickness) ud_ves "
            . "from cut c "
            . "where c.id = $cut_id";
    $fetcher = new Fetcher($sql);
    
    if($row = $fetcher->Fetch()) {
        $supplier_id = $row['supplier_id'];
        $film_brand_id = $row['film_brand_id'];
        $thickness = $row['thickness'];
        $width = $row['width'];
        $ud_ves = $row['ud_ves'];
    }
    
    $net_weight = floatval($ud_ves) * floatval($length) * floatval($width) / 1000.0 / 1000.0;
    $cell = "Цех";
    $comment = '';
    
    // Валидация длины: длина+-20% от (0,15*R*R+11,3961*R-176,4427)/(толщина пленки/20)
    $normal_length = (0.15 * floatval($radius) * floatval($radius) + 11.3961 * floatval($radius) - 176.4427) / (floatval($thickness) / 20.0);
    $max_length = $normal_length * 1.2;
    $min_length = $normal_length * 0.8;
    
    if($length > $max_length || $length < $min_length) {
        $length_valid = ISINVALID;
        $length_message = "Длина не соответствует радиусу";
        $form_valid = false;
    }
       
    if($form_valid) {
        $sql = "insert into cut_wind (cut_id, length, radius) values($cut_id, $length, $radius)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $cut_wind_id = $executer->insert_id;
        
        // Создание рулона на каждый ручей
        for($i=1; $i<19; $i++) {
            if(key_exists('stream_'.$i, $_POST) && empty($error_message)) {
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

// Получение объекта
$date = '';
$winds_count = 0;
$sql = "select DATE_FORMAT(c.date, '%d.%m.%Y') date, (select count(id) from cut_wind where cut_id = c.id) winds_count from cut c where c.id=$cut_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $winds_count = $row['winds_count'];
}

$sql = "select width from cut_stream where cut_id=$cut_id order by id";
$fetcher = new Fetcher($sql);
$i = 0;
while ($row = $fetcher->Fetch()) {
    $stream = 'stream_'.++$i;
    $$stream = $row['width'];
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
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start"></nav>
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
                <input type="hidden" name="cut_id" value="<?=$cut_id ?>" />
                <?php
                for($i=1; $i<=19; $i++):
                $stream = 'stream_'.$i;
                if(isset($$stream)):
                ?>
                <input type="hidden" name="stream_<?=$i ?>" value="<?=$$stream ?>" />
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
                <div class="form-group">
                    <button type="button" class="btn btn-dark form-control mt-3" onclick="javascript: window.location.href = '<?=APPLICATION ?>/cutter/close.php?cut_id=<?=$cut_id ?>';">Заявка выполнена</button>
                </div>
            </form>
            <?php
            include '../include/footer.php';
            include '../include/footer_mobile.php';
            ?>
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
            </script>
        </div>
    </body>
</html>
