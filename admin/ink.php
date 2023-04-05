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

$c_price_valid = "";
$c_expense_valid = "";
$m_price_valid = "";
$m_expense_valid = "";
$y_price_valid = "";
$y_expense_valid = "";
$k_price_valid = "";
$k_expense_valid = "";
$white_price_valid = "";
$white_expense_valid = "";
$panton_price_valid = "";
$panton_expense_valid = "";
$lacquer_glossy_price_valid = "";
$lacquer_glossy_expense_valid = "";
$lacquer_matte_price_valid = "";
$lacquer_matte_expense_valid = "";
$solvent_etoxipropanol_price_valid = "";
$solvent_flexol82_price_valid = "";
$solvent_part_valid = "";
$min_price_per_ink_valid = "";

$self_adhesive_laquer_price_valid = "";
$self_adhesive_laquer_expense_valid = "";

$min_percent_valid = "";

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_ink_submit')) {
    if(empty(filter_input(INPUT_POST, 'c_price')) || empty(filter_input(INPUT_POST, 'c_currency'))) {
        $c_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'c_expense'))) {
        $c_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'm_price')) || empty(filter_input(INPUT_POST, 'm_currency'))) {
        $m_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'm_expense'))) {
        $m_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'y_price')) || empty(filter_input(INPUT_POST, 'y_currency'))) {
        $y_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'y_expense'))) {
        $y_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'k_price')) || empty(filter_input(INPUT_POST, 'k_currency'))) {
        $k_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'k_expense'))) {
        $k_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'white_price')) || empty(filter_input(INPUT_POST, 'white_currency'))) {
        $white_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'white_expense'))) {
        $white_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'panton_price')) || empty(filter_input(INPUT_POST, 'panton_currency'))) {
        $panton_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'panton_expense'))) {
        $panton_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lacquer_glossy_price')) || empty(filter_input(INPUT_POST, 'lacquer_glossy_currency'))) {
        $lacquer_glossy_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lacquer_glossy_expense'))) {
        $lacquer_glossy_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lacquer_matte_price')) || empty(filter_input(INPUT_POST, 'lacquer_matte_currency'))) {
        $lacquer_matte_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lacquer_matte_expense'))) {
        $lacquer_matte_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent_etoxipropanol_price')) || empty(filter_input(INPUT_POST, 'solvent_etoxipropanol_currency'))) {
        $solvent_etoxipropanol_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent_flexol82_price')) || empty(filter_input(INPUT_POST, 'solvent_flexol82_currency'))) {
        $solvent_flexol82_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent_part'))) {
        $solvent_part_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'min_price_per_ink') === null) {
        $min_price_per_ink_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'self_adhesive_laquer_price')) || empty(filter_input(INPUT_POST, 'self_adhesive_laquer_currency'))) {
        $self_adhesive_laquer_price_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'self_adhesive_laquer_expense'))) {
        $self_adhesive_laquer_expense_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'min_percent') === null) {
        $min_percent_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_c_price = "";
        $old_c_currency = "";
        $old_c_expense = "";
        $old_m_price = "";
        $old_m_currency = "";
        $old_m_expense = "";
        $old_y_price = "";
        $old_y_currency = "";
        $old_y_expense = "";
        $old_k_price = "";
        $old_k_currency = "";
        $old_k_expense = "";
        $old_white_price = "";
        $old_white_currency = "";
        $old_white_expense = "";
        $old_panton_price = "";
        $old_panton_currency = "";
        $old_panton_expense = "";
        $old_lacquer_glossy_price = "";
        $old_lacquer_glossy_currency = "";
        $old_lacquer_glossy_expense = "";
        $old_lacquer_matte_price = "";
        $old_lacquer_matte_currency = "";
        $old_lacquer_matte_expense = "";
        $old_solvent_etoxipropanol_price = "";
        $old_solvent_etoxipropanol_currency = "";
        $old_solvent_flexol82_price = "";
        $old_solvent_flexol82_currency = "";
        $old_solvent_part = "";
        $old_min_price_per_ink = "";
        $old_self_adhesive_laquer_price = "";
        $old_self_adhesive_laquer_currency = "";
        $old_self_adhesive_laquer_expense = "";
        $old_min_percent = "";
        
        $sql = "select c_price, c_currency, c_expense, m_price, m_currency, m_expense, y_price, y_currency, y_expense, k_price, k_currency, k_expense, white_price, white_currency, white_expense, panton_price, panton_currency, panton_expense, lacquer_glossy_price, lacquer_glossy_currency, lacquer_glossy_expense, lacquer_matte_price, lacquer_matte_currency, lacquer_matte_expense, solvent_etoxipropanol_price, solvent_etoxipropanol_currency, solvent_flexol82_price, solvent_flexol82_currency, solvent_part, min_price_per_ink, self_adhesive_laquer_price, self_adhesive_laquer_currency, self_adhesive_laquer_expense, min_percent from norm_ink order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_c_price = $row["c_price"];
            $old_c_currency = $row["c_currency"];
            $old_c_expense = $row['c_expense'];
            $old_m_price = $row["m_price"];
            $old_m_currency = $row["m_currency"];
            $old_m_expense = $row['m_expense'];
            $old_y_price = $row["y_price"];
            $old_y_currency = $row["y_currency"];
            $old_y_expense = $row['y_expense'];
            $old_k_price = $row["k_price"];
            $old_k_currency = $row["k_currency"];
            $old_k_expense = $row['k_expense'];
            $old_white_price = $row["white_price"];
            $old_white_currency = $row["white_currency"];
            $old_white_expense = $row['white_expense'];
            $old_panton_price = $row["panton_price"];
            $old_panton_currency = $row["panton_currency"];
            $old_panton_expense = $row['panton_expense'];
            $old_lacquer_glossy_price = $row["lacquer_glossy_price"];
            $old_lacquer_glossy_currency = $row["lacquer_glossy_currency"];
            $old_lacquer_glossy_expense = $row['lacquer_glossy_expense'];
            $old_lacquer_matte_price  = $row['lacquer_matte_price'];
            $old_lacquer_matte_currency  = $row['lacquer_matte_currency'];
            $old_lacquer_matte_expense  = $row['lacquer_matte_expense'];
            $old_solvent_etoxipropanol_price = $row["solvent_etoxipropanol_price"];
            $old_solvent_etoxipropanol_currency = $row["solvent_etoxipropanol_currency"];
            $old_solvent_flexol82_price = $row['solvent_flexol82_price'];
            $old_solvent_flexol82_currency = $row['solvent_flexol82_currency'];
            $old_solvent_part = $row['solvent_part'];
            $old_min_price_per_ink = $row['min_price_per_ink'];
            $old_self_adhesive_laquer_price = $row['self_adhesive_laquer_price'];
            $old_self_adhesive_laquer_currency = $row['self_adhesive_laquer_currency'];
            $old_self_adhesive_laquer_expense = $row['self_adhesive_laquer_expense'];
            $old_min_percent = $row['min_percent'];
        }
        
        // Новый объект
        $new_c_price = filter_input(INPUT_POST, "c_price");
        $new_c_currency = filter_input(INPUT_POST, "c_currency");
        $new_c_expense = filter_input(INPUT_POST, 'c_expense');
        $new_m_price = filter_input(INPUT_POST, "m_price");
        $new_m_currency = filter_input(INPUT_POST, "m_currency");
        $new_m_expense = filter_input(INPUT_POST, 'm_expense');
        $new_y_price = filter_input(INPUT_POST, "y_price");
        $new_y_currency = filter_input(INPUT_POST, "y_currency");
        $new_y_expense = filter_input(INPUT_POST, 'y_expense');
        $new_k_price = filter_input(INPUT_POST, "k_price");
        $new_k_currency = filter_input(INPUT_POST, "k_currency");
        $new_k_expense = filter_input(INPUT_POST, 'k_expense');
        $new_white_price = filter_input(INPUT_POST, "white_price");
        $new_white_currency = filter_input(INPUT_POST, "white_currency");
        $new_white_expense = filter_input(INPUT_POST, 'white_expense');
        $new_panton_price = filter_input(INPUT_POST, "panton_price");
        $new_panton_currency = filter_input(INPUT_POST, "panton_currency");
        $new_panton_expense = filter_input(INPUT_POST, 'panton_expense');
        $new_lacquer_glossy_price = filter_input(INPUT_POST, "lacquer_glossy_price");
        $new_lacquer_glossy_currency = filter_input(INPUT_POST, "lacquer_glossy_currency");
        $new_lacquer_glossy_expense = filter_input(INPUT_POST, 'lacquer_glossy_expense');
        $new_lacquer_matte_price = filter_input(INPUT_POST, "lacquer_matte_price");
        $new_lacquer_matte_currency = filter_input(INPUT_POST, "lacquer_matte_currency");
        $new_lacquer_matte_expense = filter_input(INPUT_POST, 'lacquer_matte_expense');
        $new_solvent_etoxipropanol_price = filter_input(INPUT_POST, "solvent_etoxipropanol_price");
        $new_solvent_etoxipropanol_currency = filter_input(INPUT_POST, "solvent_etoxipropanol_currency");
        $new_solvent_flexol82_price = filter_input(INPUT_POST, 'solvent_flexol82_price');
        $new_solvent_flexol82_currency = filter_input(INPUT_POST, 'solvent_flexol82_currency');
        $new_solvent_part = filter_input(INPUT_POST, 'solvent_part');
        $new_min_price_per_ink = filter_input(INPUT_POST, 'min_price_per_ink');
        $new_self_adhesive_laquer_price = filter_input(INPUT_POST, 'self_adhesive_laquer_price');
        $new_self_adhesive_laquer_currency = filter_input(INPUT_POST, 'self_adhesive_laquer_currency');
        $new_self_adhesive_laquer_expense = filter_input(INPUT_POST, 'self_adhesive_laquer_expense');
        $new_min_percent = filter_input(INPUT_POST, 'min_percent');
        
        if($old_c_price != $new_c_price ||
                $old_c_currency != $new_c_currency || 
                $old_c_expense != $new_c_expense ||
                $old_m_price != $new_m_price ||
                $old_m_currency != $new_m_currency || 
                $old_m_expense != $new_m_expense ||
                $old_y_price != $new_y_price ||
                $old_y_currency != $new_y_currency || 
                $old_y_expense != $new_y_expense ||
                $old_k_price != $new_k_price ||
                $old_k_currency != $new_k_currency || 
                $old_k_expense != $new_k_expense ||
                $old_white_price != $new_white_price ||
                $old_white_currency != $new_white_currency || 
                $old_white_expense != $new_white_expense ||
                $old_panton_price != $new_panton_price ||
                $old_panton_currency != $new_panton_currency || 
                $old_panton_expense != $new_panton_expense ||
                $old_lacquer_glossy_price != $new_lacquer_glossy_price ||
                $old_lacquer_glossy_currency != $new_lacquer_glossy_currency || 
                $old_lacquer_glossy_expense != $new_lacquer_glossy_expense ||
                $old_lacquer_matte_price != $new_lacquer_matte_price ||
                $old_lacquer_matte_currency != $new_lacquer_matte_currency ||
                $old_lacquer_matte_expense != $new_lacquer_matte_expense ||
                $old_solvent_etoxipropanol_price != $new_solvent_etoxipropanol_price ||
                $old_solvent_etoxipropanol_currency != $new_solvent_etoxipropanol_currency || 
                $old_solvent_flexol82_price != $new_solvent_flexol82_price || 
                $old_solvent_flexol82_currency != $new_solvent_flexol82_currency || 
                $old_solvent_part != $new_solvent_part || 
                $old_min_price_per_ink != $new_min_price_per_ink || 
                $old_self_adhesive_laquer_price != $new_self_adhesive_laquer_price || 
                $old_self_adhesive_laquer_currency != $new_self_adhesive_laquer_currency || 
                $old_self_adhesive_laquer_expense != $new_self_adhesive_laquer_expense || 
                $old_min_percent != $new_min_percent) {
            $sql = "insert into norm_ink (c_price, c_currency, c_expense, m_price, m_currency, m_expense, y_price, y_currency, y_expense, k_price, k_currency, k_expense, white_price, white_currency, white_expense, panton_price, panton_currency, panton_expense, lacquer_glossy_price, lacquer_glossy_currency, lacquer_glossy_expense, lacquer_matte_price, lacquer_matte_currency, lacquer_matte_expense, solvent_etoxipropanol_price, solvent_etoxipropanol_currency, solvent_flexol82_price, solvent_flexol82_currency, solvent_part, min_price_per_ink, self_adhesive_laquer_price, self_adhesive_laquer_currency, self_adhesive_laquer_expense, min_percent) values ($new_c_price, '$new_c_currency', $new_c_expense, $new_m_price, '$new_m_currency', $new_m_expense, $new_y_price, '$new_y_currency', $new_y_expense, $new_k_price, '$new_k_currency', $new_k_expense, $new_white_price, '$new_white_currency', $new_white_expense, $new_panton_price, '$new_panton_currency', $new_panton_expense, $new_lacquer_glossy_price, '$new_lacquer_glossy_currency', $new_lacquer_glossy_expense, $new_lacquer_matte_price, '$new_lacquer_matte_currency', $new_lacquer_matte_expense, $new_solvent_etoxipropanol_price, '$new_solvent_etoxipropanol_currency', $new_solvent_flexol82_price, '$new_solvent_flexol82_currency', $new_solvent_part, $new_min_price_per_ink, $new_self_adhesive_laquer_price, '$new_self_adhesive_laquer_currency', $new_self_adhesive_laquer_expense, $new_min_percent)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$c_price = "";
$c_currency = "";
$c_expense = "";
$m_price = "";
$m_currency = "";
$m_expense = "";
$y_price = "";
$y_currency = "";
$y_expense = "";
$k_price = "";
$k_currency = "";
$k_expense = "";
$white_price = "";
$white_currency = "";
$white_expense = "";
$panton_price = "";
$panton_currency = "";
$panton_expense = "";
$lacquer_glossy_price = "";
$lacquer_glossy_currency = "";
$lacquer_glossy_expense = "";
$lacquer_matte_price = "";
$lacquer_matte_currency = "";
$lacquer_matte_expense = "";
$solvent_etoxipropanol_price = "";
$solvent_etoxipropanol_currency = "";
$solvent_flexol82_price = "";
$solvent_flexol82_currency = "";
$solvent_part = "";
$min_price_per_ink = "";
$self_adhesive_laquer_price = "";
$self_adhesive_laquer_currency = "";
$self_adhesive_laquer_expense = "";
$min_percent = "";

$sql = "select c_price, c_currency, c_expense, m_price, m_currency, m_expense, y_price, y_currency, y_expense, k_price, k_currency, k_expense, white_price, white_currency, white_expense, panton_price, panton_currency, panton_expense, lacquer_glossy_price, lacquer_glossy_currency, lacquer_glossy_expense, lacquer_matte_price, lacquer_matte_currency, lacquer_matte_expense, solvent_etoxipropanol_price, solvent_etoxipropanol_currency, solvent_flexol82_price, solvent_flexol82_currency, solvent_part, min_price_per_ink, self_adhesive_laquer_price, self_adhesive_laquer_currency, self_adhesive_laquer_expense, min_percent from norm_ink order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $c_price = $row["c_price"];
    $c_currency = $row["c_currency"];
    $c_expense = $row['c_expense'];
    $m_price = $row["m_price"];
    $m_currency = $row["m_currency"];
    $m_expense = $row['m_expense'];
    $y_price = $row["y_price"];
    $y_currency = $row["y_currency"];
    $y_expense = $row['y_expense'];
    $k_price = $row["k_price"];
    $k_currency = $row["k_currency"];
    $k_expense = $row['k_expense'];
    $white_price = $row["white_price"];
    $white_currency = $row["white_currency"];
    $white_expense = $row['white_expense'];
    $panton_price = $row["panton_price"];
    $panton_currency = $row["panton_currency"];
    $panton_expense = $row['panton_expense'];
    $lacquer_glossy_price = $row["lacquer_glossy_price"];
    $lacquer_glossy_currency = $row["lacquer_glossy_currency"];
    $lacquer_glossy_expense = $row['lacquer_glossy_expense'];
    $lacquer_matte_price = $row["lacquer_matte_price"];
    $lacquer_matte_currency = $row["lacquer_matte_currency"];
    $lacquer_matte_expense = $row['lacquer_matte_expense'];
    $solvent_etoxipropanol_price = $row["solvent_etoxipropanol_price"];
    $solvent_etoxipropanol_currency = $row["solvent_etoxipropanol_currency"];
    $solvent_flexol82_price = $row['solvent_flexol82_price'];
    $solvent_flexol82_currency = $row['solvent_flexol82_currency'];
    $solvent_part = $row['solvent_part'];
    $min_price_per_ink = $row['min_price_per_ink'];
    $self_adhesive_laquer_price = $row['self_adhesive_laquer_price'];
    $self_adhesive_laquer_currency = $row['self_adhesive_laquer_currency'];
    $self_adhesive_laquer_expense = $row['self_adhesive_laquer_expense'];
    $min_percent = $row['min_percent'];
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
                                    <label for="c_price">Чистый C (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only<?=$c_price_valid ?>" 
                                               id="c_price" 
                                               name="c_price" 
                                               value="<?= empty($c_price) || $c_price == 0.0 ? "" : floatval($c_price) ?>" 
                                               placeholder="Цена, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'c_price'); $(this).attr('name', 'c_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'c_price'); $(this).attr('name', 'c_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'c_price'); $(this).attr('name', 'c_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                        <div class="input-group-append">
                                            <select id="c_currency" name="c_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$c_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$c_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$c_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Чистый C обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell w-50 pl-3" style="width: 33%;">
                                <div class="form-group">
                                    <label for="c_expense">Расход смеси C (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only<?=$c_expense_valid ?>" 
                                           id="c_expense" 
                                           name="c_expense" 
                                           value="<?= empty($c_expense) || $c_expense == 0.0 ? "" : floatval($c_expense) ?>" 
                                           placeholder="Расход смеси C (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'c_expense'); $(this).attr('name', 'c_expense'); $(this).attr('placeholder', 'Расход смеси C (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'c_expense'); $(this).attr('name', 'c_expense'); $(this).attr('placeholder', 'Расход смеси C (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'c_expense'); $(this).attr('name', 'c_expense'); $(this).attr('placeholder', 'Расход смеси C (г/м2)');" />
                                    <div class="invalid-feedback">Расход смеси C обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="m_price">Чистый M (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only<?=$m_price_valid ?>" 
                                               id="m_price" 
                                               name="m_price" 
                                               value="<?= empty($m_price) || $m_price == 0.0 ? "" : floatval($m_price) ?>" 
                                               placeholder="Цена, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'm_price'); $(this).attr('name', 'm_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'm_price'); $(this).attr('name', 'm_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'm_price'); $(this).attr('name', 'm_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                        <div class="input-group-append"> 
                                            <select id="m_currency" name="m_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$m_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$m_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$m_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Чистый M обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="m_expense">Расход смеси M (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only<?=$m_expense_valid ?>" 
                                           id="m_expense" 
                                           name="m_expense" 
                                           value="<?= empty($m_expense) || $m_expense == 0.0 ? "" : floatval($m_expense) ?>" 
                                           placeholder="Расход смеси M (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'm_expense'); $(this).attr('name', 'm_expense'); $(this).attr('placeholder', 'Расход смеси M (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'm_expense'); $(this).attr('name', 'm_expense'); $(this).attr('placeholder', 'Расход смеси M (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'm_expense'); $(this).attr('name', 'm_expense'); $(this).attr('placeholder', 'Расход смеси M (г/м2)');" />
                                    <div class="invalid-feedback">Расход смеси M обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="y_price">Чистый Y (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only<?=$y_price_valid ?>" 
                                               id="y_price" 
                                               name="y_price" 
                                               value="<?= empty($y_price) || $y_price == 0.0 ? "" : floatval($y_price) ?>" 
                                               placeholder="Цена, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'y_price'); $(this).attr('name', 'y_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'y_price'); $(this).attr('name', 'y_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'y_price'); $(this).attr('name', 'y_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                        <div class="input-group-append">
                                            <select id="y_currency" name="y_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$y_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$y_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$y_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Чистый Y обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="y_expense">Расход смеси Y (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only<?=$y_expense_valid ?>" 
                                           id="y_expense" 
                                           name="y_expense" 
                                           value="<?= empty($y_expense) || $y_expense == 0.0 ? "" : floatval($y_expense) ?>" 
                                           placeholder="Расход смеси Y (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'y_expense'); $(this).attr('name', 'y_expense'); $(this).attr('placeholder', 'Расход смеси Y (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'y_expense'); $(this).attr('name', 'y_expense'); $(this).attr('placeholder', 'Расход смеси Y (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'y_expense'); $(this).attr('name', 'y_expense'); $(this).attr('placeholder', 'Расход смеси Y (г/м2)');" />
                                    <div class="invalid-feedback">Расход смеси Y обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="k_price">Чистый K (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only<?=$k_price_valid ?>" 
                                               id="k_price" 
                                               name="k_price" 
                                               value="<?= empty($k_price) || $k_price == 0.0 ? "" : floatval($k_price) ?>" 
                                               placeholder="Цена, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'k_price'); $(this).attr('name', 'k_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'k_price'); $(this).attr('name', 'k_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'k_price'); $(this).attr('name', 'k_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                        <div class="input-group-append">
                                            <select id="k_currency" name="k_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$k_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$k_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$k_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Чистый K обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="k_expense">Расход смеси K (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only<?=$k_expense_valid ?>" 
                                           id="k_expense" 
                                           name="k_expense" 
                                           value="<?= empty($k_expense) || $k_expense == 0.0 ? "" : floatval($k_expense) ?>" 
                                           placeholder="Расход смеси K (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'k_expense'); $(this).attr('name', 'k_expense'); $(this).attr('placeholder', 'Расход смеси K (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'k_expense'); $(this).attr('name', 'k_expense'); $(this).attr('placeholder', 'Расход смеси K (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'k_expense'); $(this).attr('name', 'k_expense'); $(this).attr('placeholder', 'Расход смеси K (г/м2)');" />
                                    <div class="invalid-feedback">Расход  смеси K обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="panton_price">Чистый Пантон (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only<?=$panton_price_valid ?>" 
                                               id="panton_price" 
                                               name="panton_price" 
                                               value="<?= empty($panton_price) || $panton_price == 0.0 ? "" : floatval($panton_price) ?>" 
                                               placeholder="Цена, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'panton_price'); $(this).attr('name', 'panton_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'panton_price'); $(this).attr('name', 'panton_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'panton_price'); $(this).attr('name', 'panton_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                        <div class="input-group-append">
                                            <select id="panton_currency" name="panton_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$panton_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$panton_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$panton_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Чистый Пантон обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="panton_expense">Расход смеси пантона (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only<?=$panton_expense_valid ?>" 
                                           id="panton_expense" 
                                           name="panton_expense" 
                                           value="<?= empty($panton_expense) || $panton_expense == 0.0 ? "" : floatval($panton_expense) ?>" 
                                           placeholder="Расход смеси пантона (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'panton_expense'); $(this).attr('name', 'panton_expense'); $(this).attr('placeholder', 'Расход смеси пантона (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'panton_expense'); $(this).attr('name', 'panton_expense'); $(this).attr('placeholder', 'Расход смеси пантона (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'panton_expense'); $(this).attr('name', 'panton_expense'); $(this).attr('placeholder', 'Расход смеси пантона (г/м2)');" />
                                    <div class="invalid-feedback">Расход смеси пантона обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="white_price">Чистая Белая (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only<?=$white_price_valid ?>" 
                                               id="white_price" 
                                               name="white_price" 
                                               value="<?= empty($white_price) || $white_price == 0.0 ? "" : floatval($white_price) ?>" 
                                               placeholder="Цена, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'white_price'); $(this).attr('name', 'white_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'white_price'); $(this).attr('name', 'white_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'white_price'); $(this).attr('name', 'white_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                        <div class="input-group-append">
                                            <select id="white_currency" name="white_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$white_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$white_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$white_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Чистая Белая обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="white_expense">Расход смеси белой (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only<?=$white_expense_valid ?>" 
                                           id="white_expense" 
                                           name="white_expense" 
                                           value="<?= empty($white_expense) || $white_expense == 0.0 ? "" : floatval($white_expense) ?>" 
                                           placeholder="Расход смеси белой (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'white_expense'); $(this).attr('name', 'white_expense'); $(this).attr('placeholder', 'Расход смеси белой (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'white_expense'); $(this).attr('name', 'white_expense'); $(this).attr('placeholder', 'Расход смеси белой (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'white_expense'); $(this).attr('name', 'white_expense'); $(this).attr('placeholder', 'Расход смеси белой (г/м2)');" />
                                    <div class="invalid-feedback">Расход смеси белой обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="lacquer_glossy_price">Чистый Лак глянцевый (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only<?=$lacquer_glossy_price_valid ?>" 
                                               id="lacquer_glossy_price" 
                                               name="lacquer_glossy_price" 
                                               value="<?= empty($lacquer_glossy_price) || $lacquer_glossy_price == 0.0 ? "" : floatval($lacquer_glossy_price) ?>" 
                                               placeholder="Цена, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lacquer_glossy_price'); $(this).attr('name', 'lacquer_glossy_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lacquer_glossy_price'); $(this).attr('name', 'lacquer_glossy_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'lacquer_glossy_price'); $(this).attr('name', 'lacquer_glossy_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                        <div class="input-group-append">
                                            <select id="lacquer_glossy_currency" name="lacquer_glossy_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$lacquer_glossy_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$lacquer_glossy_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$lacquer_glossy_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Чистый Лак глянцевый обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="lacquer_glossy_expense">Расход смеси лака глянцевого (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only<?=$lacquer_glossy_expense_valid ?>" 
                                           id="lacquer_glossy_expense" 
                                           name="lacquer_glossy_expense" 
                                           value="<?= empty($lacquer_glossy_expense) || $lacquer_glossy_expense == 0.0 ? "" : floatval($lacquer_glossy_expense) ?>" 
                                           placeholder="Расход смеси лака глянцевого (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'lacquer_glossy_expense'); $(this).attr('name', 'lacquer_glossy_expense'); $(this).attr('placeholder', 'Расход смеси лака глянцевого (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'lacquer_glossy_expense'); $(this).attr('name', 'lacquer_glossy_expense'); $(this).attr('placeholder', 'Расход смеси лака глянцевого (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'lacquer_glossy_expense'); $(this).attr('name', 'lacquer_glossy_expense'); $(this).attr('placeholder', 'Расход смеси лака глянцевого (г/м2)');" />
                                    <div class="invalid-feedback">Расход смеси лака глянцевого обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="lacquer_matte_price">Чистый Лак матовый (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only<?=$lacquer_matte_price_valid ?>" 
                                               id="lacquer_matte_price" 
                                               name="lacquer_matte_price" 
                                               value="<?= empty($lacquer_matte_price) || $lacquer_matte_price == 0.0 ? "" : floatval($lacquer_matte_price) ?>" 
                                               placeholder="Цена, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lacquer_matte_price'); $(this).attr('name', 'lacquer_matte_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lacquer_matte_price'); $(this).attr('name', 'lacquer_matte_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'lacquer_matte_price'); $(this).attr('name', 'lacquer_matte_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                        <div class="input-group-append">
                                            <select id="lacquer_matte_currency" name="lacquer_matte_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$lacquer_matte_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$lacquer_matte_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$lacquer_matte_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Чистый Лак матовый обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="lacquer_matte_expense">Расход смеси лака матового (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only<?=$lacquer_matte_expense_valid ?>" 
                                           id="lacquer_matte_expense" 
                                           name="lacquer_matte_expense" 
                                           value="<?= empty($lacquer_matte_expense) || $lacquer_matte_expense == 0.0 ? "" : floatval($lacquer_matte_expense) ?>" 
                                           placeholder="Расход смеси лака матового (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'lacquer_matte_expense'); $(this).attr('name', 'lacquer_matte_expense'); $(this).attr('placeholder', 'Расход смеси лака матового (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'lacquer_matte_expense'); $(this).attr('name', 'lacquer_matte_expense'); $(this).attr('placeholder', 'Расход смеси лака матового (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'lacquer_matte_expense'); $(this).attr('name', 'lacquer_matte_expense'); $(this).attr('placeholder', 'Расход смеси лака матового (г/м2)');" />
                                    <div class="invalid-feedback">Расход смеси лака матового обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="self_adhesive_laquer_price">Самоклейка, цена лака (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                            class="form-control float-only<?=$self_adhesive_laquer_price_valid ?>" 
                                            id="self_adhesive_laquer_price" 
                                            name="self_adhesive_laquer_price"
                                            value="<?= empty($self_adhesive_laquer_price) || $self_adhesive_laquer_price == 0.0 ? "" : floatval($self_adhesive_laquer_price) ?>" 
                                            placeholder="Самоклейка, цена лака (за кг)" 
                                            required="required" 
                                            onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                            onmouseup="javascript: $(this).attr('id', 'self_adhesive_laquer_price'); $(this).attr('name', 'self_adhesive_laquer_price'); $(this).attr('placeholder', 'Самоклейка, цена лака (за кг)');" 
                                            onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                            onkeyup="javascript: $(this).attr('id', 'self_adhesive_laquer_price'); $(this).attr('name', 'self_adhesive_laquer_price'); $(this).attr('placeholder', 'Самоклейка, цена лака (за кг)');" 
                                            onfocusout="javascript: $(this).attr('id', 'self_adhesive_laquer_price'); $(this).attr('name', 'self_adhesive_laquer_price'); $(this).attr('placeholder', 'Самоклейка, цена лака (за кг)');" />
                                        <div class="input-group-append"> 
                                            <select id="self_adhesive_laquer_currency" name="self_adhesive_laquer_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$self_adhesive_laquer_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$self_adhesive_laquer_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$self_adhesive_laquer_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Самоклейка, цена лака обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="self_adhesive_laquer_expense">Самоклейка, расход чистого лака (г/м<sup>2</sup>)</label>
                                    <input type="text" 
                                           class="form-control float-only<?=$self_adhesive_laquer_expense_valid ?>" 
                                           id="self_adhesive_laquer_expense" 
                                           name="self_adhesive_laquer_expense"
                                           value="<?= empty($self_adhesive_laquer_expense) || $self_adhesive_laquer_expense == 0.0 ? "" : floatval($self_adhesive_laquer_expense) ?>" 
                                           placeholder="Самоклейка, расход чистого лака (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'self_adhesive_laquer_expense'); $(this).attr('name', 'self_adhesive_laquer_expense'); $(this).attr('placeholder', 'Самоклейка, расход чистого лака (г/м2)');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'self_adhesive_laquer_expense'); $(this).attr('name', 'self_adhesive_laquer_expense'); $(this).attr('placeholder', 'Самоклейка, расход чистого лака (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'self_adhesive_laquer_expense'); $(this).attr('name', 'self_adhesive_laquer_expense'); $(this).attr('placeholder', 'Самоклейка, расход чистого лака (г/м2)');" />
                                    <div class="invalid-feedback">Самоклейка, расход чистого лака обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="solvent_etoxipropanol_price">Цена этоксипропанола (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
                                               id="solvent_etoxipropanol_price<?=$solvent_etoxipropanol_price_valid ?>" 
                                               name="solvent_etoxipropanol_price" 
                                               value="<?= empty($solvent_etoxipropanol_price) || $solvent_etoxipropanol_price == 0.0 ? "" : floatval($solvent_etoxipropanol_price) ?>" 
                                               placeholder="Цена, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'solvent_etoxipropanol_price'); $(this).attr('name', 'solvent_etoxipropanol_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'solvent_etoxipropanol_price'); $(this).attr('name', 'solvent_etoxipropanol_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'solvent_etoxipropanol_price'); $(this).attr('name', 'solvent_etoxipropanol_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                        <div class="input-group-append">
                                            <select id="solvent_etoxipropanol_currency" name="solvent_etoxipropanol_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$solvent_etoxipropanol_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$solvent_etoxipropanol_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$solvent_etoxipropanol_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Цена этоксипропанола обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="solvent_flexol82_price">Цена флексоля 82 (за кг)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only<?=$solvent_flexol82_price_valid ?>" 
                                               id="solvent_flexol82_price" 
                                               name="solvent_flexol82_price" 
                                               value="<?= empty($solvent_flexol82_price) || $solvent_flexol82_price == 0.0 ? "" : floatval($solvent_flexol82_price) ?>" 
                                               placeholder="Цена, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'solvent_flexol82_price'); $(this).attr('name', 'solvent_flexol82_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'solvent_flexol82_price'); $(this).attr('name', 'solvent_flexol82_price'); $(this).attr('placeholder', 'Цена, за кг');" 
                                               onfocusout="javascript: $(this).attr('id', 'solvent_flexol82_price'); $(this).attr('name', 'solvent_flexol82_price'); $(this).attr('placeholder', 'Цена, за кг');" />
                                        <div class="input-group-append">
                                            <select id="solvent_flexol82_currency" name="solvent_flexol82_currency" required="required">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$solvent_flexol82_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$solvent_flexol82_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$solvent_flexol82_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Цена флексоля 82 обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="solvent_part">Расход растворителя (кг) на 1 кг краски (лака)</label>
                                    <input type="text" 
                                           class="form-control float-only<?=$solvent_part_valid ?>" 
                                           id="solvent_part" 
                                           name="solvent_part" 
                                           value="<?= empty($solvent_part) || $solvent_part == 0.0 ? "" : floatval($solvent_part) ?>" 
                                           placeholder="В процентах" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'solvent_part'); $(this).attr('name', 'solvent_part'); $(this).attr('placeholder', 'В процентах');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'solvent_part'); $(this).attr('name', 'solvent_part'); $(this).attr('placeholder', 'В процентах');" 
                                           onfocusout="javascript: $(this).attr('id', 'solvent_part'); $(this).attr('name', 'solvent_part'); $(this).attr('placeholder', 'В процентах');" />
                                    <div class="invalid-feedback">Расход растворителя на 1 кг краски обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="min_percent">Минимальный процент запечатки</label>
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control int-only<?=$min_percent_valid ?>"
                                               id="min_percent"
                                               name="min_percent"
                                               value="<?= $min_percent ?>"
                                               placeholder="Мин. процент запечатки"
                                               required="required"
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');"
                                               onmouseup="javascript: $(this).attr('id', 'min_percent'); $(this).attr('name', 'min_percent'); $(this).attr('placeholder', 'Мин. процент запечатки');"
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }"
                                               onkeyup="javascript: $(this).attr('id', 'min_percent'); $(this).attr('name', 'min_percent'); $(this).attr('placeholder', 'Мин. процент запечатки');"
                                               onfocusout="javascript: $(this).attr('id', 'min_percent'); $(this).attr('name', 'min_percent'); $(this).attr('placeholder', 'Мин. процент запечатки');" />
                                        <div class="input-group-append"><div class="input-group-text">%</div></div>
                                    </div>
                                    <div class="invalid-feedback">Минимальный процент запечатки обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="min_price_per_ink">Минимальная стоимость 1 цвета (руб)</label>
                                    <input type="text"
                                           class="form-control int-only<?=$min_price_per_ink_valid ?>"
                                           id="min_price_per_ink"
                                           name="min_price_per_ink"
                                           value="<?= floatval($min_price_per_ink) ?>"
                                           placeholder="Мин. стоимость 1 цвета, руб"
                                           required="required"
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');"
                                           onmouseup="javascript: $(this).attr('id', 'min_price_per_ink'); $(this).attr('name', 'min_price_per_ink'); $(this).attr('placeholder', 'Мин. стоимость 1 цвета, руб');"
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }"
                                           onkeyup="javascript: $(this).attr('id', 'min_price_per_ink'); $(this).attr('name', 'min_price_per_ink'); $(this).attr('placeholder', 'Мин. стоимость 1 цвета, руб');"
                                           onfocusout="javascript: $(this).attr('id', 'min_price_per_ink'); $(this).attr('name', 'min_price_per_ink'); $(this).attr('placeholder', 'Мин. стоимость 1 цвета, руб');" />
                                    <div class="invalid-feedback">Минимальная стоимость 1 цвета обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <button type="submit" id="norm_ink_submit" name="norm_ink_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                            </div>
                            <div class="d-table-cell pl-3 pr-3"></div>
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