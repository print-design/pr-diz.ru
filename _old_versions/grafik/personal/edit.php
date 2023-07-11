<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$fio_valid = '';
$username_valid = '';
        
// Обработка отправки формы
$user_edit_submit = filter_input(INPUT_POST, 'user_edit_submit');
if($user_edit_submit !== null) {
    $fio = filter_input(INPUT_POST, 'fio');
    if($fio == '') {
        $fio_valid = ISINVALID;
        $form_valid = false;
    }

    $username = filter_input(INPUT_POST, 'username');
    if($username == '') {
        $username_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $username = addslashes($username);
        $error_message = (new Executer("update user set fio='$fio', username='$username' where id=".GetUserId()))->error;
        
        if($error_message == '') {
            header('Location: '.APPLICATION.'/personal/');
        }
    }
}
       
// Получение личных данных
$row = (new Fetcher("select fio, username from user where id=".GetUserId()))->Fetch();
$fio = $row['fio'];
$username = $row['username'];
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
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="d-flex justify-content-between">
                        <div class="p-1">
                            <h1>Редактирование личных данных</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/personal/" class="btn btn-outline-dark"><i class="fas fa-undo-alt"></i>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr/>
                    <form method="post">
                        <div class="form-group">
                            <label for="name">ФИО</label>
                            <input type="text" id="fio" name="fio" class="form-control<?=$fio_valid ?>" value="<?=htmlentities($fio) ?>" autocomplete="off" required="required"/>
                            <div class="invalid-feedback">ФИО обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="username">Логин</label>
                            <input type="text" id="username" name="username" class="form-control<?=$username_valid ?>" value="<?=$username ?>" autocomplete="off" required="required"/>
                            <div class="invalid-feedback">Логин обязательно</div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="user_edit_submit" name="user_edit_submit">Сохранить</button>
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