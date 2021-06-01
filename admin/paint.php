<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Печатная машина
$machine_id = filter_input(INPUT_GET, 'machine_id');

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$seuro_valid = "";
$meuro_valid = "";
$ueuro_valid = "";
$keuro_valid = "";
$whiteeuro_valid = "";
$pantoneuro_valid = "";
$lacquereuro_valid = "";
$paint_solvent_valid = "";
$solvent_valid = "";

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_paint_submit')) {
    if(empty(filter_input(INPUT_POST, 'seuro'))) {
        $seuro_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'meuro'))) {
        $meuro_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'ueuro'))) {
        $ueuro_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'keuro'))) {
        $keuro_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'whiteeuro'))) {
        $whiteeuro_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'pantoneuro'))) {
        $pantoneuro_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lacquereuro'))) {
        $lacquereuro_valid = ISINVALID;
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
        $old_seuro = "";
        $old_meuro = "";
        $old_ueuro = "";
        $old_keuro = "";
        $old_whiteeuro = "";
        $old_pantoneuro = "";
        $old_lacquereuro = "";
        $old_paint_solvent = "";
        $old_solvent = "";
        
        $sql = "select seuro, meuro, ueuro, keuro, whiteeuro, pantoneuro, lacquereuro, paint_solvent, solvent from norm_paint where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_seuro = $row["seuro"];
            $old_meuro = $row["meuro"];
            $old_ueuro = $row["ueuro"];
            $old_keuro = $row["keuro"];
            $old_whiteeuro = $row["whiteeuro"];
            $old_pantoneuro = $row["pantoneuro"];
            $old_lacquereuro = $row["lacquereuro"];
            $old_paint_solvent = $row["paint_solvent"];
            $old_solvent = $row["solvent"];
        }
        
        // Новый объект
        $new_seuro = filter_input(INPUT_POST, "seuro");
        $new_meuro = filter_input(INPUT_POST, "meuro");
        $new_ueuro = filter_input(INPUT_POST, "ueuro");
        $new_keuro = filter_input(INPUT_POST, "keuro");
        $new_whiteeuro = filter_input(INPUT_POST, "whiteeuro");
        $new_pantoneuro = filter_input(INPUT_POST, "pantoneuro");
        $new_lacquereuro = filter_input(INPUT_POST, "lacquereuro");
        $new_paint_solvent = filter_input(INPUT_POST, "paint_solvent");
        $new_solvent = filter_input(INPUT_POST, "solvent");
        
        if($old_seuro != $new_seuro ||
                $old_meuro != $new_meuro ||
                $old_ueuro != $new_ueuro ||
                $old_keuro != $new_keuro ||
                $old_whiteeuro != $new_whiteeuro ||
                $old_pantoneuro != $new_pantoneuro ||
                $old_lacquereuro != $new_lacquereuro ||
                $old_paint_solvent != $new_paint_solvent ||
                $old_solvent != $new_solvent) {
            $sql = "insert into norm_paint (machine_id, seuro, meuro, ueuro, keuro, whiteeuro, pantoneuro, lacquereuro, paint_solvent, solvent) values ($machine_id, $new_seuro, $new_meuro, $new_ueuro, $new_keuro, $new_whiteeuro, $new_pantoneuro, $new_lacquereuro, $new_paint_solvent, $new_solvent)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$seuro = "";
$meuro = "";
$ueuro = "";
$keuro = "";
$whiteeuro = "";
$pantoneuro = "";
$lacquereuro = "";
$paint_solvent = "";
$solvent = "";

$sql = "select seuro, meuro, ueuro, keuro, whiteeuro, pantoneuro, lacquereuro, paint_solvent, solvent from norm_paint where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $seuro = $row["seuro"];
    $meuro = $row["meuro"];
    $ueuro = $row["ueuro"];
    $keuro = $row["keuro"];
    $whiteeuro = $row["whiteeuro"];
    $pantoneuro = $row["pantoneuro"];
    $lacquereuro = $row["lacquereuro"];
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
                                    <label for="seuro">С Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="seuro" name="seuro" value="<?= empty($seuro) ? "" : floatval($seuro) ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">С Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell w-50 pl-3">
                                <div class="form-group">
                                    <label for="pantoneuro">Пантоны Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="pantoneuro" name="pantoneuro" value="<?= empty($pantoneuro) ? "" : floatval($pantoneuro) ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">Пантоны Евро обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="meuro">М Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="meuro" name="meuro" value="<?= empty($meuro) ? "" : floatval($meuro) ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">М Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="lacquereuro">Лак Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="lacquereuro" name="lacquereuro" value="<?= empty($lacquereuro) ? "" : floatval($lacquereuro) ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">Лак Евро обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="ueuro">У Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="ueuro" name="ueuro" value="<?= empty($ueuro) ? "" : floatval($ueuro) ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">У Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="paint_solvent">Соотношение краски и растворителя (в процентах)</label>
                                    <input type="text" class="form-control float-only" id="paint_solvent" name="paint_solvent" value="<?= empty($paint_solvent) ? "" : floatval($paint_solvent) ?>" placeholder="В процентах" required="required" />
                                    <div class="invalid-feedback">Соотношение краски и растворителя обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="keuro">К Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="keuro" name="keuro" value="<?= empty($keuro) ? "" : floatval($keuro) ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">К Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="solvent">Стоимость растворителя ЕВРО (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="solvent" name="solvent" value="<?= empty($solvent) ? "" : floatval($solvent) ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">Стоимость растворителя ЕВРО обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="whiteeuro">Белая Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="whiteeuro" name="whiteeuro" value="<?= empty($whiteeuro) ? "" : floatval($whiteeuro) ?>" placeholder="Стоимость, кг" required="required" />
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