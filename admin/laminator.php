<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$price_valid = '';
$speed_valid = '';
$max_width_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_laminator_submit')) {
    if(empty(filter_input(INPUT_POST, 'price'))) {
        $price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'speed'))) {
        $speed_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'max_width'))) {
        $max_width_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_price = '';
        $old_speed = '';
        $old_max_width = '';
        
        $sql = "select price, speed, max_width from norm_laminator order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_price = $row['price'];
            $old_speed = $row['speed'];
            $old_max_width = $row['max_width'];
        }
        
        // Новый объект
        $new_price = filter_input(INPUT_POST, 'price');
        $new_speed = filter_input(INPUT_POST, 'speed');
        $new_max_width = filter_input(INPUT_POST, 'max_width');
        
        if($old_price != $new_price || $old_speed != $new_speed || $old_max_width != $new_max_width) {
            $sql = "insert into norm_laminator (price, speed, max_width) values ($new_price, $new_speed, $new_max_width)";
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
$max_width = '';

$sql = "select price, speed, max_width from norm_laminator order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $price = $row['price'];
    $speed = $row['speed'];
    $max_width = $row['max_width'];
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
            
            if(null !== filter_input(INPUT_POST, 'norm_laminator_submit') && empty($error_message)):
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
                            <label for="price">Стоимость работы оборудования, руб/час</label>
                            <input type="text" 
                                   class="form-control float-only" 
                                   id="price" 
                                   name="price" 
                                   value="<?= empty($price) ? "" : floatval($price) ?>" 
                                   placeholder="Стоимость, руб/час" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'price'); $(this).attr('name', 'price'); $(this).attr('placeholder', 'Стоимость, руб/час');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'price'); $(this).attr('name', 'price'); $(this).attr('placeholder', 'Стоимость, руб/час');" 
                                   onfocusout="javascript: $(this).attr('id', 'price'); $(this).attr('name', 'price'); $(this).attr('placeholder', 'Стоимость, руб/час');" />
                            <div class="invalid-feedback">Стоимость обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="speed">Скорость работы оборудования, км/час</label>
                            <input type="text" 
                                   class="form-control float-only" 
                                   id="speed" 
                                   name="speed" 
                                   value="<?= empty($speed) ? "" : floatval($speed) ?>" 
                                   placeholder="Скорость, км/час" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'speed'); $(this).attr('name', 'speed'); $(this).attr('placeholder', 'Скорость, км/час');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'speed'); $(this).attr('name', 'speed'); $(this).attr('placeholder', 'Скорость, км/час');" 
                                   onfocusout="javascript: $(this).attr('id', 'speed'); $(this).attr('name', 'speed'); $(this).attr('placeholder', 'Скорость, км/час');" />
                            <div class="invalid-feedback">Скорость обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="max_width">Максимальная ширина материала, мм</label>
                            <input type="text" 
                                   class="form-control int-only" 
                                   id="max_width" 
                                   name="max_width" 
                                   value="<?= empty($max_width) ? "" : intval($max_width) ?>" 
                                   placeholder="Ширина, мм" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'max_width'); $(this).attr('name', 'max_width'); $(this).attr('placeholder', 'Ширина, мм');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'max_width'); $(this).attr('name', 'max_width'); $(this).attr('placeholder', 'Ширина, мм');" 
                                   onfocusout="javascript: $(this).attr('id', 'max_width'); $(this).attr('name', 'max_width'); $(this).attr('placeholder', 'Ширина, мм');" />
                            <div class="invalid-feedback">Максимальная ширина материала обязательно</div>
                        </div>
                        <button type="submit" id="norm_laminator_submit" name="norm_laminator_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>