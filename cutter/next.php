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
    
    $radius = filter_input(INPUT_POST, 'radius');
    if(empty($radius)) {
        $radius_valid = ISINVALID;
        $form_valid = false;
    }
    
    $cut_id = filter_input(INPUT_POST, 'cut_id');
       
    if($form_valid) {
        $length = preg_replace("/\D/", "", filter_input(INPUT_POST, 'length'));
        
        $sql = "insert into cut_wind (cut_id, length, radius) values($cut_id, $length, $radius)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/cutter/next.php?cut_id='.$cut_id);
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
            <h1>Нарезка 1 / <?=$date ?></h1>
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
                <input type="hidden" id="cut_id" name="cut_id" value="<?=$cut_id ?>" />
                <div class="form-group">
                    <label for="length">Длина, м</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only int-format<?=$length_valid ?>" id="length" name="length" value="<?= filter_input(INPUT_POST, 'length') ?>" required="required" />
                        <div class="input-group-append"><span class="input-group-text">м</span></div>
                        <div class="invalid-feedback">Длина обязательно</div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="radius">Радиус от вала, мм</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only<?=$radius_valid ?>" id="radius" name="radius" value="<?= filter_input(INPUT_POST, 'radius') ?>" required="required" />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback">Радиус от вала обязательно</div>
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
