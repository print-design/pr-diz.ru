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

$c_valid = "";
$m_valid = "";
$y_valid = "";
$k_valid = "";
$white_valid = "";
$panton_valid = "";
$lacquer_valid = "";
$paint_solvent_valid = "";
$solvent_valid = "";

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_paint_submit')) {
    if(empty(filter_input(INPUT_POST, 'c')) || empty(filter_input(INPUT_POST, 'c_currency'))) {
        $c_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'm')) || empty(filter_input(INPUT_POST, 'm_currency'))) {
        $m_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'y')) || empty(filter_input(INPUT_POST, 'y_currency'))) {
        $y_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'k')) || empty(filter_input(INPUT_POST, 'k_currency'))) {
        $k_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'white')) || empty(filter_input(INPUT_POST, 'white_currency'))) {
        $white_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'panton')) || empty(filter_input(INPUT_POST, 'panton_currency'))) {
        $panton_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lacquer')) || empty(filter_input(INPUT_POST, 'lacquer_currency'))) {
        $lacquer_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'paint_solvent'))) {
        $paint_solvent_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent')) || empty(filter_input(INPUT_POST, 'solvent_currency'))) {
        $solvent_valid = ISINVALID;
        $form_valid = false;
    }
    
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    if($form_valid) {
        // Старый объект
        $old_c = "";
        $old_c_currency = "";
        $old_m = "";
        $old_m_currency = "";
        $old_y = "";
        $old_y_currency = "";
        $old_k = "";
        $old_k_currency = "";
        $old_white = "";
        $old_white_currency = "";
        $old_panton = "";
        $old_panton_currency = "";
        $old_lacquer = "";
        $old_lacquer_currency = "";
        $old_paint_solvent = "";
        $old_solvent = "";
        $old_solvent_currency = "";
        
        $sql = "select c, c_currency, m, m_currency, y, y_currency, k, k_currency, white, white_currency, panton, panton_currency, lacquer, lacquer_currency, paint_solvent, solvent, solvent_currency from norm_paint where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_c = $row["c"];
            $old_c_currency = $row["c_currency"];
            $old_m = $row["m"];
            $old_m_currency = $row["m_currency"];
            $old_y = $row["y"];
            $old_y_currency = $row["y_currency"];
            $old_k = $row["k"];
            $old_k_currency = $row["k_currency"];
            $old_white = $row["white"];
            $old_white_currency = $row["white_currency"];
            $old_panton = $row["panton"];
            $old_panton_currency = $row["panton_currency"];
            $old_lacquer = $row["lacquer"];
            $old_lacquer_currency = $row["lacquer_currency"];
            $old_paint_solvent = $row["paint_solvent"];
            $old_solvent = $row["solvent"];
            $old_solvent_currency = $row["solvent_currency"];
        }
        
        // Новый объект
        $new_c = filter_input(INPUT_POST, "c");
        $new_c_currency = filter_input(INPUT_POST, "c_currency");
        $new_m = filter_input(INPUT_POST, "m");
        $new_m_currency = filter_input(INPUT_POST, "m_currency");
        $new_y = filter_input(INPUT_POST, "y");
        $new_y_currency = filter_input(INPUT_POST, "y_currency");
        $new_k = filter_input(INPUT_POST, "k");
        $new_k_currency = filter_input(INPUT_POST, "k_currency");
        $new_white = filter_input(INPUT_POST, "white");
        $new_white_currency = filter_input(INPUT_POST, "white_currency");
        $new_panton = filter_input(INPUT_POST, "panton");
        $new_panton_currency = filter_input(INPUT_POST, "panton_currency");
        $new_lacquer = filter_input(INPUT_POST, "lacquer");
        $new_lacquer_currency = filter_input(INPUT_POST, "lacquer_currency");
        $new_paint_solvent = filter_input(INPUT_POST, "paint_solvent");
        $new_solvent = filter_input(INPUT_POST, "solvent");
        $new_solvent_currency = filter_input(INPUT_POST, "solvent_currency");
        
        if($old_c != $new_c ||
                $old_c_currency != $new_c_currency ||
                $old_m != $new_m ||
                $old_m_currency != $new_m_currency ||
                $old_y != $new_y ||
                $old_y_currency != $new_y_currency ||
                $old_k != $new_k ||
                $old_k_currency != $new_k_currency ||
                $old_white != $new_white ||
                $old_white_currency != $new_white_currency ||
                $old_panton != $new_panton ||
                $old_panton_currency != $new_panton_currency ||
                $old_lacquer != $new_lacquer ||
                $old_lacquer_currency != $new_lacquer_currency ||
                $old_paint_solvent != $new_paint_solvent ||
                $old_solvent != $new_solvent ||
                $old_solvent_currency != $new_solvent_currency) {
            $sql = "insert into norm_paint (machine_id, c, c_currency, m, m_currency, y, y_currency, k, k_currency, white, white_currency, panton, panton_currency, lacquer, lacquer_currency, paint_solvent, solvent, solvent_currency) values ($machine_id, $new_c, '$new_c_currency', $new_m, '$new_m_currency', $new_y, '$new_m_currency', $new_k, '$new_k_currency', $new_white, '$new_white_currency', $new_panton, '$new_panton_currency', $new_lacquer, '$new_lacquer_currency', $new_paint_solvent, $new_solvent, '$new_solvent_currency')";
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
$m = "";
$m_currency = "";
$y = "";
$y_currency = "";
$k = "";
$k_currency = "";
$white = "";
$white_currency = "";
$panton = "";
$panton_currency = "";
$lacquer = "";
$lacquer_currency = "";
$paint_solvent = "";
$solvent = "";
$solvent_currency = "";

$sql = "select c, c_currency, m, m_currency, y, y_currency, k, k_currency, white, white_currency, panton, panton_currency, lacquer, lacquer_currency, paint_solvent, solvent, solvent_currency from norm_paint where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $c = $row["c"];
    $c_currency = $row["c_currency"];
    $m = $row["m"];
    $m_currency = $row["m_currency"];
    $y = $row["y"];
    $y_currency = $row["y_currency"];
    $k = $row["k"];
    $k_currency = $row["k_currency"];
    $white = $row["white"];
    $white_currency = $row["white_currency"];
    $panton = $row["panton"];
    $panton_currency = $row["panton_currency"];
    $lacquer = $row["lacquer"];
    $lacquer_currency = $row["lacquer_currency"];
    $paint_solvent = $row["paint_solvent"];
    $solvent = $row["solvent"];
    $solvent_currency = $row["solvent_currency"];
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
            
            if(null !== filter_input(INPUT_POST, 'norm_paint_submit') && empty($error_message)):
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
            <form method="post">
                <input type="hidden" id="machine_id" name="machine_id" value="<?= filter_input(INPUT_GET, 'machine_id') ?>" />
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
                                               value="<?= empty($c) ? "" : floatval($c) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'c'); $(this).attr('name', 'c'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
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
                                           value="<?= empty($c_expense) ? "" : floatval($c_expense) ?>" 
                                           placeholder="Расход C (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'c_expense'); $(this).attr('name', 'c_expense'); $(this).attr('placeholder', 'Расход C (г/м2)');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'c_expense'); $(this).attr('name', 'c_expense'); $(this).attr('placeholder', 'Расход C (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'c_expense'); $(this).attr('name', 'c_expense'); $(this).attr('placeholder', 'Расход C (г/м2)');" />
                                    <div class="invalid-feedback">C обязательно</div>
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
                                               value="<?= empty($m) ? "" : floatval($m) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'm'); $(this).attr('name', 'm'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
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
                                           value="<?= empty($m_expense) ? "" : floatval($m_expense) ?>" 
                                           placeholder="Расход M (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'm_expense'); $(this).attr('name', 'm_expense'); $(this).attr('placeholder', 'Расход M (г/м2)');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'm_expense'); $(this).attr('name', 'm_expense'); $(this).attr('placeholder', 'Расход M (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'm_expense'); $(this).attr('name', 'm_expense'); $(this).attr('placeholder', 'Расход M (г/м2)');" />
                                    <div class="invalid-feedback">M обязательно</div>
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
                                               value="<?= empty($y) ? "" : floatval($y) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'y'); $(this).attr('name', 'y'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
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
                                           value="<?= empty($y_expense) ? "" : floatval($y_expense) ?>" 
                                           placeholder="Расход Y (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'y_expense'); $(this).attr('name', 'y_expense'); $(this).attr('placeholder', 'Расход Y (г/м2)');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'y_expense'); $(this).attr('name', 'y_expense'); $(this).attr('placeholder', 'Расход Y (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'y_expense'); $(this).attr('name', 'y_expense'); $(this).attr('placeholder', 'Расход Y (г/м2)');" />
                                    <div class="invalid-feedback">Y обязательно</div>
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
                                               value="<?= empty($k) ? "" : floatval($k) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'k'); $(this).attr('name', 'k'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
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
                                           value="<?= empty($k_expense) ? "" : floatval($k_expense) ?>" 
                                           placeholder="Расход K (г/м2)" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'k_expense'); $(this).attr('name', 'k_expense'); $(this).attr('placeholder', 'Расход K (г/м2)');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'k_expense'); $(this).attr('name', 'k_expense'); $(this).attr('placeholder', 'Расход K (г/м2)');" 
                                           onfocusout="javascript: $(this).attr('id', 'k_expense'); $(this).attr('name', 'k_expense'); $(this).attr('placeholder', 'Расход K (г/м2)');" />
                                    <div class="invalid-feedback">K обязательно</div>
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
                                               value="<?= empty($panton) ? "" : floatval($panton) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'panton'); $(this).attr('name', 'panton'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
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
                            <div class="d-table-cell pl-3"></div>
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
                                               value="<?= empty($white) ? "" : floatval($white) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'white'); $(this).attr('name', 'white'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
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
                            <div class="d-table-cell pl-3"></div>
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
                                               value="<?= empty($lacquer) ? "" : floatval($lacquer) ?>" 
                                               placeholder="Стоимость, за кг" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lacquer'); $(this).attr('name', 'lacquer'); $(this).attr('placeholder', 'Стоимость, за кг');" 
                                               onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
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
                            <div class="d-table-cell pl-3"></div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="solvent">Стоимость растворителя (за кг)</label>
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
                                    <div class="invalid-feedback">Стоимость растворителя обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3"></div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="paint_solvent">Соотношение краски и растворителя (в процентах)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="paint_solvent" 
                                               name="paint_solvent" 
                                               value="<?= empty($paint_solvent) ? "" : floatval($paint_solvent) ?>" 
                                               placeholder="В процентах" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'paint_solvent'); $(this).attr('name', 'paint_solvent'); $(this).attr('placeholder', 'В процентах');" 
                                               onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onkeyup="javascript: $(this).attr('id', 'paint_solvent'); $(this).attr('name', 'paint_solvent'); $(this).attr('placeholder', 'В процентах');" 
                                               onfocusout="javascript: $(this).attr('id', 'paint_solvent'); $(this).attr('name', 'paint_solvent'); $(this).attr('placeholder', 'В процентах');" />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <div class="invalid-feedback">Соотношение краски и растворителя обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3"></div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <button type="submit" id="norm_paint_submit" name="norm_paint_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
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
            $('#paint_solvent').keydown(function(e) {
                if(!KeyDownLimitFloatValue($(e.target), e, 100)) {
                    return false;
                }
            });
    
            $("#paint_solvent").change(function(){
                ChangeLimitFloatValue($(this), 100);
            });
        </script>
    </body>
</html>