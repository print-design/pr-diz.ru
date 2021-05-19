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
    
    if($form_valid) {
        // Старый объект
        $old_glueeuro = '';
        $old_solventeuro = '';
        $old_glue_solvent = '';
        
        $sql = "select glueeuro, solventeuro, glue_solvent from norm_glue order by date desc limit 1";
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
            $sql = "insert into norm_glue (glueeuro, solventeuro, glue_solvent) values ($new_glueeuro, $new_solventeuro, $new_glue_solvent)";
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

$sql = "select glueeuro, solventeuro, glue_solvent from norm_glue order by date desc limit 1";
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
                        <div class="form-group">
                            <label for="glueeuro">Стоимость клея ЕВРО (руб/кг)</label>
                            <input type="text" class="form-control float-only" id="glueeuro" name="glueeuro" value="<?= floatval($glueeuro) ?>" placeholder="Стоимость, кг" required="required" />
                            <div class="invalid-feedback">Стоимость клея ЕВРО обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="solventeuro">Стоимость растворителя для клея ЕВРО (руб/кг)</label>
                            <input type="text" class="form-control float-only" id="solventeuro" name="solventeuro" value="<?= floatval($solventeuro) ?>" placeholder="Стоимость, кг" required="required" />
                            <div class="invalid-feedback">Стоимость растворителя для клея ЕВРО обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="glue_solvent">Соотношение клея и растворителя (в процентах)</label>
                            <input type="text" class="form-control float-only" id="glue_solvent" name="glue_solvent" value="<?= floatval($glue_solvent) ?>" placeholder="В процентах" required="required" />
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