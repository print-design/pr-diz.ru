<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$first_name_valid = '';
$username_valid = '';
$password_valid = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['manager_create_submit'])) {
    if($_POST['first_name'] == '') {
        $first_name_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($_POST['username'] == '') {
        $username_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($_POST['password'] == '') {
        $password_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($form_valid) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if($conn->connect_error) {
            die('Ошибка соединения: '.$conn->connect_error);
        }
                
        $last_name = addslashes($_POST['last_name']);
        $first_name = addslashes($_POST['first_name']);
        $middle_name = addslashes($_POST['middle_name']);
        $username = addslashes($_POST['username']);
        $password = addslashes($_POST['password']);
                
        $sql = "insert into manager"
                . "(last_name, first_name, middle_name, username, password) "
                . "values "
                . "('$last_name', '$first_name', '$middle_name', '$username', password('$password'))";
                
        $conn->query('set names utf8');
        if ($conn->query($sql) === true) {
            header('Location: '.APPLICATION.'/manager/');
        }
        else {
            $error_message = $conn->error;
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
                            <h1>Новый менеджер</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/manager/" class="btn btn-outline-dark"><span class="font-awesome">&#xf0e2;</span>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr/>
                    <form method="post">
                        <div class="form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['last_name']) ? $_POST['last_name'] : '' ?>" autocomplete="off"/>
                        </div>
                        <div class="form-group">
                            <label for="first_name">Имя</label>
                            <input type="text" id="first_name" name="first_name" class="form-control<?=$first_name_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['first_name']) ? $_POST['first_name'] : '' ?>" required="required" autocomplete="off"/>
                            <div class="invalid-feedback">Имя обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="middle_name">Отчество</label>
                            <input type="text" id="middle_name" name="middle_name" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['middle_name']) ? $_POST['middle_name'] : '' ?>" autocomplete="off"/>
                        </div>
                        <div class="form-group">
                            <label for="username">Логин</label>
                            <input type="text" id="username" name="username" class="form-control<?=$username_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) ? $_POST['username'] : '' ?>" required="required" autocomplete="off"/>
                            <div class="invalid-feedback">Логин обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="password">Пароль</label>
                            <input type="password" id="password" name="password" class="form-control<?=$password_valid ?>" required="required" autocomplete="off"/>
                            <div class="invalid-feedback">Пароль обязательно</div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="manager_create_submit" name="manager_create_submit">Сохранить</button>
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