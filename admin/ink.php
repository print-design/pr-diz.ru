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

$c_valid = "";
$c_expense_valid = "";
$m_valid = "";
$m_expense_valid = "";
$y_valid = "";
$y_expense_valid = "";
$k_valid = "";
$k_expense_valid = "";
$white_valid = "";
$white_expense_valid = "";
$panton_valid = "";
$panton_expense_valid = "";
$lacquer_valid = "";
$lacquer_expense_valid = "";
$ink_solvent_valid = "";
$solvent_etoxipropanol_valid = "";
$solvent_flexol82_valid = "";
$lacquer_solvent_valid = "";
$min_price_valid = "";

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_ink_submit')) {
    if(empty(filter_input(INPUT_POST, 'c')) || empty(filter_input(INPUT_POST, 'c_currency'))) {
        $c_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'c_expense'))) {
        $c_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'm')) || empty(filter_input(INPUT_POST, 'm_currency'))) {
        $m_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'm_expense'))) {
        $m_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'y')) || empty(filter_input(INPUT_POST, 'y_currency'))) {
        $y_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'y_expense'))) {
        $y_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'k')) || empty(filter_input(INPUT_POST, 'k_currency'))) {
        $k_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'k_expense'))) {
        $k_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'white')) || empty(filter_input(INPUT_POST, 'white_currency'))) {
        $white_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'white_expense'))) {
        $white_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'panton')) || empty(filter_input(INPUT_POST, 'panton_currency'))) {
        $panton_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'panton_expense'))) {
        $panton_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lacquer')) || empty(filter_input(INPUT_POST, 'lacquer_currency'))) {
        $lacquer_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lacquer_expense'))) {
        $lacquer_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'ink_solvent'))) {
        $ink_solvent_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent_etoxipropanol')) || empty(filter_input(INPUT_POST, 'solvent_etoxipropanol_currency'))) {
        $solvent_etoxipropanol_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent_flexol82')) || empty(filter_input(INPUT_POST, 'solvent_flexol82_currency'))) {
        $solvent_flexol82_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lacquer_solvent'))) {
        $lacquer_solvent_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'min_price'))) {
        $min_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_c = "";
        $old_c_currency = "";
        $old_c_expense = "";
        $old_m = "";
        $old_m_currency = "";
        $old_m_expense = "";
        $old_y = "";
        $old_y_currency = "";
        $old_y_expense = "";
        $old_k = "";
        $old_k_currency = "";
        $old_k_expense = "";
        $old_white = "";
        $old_white_currency = "";
        $old_white_expense = "";
        $old_panton = "";
        $old_panton_currency = "";
        $old_panton_expense = "";
        $old_lacquer = "";
        $old_lacquer_currency = "";
        $old_lacquer_expense = "";
        $old_ink_solvent = "";
        $old_solvent_etoxipropanol = "";
        $old_solvent_etoxipropanol_currency = "";
        $old_solvent_flexol82 = "";
        $old_solvent_flexol82_currency = "";
        $old_lacquer_solvent = "";
        $old_min_price = "";
        
        $sql = "select c, c_currency, c_expense, m, m_currency, m_expense, y, y_currency, y_expense, k, k_currency, k_expense, white, white_currency, white_expense, panton, panton_currency, panton_expense, lacquer, lacquer_currency, lacquer_expense, ink_solvent, solvent_etoxipropanol, solvent_etoxipropanol_currency, solvent_flexol82, solvent_flexol82_currency, lacquer_solvent, min_price from norm_ink order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_c = $row["c"];
            $old_c_currency = $row["c_currency"];
            $old_c_expense = $row['c_expense'];
            $old_m = $row["m"];
            $old_m_currency = $row["m_currency"];
            $old_m_expense = $row['m_expense'];
            $old_y = $row["y"];
            $old_y_currency = $row["y_currency"];
            $old_y_expense = $row['y_expense'];
            $old_k = $row["k"];
            $old_k_currency = $row["k_currency"];
            $old_k_expense = $row['k_expense'];
            $old_white = $row["white"];
            $old_white_currency = $row["white_currency"];
            $old_white_expense = $row['white_expense'];
            $old_panton = $row["panton"];
            $old_panton_currency = $row["panton_currency"];
            $old_panton_expense = $row['panton_expense'];
            $old_lacquer = $row["lacquer"];
            $old_lacquer_currency = $row["lacquer_currency"];
            $old_lacquer_expense = $row['lacquer_expense'];
            $old_ink_solvent = $row["ink_solvent"];
            $old_solvent_etoxipropanol = $row["solvent_etoxipropanol"];
            $old_solvent_etoxipropanol_currency = $row["solvent_etoxipropanol_currency"];
            $old_solvent_flexol82 = $row['solvent_flexol82'];
            $old_solvent_flexol82_currency = $row['solvent_flexol82_currency'];
            $old_lacquer_solvent = $row['lacquer_solvent'];
            $old_min_price = $row['min_price'];
        }
        
        // Новый объект
        $new_c = filter_input(INPUT_POST, "c");
        $new_c_currency = filter_input(INPUT_POST, "c_currency");
        $new_c_expense = filter_input(INPUT_POST, 'c_expense');
        $new_m = filter_input(INPUT_POST, "m");
        $new_m_currency = filter_input(INPUT_POST, "m_currency");
        $new_m_expense = filter_input(INPUT_POST, 'm_expense');
        $new_y = filter_input(INPUT_POST, "y");
        $new_y_currency = filter_input(INPUT_POST, "y_currency");
        $new_y_expense = filter_input(INPUT_POST, 'y_expense');
        $new_k = filter_input(INPUT_POST, "k");
        $new_k_currency = filter_input(INPUT_POST, "k_currency");
        $new_k_expense = filter_input(INPUT_POST, 'k_expense');
        $new_white = filter_input(INPUT_POST, "white");
        $new_white_currency = filter_input(INPUT_POST, "white_currency");
        $new_white_expense = filter_input(INPUT_POST, 'white_expense');
        $new_panton = filter_input(INPUT_POST, "panton");
        $new_panton_currency = filter_input(INPUT_POST, "panton_currency");
        $new_panton_expense = filter_input(INPUT_POST, 'panton_expense');
        $new_lacquer = filter_input(INPUT_POST, "lacquer");
        $new_lacquer_currency = filter_input(INPUT_POST, "lacquer_currency");
        $new_lacquer_expense = filter_input(INPUT_POST, 'lacquer_expense');
        $new_ink_solvent = filter_input(INPUT_POST, "ink_solvent");
        $new_solvent_etoxipropanol = filter_input(INPUT_POST, "solvent_etoxipropanol");
        $new_solvent_etoxipropanol_currency = filter_input(INPUT_POST, "solvent_etoxipropanol_currency");
        $new_solvent_flexol82 = filter_input(INPUT_POST, 'solvent_flexol82');
        $new_solvent_flexol82_currency = filter_input(INPUT_POST, 'solvent_flexol82_currency');
        $new_lacquer_solvent = filter_input(INPUT_POST, 'lacquer_solvent');
        $new_min_price = filter_input(INPUT_POST, 'min_price');
        
        if($old_c != $new_c ||
                $old_c_currency != $new_c_currency || 
                $old_c_expense != $new_c_expense ||
                $old_m != $new_m ||
                $old_m_currency != $new_m_currency || 
                $old_m_expense != $new_m_expense ||
                $old_y != $new_y ||
                $old_y_currency != $new_y_currency || 
                $old_y_expense != $new_y_expense ||
                $old_k != $new_k ||
                $old_k_currency != $new_k_currency || 
                $old_k_expense != $new_k_expense ||
                $old_white != $new_white ||
                $old_white_currency != $new_white_currency || 
                $old_white_expense != $new_white_expense ||
                $old_panton != $new_panton ||
                $old_panton_currency != $new_panton_currency || 
                $old_panton_expense != $new_panton_expense ||
                $old_lacquer != $new_lacquer ||
                $old_lacquer_currency != $new_lacquer_currency || 
                $old_lacquer_expense != $new_lacquer_expense ||
                $old_ink_solvent != $new_ink_solvent ||
                $old_solvent_etoxipropanol != $new_solvent_etoxipropanol ||
                $old_solvent_etoxipropanol_currency != $new_solvent_etoxipropanol_currency || 
                $old_solvent_flexol82 != $new_solvent_flexol82 || 
                $old_solvent_flexol82_currency != $new_solvent_flexol82_currency || 
                $old_lacquer_solvent != $new_lacquer_solvent || 
                $old_min_price != $new_min_price) {
            $sql = "insert into norm_ink (c, c_currency, c_expense, m, m_currency, m_expense, y, y_currency, y_expense, k, k_currency, k_expense, white, white_currency, white_expense, panton, panton_currency, panton_expense, lacquer, lacquer_currency, lacquer_expense, ink_solvent, solvent_etoxipropanol, solvent_etoxipropanol_currency, solvent_flexol82, solvent_flexol82_currency, lacquer_solvent, min_price) values ($new_c, '$new_c_currency', $new_c_expense, $new_m, '$new_m_currency', $new_m_expense, $new_y, '$new_y_currency', $new_y_expense, $new_k, '$new_k_currency', $new_k_expense, $new_white, '$new_white_currency', $new_white_expense, $new_panton, '$new_panton_currency', $new_panton_expense, $new_lacquer, '$new_lacquer_currency', $new_lacquer_expense, $new_ink_solvent, $new_solvent_etoxipropanol, '$new_solvent_etoxipropanol_currency', $new_solvent_flexol82, '$new_solvent_flexol82_currency', $new_lacquer_solvent, $new_min_price)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$c = "";
$c_currency = "";
$c_expense = "";
$m = "";
$m_currency = "";
$m_expense = "";
$y = "";
$y_currency = "";
$y_expense = "";
$k = "";
$k_currency = "";
$k_expense = "";
$white = "";
$white_currency = "";
$white_expense = "";
$panton = "";
$panton_currency = "";
$panton_expense = "";
$lacquer = "";
$lacquer_currency = "";
$lacquer_expense = "";
$ink_solvent = "";
$solvent_etoxipropanol = "";
$solvent_etoxipropanol_currency = "";
$solvent_flexol82 = "";
$solvent_flexol82_currency = "";
$lacquer_solvent = "";
$min_price = "";

$sql = "select c, c_currency, c_expense, m, m_currency, m_expense, y, y_currency, y_expense, k, k_currency, k_expense, white, white_currency, white_expense, panton, panton_currency, panton_expense, lacquer, lacquer_currency, lacquer_expense, ink_solvent, solvent_etoxipropanol, solvent_etoxipropanol_currency, solvent_flexol82, solvent_flexol82_currency, lacquer_solvent, min_price from norm_ink order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $c = $row["c"];
    $c_currency = $row["c_currency"];
    $c_expense = $row['c_expense'];
    $m = $row["m"];
    $m_currency = $row["m_currency"];
    $m_expense = $row['m_expense'];
    $y = $row["y"];
    $y_currency = $row["y_currency"];
    $y_expense = $row['y_expense'];
    $k = $row["k"];
    $k_currency = $row["k_currency"];
    $k_expense = $row['k_expense'];
    $white = $row["white"];
    $white_currency = $row["white_currency"];
    $white_expense = $row['white_expense'];
    $panton = $row["panton"];
    $panton_currency = $row["panton_currency"];
    $panton_expense = $row['panton_expense'];
    $lacquer = $row["lacquer"];
    $lacquer_currency = $row["lacquer_currency"];
    $lacquer_expense = $row['lacquer_expense'];
    $ink_solvent = $row["ink_solvent"];
    $solvent_etoxipropanol = $row["solvent_etoxipropanol"];
    $solvent_etoxipropanol_currency = $row["solvent_etoxipropanol_currency"];
    $solvent_flexol82 = $row['solvent_flexol82'];
    $solvent_flexol82_currency = $row['solvent_flexol82_currency'];
    $lacquer_solvent = $row['lacquer_solvent'];
    $min_price = $row['min_price'];
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
            
            if(null !== filter_input(INPUT_POST, 'norm_ink_submit') && empty($error_message)):
            ?>
            <div class="alert alert-success">Данные сохранены</div>
            <?php
            endif;
            ?>
            <form method="post">
                <div class="row">
                    <div class="col-12 col-md-8 col-lg-4 d-table">
                        <div class="d-table-row">
                            <div class="d-table-cell w-50 pr-3">
                                <div class="form-group">
                                    <label for="c">C (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
                                               id="c" 
                                               name="c" 
                                               value="<?= empty($c) || $c == 0.0 ? "" : floatval($c) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'c'); $(this).attr('name', 'c'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'c'); $(this).attr('name', 'c'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'c'); $(this).attr('name', 'c'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                        <div class="input-group-append">
                                            <select id="c_currency" name="c_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$c_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$c_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$c_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">C обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell w-50 pl-3">
                                <div class="form-group">
                                    <label for="c_expense">Расход C (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="c_expense" 
                                           name="c_expense" 
                                           value="<?= empty($c_expense) || $c_expense == 0.0 ? "" : floatval($c_expense) ?>" 
                                           placeholder="Расход C (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'c_expense'); $(this).attr('name', 'c_expense'); $(this).attr('placeholder', 'Расход C (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'c_expense'); $(this).attr('name', 'c_expense'); $(this).attr('placeholder', 'Расход C (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'c_expense'); $(this).attr('name', 'c_expense'); $(this).attr('placeholder', 'Расход C (г/м2)');" />
                                    <div class="invalid-feedback">Расход C обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="m">M (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
                                               id="m" 
                                               name="m" 
                                               value="<?= empty($m) || $m == 0.0 ? "" : floatval($m) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'm'); $(this).attr('name', 'm'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'm'); $(this).attr('name', 'm'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'm'); $(this).attr('name', 'm'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                        <div class="input-group-append">
                                            <select id="m_currency" name="m_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$m_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$m_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$m_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">M обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="m_expense">Расход M (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="m_expense" 
                                           name="m_expense" 
                                           value="<?= empty($m_expense) || $m_expense == 0.0 ? "" : floatval($m_expense) ?>" 
                                           placeholder="Расход M (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'm_expense'); $(this).attr('name', 'm_expense'); $(this).attr('placeholder', 'Расход M (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'm_expense'); $(this).attr('name', 'm_expense'); $(this).attr('placeholder', 'Расход M (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'm_expense'); $(this).attr('name', 'm_expense'); $(this).attr('placeholder', 'Расход M (г/м2)');" />
                                    <div class="invalid-feedback">Расход M обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="y">Y (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
                                               id="y" 
                                               name="y" 
                                               value="<?= empty($y) || $y == 0.0 ? "" : floatval($y) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'y'); $(this).attr('name', 'y'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'y'); $(this).attr('name', 'y'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'y'); $(this).attr('name', 'y'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                        <div class="input-group-append">
                                            <select id="y_currency" name="y_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$y_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$y_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$y_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Y обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="y_expense">Расход Y (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="y_expense" 
                                           name="y_expense" 
                                           value="<?= empty($y_expense) || $y_expense == 0.0 ? "" : floatval($y_expense) ?>" 
                                           placeholder="Расход Y (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'y_expense'); $(this).attr('name', 'y_expense'); $(this).attr('placeholder', 'Расход Y (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'y_expense'); $(this).attr('name', 'y_expense'); $(this).attr('placeholder', 'Расход Y (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'y_expense'); $(this).attr('name', 'y_expense'); $(this).attr('placeholder', 'Расход Y (г/м2)');" />
                                    <div class="invalid-feedback">Расход Y обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="k">K (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
                                               id="k" 
                                               name="k" 
                                               value="<?= empty($k) || $k == 0.0 ? "" : floatval($k) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'k'); $(this).attr('name', 'k'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'k'); $(this).attr('name', 'k'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'k'); $(this).attr('name', 'k'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                        <div class="input-group-append">
                                            <select id="k_currency" name="k_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$k_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$k_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$k_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">K обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="k_expense">Расход K (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="k_expense" 
                                           name="k_expense" 
                                           value="<?= empty($k_expense) || $k_expense == 0.0 ? "" : floatval($k_expense) ?>" 
                                           placeholder="Расход K (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'k_expense'); $(this).attr('name', 'k_expense'); $(this).attr('placeholder', 'Расход K (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'k_expense'); $(this).attr('name', 'k_expense'); $(this).attr('placeholder', 'Расход K (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'k_expense'); $(this).attr('name', 'k_expense'); $(this).attr('placeholder', 'Расход K (г/м2)');" />
                                    <div class="invalid-feedback">Расход K обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="panton">Пантоны (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
                                               id="panton" 
                                               name="panton" 
                                               value="<?= empty($panton) || $panton == 0.0 ? "" : floatval($panton) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'panton'); $(this).attr('name', 'panton'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'panton'); $(this).attr('name', 'panton'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'panton'); $(this).attr('name', 'panton'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                        <div class="input-group-append">
                                            <select id="panton_currency" name="panton_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$panton_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$panton_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$panton_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Пантоны обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="panton_expense">Расход пантонов (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="panton_expense" 
                                           name="panton_expense" 
                                           value="<?= empty($panton_expense) || $panton_expense == 0.0 ? "" : floatval($panton_expense) ?>" 
                                           placeholder="Расход пантонов (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'panton_expense'); $(this).attr('name', 'panton_expense'); $(this).attr('placeholder', 'Расход пантонов (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'panton_expense'); $(this).attr('name', 'panton_expense'); $(this).attr('placeholder', 'Расход пантонов (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'panton_expense'); $(this).attr('name', 'panton_expense'); $(this).attr('placeholder', 'Расход пантонов (г/м2)');" />
                                    <div class="invalid-feedback">Расход пантонов обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="white">Белая (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
                                               id="white" 
                                               name="white" 
                                               value="<?= empty($white) || $white == 0.0 ? "" : floatval($white) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'white'); $(this).attr('name', 'white'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'white'); $(this).attr('name', 'white'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'white'); $(this).attr('name', 'white'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                        <div class="input-group-append">
                                            <select id="white_currency" name="white_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$white_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$white_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$white_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Белая обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="white_expense">Расход белой (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="white_expense" 
                                           name="white_expense" 
                                           value="<?= empty($white_expense) || $white_expense == 0.0 ? "" : floatval($white_expense) ?>" 
                                           placeholder="Расход белой (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'white_expense'); $(this).attr('name', 'white_expense'); $(this).attr('placeholder', 'Расход белой (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'white_expense'); $(this).attr('name', 'white_expense'); $(this).attr('placeholder', 'Расход белой (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'white_expense'); $(this).attr('name', 'white_expense'); $(this).attr('placeholder', 'Расход белой (г/м2)');" />
                                    <div class="invalid-feedback">Расход белой обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="lacquer">Лак (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
                                               id="lacquer" 
                                               name="lacquer" 
                                               value="<?= empty($lacquer) || $lacquer == 0.0 ? "" : floatval($lacquer) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lacquer'); $(this).attr('name', 'lacquer'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lacquer'); $(this).attr('name', 'lacquer'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'lacquer'); $(this).attr('name', 'lacquer'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                        <div class="input-group-append">
                                            <select id="lacquer_currency" name="lacquer_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$lacquer_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$lacquer_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$lacquer_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Лак обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="lacquer_expense">Расход лака (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="lacquer_expense" 
                                           name="lacquer_expense" 
                                           value="<?= empty($lacquer_expense) || $lacquer_expense == 0.0 ? "" : floatval($lacquer_expense) ?>" 
                                           placeholder="Расход лака (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'lacquer_expense'); $(this).attr('name', 'lacquer_expense'); $(this).attr('placeholder', 'Расход лака (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'lacquer_expense'); $(this).attr('name', 'lacquer_expense'); $(this).attr('placeholder', 'Расход лака (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'lacquer_expense'); $(this).attr('name', 'lacquer_expense'); $(this).attr('placeholder', 'Расход лака (г/м2)');" />
                                    <div class="invalid-feedback">Расход лака обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="solvent_etoxipropanol">Стоимость растворителя "этоксипропанол"<br /> (за кг)<br />для красок (на всех машинах кроме Comiflex)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
                                               id="solvent_etoxipropanol" 
                                               name="solvent_etoxipropanol" 
                                               value="<?= empty($solvent_etoxipropanol) || $solvent_etoxipropanol == 0.0 ? "" : floatval($solvent_etoxipropanol) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'solvent_etoxipropanol'); $(this).attr('name', 'solvent_etoxipropanol'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'solvent_etoxipropanol'); $(this).attr('name', 'solvent_etoxipropanol'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'solvent_etoxipropanol'); $(this).attr('name', 'solvent_etoxipropanol'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                        <div class="input-group-append">
                                            <select id="solvent_etoxipropanol_currency" name="solvent_etoxipropanol_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$solvent_etoxipropanol_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$solvent_etoxipropanol_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$solvent_etoxipropanol_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Стоимость растворителя для красок обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="solvent_flexol82">Стоимость растворителя "флексоль 82"<br />(за кг)<br />для лака и (только на Comiflex) для красок</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
                                               id="solvent_flexol82" 
                                               name="solvent_flexol82" 
                                               value="<?= empty($solvent_flexol82) || $solvent_flexol82 == 0.0 ? "" : floatval($solvent_flexol82) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'solvent_flexol82'); $(this).attr('name', 'solvent_flexol82'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'solvent_flexol82'); $(this).attr('name', 'solvent_flexol82'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'solvent_flexol82'); $(this).attr('name', 'solvent_flexol82'); $(this).attr('placeholder', 'Стоимость, за кг');" />
                                        <div class="input-group-append">
                                            <select id="solvent_flexol82_currency" name="solvent_flexol82_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$solvent_flexol82_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$solvent_flexol82_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$solvent_flexol82_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Стоимость растворителя "флексоль 82" обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="ink_solvent">Отношение краски к растворителю (в процентах)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="ink_solvent" 
                                               name="ink_solvent" 
                                               value="<?= empty($ink_solvent) || $ink_solvent == 0.0 ? "" : floatval($ink_solvent) ?>" 
                                               placeholder="В процентах" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'ink_solvent'); $(this).attr('name', 'ink_solvent'); $(this).attr('placeholder', 'В процентах');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'ink_solvent'); $(this).attr('name', 'ink_solvent'); $(this).attr('placeholder', 'В процентах');" 
                                               onfocusout="javascript: $(this).attr('id', 'ink_solvent'); $(this).attr('name', 'ink_solvent'); $(this).attr('placeholder', 'В процентах');" />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <div class="invalid-feedback">Отношение краски к растворителю обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="lacquer_solvent">Отношение лака к растворителю (в процентах)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="lacquer_solvent" 
                                               name="lacquer_solvent" 
                                               value="<?= empty($lacquer_solvent) || $lacquer_solvent == 0.0 ? "" : floatval($lacquer_solvent) ?>" 
                                               placeholder="В процентах" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lacquer_solvent'); $(this).attr('name', 'lacquer_solvent'); $(this).attr('placeholder', 'В процентах');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lacquer_solvent'); $(this).attr('name', 'lacquer_solvent'); $(this).attr('placeholder', 'В процентах');" 
                                               onfocusout="javascript: $(this).attr('id', 'lacquer_solvent'); $(this).attr('name', 'lacquer_solvent'); $(this).attr('placeholder', 'В процентах');" />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <div class="invalid-feedback">Отношение лака к растворителю обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="min_price">Ограничение на минимальную стоимость, руб</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="min_price" 
                                               name="min_price" 
                                               value="<?= empty($min_price) || $min_price == 0.0 ? "" : floatval($min_price) ?>" 
                                               placeholder="Мин. стоимость, руб" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'min_price'); $(this).attr('name', 'min_price'); $(this).attr('placeholder', 'Мин. стоимость, руб');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'min_price'); $(this).attr('name', 'min_price'); $(this).attr('placeholder', 'Мин. стоимость, руб');" 
                                               onfocusout="javascript: $(this).attr('id', 'min_price'); $(this).attr('name', 'min_price'); $(this).attr('placeholder', 'Мин. стоимость, руб');" />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <div class="invalid-feedback">Ограничение на минимальную стоимость обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3"></div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <button type="submit" id="norm_ink_submit" name="norm_ink_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                            </div>
                            <div class="d-table-cell pl-3"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // В поле "процент" ограничиваем значения: целые числа от 1 до 100
            $('#ink_solvent').keydown(function(e) {
                if(!KeyDownLimitFloatValue($(e.target), e, 100)) {
                    return false;
                }
            });
    
            $("#ink_solvent").change(function(){
                ChangeLimitFloatValue($(this), 100);
            });
        </script>
    </body>
</html>