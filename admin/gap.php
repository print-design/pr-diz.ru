<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Машина
$machine_id = filter_input(INPUT_GET, 'machine_id');

$atlas = 5;

// Если машина - не атлас, перекидываем на machine.php
if($machine_id != $atlas) {
    header('Location: machine.php?machine_id='.$machine_id);
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$gap_raport_valid = '';
$gap_stream_valid = '';
$ski_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_gap_submit')) {
    if(empty(filter_input(INPUT_POST, 'gap_raport'))) {
        $gap_raport_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'gap_stream'))) {
        $gap_stream_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'ski'))) {
        $ski_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_gap_raport = '';
        $old_gap_stream = '';
        $old_ski = '';
        
        $sql = "select gap_raport, gap_stream, ski from norm_gap where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_gap_raport = $row['gap_raport'];
            $old_gap_stream = $row['gap_stream'];
            $old_ski = $row['ski'];
        }
        
        // Новый объект
        $new_gap_raport = filter_input(INPUT_POST, 'gap_raport');
        $new_gap_stream = filter_input(INPUT_POST, 'gap_stream');
        $new_ski = filter_input(INPUT_POST, 'ski');
        
        if($old_gap_raport != $new_gap_raport || $old_gap_stream != $new_gap_stream || $old_ski != $new_ski) {
            $sql = "insert into norm_gap (machine_id, gap_raport, gap_stream, ski) values ($machine_id, $new_gap_raport, $new_gap_stream, $new_ski)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$gap_raport = '';
$gap_stream = '';
$ski = '';

$sql = "select gap_raport, gap_stream, ski from norm_gap where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $gap_raport = $row['gap_raport'];
    $gap_stream = $row['gap_stream'];
    $ski = $row['ski'];
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
            ?>
            <hr />
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            if(null !== filter_input(INPUT_POST, 'norm_gap_submit') && empty($error_message)):
            ?>
            <div class="alert alert-success">Данные сохранены</div>
            <?php
            endif;
            ?>
            <div class="row">
                <div class="col-12 col-md-4 col-lg-2">
                    <form method="post">
                        <input type="hidden" id="machine_id" name="machine_id" value="<?= filter_input(INPUT_GET, 'machine_id') ?>" />
                        <div class="form-group">
                            <label for="gap_raport">ЗазорРапорт (минимальное расстояние между этикетками), мм</label>
                            <input type="text" 
                                   class="form-control float-only<?=$gap_raport_valid ?>" 
                                   id="gap_raport" 
                                   name="gap_raport" 
                                   value="<?= empty($gap_raport) ? "" : floatval($gap_raport) ?>" 
                                   placeholder="ЗазорРапорт, мм" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'gap_raport'); $(this).attr('name', 'gap_raport'); $(this).attr('placeholder', 'ЗазорРапорт, мм');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'gap_raport'); $(this).attr('name', 'gap_raport'); $(this).attr('placeholder', 'ЗазорРапорт, мм');" 
                                   onfocusout="javascript: $(this).attr('id', 'gap_raport'); $(this).attr('name', 'gap_raport'); $(this).attr('placeholder', 'ЗазорРапорт, мм');" />
                            <div class="invalid-feedback">ЗазорРапорт обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="gap_stream">ЗазорРучей (минимальное расстояние между ручьями), мм</label>
                            <input type="text" 
                                   class="form-control float-only<?=$gap_stream_valid ?>" 
                                   id="gap_stream" 
                                   name="gap_stream" 
                                   value="<?= empty($gap_stream) ? "" : floatval($gap_stream) ?>" 
                                   placeholder="ЗазорРучей, мм" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'gap_stream'); $(this).attr('name', 'gap_stream'); $(this).attr('placeholder', 'ЗазорРучей, мм');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'gap_stream'); $(this).attr('name', 'gap_stream'); $(this).attr('placeholder', 'ЗазорРучей, мм');" 
                                   onfocusout="javascript: $(this).attr('id', 'gap_stream'); $(this).attr('name', 'gap_stream'); $(this).attr('placeholder', 'ЗазорРучей, мм');" />
                            <div class="invalid-feedback">ЗазорРучей обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="ski">Ширина 1 лыжи, мм</label>
                            <input type="text" 
                                   class="form-control float-only<?=$ski_valid ?>" 
                                   id="ski" 
                                   name="ski" 
                                   value="<?= empty($ski) ? "" : floatval($ski) ?>" 
                                   placeholder="Ширина 1 лыжи, мм" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'ski'); $(this).attr('name', 'ski'); $(this).attr('placeholder', 'Ширина 1 лыжи, мм');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'ski'); $(this).attr('name', 'ski'); $(this).attr('placeholder', 'Ширина 1 лыжи, мм');" 
                                   onfocusout="javascript: $(this).attr('id', 'ski'); $(this).attr('name', 'ski'); $(this).attr('placeholder', 'Ширина 1 лыжи, мм');" />
                            <div class="invalid-feedback">Ширина 1 лыжи обязательно</div>
                        </div>
                        <button type="submit" id="norm_gap_submit" name="norm_gap_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>