<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Машина
$machine_id = filter_input(INPUT_GET, 'machine_id');

// Номер ламинатора
const MACHINE_LAMINATOR = 5;

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$time_valid = '';
$length_valid = '';
$waste_percent_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_fitting_submit')) {
    if(empty(filter_input(INPUT_POST, 'time'))) {
        $time_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'length'))) {
        $length_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'waste_percent'))) {
        $waste_percent_valid = false;
        $form_valid = false;
    }
    
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    if($form_valid) {
        // Старый объект
        $old_time = '';
        $old_length = '';
        $old_waste_percent = '';
        
        $sql = "select time, length, waste_percent from norm_fitting where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_time = $row['time'];
            $old_length = $row['length'];
            $old_waste_percent = $row['waste_percent'];
        }
        
        // Новый объект
        $new_time = filter_input(INPUT_POST, 'time');
        $new_length = filter_input(INPUT_POST, 'length');
        $new_waste_percent = filter_input(INPUT_POST, 'waste_percent');
        
        if($old_time != $new_time || $old_length != $new_length || $old_waste_percent != $new_waste_percent) {
            $sql = "insert into norm_fitting (machine_id, time, length, waste_percent) values ($machine_id, $new_time, $new_length, $new_waste_percent)";
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
$length = '';
$waste_percent = '';

$sql = "select time, length, waste_percent from norm_fitting where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $time = $row['time'];
    $length = $row['length'];
    $waste_percent = $row['waste_percent'];
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
            
            if(null !== filter_input(INPUT_POST, 'norm_fitting_submit') && empty($error_message)):
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
                            <label for="time">Время приладки<?= $machine_id == MACHINE_LAMINATOR ? "" : " 1 краски" ?> (мин)</label>
                            <input type="text" 
                                   class="form-control float-only" 
                                   id="time" 
                                   name="time" 
                                   value="<?= empty($time) ? "" : floatval($time) ?>" 
                                   placeholder="Время, мин" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'time'); $(this).attr('name', 'time'); $(this).attr('placeholder', 'Время, мин');" 
                                   onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onkeyup="javascript: $(this).attr('id', 'time'); $(this).attr('name', 'time'); $(this).attr('placeholder', 'Время, мин');" 
                                   onfocusout="javascript: $(this).attr('id', 'time'); $(this).attr('name', 'time'); $(this).attr('placeholder', 'Время, мин');" />
                            <div class="invalid-feedback">Время обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="length">Метраж приладки<?= $machine_id == MACHINE_LAMINATOR ? "" : " 1 краски" ?> (метры)</label>
                            <input type="text" 
                                   class="form-control float-only" 
                                   id="length" 
                                   name="length" 
                                   value="<?= empty($length) ? "" : floatval($length) ?>" 
                                   placeholder="Метраж, метры" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Метраж, метры');" 
                                   onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onkeyup="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Метраж, метры');" 
                                   onfocusout="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Метраж, метры');" />
                            <div class="invalid-feedback">Метраж обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="length">Общий процент отходов</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="waste_percent" 
                                       name="waste_percent" 
                                       value="<?= empty($waste_percent) ? "" : intval($waste_percent) ?>" 
                                       placeholder="Общий процент отходов" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'waste_percent'); $(this).attr('name', 'waste_percent'); $(this).attr('placeholder', 'Общий процент отходов');" 
                                       onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onkeyup="javascript: $(this).attr('id', 'waste_percent'); $(this).attr('name', 'waste_percent'); $(this).attr('placeholder', 'Общий процент отходов');" 
                                       onfocusout="javascript: $(this).attr('id', 'waste_percent'); $(this).attr('name', 'waste_percent'); $(this).attr('placeholder', 'Метраж, метры');" />
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
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
        <script>
            // В поле "процент" ограничиваем значения: целые числа от 1 до 100
            $('#waste_percent').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 100)) {
                    return false;
                }
            });
    
            $("#waste_percent").change(function(){
                ChangeLimitIntValue($(this), 100);
            });
        </script>
    </body>
</html>