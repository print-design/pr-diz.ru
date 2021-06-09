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

// Страница не предназначена для ламинатора
if($machine_id == MACHINE_LAMINATOR) {
    header("Location: ".APPLICATION."/admin/glue.php".BuildQuery("machine_id", $machine_id));
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$colorfulness_valid = '';
$max_width_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'characteristics_submit')) {
    if(empty(filter_input(INPUT_POST, 'colorfulness'))) {
        $colorfulness_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'max_width'))) {
        $max_width_valid = ISINVALID;
        $form_valid = false;
    }
    
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    if($form_valid) {
        // Старый объект
        $old_colorfulness = '';
        $old_max_width = '';
        
        $sql = "select colorfulness, max_width from machine where id = $machine_id";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_colorfulness = $row['colorfulness'];
            $old_max_width = $row['max_width'];
        }
        
        // Новый объект
        $new_colorfulness = filter_input(INPUT_POST, 'colorfulness');
        $new_max_width = filter_input(INPUT_POST, 'max_width');
        
        if($old_colorfulness != $new_colorfulness || $old_max_width != $new_max_width) {
            $sql = "update machine set colorfulness = $new_colorfulness, max_width = $new_max_width where id = $machine_id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$colorfulness = '';
$max_width = '';

$sql = "select colorfulness, max_width from machine where id = $machine_id";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $colorfulness = $row['colorfulness'];
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
            
            if(null !== filter_input(INPUT_POST, 'characteristics_submit') && empty($error_message)):
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
                            <label for="colorfulness">Красочность</label>
                            <select id="colorfulness" name="colorfulness" class="form-control">
                                <option value="" hidden="hidden">Красочность...</option>
                                <?php
                                for($i=1; $i<=8; $i++):
                                $selected = '';
                                if($i == $colorfulness) {
                                    $selected = " selected='selected'";
                                }
                                ?>
                                <option value="<?=$i ?>"<?=$selected ?>><?=$i ?></option>
                                <?php endfor; ?>
                            </select>
                            <div class="invalid-feedback">Красочность обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="max_width">Максимальная ширина материала</label>
                            <input type="text" 
                                   class="form-control float-only" 
                                   id="max_width" 
                                   name="max_width" 
                                   value="<?= empty($max_width) || $max_width == 0.0 ? "" : floatval($max_width) ?>" 
                                   placeholder="Максимальная ширина материала" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'max_width'); $(this).attr('name', 'max_width'); $(this).attr('placeholder', 'Максимальная ширина материала');" 
                                   onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onkeyup="javascript: $(this).attr('id', 'max_width'); $(this).attr('name', 'max_width'); $(this).attr('placeholder', 'Максимальная ширина материала');" 
                                   onfocusout="javascript: $(this).attr('id', 'max_width'); $(this).attr('name', 'max_width'); $(this).attr('placeholder', 'Максимальная ширина материала');" />
                            <div class="invalid-feedback">Максимальная ширина материала обязательно</div>
                        </div>
                        <button type="submit" id="characteristics_submit" name="characteristics_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>