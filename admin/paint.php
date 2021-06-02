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
    if(empty(filter_input(INPUT_POST, 'c'))) {
        $c_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'm'))) {
        $m_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'y'))) {
        $y_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'k'))) {
        $k_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'white'))) {
        $white_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'panton'))) {
        $panton_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lacquer'))) {
        $lacquer_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'paint_solvent'))) {
        $paint_solvent_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solvent'))) {
        $solvent_valid = ISINVALID;
        $form_valid = false;
    }
    
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    if($form_valid) {
        // Старый объект
        $old_c = "";
        $old_m = "";
        $old_y = "";
        $old_k = "";
        $old_white = "";
        $old_panton = "";
        $old_lacquer = "";
        $old_paint_solvent = "";
        $old_solvent = "";
        
        $sql = "select c, m, y, k, white, panton, lacquer, paint_solvent, solvent from norm_paint where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_c = $row["c"];
            $old_m = $row["m"];
            $old_y = $row["y"];
            $old_k = $row["k"];
            $old_white = $row["white"];
            $old_panton = $row["panton"];
            $old_lacquer = $row["lacquer"];
            $old_paint_solvent = $row["paint_solvent"];
            $old_solvent = $row["solvent"];
        }
        
        // Новый объект
        $new_c = filter_input(INPUT_POST, "c");
        $new_m = filter_input(INPUT_POST, "m");
        $new_y = filter_input(INPUT_POST, "y");
        $new_k = filter_input(INPUT_POST, "k");
        $new_white = filter_input(INPUT_POST, "white");
        $new_panton = filter_input(INPUT_POST, "panton");
        $new_lacquer = filter_input(INPUT_POST, "lacquer");
        $new_paint_solvent = filter_input(INPUT_POST, "paint_solvent");
        $new_solvent = filter_input(INPUT_POST, "solvent");
        
        if($old_c != $new_c ||
                $old_m != $new_m ||
                $old_y != $new_y ||
                $old_k != $new_k ||
                $old_white != $new_white ||
                $old_panton != $new_panton ||
                $old_lacquer != $new_lacquer ||
                $old_paint_solvent != $new_paint_solvent ||
                $old_solvent != $new_solvent) {
            $sql = "insert into norm_paint (machine_id, c, m, y, k, white, panton, lacquer, paint_solvent, solvent) values ($machine_id, $new_c, $new_m, $new_y, $new_k, $new_white, $new_panton, $new_lacquer, $new_paint_solvent, $new_solvent)";
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
$m = "";
$y = "";
$k = "";
$white = "";
$panton = "";
$lacquer = "";
$paint_solvent = "";
$solvent = "";

$sql = "select c, m, y, k, white, panton, lacquer, paint_solvent, solvent from norm_paint where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $c = $row["c"];
    $m = $row["m"];
    $y = $row["y"];
    $k = $row["k"];
    $white = $row["white"];
    $panton = $row["panton"];
    $lacquer = $row["lacquer"];
    $paint_solvent = $row["paint_solvent"];
    $solvent = $row["solvent"];
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
                                    <label for="seuro">C Евро (евро/кг)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="c" 
                                           name="c" 
                                           value="<?= empty($c) ? "" : floatval($c) ?>" 
                                           placeholder="Стоимость, евро/кг" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'c'); $(this).attr('name', 'c'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'c'); $(this).attr('name', 'c'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onfocusout="javascript: $(this).attr('id', 'c'); $(this).attr('name', 'c'); $(this).attr('placeholder', 'Стоимость, евро/кг');" />
                                    <div class="invalid-feedback">C Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell w-50 pl-3">
                                <div class="form-group">
                                    <label for="pantoneuro">Пантоны Евро (евро/кг)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="panton" 
                                           name="panton" 
                                           value="<?= empty($panton) ? "" : floatval($panton) ?>" 
                                           placeholder="Стоимость, евро/кг" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'panton'); $(this).attr('name', 'panton'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'panton'); $(this).attr('name', 'panton'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onfocusout="javascript: $(this).attr('id', 'panton'); $(this).attr('name', 'panton'); $(this).attr('placeholder', 'Стоимость, евро/кг');" />
                                    <div class="invalid-feedback">Пантоны Евро обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="meuro">M Евро (евро/кг)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="m" 
                                           name="m" 
                                           value="<?= empty($m) ? "" : floatval($m) ?>" 
                                           placeholder="Стоимость, евро/кг" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'm'); $(this).attr('name', 'm'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'm'); $(this).attr('name', 'm'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onfocusout="javascript: $(this).attr('id', 'm'); $(this).attr('name', 'm'); $(this).attr('placeholder', 'Стоимость, евро/кг');" />
                                    <div class="invalid-feedback">M Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="lacquereuro">Лак Евро (евро/кг)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="lacquer" 
                                           name="lacquer" 
                                           value="<?= empty($lacquer) ? "" : floatval($lacquer) ?>" 
                                           placeholder="Стоимость, евро/кг" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'lacquer'); $(this).attr('name', 'lacquer'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'lacquer'); $(this).attr('name', 'lacquer'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onfocusout="javascript: $(this).attr('id', 'lacquer'); $(this).attr('name', 'lacquer'); $(this).attr('placeholder', 'Стоимость, евро/кг');" />
                                    <div class="invalid-feedback">Лак Евро обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="ueuro">Y Евро (евро/кг)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="y" 
                                           name="y" 
                                           value="<?= empty($y) ? "" : floatval($y) ?>" 
                                           placeholder="Стоимость, евро/кг" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'y'); $(this).attr('name', 'y'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'y'); $(this).attr('name', 'y'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onfocusout="javascript: $(this).attr('id', 'y'); $(this).attr('name', 'y'); $(this).attr('placeholder', 'Стоимость, евро/кг');" />
                                    <div class="invalid-feedback">Y Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="paint_solvent">Соотношение краски и растворителя (в процентах)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control float-only" 
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
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="keuro">K Евро (евро/кг)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="k" 
                                           name="k" 
                                           value="<?= empty($k) ? "" : floatval($k) ?>" 
                                           placeholder="Стоимость, евро/кг" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'k'); $(this).attr('name', 'k'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'k'); $(this).attr('name', 'k'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onfocusout="javascript: $(this).attr('id', 'k'); $(this).attr('name', 'k'); $(this).attr('placeholder', 'Стоимость, евро/кг');" />
                                    <div class="invalid-feedback">K обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="solvent">Стоимость растворителя (руб/кг)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="solvent" 
                                           name="solvent" 
                                           value="<?= empty($solvent) ? "" : floatval($solvent) ?>" 
                                           placeholder="Стоимость, руб/кг" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'solvent'); $(this).attr('name', 'solvent'); $(this).attr('placeholder', 'Стоимость, руб/кг');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'solvent'); $(this).attr('name', 'solvent'); $(this).attr('placeholder', 'Стоимость, руб/кг');" 
                                           onfocusout="javascript: $(this).attr('id', 'solvent'); $(this).attr('name', 'solvent'); $(this).attr('placeholder', 'Стоимость, руб/кг');" />
                                    <div class="invalid-feedback">Стоимость растворителя обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="whiteeuro">Белая Евро (евро/кг)</label>
                                    <input type="text" 
                                           class="form-control float-only" 
                                           id="white" 
                                           name="white" 
                                           value="<?= empty($white) ? "" : floatval($white) ?>" 
                                           placeholder="Стоимость, евро/кг" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'white'); $(this).attr('name', 'white'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onkeyup="javascript: $(this).attr('id', 'white'); $(this).attr('name', 'white'); $(this).attr('placeholder', 'Стоимость, евро/кг');" 
                                           onfocusout="javascript: $(this).attr('id', 'white'); $(this).attr('name', 'white'); $(this).attr('placeholder', 'Стоимость, евро/кг');" />
                                    <div class="invalid-feedback">Белая Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3 align-bottom"></div>
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
    </body>
</html>