<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$flint_price_valid = '';
$kodak_price_valid = '';
$scotch_price_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_cliche_submit')) {
    if(is_nan(filter_input(INPUT_POST, 'flint_price')) || empty(filter_input(INPUT_POST, 'flint_currency'))) {
        $flint_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(is_nan(filter_input(INPUT_POST, 'kodak_price')) || empty(filter_input(INPUT_POST, 'kodak_currency'))) {
        $kodak_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(is_nan(filter_input(INPUT_POST, 'scotch_price')) || empty(filter_input(INPUT_POST, 'scotch_currency'))) {
        $scotch_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_flint_price = "";
        $old_flint_currency = "";
        $old_kodak_price = "";
        $old_kodak_currency = "";
        $old_scotch_price = "";
        $old_scotch_currency = "";
        
        $sql = "select flint_price, flint_currency, kodak_price, kodak_currency, scotch_price, scotch_currency from norm_cliche order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_flint_price = $row['flint_price'];
            $old_flint_currency = $row['flint_currency'];
            $old_kodak_price = $row['kodak_price'];
            $old_kodak_currency = $row['kodak_currency'];
            $old_scotch_price = $row['scotch_price'];
            $old_scotch_currency = $row['scotch_currency'];
        }

        // Новый объект
        $new_flint_price = filter_input(INPUT_POST, 'flint_price');
        $new_flint_currency = filter_input(INPUT_POST, 'flint_currency');
        $new_kodak_price = filter_input(INPUT_POST, 'kodak_price');
        $new_kodak_currency = filter_input(INPUT_POST, 'kodak_currency');
        $new_scotch_price = filter_input(INPUT_POST, 'scotch_price');
        $new_scotch_currency = filter_input(INPUT_POST, 'scotch_currency');
        
        if($old_flint_price != $new_flint_price || 
                $old_flint_currency != $new_flint_currency || 
                $old_kodak_price != $new_kodak_price || 
                $old_kodak_currency != $new_kodak_currency || 
                $old_scotch_price != $new_scotch_price || 
                $old_scotch_currency != $new_scotch_currency) {
            $sql = "insert into norm_cliche (flint_price, flint_currency, kodak_price, kodak_currency, scotch_price, scotch_currency) values ($new_flint_price, '$new_flint_currency', $new_kodak_price, '$new_kodak_currency', $new_scotch_price, '$new_scotch_currency')";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$flint_price = "";
$flint_currency = "";
$kodak_price = "";
$kodak_currency = "";
$scotch_price = "";
$scotch_currency = "";

$sql = "select flint_price, flint_currency, kodak_price, kodak_currency, scotch_price, scotch_currency from norm_cliche order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $flint_price = $row['flint_price'];
    $flint_currency = $row['flint_currency'];
    $kodak_price = $row['kodak_price'];
    $kodak_currency = $row['kodak_currency'];
    $scotch_price = $row['scotch_price'];
    $scotch_currency = $row['scotch_currency'];
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
            
            if(null !== filter_input(INPUT_POST, 'norm_cliche_submit') && empty($error_message)):
            ?>
            <div class="alert alert-success">Данные сохранены</div>
            <?php
            endif;
            ?>
            <div class="row">
                <div class="col-12 col-md-4 col-lg-2">
                    <form method="post">
                        <div class="form-group">
                            <label for="flint_price">Flint (за см<sup>2</sup>)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only" 
                                       id="flint_price" 
                                       name="flint_price" 
                                       value="<?= empty($flint_price) ? "" : floatval($flint_price) ?>" 
                                       placeholder="Стоимость, за см2" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'flint_price'); $(this).attr('name', 'flint_price'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'flint_price'); $(this).attr('name', 'flint_price'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onfocusout="javascript: $(this).attr('id', 'flint_price'); $(this).attr('name', 'flint_price'); $(this).attr('placeholder', 'Стоимость, за см2');" />
                                <div class="input-group-append">
                                    <select id="flint_currency" name="flint_currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="rub"<?=$flint_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="usd"<?=$flint_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="euro"<?=$flint_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback">Flint обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="kodak_price">Kodak (за см<sup>2</sup>)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only" 
                                       id="kodak_price" 
                                       name="kodak_price" 
                                       value="<?= empty($kodak_price) ? "" : floatval($kodak_price) ?>" 
                                       placeholder="Стоимость, за см2" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'kodak_price'); $(this).attr('name', 'kodak_price'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'kodak_price'); $(this).attr('name', 'kodak_price'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onfocusout="javascript: $(this).attr('id', 'kodak_price'); $(this).attr('name', 'kodak_price'); $(this).attr('placeholder', 'Стоимость, за см2');" />
                                <div class="input-group-append">
                                    <select id="kodak_currency" name="kodak_currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="rub"<?=$kodak_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="usd"<?=$kodak_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="euro"<?=$kodak_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback">Kodak обязательно</div>
                        </div>                        
                        <div class="form-group">
                            <label for="scotch_price">Скотч (за м<sup>2</sup>)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only" 
                                       id="scotch_price" 
                                       name="scotch_price" 
                                       value="<?= empty($scotch_price) ? "" : floatval($scotch_price) ?>" 
                                       placeholder="Скотч, м2" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'scotch_price'); $(this).attr('name', 'scotch_price'); $(this).attr('placeholder', 'Скотч, м2');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'scotch_price'); $(this).attr('name', 'scotch_price'); $(this).attr('placeholder', 'Скотч, м2');" 
                                       onfocusout="javascript: $(this).attr('id', 'scotch_price'); $(this).attr('name', 'scotch_price'); $(this).attr('placeholder', 'Скотч, м2');" />
                                <div class="input-group-append">
                                    <select id="scotch_currency" name="scotch_currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="rub"<?=$scotch_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="usd"<?=$scotch_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="euro"<?=$scotch_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback">Скотч обязательно</div>
                        </div>
                        <button type="submit" id="norm_cliche_submit" name="norm_cliche_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>