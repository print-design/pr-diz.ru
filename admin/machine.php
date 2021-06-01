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

$price_valid = '';
$lam_speed_valid = '';
$speed_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_machine_submit')) {
    if(empty(filter_input(INPUT_POST, 'price'))) {
        $price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'speed'))) {
        $speed_valid = ISINVALID;
        $form_valid = false;
    }
    
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    if($form_valid) {
        // Старый объект
        $old_price = '';
        $old_speed = '';
        
        $sql = "select price, speed from norm_machine where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_price = $row['price'];
            $old_speed = $row['speed'];
        }
        
        // Новый объект
        $new_price = filter_input(INPUT_POST, 'price');
        $new_speed = filter_input(INPUT_POST, 'speed');
        
        if($old_price != $new_price || $old_speed != $new_speed) {
            $sql = "insert into norm_machine (machine_id, price, speed) values ($machine_id, $new_price, $new_speed)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$price = '';
$speed = '';

$sql = "select price, speed from norm_machine where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $price = $row['price'];
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
                            <label for="price">Стоимость работы оборудования (руб/час)</label>
                            <input type="text" class="form-control float-only" id="price" name="price" value="<?= empty($price) ? "" : floatval($price) ?>" placeholder="Стоимость, час" required="required" />
                            <div class="invalid-feedback">Стоимость обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="speed">Скорость работы оборудования (м/мин)</label>
                            <input type="text" class="form-control float-only" id="speed" name="speed" value="<?= empty($speed) ? "" : floatval($speed) ?>" placeholder="Скорость, м/мин" required="required" />
                            <div class="invalid-feedback">Скорость обязательно</div>
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