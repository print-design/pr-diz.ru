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

$usd_valid = '';
$euro_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'currency_submit')) {
    if(empty(filter_input(INPUT_POST, 'usd'))) {
        $usd_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'euro'))) {
        $euro_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_usd = '';
        $old_euro = '';
        
        $sql = "select usd, euro from currency order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_usd = $row['time'];
            $old_euro = $row['length'];
        }
        
        // Новый объект
        $new_usd = filter_input(INPUT_POST, 'usd');
        $new_euro = filter_input(INPUT_POST, 'euro');
        
        if($old_usd != $new_usd || $old_euro != $new_euro) {
            $sql = "insert into currency (machine_id, usd, euro) values ($machine_id, $new_usd, $new_euro)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$usd = '';
$euro = '';

$sql = "select usd, euro from currency order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $usd = $row['usd'];
    $euro = $row['euro'];
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
            
            if(null !== filter_input(INPUT_POST, 'currency_submit') && empty($error_message)):
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
                        <input type="hidden" id="machine_id" name="machine_id" value="<?= $machine_id ?>" />
                        <div class="form-group">
                            <label for="time">Время приладки<?= $machine_id == MACHINE_LAMINATOR ? "" : " 1 краски" ?> (руб/час)</label>
                            <input type="text" 
                                   class="form-control float-only" 
                                   id="time" 
                                   name="time" 
                                   value="<?= empty($time) ? "" : floatval($time) ?>" 
                                   placeholder="Стоимость, руб/час" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'time'); $(this).attr('name', 'time'); $(this).attr('placeholder', 'Стоимость, руб/час');" 
                                   onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onkeyup="javascript: $(this).attr('id', 'time'); $(this).attr('name', 'time'); $(this).attr('placeholder', 'Стоимость, руб/час');" 
                                   onfocusout="javascript: $(this).attr('id', 'time'); $(this).attr('name', 'time'); $(this).attr('placeholder', 'Стоимость, руб/час');" />
                            <div class="invalid-feedback">Время обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="length">Метраж приладки<?= $machine_id == MACHINE_LAMINATOR ? "" : " 1 краски" ?> (руб/м)</label>
                            <input type="text" 
                                   class="form-control float-only" 
                                   id="length" 
                                   name="length" 
                                   value="<?= empty($length) ? "" : floatval($length) ?>" 
                                   placeholder="Стоимость, руб/м" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Стоимость, руб/м');" 
                                   onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onkeyup="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Стоимость, руб/м');" 
                                   onfocusout="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Стоимость, руб/м');" />
                            <div class="invalid-feedback">Метраж обязательно</div>
                        </div>
                        <button type="submit" id="norm_fitting_submit" name="norm_fitting_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>