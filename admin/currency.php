<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
$form_valid = true;
$error_message = '';

$usd_valid = '';
$euro_valid = '';

// Сохранение введённых значений
if(null !== filter_input(INPUT_POST, 'currency_submit')) {
    if(empty(filter_input(INPUT_POST, 'usd'))) {
        $usd_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'euro'))) {
        $euro_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Старый объект
        $old_usd = '';
        $old_euro = '';
        
        $sql = "select usd, euro from currency order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $old_usd = $row['usd'];
            $old_euro = $row['euro'];
        }
        
        // Новый объект
        $new_usd = filter_input(INPUT_POST, 'usd');
        $new_euro = filter_input(INPUT_POST, 'euro');
        
        if($old_usd != $new_usd || $old_euro != $new_euro) {
            $sql = "insert into currency (usd, euro) values ($new_usd, $new_euro)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $error_message = "Данные не изменились";
        }
    }
}

// Получение объекта
$usd = '';
$euro = '';

$sql = "select usd, euro from currency order by date desc limit 1";
$fetcher = new Fetcher($sql);
if(empty($error_message)) {
    $error_message = $fetcher->error;
}

if($row = $fetcher->Fetch()) {
    $usd = $row['usd'];
    $euro = $row['euro'];
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
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            if(null !== filter_input(INPUT_POST, 'currency_submit') && empty($error_message)):
            ?>
            <div class="alert alert-success">Данные сохранены</div>
            <?php
            endif;
            ?>
            <div class="row">
                <div class="col-12 col-md-4 col-lg-2">
                    <form method="post">
                        <div class="form-group">
                            <label for="usd">Доллар</label>
                            <div class="input-group">
                                <input type="text" class="form-control float-only<?=$usd_valid ?>" id="usd" name="usd" value="<?= empty($usd) ? "" : floatval($usd) ?>" placeholder="Доллар" required="required" autocomplete="off" />
                                <div class="input-group-append"><span class="input-group-text">руб.</span></div>
                            </div>
                            <div class="invalid-feedback">Доллар обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="euro">Евро</label>
                            <div class="input-group">
                                <input type="text" class="form-control float-only<?=$euro_valid ?>" id="euro" name="euro" value="<?= empty($euro) ? "" : floatval($euro) ?>" placeholder="Евро" required="required" autocomplete="off" />
                                <div class="input-group-append"><span class="input-group-text">руб.</span></div>
                            </div>
                            <div class="invalid-feedback">Евро обязательно</div>
                        </div>
                        <button type="submit" id="currency_submit" name="currency_submit" class="btn btn-dark w-100 mt-5">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>