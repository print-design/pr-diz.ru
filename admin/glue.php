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

// Страница предназначена только для ламинатора
if($machine_id != MACHINE_LAMINATOR) {
    header("Location: ".APPLICATION."/admin/colorfulness.php".BuildQuery("machine_id", $machine_id));
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$glue_valid = '';
$glue_expense_valid = '';
$solvent_valid = '';
$glue_solvent_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_glue_submit')) {
    if(empty(filter_input(INPUT_POST, 'glue')) || empty(filter_input(INPUT_POST, 'glue_currency'))) {
        $glue_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'glue_expense'))) {
        $glue_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent')) || empty(filter_input(INPUT_POST, 'solvent_currency'))) {
        $solvent_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'glue_solvent'))) {
        $glue_solvent_valid = ISINVALID;
        $form_valid = false;
    }
    
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    if($form_valid) {
        // Старый объект
        $old_glue = '';
        $old_glue_currency = '';
        $old_glue_expense = '';
        $old_solvent = '';
        $old_solvent_currency = '';
        $old_glue_solvent = '';
        
        $sql = "select glue, glue_currency, glue_expense, solvent, solvent_currency, glue_solvent from norm_glue where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_glue = $row['glue'];
            $old_glue_currency = $row['glue_currency'];
            $old_glue_expense = $row['glue_expense'];
            $old_solvent = $row['solvent'];
            $old_solvent_currency = $row['solvent_currency'];
            $old_glue_solvent = $row['glue_solvent'];
        }
        
        // Новый объект
        $new_glue = filter_input(INPUT_POST, 'glue');
        $new_glue_currency = filter_input(INPUT_POST, 'glue_currency');
        $new_glue_expense = filter_input(INPUT_POST, 'glue_expense');
        $new_solvent = filter_input(INPUT_POST, 'solvent');
        $new_solvent_currency = filter_input(INPUT_POST, 'solvent_currency');
        $new_glue_solvent = filter_input(INPUT_POST, 'glue_solvent');
        
        if($old_glue != $new_glue || 
                $old_glue_currency != $new_glue_currency || 
                $old_glue_expense != $new_glue_expense ||
                $old_solvent != $new_solvent || 
                $old_solvent_currency != $new_solvent_currency || 
                $old_glue_solvent != $new_glue_solvent) {
            $sql = "insert into norm_glue (machine_id, glue, glue_currency, glue_expense, solvent, solvent_currency, glue_solvent) values ($machine_id, $new_glue, '$new_glue_currency', $new_glue_expense, $new_solvent, '$new_solvent_currency', $new_glue_solvent)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$glue = '';
$glue_currency = '';
$glue_expense = '';
$solvent = '';
$solvent_currency = '';
$glue_solvent = '';

$sql = "select glue, glue_currency, glue_expense, solvent, solvent_currency, glue_solvent from norm_glue where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $glue = $row['glue'];
    $solvent = $row['solvent'];
    $glue_currency = $row['glue_currency'];
    $glue_expense = $row['glue_expense'];
    $solvent_currency = $row['solvent_currency'];
    $glue_solvent = $row['glue_solvent'];
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
            
            if(null !== filter_input(INPUT_POST, 'norm_glue_submit') && empty($error_message)):
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
                            <label for="glue">Стоимость клея (за кг)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only" 
                                       id="glue" 
                                       name="glue" 
                                       value="<?= empty($glue) ? "" : floatval($glue) ?>" 
                                       placeholder="Стоимость, за кг" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'glue'); $(this).attr('name', 'glue'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                       onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onkeyup="javascript: $(this).attr('id', 'glue'); $(this).attr('name', 'glue'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                       onfocusout="javascript: $(this).attr('id', 'glue'); $(this).attr('name', 'glue'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                <div class="input-group-append">
                                    <select id="glue_currency" name="glue_currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="rub"<?=$glue_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="usd"<?=$glue_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="euro"<?=$glue_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback">Стоимость клея обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="glue">Расход клея, г/м<sup>2</sup></label>
                            <input type="text" 
                                   class="form-control float-only" 
                                   id="glue_expense" 
                                   name="glue_expense" 
                                   value="<?= empty($glue_expense) ? "" : floatval($glue_expense) ?>" 
                                   placeholder="Расход клея, г/м2" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'glue_expense'); $(this).attr('name', 'glue_expense'); $(this).attr('placeholder', 'Расход клея, г/м2');" 
                                   onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onkeyup="javascript: $(this).attr('id', 'glue_expense'); $(this).attr('name', 'glue_expense'); $(this).attr('placeholder', 'Расход клея, г/м2');" 
                                   onfocusout="javascript: $(this).attr('id', 'glue_expense'); $(this).attr('name', 'glue_expense'); $(this).attr('placeholder', 'Расход клея, г/м2');" />
                            <div class="invalid-feedback">Стоимость клея обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="solvent">Стоимость растворителя для клея (за кг)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only" 
                                       id="solvent" 
                                       name="solvent" 
                                       value="<?= empty($solvent) ? "" : floatval($solvent) ?>" 
                                       placeholder="Стоимость, за кг" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'solvent'); $(this).attr('name', 'solvent'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                       onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onkeyup="javascript: $(this).attr('id', 'solvent'); $(this).attr('name', 'solvent'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                       onfocusout="javascript: $(this).attr('id', 'solvent'); $(this).attr('name', 'solvent'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                <div class="input-group-append">
                                    <select id="solvent_currency" name="solvent_currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="rub"<?=$solvent_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="usd"<?=$solvent_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="euro"<?=$solvent_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback">Стоимость растворителя для клея обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="glue_solvent">Соотношение клея и растворителя (в процентах)</label>
                            <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="glue_solvent" 
                                   name="glue_solvent" 
                                   value="<?= empty($glue_solvent) ? "" : floatval($glue_solvent) ?>" 
                                   placeholder="В процентах" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'glue_solvent'); $(this).attr('name', 'glue_solvent'); $(this).attr('placeholder', 'В процентах');" 
                                   onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onkeyup="javascript: $(this).attr('id', 'glue_solvent'); $(this).attr('name', 'glue_solvent'); $(this).attr('placeholder', 'В процентах');" 
                                   onfocusout="javascript: $(this).attr('id', 'glue_solvent'); $(this).attr('name', 'glue_solvent'); $(this).attr('placeholder', 'В процентах');" />
                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <div class="invalid-feedback">Соотношение клея и растворителя обязательно</div>
                        </div>
                        <button type="submit" id="norm_glue_submit" name="norm_glue_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // В поле "процент" ограничиваем значения: целые числа от 1 до 100
            $('#glue_solvent').keydown(function(e) {
                if(!KeyDownLimitFloatValue($(e.target), e, 100)) {
                    return false;
                }
            });
    
            $("#glue_solvent").change(function(){
                ChangeLimitFloatValue($(this), 100);
            });
        </script>
    </body>
</html>