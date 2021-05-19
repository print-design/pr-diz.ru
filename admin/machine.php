<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$lamination_valid = '';
$lam_speed_valid = '';
$zbs6_valid = '';
$zbs8_valid = '';
$comiflex_valid = '';

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
    
    if(empty(filter_input(INPUT_POST, 'zbs6'))) {
        $zbs6_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'zbs8'))) {
        $zbs8_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'comiflex'))) {
        $comiflex_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_lamination = '';
        $old_lam_speed = '';
        $old_zbs6 = '';
        $old_zbs8 = '';
        $old_comiflex = '';
        
        $sql = "select lamination, lam_speed, zbs6, zbs8, comiflex from norm_machine order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_lamination = $row['lamination'];
            $old_lam_speed = $row['lam_speed'];
            $old_zbs6 = $row['zbs6'];
            $old_zbs8 = $row['zbs8'];
            $old_comiflex = $row['comiflex'];
        }
        
        // Новый объект
        $new_lamination = filter_input(INPUT_POST, 'lamination');
        $new_lam_speed = filter_input(INPUT_POST, 'lam_speed');
        $new_zbs6 = filter_input(INPUT_POST, 'zbs6');
        $new_zbs8 = filter_input(INPUT_POST, 'zbs8');
        $new_comiflex = filter_input(INPUT_POST, 'comiflex');
        
        if($old_lamination != $new_lamination || $old_lam_speed != $new_lam_speed || $old_zbs6 != $new_zbs6 || $old_zbs8 != $new_zbs8 || $old_comiflex != $new_comiflex) {
            $sql = "insert into norm_machine (lamination, lam_speed, zbs6, zbs8, comiflex) values ($new_lamination, $new_lam_speed, $new_zbs6, $new_zbs8, $new_comiflex)";
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
$zbs6 = '';
$zbs8 = '';
$comiflex = '';

$sql = "select lamination, lam_speed, zbs6, zbs8, comiflex from norm_machine order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $lamination = $row['lamination'];
    $lam_speed = $row['lam_speed'];
    $zbs6 = $row['zbs6'];
    $zbs8 = $row['zbs8'];
    $comiflex = $row['comiflex'];
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
                        <div class="form-group">
                            <label for="zbs6">Стоимость работы печатной машины ZBS 6 цветов (руб/час)</label>
                            <input type="text" class="form-control float-only" id="zbs6" name="zbs6" value="<?= empty($zbs6) ? "" : floatval($zbs6) ?>" placeholder="Стоимость, час" required="required" />
                            <div class="invalid-feedback">Стоимость работы печатной машины ZBS 6 цветов обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="zbs8">Стоимость работы печатной машины ZBS 8 цветов (руб/час)</label>
                            <input type="text" class="form-control float-only" id="zbs8" name="zbs8" value="<?= empty($zbs8) ? "" : floatval($zbs8) ?>" placeholder="Стоимость, час" required="required" />
                            <div class="invalid-feedback">Стоимость работы печатной машины ZBS 8 цветов обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="comiflex">Стоимость работы печатной машины Comiflex (руб/час)</label>
                            <input type="text" class="form-control float-only" id="comiflex" name="comiflex" value="<?= empty($comiflex) ? "" : floatval($comiflex) ?>" placeholder="Стоимость, час" required="required" />
                            <div class="invalid-feedback">Стоимость работы печатной машины Comiflex обязательно</div>
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