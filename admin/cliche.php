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

$flint_valid = '';
$kodak_valid = '';
$tver_valid = '';
$film_valid = '';
$scotch_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_cliche_submit')) {
    if(is_nan(filter_input(INPUT_POST, 'flint')) || empty(filter_input(INPUT_POST, 'flint_currency'))) {
        $flint_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(is_nan(filter_input(INPUT_POST, 'kodak')) || empty(filter_input(INPUT_POST, 'kodak_currency'))) {
        $kodak_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(is_nan(filter_input(INPUT_POST, 'tver')) || empty(filter_input(INPUT_POST, 'tver_currency'))) {
        $tver_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(is_nan(filter_input(INPUT_POST, 'film')) || empty(filter_input(INPUT_POST, 'film_currency'))) {
        $film_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(is_nan(filter_input(INPUT_POST, 'scotch'))) {
        $scotch_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_flint = "";
        $old_kodak = "";
        $old_tver = "";
        $old_film = "";
        $old_flint_currency = "";
        $old_kodak_currency = "";
        $old_tver_currency = "";
        $old_film_currency = "";
        $old_scotch = "";
        $old_scotch_currency = "";
        
        $sql = "select flint, flint_currency, kodak, kodak_currency, tver, tver_currency, film, film_currency, scotch, scotch_currency from norm_cliche order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_flint = $row['flint'];
            $old_kodak = $row['kodak'];
            $old_tver = $row['tver'];
            $old_film = $row['film'];
            $old_flint_currency = $row['flint_currency'];
            $old_kodak_currency = $row['kodak_currency'];
            $old_tver_currency = $row['tver_currency'];
            $old_film_currency = $row['film_currency'];
            $old_scotch = $row['scotch'];
            $old_scotch_currency = $row['scotch_currency'];
        }

        // Новый объект
        $new_flint = filter_input(INPUT_POST, 'flint');
        $new_kodak = filter_input(INPUT_POST, 'kodak');
        $new_tver = filter_input(INPUT_POST, 'tver');
        $new_film = filter_input(INPUT_POST, 'film');
        $new_flint_currency = filter_input(INPUT_POST, 'flint_currency');
        $new_kodak_currency = filter_input(INPUT_POST, 'kodak_currency');
        $new_tver_currency = filter_input(INPUT_POST, 'tver_currency');
        $new_film_currency = filter_input(INPUT_POST, 'film_currency');
        $new_scotch = filter_input(INPUT_POST, 'scotch');
        $new_scotch_currency = filter_input(INPUT_POST, 'scotch_currency');
        
        if($old_flint != $new_flint || 
                $old_flint_currency != $new_flint_currency || 
                $old_kodak != $new_kodak || 
                $old_kodak_currency != $new_kodak_currency || 
                $old_tver != $new_tver || 
                $old_tver_currency != $new_tver_currency || 
                $old_film != $new_film || 
                $old_film_currency != $new_film_currency || 
                $old_scotch != $new_scotch || 
                $old_scotch_currency != $new_scotch_currency) {
            $sql = "insert into norm_cliche (flint, flint_currency, kodak, kodak_currency, tver, tver_currency, film, film_currency, scotch, scotch_currency) values ($new_flint, '$new_flint_currency', $new_kodak, '$new_kodak_currency', $new_tver, '$new_tver_currency', $new_film, '$new_film_currency', $new_scotch, '$new_scotch_currency')";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$flint = "";
$kodak = "";
$tver = "";
$film = "";
$flint_currency = "";
$kodak_currency = "";
$tver_currency = "";
$film_currency = "";
$scotch = "";
$scotch_currency = "";

$sql = "select flint, kodak, flint_currency, kodak_currency, tver, tver_currency, film, film_currency, scotch, scotch_currency from norm_cliche order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $flint = $row['flint'];
    $kodak = $row['kodak'];
    $tver = $row['tver'];
    $film = $row['film'];
    $flint_currency = $row['flint_currency'];
    $kodak_currency = $row['kodak_currency'];
    $tver_currency = $row['tver_currency'];
    $film_currency = $row['film_currency'];
    $scotch = $row['scotch'];
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
                            <label for="flint">Flint (за см<sup>2</sup>)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only" 
                                       id="flint" 
                                       name="flint" 
                                       value="<?= empty($flint) ? "" : floatval($flint) ?>" 
                                       placeholder="Стоимость, за см2" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'flint'); $(this).attr('name', 'flint'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'flint'); $(this).attr('name', 'flint'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onfocusout="javascript: $(this).attr('id', 'flint'); $(this).attr('name', 'flint'); $(this).attr('placeholder', 'Стоимость, за см2');" />
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
                            <label for="kodak">Kodak (за см<sup>2</sup>)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only" 
                                       id="kodak" 
                                       name="kodak" 
                                       value="<?= empty($kodak) ? "" : floatval($kodak) ?>" 
                                       placeholder="Стоимость, за см2" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'kodak'); $(this).attr('name', 'kodak'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'kodak'); $(this).attr('name', 'kodak'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onfocusout="javascript: $(this).attr('id', 'kodak'); $(this).attr('name', 'kodak'); $(this).attr('placeholder', 'Стоимость, за см2');" />
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
                            <label for="tver">Тверь (за см<sup>2</sup>)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only" 
                                       id="tver" 
                                       name="tver" 
                                       value="<?= empty($tver) ? "" : floatval($tver) ?>" 
                                       placeholder="Стоимость, за см2" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'tver'); $(this).attr('name', 'tver'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'tver'); $(this).attr('name', 'tver'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onfocusout="javascript: $(this).attr('id', 'tver'); $(this).attr('name', 'tver'); $(this).attr('placeholder', 'Стоимость, за см2');" />
                                <div class="input-group-append">
                                    <select id="tver_currency" name="tver_currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="rub"<?=$tver_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="usd"<?=$tver_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="euro"<?=$tver_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback">Тверь обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="tver">Плёнка (за см<sup>2</sup>)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only" 
                                       id="film" 
                                       name="film" 
                                       value="<?= empty($film) ? "" : floatval($film) ?>" 
                                       placeholder="Стоимость, за см2" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'film'); $(this).attr('name', 'film'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'film'); $(this).attr('name', 'film'); $(this).attr('placeholder', 'Стоимость, за см2');" 
                                       onfocusout="javascript: $(this).attr('id', 'film'); $(this).attr('name', 'film'); $(this).attr('placeholder', 'Стоимость, за см2');" />
                                <div class="input-group-append">
                                    <select id="film_currency" name="film_currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="rub"<?=$film_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="usd"<?=$film_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="euro"<?=$film_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="invalid-feedback">Плёнка обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="scotch">Скотч (за м<sup>2</sup>)</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control float-only" 
                                       id="scotch" 
                                       name="scotch" 
                                       value="<?= empty($scotch) ? "" : floatval($scotch) ?>" 
                                       placeholder="Скотч, м2" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'scotch'); $(this).attr('name', 'scotch'); $(this).attr('placeholder', 'Скотч, м2');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'scotch'); $(this).attr('name', 'scotch'); $(this).attr('placeholder', 'Скотч, м2');" 
                                       onfocusout="javascript: $(this).attr('id', 'scotch'); $(this).attr('name', 'scotch'); $(this).attr('placeholder', 'Скотч, м2');" />
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