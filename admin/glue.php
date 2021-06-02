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

// Страница предназначена только для ламинатора
if($machine_id != MACHINE_LAMINATOR) {
    header("Location: ".APPLICATION."/admin/form.php".BuildQuery("machine_id", $machine_id));
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$glueeuro_valid = '';
$solventeuro_valid = '';
$glue_solvent_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_glue_submit')) {
    if(empty(filter_input(INPUT_POST, 'glueeuro'))) {
        $glueeuro_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'solventeuro'))) {
        $solventeuro_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'glue_solvent'))) {
        $glue_solvent_valid = ISINVALID;
        $form_valid = false;
    }
    
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    if($form_valid) {
        // Старый объект
        $old_glueeuro = '';
        $old_solventeuro = '';
        $old_glue_solvent = '';
        
        $sql = "select glueeuro, solventeuro, glue_solvent from norm_glue where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_glueeuro = $row['glueeuro'];
            $old_solventeuro = $row['solventeuro'];
            $old_glue_solvent = $row['glue_solvent'];
        }
        
        // Новый объект
        $new_glueeuro = filter_input(INPUT_POST, 'glueeuro');
        $new_solventeuro = filter_input(INPUT_POST, 'solventeuro');
        $new_glue_solvent = filter_input(INPUT_POST, 'glue_solvent');
        
        if($old_glueeuro != $new_glueeuro || $old_solventeuro != $new_solventeuro || $old_glue_solvent != $new_glue_solvent) {
            $sql = "insert into norm_glue (machine_id, glueeuro, solventeuro, glue_solvent) values ($machine_id, $new_glueeuro, $new_solventeuro, $new_glue_solvent)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$glueeuro = '';
$solventeuro = '';
$glue_solvent = '';

$sql = "select glueeuro, solventeuro, glue_solvent from norm_glue where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $glueeuro = $row['glueeuro'];
    $solventeuro = $row['solventeuro'];
    $glue_solvent = $row['glue_solvent'];
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
                        <input type="hidden" id="machine_id" name="machine_id" value="<?= filter_input(INPUT_GET, 'machine_id') ?>" />
                        <div class="form-group">
                            <label for="glueeuro">Стоимость клея ЕВРО (руб/кг)</label>
                            <input type="text" class="form-control float-only" id="glueeuro" name="glueeuro" value="<?= empty($glueeuro) ? "" : floatval($glueeuro) ?>" placeholder="Стоимость, руб/кг" required="required" />
                            <div class="invalid-feedback">Стоимость клея обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="solventeuro">Стоимость растворителя для клея ЕВРО (руб/кг)</label>
                            <input type="text" class="form-control float-only" id="solventeuro" name="solventeuro" value="<?= empty($solventeuro) ? "" : floatval($solventeuro) ?>" placeholder="Стоимость, руб/кг" required="required" />
                            <div class="invalid-feedback">Стоимость растворителя для клея обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="glue_solvent">Соотношение клея и растворителя (в процентах)</label>
                            <div class="input-group">
                            <input type="text" class="form-control float-only" id="glue_solvent" name="glue_solvent" value="<?= empty($glue_solvent) ? "" : floatval($glue_solvent) ?>" placeholder="В процентах" required="required" />
                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <div class="invalid-feedback">Соотношение клея и растворителя обязательно</div>
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