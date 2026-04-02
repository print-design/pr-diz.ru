<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    include '../include/_unauthorized.php';
}

// Машина
$machine_id = filter_input(INPUT_GET, 'machine_id');

// Валидация формы
$form_valid = true;
$error_message = '';

$price_valid = '';
$speed_valid = '';
$width_valid = '';
$vaporization_expense_valid = '';

$price_run2_valid = '';
$speed_run2_valid = '';

$min_weight_valid = '';

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
    
    if(empty(filter_input(INPUT_POST, 'width'))) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(null === filter_input(INPUT_POST, 'vaporization_expense')) {
        $vaporization_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(key_exists('price_run2', $_POST) && empty(filter_input(INPUT_POST, 'price_run2'))) {
        $price_run2_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(key_exists('speed_run2', $_POST) && empty(filter_input(INPUT_POST, 'speed_run2'))) {
        $speed_run2_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(null === filter_input(INPUT_POST, 'min_weight')) {
        $min_weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_price = '';
        $old_speed = '';
        $old_width = '';
        $old_vaporization_expense = '';
        
        $old_price_run2 = '';
        $old_speed_run2 = '';
        
        $old_min_weight = '';
        
        $sql = "select price, speed, width, vaporization_expense, price_run2, speed_run2, min_weight from norm_machine where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_price = $row['price'];
            $old_speed = $row['speed'];
            $old_width = $row['width'];
            $old_vaporization_expense = $row['vaporization_expense'];
            
            $old_price_run2 = $row['price_run2'];
            $old_speed_run2 = $row['speed_run2'];
            
            $old_min_weight = $row['min_weight'];
        }
        
        // Новый объект
        $new_price = filter_input(INPUT_POST, 'price');
        $new_speed = filter_input(INPUT_POST, 'speed');
        $new_width = filter_input(INPUT_POST, 'width');
        $new_vaporization_expense = filter_input(INPUT_POST, 'vaporization_expense');
        
        $new_price_run2 = filter_input(INPUT_POST, 'price_run2'); if($new_price_run2 === null) $new_price_run2 = "NULL";
        $new_speed_run2 = filter_input(INPUT_POST, 'speed_run2'); if($new_speed_run2 === null) $new_speed_run2 = "NULL";
        
        $new_min_weight = filter_input(INPUT_POST, 'min_weight'); if($new_min_weight === null) $new_min_weight = "NULL";
        
        if($old_price != $new_price || 
                $old_speed != $new_speed || 
                $old_width != $new_width || 
                $old_vaporization_expense != $new_vaporization_expense || 
                ($new_price_run2 != "NULL" && $old_price_run2 != $new_price_run2) || 
                ($new_speed_run2 != "NULL" && $old_speed_run2 != $new_speed_run2) || 
                $old_min_weight != $new_min_weight) {
            $sql = "insert into norm_machine (machine_id, price, speed, width, vaporization_expense, price_run2, speed_run2, min_weight) values ($machine_id, $new_price, $new_speed, $new_width, $new_vaporization_expense, $new_price_run2, $new_speed_run2, $new_min_weight)";
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
$width = '';
$vaporization_expense = '';

$price_run2 = '';
$speed_run2 = '';

$min_weight = '';

$sql = "select price, speed, width, vaporization_expense, price_run2, speed_run2, min_weight from norm_machine where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $price = $row['price'];
    $speed = $row['speed'];
    $width = $row['width'];
    $vaporization_expense = $row['vaporization_expense'];
    
    $price_run2 = $row['price_run2'];
    $speed_run2 = $row['speed_run2'];
    
    $min_weight = $row['min_weight'];
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
            
            if(null !== filter_input(INPUT_POST, 'norm_machine_submit') && empty($error_message)):
            ?>
            <div class="alert alert-success">Данные сохранены</div>
            <?php
            endif;
            ?>
            <form method="post">
                <input type="hidden" id="machine_id" name="machine_id" value="<?= filter_input(INPUT_GET, 'machine_id') ?>" />
                <div class="row">
                    <div class="col-12 col-md-4 col-lg-2">
                        <?php if($machine_id == PRINTER_SOMA_OPTIMA): ?>
                        <h2>Прогон 1</h2>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="price">Цена работы оборудования, руб/час</label>
                            <input type="text" class="form-control float-only<?=$price_valid ?>" id="price" name="price" value="<?= empty($price) ? "" : floatval($price) ?>" placeholder="Цена, руб/час" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Цена обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="speed">Скорость работы оборудования, км/час</label>
                            <input type="text" class="form-control float-only<?=$speed_valid ?>" id="speed" name="speed" value="<?= empty($speed) ? "" : floatval($speed) ?>" placeholder="Скорость, км/час" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Скорость обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="width">Ширина машины, мм</label>
                            <input type="text" class="form-control int-only<?=$width_valid ?>" id="width" name="width" value="<?= empty($width) ? "" : intval($width) ?>" placeholder="Ширина машины, мм" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Ширина машины обязательно</div>
                        </div>
                        <?php if($machine_id == PRINTER_ATLAS): ?>
                        <input type="hidden" id="vaporization_expense" name="vaporization_expense" value="0" />
                        <?php else: ?>
                        <div class="form-group">
                            <label for="vaporization_expense">Расход растворителя на испарение, г/м<sup>2</sup></label>
                            <input type="text" class="form-control float-only<?=$vaporization_expense_valid ?>" id="vaporization_expense" name="vaporization_expense" value="<?= empty($vaporization_expense) ? "" : floatval($vaporization_expense) ?>" placeholder="Расх. раств. на испар., г/м2" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Расход растворителя на испарение обязательно</div>
                        </div>
                        <?php endif; ?>
                        <button type="submit" id="norm_machine_submit" name="norm_machine_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </div>
                    <?php if($machine_id == PRINTER_SOMA_OPTIMA): ?>
                    <div class="col-12 col-md-4 col-lg-2">
                        <h2>Прогон 2</h2>
                        <div class="form-group">
                            <label for="price_run2">Цена работы оборудования, руб/час</label>
                            <input type="text" class="form-control float-only<?=$price_run2_valid ?>" id="price_run2" name="price_run2" value="<?= empty($price_run2) ? "" : floatval($price_run2) ?>" placeholder="Цена, руб/час" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Цена обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="speed_run2">Скорость работы оборудования, км/час</label>
                            <input type="text" class="form-control float-only<?=$speed_run2_valid ?>" id="speed_run2" name="speed_run2" value="<?= empty($speed_run2) ? "" : floatval($speed_run2) ?>" placeholder="Скорость, км/час" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Скорость обязательно</div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-12 col-md-4 col-lg-2">
                        <?php if($machine_id == PRINTER_SOMA_OPTIMA): ?>
                        <h2>&nbsp;</h2>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="min_weight">Минимальная масса заказа, кг</label>
                            <input type="text" class="form-control int-only<?=$min_weight_valid ?>" id="min_weight" name="min_weight" value="<?= empty($min_weight) ? "" : intval($min_weight) ?>" placeholder="Минимальная масса заказа, кг" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Минимальная масса заказа обязательно</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>