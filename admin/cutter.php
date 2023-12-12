<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Резка
$cutter_id = filter_input(INPUT_GET, 'cutter_id');

// Валидация формы
$form_valid = true;
$error_message = '';

$time_valid = '';
$speed_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_cutter_submit')) {
    if(empty(filter_input(INPUT_POST, 'time'))) {
        $time_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'speed'))) {
        $speed_valid = ISINVALID;
        $form_valid = false;
    }
    
    $cutter_id = filter_input(INPUT_POST, 'cutter_id');
    
    if($form_valid) {
        // Старый объект
        $old_time = '';
        $old_speed = '';
        
        $sql = "select time, speed from norm_cutter where cutter_id = $cutter_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_time = $row['time'];
            $old_speed = $row['speed'];
        }
        
        // Новый объект
        $new_time = filter_input(INPUT_POST, 'time');
        $new_speed = filter_input(INPUT_POST, 'speed');
        
        if($old_time != $new_time || $old_speed != $new_speed) {
            $sql = "insert into norm_cutter (cutter_id, time, speed) values ($cutter_id, $new_time, $new_speed)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$time = '';
$speed = '';

$sql = "select time, speed from norm_cutter where cutter_id = $cutter_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $time = $row['time'];
    $speed = $row['speed'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_admin.php';
        ?>
        <div class="container-fluid">
            <?php
            include '../include/subheader_norm.php';
            
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            if(null !== filter_input(INPUT_POST, 'norm_cutter_submit') && empty($error_message)) {
                echo "<div class='alert alert-success'>Данные сохранены</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-4 col-lg-2">
                    <form method="post">
                        <input type="hidden" name="cutter_id" value="<?=$cutter_id ?>" />
                        <div class="form-group">
                            <label for="time">Время приладки, мин</label>
                            <input type="text" class="form-control float-only<?=$time_valid ?>" id="time" name="time" value="<?= empty($time) ? "" : floatval($time) ?>" placeholder="Время приладки, мин" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Время обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="speed">Скорость работы, км/час</label>
                            <input type="text" class="form-control float-only<?=$speed_valid ?>" id="speed" name="speed" value="<?= empty($speed) ? "" : floatval($speed) ?>" placeholder="Скорость работы, км/час" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Скорость работы обязательно</div>
                        </div>
                        <button type="submit" name="norm_cutter_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>