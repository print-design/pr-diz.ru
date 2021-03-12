<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$old_password_valid = '';
$new_password_valid = '';
$confirm_valid = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password_change_submit'])) {
    if($_POST['old_password'] == '') {
        $old_password_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($_POST['new_password'] == '') {
        $new_password_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($_POST['new_password'] != $_POST['confirm']) {
        $confirm_valid = ISINVALID;
        $form_valid = false;
    }
                        
    if($form_valid) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if($conn->connect_error) {
            die('Ошибка соединения: '.$conn->connect_error);
        }

        $sql = "select count(*) count from manager where id=". GetManagerId()." and password=password('".$_POST['old_password']."')";
        $conn->query('set names utf8');
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            if($row = $result->fetch_assoc()) {
                if($row['count'] == 0){
                    $error_message = "Неправильный текущий пароль";
                }
                else {
                    $new_password = $_POST['new_password'];
                    $update_sql="update manager set password=password('$new_password') where id=".GetManagerId();
                    if ($conn->query($update_sql) === true) {
                        header('Location: '.APPLICATION.'/personal/index.php?password=true');
                    }
                    else {
                        $error_message = $conn->error;
                    }
                }
            }
        }
        $conn->close();
    }
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
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1>Смена пароля</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/personal/" class="btn btn-outline-dark"><span class="font-awesome">&#xf0e2;</span>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr/>
                    <form method="post">
                        <div class="form-group">
                            <label for="old_password">Текущий пароль</label>
                            <input type="password" id="old_password" name="old_password" class="form-control<?=$old_password_valid ?>" required="required"/>
                            <div class="invalid-feedback">Текущий пароль обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Новый пароль</label>
                            <input type="password" id="new_password" name="new_password" class="form-control<?=$new_password_valid ?>" required="required"/>
                            <div class="invalid-feedback">Новый пароль обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="confirm">Введите новый пароль повторно</label>
                            <input type="password" id="confirm" name="confirm" class="form-control<?=$confirm_valid ?>" required="required"/>
                            <div class="invalid-feedback">Новый пароль и его подтверждение не совпадают</div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="password_change_submit" name="password_change_submit">Сменить</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php
            include '../include/footer.php';
            ?>
        </div>
    </body>
</html>