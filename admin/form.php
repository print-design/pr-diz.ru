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

$flint_valid = '';
$kodak_valid = '';
$overmeasure_valid = '';
$scotch_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_form_submit')) {
    if(empty(filter_input(INPUT_POST, 'flint'))) {
        $flint_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'kodak'))) {
        $kodak_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'overmeasure'))) {
        $overmeasure_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'scotch'))) {
        $scotch_valid = ISINVALID;
        $form_valid = false;
    }
    
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    if($form_valid) {
        // Старый объект
        $old_flint = "";
        $old_kodak = "";
        $old_overmeasure = "";
        $old_scotch = "";
        
        $sql = "select flint, kodak, overmeasure, scotch from norm_form where machine_id = $machine_id order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_flint = $row['flint'];
            $old_kodak = $row['kodak'];
            $old_overmeasure = $row['overmeasure'];
            $old_scotch = $row['scotch'];
        }

        // Новый объект
        $new_flint = filter_input(INPUT_POST, 'flint');
        $new_kodak = filter_input(INPUT_POST, 'kodak');
        $new_overmeasure = filter_input(INPUT_POST, 'overmeasure');
        $new_scotch = filter_input(INPUT_POST, 'scotch');
        
        if($old_flint != $new_flint || $old_kodak != $new_kodak || $old_overmeasure != $new_overmeasure || $old_scotch != $new_scotch) {
            $sql = "insert into norm_form (machine_id, flint, kodak, overmeasure, scotch) values ($machine_id, $new_flint, $new_kodak, $new_overmeasure, $new_scotch)";
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
$overmeasure = "";
$scotch = "";

$sql = "select flint, kodak, overmeasure, scotch from norm_form where machine_id = $machine_id order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $flint = $row['flint'];
    $kodak = $row['kodak'];
    $overmeasure = $row['overmeasure'];
    $scotch = $row['scotch'];
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
            
            if(null !== filter_input(INPUT_POST, 'norm_form_submit') && empty($error_message)):
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
                            <label for="flint">Flint (руб/м<sup>2</sup>)</label>
                            <input type="text" class="form-control float-only" id="flint" name="flint" value="<?= empty($flint) ? "" : floatval($flint) ?>" placeholder="Стоимость, м2" required="required" />
                            <div class="invalid-feedback">Flint обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="kodak">Kodak (руб/м<sup>2</sup>)</label>
                            <input type="text" class="form-control float-only" id="kodak" name="kodak" value="<?= empty($kodak) ? "" : floatval($kodak) ?>" placeholder="Стоимость, м2" required="required" />
                            <div class="invalid-feedback">Kodak обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="overmeasure">Припуски (мм)</label>
                            <input type="text" class="form-control float-only" id="overmeasure" name="overmeasure" value="<?= empty($overmeasure) ? "" : floatval($overmeasure) ?>" placeholder="Припуски, мм" required="required" />
                            <div class="invalid-feedback">Припуски обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="scotch">Скотч (мм)</label>
                            <input type="text" class="form-control float-only" id="scotch" name="scotch" value="<?= empty($scotch) ? "" : floatval($scotch) ?>" placeholder="Скотч, мм" required="required" />
                            <div class="invalid-feedback">Скотч обязательно</div>
                        </div>
                        <button type="submit" id="norm_form_submit" name="norm_form_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>