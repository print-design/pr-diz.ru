<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$fio_valid = '';
$username_valid = '';
        
// Обработка отправки формы
$user_edit_submit = filter_input(INPUT_POST, 'user_edit_submit');
if($user_edit_submit !== null) {
    $id = filter_input(INPUT_POST, 'id');
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
        $fio = addslashes($fio);
        $username = addslashes($username);
        $quit = filter_input(INPUT_POST, 'quit') == 'on' ? 1 : 0;
        $sql = "update user set fio='$fio', quit=$quit, username='$username' where id=$id";
        
        $password = filter_input(INPUT_POST, 'password');
        if($password !== '') {
            $sql = "update user set fio='$fio', quit=$quit, username='$username', password=password('$password') where id=$id";
        }
        
        $error_message = (new Executer($sql))->error;
        if($error_message == '') {
            header('Location: '.APPLICATION."/user/details.php?id=$id");
        }
    }
}

// Если нет параметра id, переход к списку
$id = filter_input(INPUT_GET, 'id');
if ($id === null) {
    header('Location: '.APPLICATION.'/user/');
}

// Получение объекта
$fio = '';
$username = '';

if ($row = (new Fetcher("select fio, quit, username from user where id=$id"))->Fetch()) {
    $fio = $row['fio'];
    $checked = ($row['quit'] == 0 ? "" : " checked='checked'");
    $username = $row['username'];
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
                            <h1>Редактирование пользователя</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/user/details.php?id=<?=$_GET['id'] ?>" class="btn btn-outline-dark"><i class="fas fa-undo-alt"></i>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr/>
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$_GET['id'] ?>"/>
                        <div class="form-group">
                            <label for="name">ФИО</label>
                            <input type="text" id="fio" name="fio" class="form-control<?=$fio_valid ?>" value="<?=htmlentities($fio) ?>" autocomplete="off" required="required"/>
                            <div class="invalid-feedback">ФИО обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="username">Логин</label>
                            <input type="text" id="username" name="username" class="form-control<?=$username_valid ?>" value="<?=htmlentities($username) ?>" autocomplete="off" required="required"/>
                            <div class="invalid-feedback">Логин обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="password">Пароль <span class="text-danger">(Если оставить поле пустым, пароль останется прежним)</span></label>
                            <input type="password" id="password" name="password" class="form-control" />
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="quit" name="quit"<?=$checked ?> />
                            <label class="form-check-label" for="quit">Уволился</label>
                        </div>
                        <br/>
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