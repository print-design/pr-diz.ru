<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Ламинатор
$laminator_id = filter_input(INPUT_GET, 'laminator_id');

// "Растворителя" / "отвердителя"
$solvent_genitive = "растворителя";
if($laminator_id == 2) $solvent_genitive = "отвердителя";

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$glue_price_valid = '';
$glue_expense_valid = '';
$glue_expense_pet_valid = '';
$solvent_price_valid = '';
$solvent_part_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_glue_submit')) {
    $laminator_id = filter_input(INPUT_POST, 'laminator_id');
    
    if(empty(filter_input(INPUT_POST, 'glue_price')) || empty(filter_input(INPUT_POST, 'glue_currency'))) {
        $glue_price_valid = ISINVALID;
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
    
    if(empty(filter_input(INPUT_POST, 'solvent_price')) || empty(filter_input(INPUT_POST, 'solvent_currency'))) {
        $solvent_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent_part'))) {
        $solvent_part_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_glue_price = '';
        $old_glue_currency = '';
        $old_glue_expense = '';
        $old_glue_expense_pet = '';
        $old_solvent_price = '';
        $old_solvent_currency = '';
        $old_solvent_part = '';
        
        $sql = "select glue_price, glue_currency, glue_expense, glue_expense_pet, solvent_price, solvent_currency, solvent_part from norm_glue where laminator_id = $laminator_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_glue_price = $row['glue_price'];
            $old_glue_currency = $row['glue_currency'];
            $old_glue_expense = $row['glue_expense'];
            $old_glue_expense_pet = $row['glue_expense_pet'];
            $old_solvent_price = $row['solvent_price'];
            $old_solvent_currency = $row['solvent_currency'];
            $old_solvent_part = $row['solvent_part'];
        }
        
        // Новый объект
        $new_glue_price = filter_input(INPUT_POST, 'glue_price');
        $new_glue_currency = filter_input(INPUT_POST, 'glue_currency');
        $new_glue_expense = filter_input(INPUT_POST, 'glue_expense');
        $new_glue_expense_pet = filter_input(INPUT_POST, 'glue_expense_pet');
        $new_solvent_price = filter_input(INPUT_POST, 'solvent_price');
        $new_solvent_currency = filter_input(INPUT_POST, 'solvent_currency');
        $new_solvent_part = filter_input(INPUT_POST, 'solvent_part');
        
        if($old_glue_price != $new_glue_price || 
                $old_glue_currency != $new_glue_currency || 
                $old_glue_expense != $new_glue_expense || 
                $old_glue_expense_pet != $new_glue_expense_pet || 
                $old_solvent_price != $new_solvent_price || 
                $old_solvent_currency != $new_solvent_currency || 
                $old_solvent_part != $new_solvent_part) {
            $sql = "insert into norm_glue (laminator_id, glue_price, glue_currency, glue_expense, glue_expense_pet, solvent_price, solvent_currency, solvent_part) values ($laminator_id, $new_glue_price, '$new_glue_currency', $new_glue_expense, $new_glue_expense_pet, $new_solvent_price, '$new_solvent_currency', $new_solvent_part)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$glue_price = '';
$glue_currency = '';
$glue_expense = '';
$glue_expense_pet = '';
$solvent_price = '';
$solvent_currency = '';
$solvent_part = '';

$sql = "select glue_price, glue_currency, glue_expense, glue_expense_pet, solvent_price, solvent_currency, solvent_part from norm_glue where laminator_id = $laminator_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $glue_price = $row['glue_price'];
    $glue_currency = $row['glue_currency'];
    $solvent_price = $row['solvent_price'];
    $glue_expense = $row['glue_expense'];
    $glue_expense_pet = $row['glue_expense_pet'];
    $solvent_currency = $row['solvent_currency'];
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
        include '../include/header_admin.php';
        ?>
        <div class="container-fluid">
            <?php
            include '../include/subheader_norm.php';
            
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            if(null !== filter_input(INPUT_POST, 'norm_glue_submit') && empty($error_message)):
            ?>
            <div class="alert alert-success">Данные сохранены</div>
            <?php
            endif;
            ?>
            <div class="row">
                <div class="col-12 col-md-4 col-lg-2">
                    <form method="post">
                        <input type="hidden" id="laminator_id" name="laminator_id" value="<?= filter_input(INPUT_GET, 'laminator_id') ?>" />
                        <div class="form-group">
                            <label for="glue_price">Цена чистого клея (за кг)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only<?=$glue_price_valid ?>" 
                                       id="glue_price" 
                                       name="glue_price" 
                                       value="<?= empty($glue_price) ? "" : floatval($glue_price) ?>" 
                                       placeholder="Цена, за кг" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'glue_price'); $(this).attr('name', 'glue_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'glue_price'); $(this).attr('name', 'glue_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                       onfocusout="javascript: $(this).attr('id', 'glue_price'); $(this).attr('name', 'glue_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                <div class="input-group-append">
                                    <select id="glue_currency" name="glue_currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="rub"<?=$glue_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="usd"<?=$glue_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="euro"<?=$glue_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback">Цена чистого клея обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="solvent_price">Цена <?=$solvent_genitive ?> для клея (за кг)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only<?=$solvent_price_valid ?>" 
                                       id="solvent_price" 
                                       name="solvent_price" 
                                       value="<?= empty($solvent_price) ? "" : floatval($solvent_price) ?>" 
                                       placeholder="Цена, за кг" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'solvent_price'); $(this).attr('name', 'solvent_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'solvent_price'); $(this).attr('name', 'solvent_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                       onfocusout="javascript: $(this).attr('id', 'solvent_price'); $(this).attr('name', 'solvent_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                <div class="input-group-append">
                                    <select id="solvent_currency" name="solvent_currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="rub"<?=$solvent_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="usd"<?=$solvent_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="euro"<?=$solvent_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback">Цена <?=$solvent_genitive ?> для клея обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="glue_expense">Расход смеси клея, г/м<sup>2</sup></label>
                            <input type="text" 
                                   class="form-control float-only<?=$glue_expense_valid ?>" 
                                   id="glue_expense" 
                                   name="glue_expense" 
                                   value="<?= empty($glue_expense) ? "" : floatval($glue_expense) ?>" 
                                   placeholder="Расход смеси клея, г/м2" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'glue_expense'); $(this).attr('name', 'glue_expense'); $(this).attr('placeholder', 'Расход смеси клея, г/м2');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'glue_expense'); $(this).attr('name', 'glue_expense'); $(this).attr('placeholder', 'Расход смеси клея, г/м2');" 
                                   onfocusout="javascript: $(this).attr('id', 'glue_expense'); $(this).attr('name', 'glue_expense'); $(this).attr('placeholder', 'Расход смеси клея, г/м2');" />
                            <div class="invalid-feedback">Расход смеси клея обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="glue_expense_pet">Расход смеси клея при ламинации ПЭТ, г/м<sup>2</sup></label>
                            <input type="text" 
                                   class="form-control float-only<?=$glue_expense_pet_valid ?>" 
                                   id="glue_expense_pet" 
                                   name="glue_expense_pet" 
                                   value="<?= empty($glue_expense_pet) ? "" : floatval($glue_expense_pet) ?>" 
                                   placeholder="Расход смеси клея при ламинации ПЭТ, г/м2" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'glue_expense_pet'); $(this).attr('name', 'glue_expense_pet'); $(this).attr('placeholder', 'Расход смеси клея при ламинации ПЭТ, г/м2');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'glue_expense_pet'); $(this).attr('name', 'glue_expense_pet'); $(this).attr('placeholder', 'Расход смеси клея при ламинации ПЭТ, г/м2');" 
                                   onfocusout="javascript: $(this).attr('id', 'glue_expense_pet'); $(this).attr('name', 'glue_expense_pet'); $(this).attr('placeholder', 'Расход смеси клея при ламинации ПЭТ, г/м2');" />
                            <div class="invalid-feedback">Расход смеси клея при ламинации ПЭТ обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="solvent_part">Расход <?=$solvent_genitive ?> (кг) на 1 кг клея</label>
                            <input type="text" 
                                   class="form-control float-only<?=$solvent_part_valid ?>" 
                                   id="solvent_part" 
                                   name="solvent_part" 
                                   value="<?= empty($solvent_part) ? "" : floatval($solvent_part) ?>" 
                                   placeholder="Растворитель" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'solvent_part'); $(this).attr('name', 'solvent_part'); $(this).attr('placeholder', 'Растворитель');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'solvent_part'); $(this).attr('name', 'solvent_part'); $(this).attr('placeholder', 'Растворитель');" 
                                   onfocusout="javascript: $(this).attr('id', 'solvent_part'); $(this).attr('name', 'solvent_part'); $(this).attr('placeholder', 'Растворитель');" />
                            <div class="invalid-feedback">Расход <?=$solvent_genitive ?> на 1 кг клея обязательно</div>
                        </div>
                        <button type="submit" id="norm_glue_submit" name="norm_glue_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>