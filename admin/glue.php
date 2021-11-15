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

$glue_valid = '';
$glue_expense_valid = '';
$glue_expense_pet_valid = '';
$solvent_valid = '';
$glue_part_valid = '';
$solvent_part_valid = '';

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
    
    if(empty(filter_input(INPUT_POST, 'glue_expense_pet'))) {
        $glue_expense_pet_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent')) || empty(filter_input(INPUT_POST, 'solvent_currency'))) {
        $solvent_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'glue_part'))) {
        $glue_part_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent_part'))) {
        $solvent_part_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_glue = '';
        $old_glue_currency = '';
        $old_glue_expense = '';
        $old_glue_expense_pet = '';
        $old_solvent = '';
        $old_solvent_currency = '';
        $old_glue_part = '';
        $old_solvent_part = '';
        
        $sql = "select glue, glue_currency, glue_expense, glue_expense_pet, solvent, solvent_currency, glue_part, solvent_part from norm_glue order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_glue = $row['glue'];
            $old_glue_currency = $row['glue_currency'];
            $old_glue_expense = $row['glue_expense'];
            $old_glue_expense_pet = $row['glue_expense_pet'];
            $old_solvent = $row['solvent'];
            $old_solvent_currency = $row['solvent_currency'];
            $old_glue_part = $row['glue_part'];
            $old_solvent_part = $row['solvent_part'];
        }
        
        // Новый объект
        $new_glue = filter_input(INPUT_POST, 'glue');
        $new_glue_currency = filter_input(INPUT_POST, 'glue_currency');
        $new_glue_expense = filter_input(INPUT_POST, 'glue_expense');
        $new_glue_expense_pet = filter_input(INPUT_POST, 'glue_expense_pet');
        $new_solvent = filter_input(INPUT_POST, 'solvent');
        $new_solvent_currency = filter_input(INPUT_POST, 'solvent_currency');
        $new_glue_part = filter_input(INPUT_POST, 'glue_part');
        $new_solvent_part = filter_input(INPUT_POST, 'solvent_part');
        
        if($old_glue != $new_glue || 
                $old_glue_currency != $new_glue_currency || 
                $old_glue_expense != $new_glue_expense || 
                $old_glue_expense_pet != $new_glue_expense_pet || 
                $old_solvent != $new_solvent || 
                $old_solvent_currency != $new_solvent_currency || 
                $old_glue_part != $new_glue_part || 
                $old_solvent_part != $new_solvent_part) {
            $sql = "insert into norm_glue (glue, glue_currency, glue_expense, glue_expense_pet, solvent, solvent_currency, glue_part, solvent_part) values ($new_glue, '$new_glue_currency', $new_glue_expense, $new_glue_expense_pet, $new_solvent, '$new_solvent_currency', $new_glue_part, $new_solvent_part)";
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
$glue_expense_pet = '';
$solvent = '';
$solvent_currency = '';
$glue_part = '';
$solvent_part = '';

$sql = "select glue, glue_currency, glue_expense, glue_expense_pet, solvent, solvent_currency, glue_part, solvent_part from norm_glue order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $glue = $row['glue'];
    $solvent = $row['solvent'];
    $glue_currency = $row['glue_currency'];
    $glue_expense = $row['glue_expense'];
    $glue_expense_pet = $row['glue_expense_pet'];
    $solvent_currency = $row['solvent_currency'];
    $glue_part = $row['glue_part'];
    $solvent_part = $row['solvent_part'];
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
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
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
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'glue_expense'); $(this).attr('name', 'glue_expense'); $(this).attr('placeholder', 'Расход клея, г/м2');" 
                                   onfocusout="javascript: $(this).attr('id', 'glue_expense'); $(this).attr('name', 'glue_expense'); $(this).attr('placeholder', 'Расход клея, г/м2');" />
                            <div class="invalid-feedback">Расход клея обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="glue">Расход клея при ламинации ПЭТ, г/м<sup>2</sup></label>
                            <input type="text" 
                                   class="form-control float-only" 
                                   id="glue_expense_pet" 
                                   name="glue_expense_pet" 
                                   value="<?= empty($glue_expense_pet) ? "" : floatval($glue_expense_pet) ?>" 
                                   placeholder="Расход клея при ламинации ПЭТ, г/м2" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'glue_expense_pet'); $(this).attr('name', 'glue_expense_pet'); $(this).attr('placeholder', 'Расход клея при ламинации ПЭТ, г/м2');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'glue_expense_pet'); $(this).attr('name', 'glue_expense_pet'); $(this).attr('placeholder', 'Расход клея при ламинации ПЭТ, г/м2');" 
                                   onfocusout="javascript: $(this).attr('id', 'glue_expense_pet'); $(this).attr('name', 'glue_expense_pet'); $(this).attr('placeholder', 'Расход клея при ламинации ПЭТ, г/м2');" />
                            <div class="invalid-feedback">Расход клея при ламинации ПЭТ обязательно</div>
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
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
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
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="glue_part">Доля клея</label>
                                <input type="text" 
                                       class="form-control int-only" 
                                       id="glue_part" 
                                       name="glue_part" 
                                       value="<?= empty($glue_part) ? "" : $glue_part ?>" 
                                       placeholder="Клей" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'glue_part'); $(this).attr('name', 'glue_part'); $(this).attr('placeholder', 'Клей');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'glue_part'); $(this).attr('name', 'glue_part'); $(this).attr('placeholder', 'Клей');" 
                                       onfocusout="javascript: $(this).attr('id', 'glue_part'); $(this).attr('name', 'glue_part'); $(this).attr('placeholder', 'Клей');" />
                                <div class="invalid-feedback">Доля клея обязательно</div>
                            </div>
                            <div class="form-group col-6">
                                <label for="solvent_part">Доля растворителя</label>
                                <input type="text" 
                                       class="form-control int-only" 
                                       id="solvent_part" 
                                       name="solvent_part" 
                                       value="<?= empty($solvent_part) ? "" : $solvent_part ?>" 
                                       placeholder="Растворитель" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'solvent_part'); $(this).attr('name', 'solvent_part'); $(this).attr('placeholder', 'Растворитель');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'solvent_part'); $(this).attr('name', 'solvent_part'); $(this).attr('placeholder', 'Растворитель');" 
                                       onfocusout="javascript: $(this).attr('id', 'solvent_part'); $(this).attr('name', 'solvent_part'); $(this).attr('placeholder', 'Растворитель');" />
                                <div class="invalid-feedback">Доля растворителя обязательно</div>
                            </div>
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
            // В поле "Доля клея" ограничиваем значения: целые числа от 1 до 100
            $('#glue_part').keydown(function(e) {
                if(!KeyDownLimitFloatValue($(e.target), e, 100)) {
                    return false;
                }
            });
    
            $("#glue_part").change(function(){
                ChangeLimitFloatValue($(this), 100);
            });
            
            // В поле "Доля растворителя" ограничиваем значения: целые числа от 1 до 100
            $('#solvent_part').keydown(function(e) {
                if(!KeyDownLimitFloatValue($(e.target), e, 100)) {
                    return false;
                }
            });
    
            $("#solvent_part").change(function(){
                ChangeLimitFloatValue($(this), 100);
            });
        </script>
    </body>
</html>