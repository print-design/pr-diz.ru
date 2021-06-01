<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Печатная машина
$machine_id = filter_input(INPUT_GET, 'machine_id');

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$lamination_valid = '';
$lam_speed_valid = '';
$speed_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_machine_submit')) {
    if(empty(filter_input(INPUT_POST, 'lamination'))) {
        $lamination_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lam_speed'))) {
        $lam_speed_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'speed'))) {
        $speed_valid = ISINVALID;
        $form_valid = false;
    }
    
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    if($form_valid) {
        // Старый объект
        $old_lamination = '';
        $old_lam_speed = '';
        $old_speed = '';
        
        $sql = "select lamination, lam_speed, speed from norm_machine where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_lamination = $row['lamination'];
            $old_lam_speed = $row['lam_speed'];
            $old_speed = $row['speed'];
        }
        
        // Новый объект
        $new_lamination = filter_input(INPUT_POST, 'lamination');
        $new_lam_speed = filter_input(INPUT_POST, 'lam_speed');
        $new_speed = filter_input(INPUT_POST, 'speed');
        
        if($old_lamination != $new_lamination || $old_lam_speed != $new_lam_speed || $old_speed != $new_speed) {
            $sql = "insert into norm_machine (machine_id, lamination, lam_speed, speed) values ($machine_id, $new_lamination, $new_lam_speed, $new_speed)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$lamination = '';
$lam_speed = '';
$speed = '';

$sql = "select lamination, lam_speed, speed from norm_machine where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $lamination = $row['lamination'];
    $lam_speed = $row['lam_speed'];
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
        include '../include/header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            if(null !== filter_input(INPUT_POST, 'norm_machine_submit') && empty($error_message)):
            ?>
            <div class="alert alert-success">Данные сохранены</div>
            <?php
            endif;
            ?>
            <div class="d-flex justify-content-start">
                <div class="p-1">
                    <?php
                    include '../include/subheader_admin.php';
                    ?>
                </div>
            </div>
            <?php
            include '../include/subheader_norm.php';
            ?>
            <hr />
            <div class="row">
                <div class="col-12 col-md-4 col-lg-2">
                    <form method="post">
                        <input type="hidden" id="machine_id" name="machine_id" value="<?= filter_input(INPUT_GET, 'machine_id') ?>" />
                        <div class="form-group">
                            <label for="speed">Стоимость работы печатной машины (руб/час)</label>
                            <input type="text" class="form-control float-only" id="speed" name="speed" value="<?= empty($speed) ? "" : floatval($speed) ?>" placeholder="Стоимость, час" required="required" />
                            <div class="invalid-feedback">Стоимость работы печатной машины обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="lamination">Стоимость работы ламинации (руб/час)</label>
                            <input type="text" class="form-control float-only" id="lamination" name="lamination" value="<?= empty($lamination) ? "" : floatval($lamination) ?>" placeholder="Стоимость, час" required="required" />
                            <div class="invalid-feedback">Стоимость работы ламинации обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="lam_speed">Скорость работы ламинатора (м/мин)</label>
                            <input type="text" class="form-control float-only" id="lam_speed" name="lam_speed" value="<?= empty($lam_speed) ? "" : floatval($lam_speed) ?>" placeholder="Скорость, м/мин" required="required" />
                            <div class="invalid-feedback">Скорость работы ламинатора обязательно</div>
                        </div>
                        <button type="submit" id="norm_machine_submit" name="norm_machine_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>