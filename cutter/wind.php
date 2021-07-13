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
            $length = preg_replace("/\D/", "", filter_input(INPUT_POST, 'length'));
                    
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
            <h1>Нарезка 1 / <?=date('d.m.Y') ?></h1>
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
                <input type="hidden" name="supplier_id" value="<?=$_REQUEST['supplier_id'] ?>" />
                <input type="hidden" name="film_brand_id" value="<?=$_REQUEST['film_brand_id'] ?>" />
                <input type="hidden" name="thickness" value="<?=$_REQUEST['thickness'] ?>" />
                <input type="hidden" name="width" value="<?=$_REQUEST['width'] ?>" />
                <input type="hidden" name="streams_count" value="<?=$_REQUEST['streams_count'] ?>" />
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
                        <div class="invalid-feedback">Число, макс. 30000</div>
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
        </div>
    </body>
</html>
