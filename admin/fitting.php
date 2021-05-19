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

$zbs6_time_valid = '';
$zbs6_length_valid = '';
$zbs8_time_valid = '';
$zbs8_length_valid = '';
$comiflex_time_valid = '';
$comiflex_length_valid = '';
$lamination_time_valid = '';
$lamination_length_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'norm_fitting_submit')) {
    if(empty(filter_input(INPUT_POST, 'zbs6_time'))) {
        $zbs6_time_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'zbs6_length'))) {
        $zbs6_length_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'zbs8_time'))) {
        $zbs8_time_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'zbs8_length'))) {
        $zbs8_length_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'comiflex_time'))) {
        $comiflex_time_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'comiflex_length'))) {
        $comiflex_length_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lamination_time'))) {
        $lamination_time_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'lamination_length'))) {
        $lamination_length_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_zbs6_time = '';
        $old_zbs6_length = '';
        $old_zbs8_time = '';
        $old_zbs8_length = '';
        $old_comiflex_time = '';
        $old_comiflex_length = '';
        $old_lamination_time = '';
        $old_lamination_length = '';
        
        $sql = "select zbs6_time, zbs6_length, zbs8_time, zbs8_length, comiflex_time, comiflex_length, lamination_time, lamination_length from norm_fitting order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_zbs6_time = $row['zbs6_time'];
            $old_zbs6_length = $row['zbs6_length'];
            $old_zbs8_time = $row['zbs8_time'];
            $old_zbs8_length = $row['zbs8_length'];
            $old_comiflex_time = $row['comiflex_time'];
            $old_comiflex_length = $row['comiflex_length'];
            $old_lamination_time = $row['lamination_time'];
            $old_lamination_length = $row['lamination_length'];
        }
        
        // Новый объект
        $new_zbs6_time = filter_input(INPUT_POST, 'zbs6_time');
        $new_zbs6_length = filter_input(INPUT_POST, 'zbs6_length');
        $new_zbs8_time = filter_input(INPUT_POST, 'zbs8_time');
        $new_zbs8_length = filter_input(INPUT_POST, 'zbs8_length');
        $new_comiflex_time = filter_input(INPUT_POST, 'comiflex_time');
        $new_comiflex_length = filter_input(INPUT_POST, 'comiflex_length');
        $new_lamination_time = filter_input(INPUT_POST, 'lamination_time');
        $new_lamination_length = filter_input(INPUT_POST, 'lamination_length');
        
        if($old_zbs6_time != $new_zbs6_time || $old_zbs6_length != $new_zbs6_length ||
                $old_zbs8_time != $new_zbs8_time || $old_zbs8_length != $new_zbs8_length ||
                $old_comiflex_time != $new_comiflex_time || $old_comiflex_length != $new_comiflex_length || 
                $old_lamination_time != $new_lamination_time || $old_lamination_length != $new_lamination_length) {
            $sql = "insert into norm_fitting (zbs6_time, zbs6_length, zbs8_time, zbs8_length, comiflex_time, comiflex_length, lamination_time, lamination_length) values ($new_zbs6_time, $new_zbs6_length, $new_zbs8_time, $new_zbs8_length, $new_comiflex_time, $new_comiflex_length, $new_lamination_time, $new_lamination_length)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$zbs6_time = '';
$zbs6_length = '';
$zbs8_time = '';
$zbs8_length = '';
$comiflex_time = '';
$comiflex_length = '';
$lamination_time = '';
$lamination_length = '';

$sql = "select zbs6_time, zbs6_length, zbs8_time, zbs8_length, comiflex_time, comiflex_length, lamination_time, lamination_length from norm_fitting order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $zbs6_time = $row['zbs6_time'];
    $zbs6_length = $row['zbs6_length'];
    $zbs8_time = $row['zbs8_time'];
    $zbs8_length = $row['zbs8_length'];
    $comiflex_time = $row['comiflex_time'];
    $comiflex_length = $row['comiflex_length'];
    $lamination_time = $row['lamination_time'];
    $lamination_length = $row['lamination_length'];
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
            <div class="row">
                <div class="col-12 col-md-8 col-lg-4">
                    <form method="post">
                        <div class="d-table w-100">
                            <div class="d-table-row">
                                <div class="d-table-cell w-50 pr-3">
                                    <h2>Время</h2>
                                </div>
                                <div class="d-table-cell w-50 pl-3">
                                    <h2>Метраж</h2>
                                </div>
                            </div>
                            <div class="d-table-row">
                                <div class="d-table-cell pr-3">
                                    <div class="form-group">
                                        <label for="zbs6_time">ZBS 6 цвет (руб/час)</label>
                                        <input type="text" class="form-control float-only" id="zbs6_time" name="zbs6_time" value="<?=$zbs6_time ?>" placeholder="Стоимость, час" required="required" />
                                        <div class="invalid-feedback">ZBS 6 цвет обязательно</div>
                                    </div>
                                </div>
                                <div class="d-table-cell pl-3">
                                    <div class="form-group">
                                        <label for="zbs6_length">ZBS 6 цвет (руб/м)</label>
                                        <input type="text" class="form-control float-only" id="zbs6_length" name="zbs6_length" value="<?=$zbs6_length ?>" placeholder="Стоимость, м" required="required" />
                                        <div class="invalid-feedback">ZBS 6 цвет обязательно</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-table-row">
                                <div class="d-table-cell pr-3">
                                    <div class="form-group">
                                        <label for="zbs8_time">ZBS 8 цвет (руб/час)</label>
                                        <input type="text" class="form-control float-only" id="zbs8_time" name="zbs8_time" value="<?=$zbs8_time ?>" placeholder="Стоимость, час" required="required" />
                                        <div class="invalid-feedback">ZBS 8 цвет обязательно</div>
                                    </div>
                                </div>
                                <div class="d-table-cell pl-3">
                                    <div class="form-group">
                                        <label for="zbs8_length">ZBS 8 цвет (руб/м)</label>
                                        <input type="text" class="form-control float-only" id="zbs8_length" name="zbs8_length" value="<?=$zbs8_length ?>" placeholder="Стоимость, м" required="required" />
                                        <div class="invalid-feedback">ZBS 8 цвет обязательно</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-table-row">
                                <div class="d-table-cell pr-3">
                                    <div class="form-group">
                                        <label for="comiflex_time">Comiflex (руб/час)</label>
                                        <input type="text" class="form-control float-only" id="comiflex_time" name="comiflex_time" value="<?=$comiflex_time ?>" placeholder="Стоимость, час" required="required" />
                                        <div class="invalid-feedback">Comiflex обязательно</div>
                                    </div>
                                </div>
                                <div class="d-table-cell pl-3">
                                    <div class="form-group">
                                        <label for="comiflex_length">Comiflex (руб/м)</label>
                                        <input type="text" class="form-control float-only" id="comiflex_length" name="comiflex_length" value="<?=$comiflex_length ?>" placeholder="Стоимость, м" required="required" />
                                        <div class="invalid-feedback">Comiflex обязательно</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-table-row">
                                <div class="d-table-cell pr-3">
                                    <div class="form-group">
                                        <label for="lamination_time">Ламинация (руб/час)</label>
                                        <input type="text" class="form-control float-only" id="lamination_time" name="lamination_time" value="<?=$lamination_time ?>" placeholder="Стоимость, час" required="required" />
                                        <div class="invalid-feedback">Ламинация обязательно</div>
                                    </div>
                                </div>
                                <div class="d-table-cell pl-3">
                                    <div class="form-group">
                                        <label for="lamination_length">Ламинация (руб/м)</label>
                                        <input type="text" class="form-control float-only" id="lamination_length" name="lamination_length" value="<?=$lamination_length ?>" placeholder="Стоимость, м" required="required" />
                                        <div class="invalid-feedback">Ламинация обязательно</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-table-row">
                                <div class="d-table-cell pr-3">
                                    <button type="submit" id="norm_fitting_submit" name="norm_fitting_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                                </div>
                                <div class="d-table-cell pl-3"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>