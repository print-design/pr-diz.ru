<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

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
        
        $sql = "select seuro, meuro, ueuro, keuro, whiteeuro, pantoneuro, lacquereuro, paint_solvent, solvent from norm_paint order by date desc limit 1";
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
            $sql = "insert into norm_paint (seuro, meuro, ueuro, keuro, whiteeuro, pantoneuro, lacquereuro, paint_solvent, solvent) values ($new_seuro, $new_meuro, $new_ueuro, $new_keuro, $new_whiteeuro, $new_pantoneuro, $new_lacquereuro, $new_paint_solvent, $new_solvent)";
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

$sql = "select seuro, meuro, ueuro, keuro, whiteeuro, pantoneuro, lacquereuro, paint_solvent, solvent from norm_paint order by date desc limit 1";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;

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
                <div class="row">
                    <div class="col-12 col-md-8 col-lg-4 d-table">
                        <div class="d-table-row">
                            <div class="d-table-cell w-50 pr-3">
                                <div class="form-group">
                                    <label for="seuro" style="font-size: large;">С Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="seuro" name="seuro" value="<?=$seuro ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">С Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell w-50 pl-3">
                                <div class="form-group">
                                    <label for="pantoneuro" style="font-size: large;">Пантоны Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="pantoneuro" name="pantoneuro" value="<?=$pantoneuro ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">Пантоны Евро обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="meuro" style="font-size: large;">М Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="meuro" name="meuro" value="<?=$meuro ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">М Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="lacquereuro" style="font-size: large;">Лак Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="lacquereuro" name="lacquereuro" value="<?=$lacquereuro ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">Лак Евро обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="ueuro" style="font-size: large;">У Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="ueuro" name="ueuro" value="<?=$ueuro ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">У Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="paint_solvent" style="font-size: large;">Соотношение краски и растворителя (в процентах)</label>
                                    <input type="text" class="form-control float-only" id="paint_solvent" name="paint_solvent" value="<?=$paint_solvent ?>" placeholder="В процентах" required="required" />
                                    <div class="invalid-feedback">Соотношение краски и растворителя обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="keuro" style="font-size: large;">К Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="keuro" name="keuro" value="<?=$keuro ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">К Евро обязательно</div>
                                </div>
                            </div>
                            <div class="d-table-cell pl-3">
                                <div class="form-group">
                                    <label for="solvent" style="font-size: large;">Стоимость растворителя ЕВРО (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="solvent" name="solvent" value="<?=$solvent ?>" placeholder="Стоимость, кг" required="required" />
                                    <div class="invalid-feedback">Стоимость растворителя ЕВРО обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-table-row">
                            <div class="d-table-cell pr-3">
                                <div class="form-group">
                                    <label for="whiteeuro" style="font-size: large;">Белая Евро (руб/кг)</label>
                                    <input type="text" class="form-control float-only" id="whiteeuro" name="whiteeuro" value="<?=$whiteeuro ?>" placeholder="Стоимость, кг" required="required" />
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