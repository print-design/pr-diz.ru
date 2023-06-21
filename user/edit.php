<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на список
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION.'/user/');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$first_name_valid = '';
$last_name_valid = '';
$role_id_valid = '';
$email_valid = '';
$phone_valid = '';
$username_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'user_edit_submit')) {
    $first_name = filter_input(INPUT_POST, 'first_name');
    if(empty($first_name)) {
        $first_name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $last_name = filter_input(INPUT_POST, 'last_name');
    if(empty($last_name)) {
        $last_name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $role_id = filter_input(INPUT_POST, 'role_id');
    if(empty($role_id)) {
        $role_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $email = filter_input(INPUT_POST, 'email');
    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_valid = ISINVALID;
        $form_valid = false;
    }
    
    $phone = filter_input(INPUT_POST, 'phone');
    if(empty($phone)) {
        $phone_valid = ISINVALID;
        $form_valid = false;
    }
    
    $username = filter_input(INPUT_POST, 'username');
    if(empty($username)) {
        $username_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $id = filter_input(INPUT_POST, 'id');
        $first_name = addslashes($first_name);
        $last_name = addslashes($last_name);
        $email = addslashes($email);
        $phone = addslashes($phone);
        $username = addslashes($username);
        
        $sql = "update user set username='$username', first_name='$first_name', last_name='$last_name', role_id=$role_id, email='$email', phone='$phone' where id=$id";
        
        $password = filter_input(INPUT_POST, 'password');
        if(!empty($password)) {
            $password = addslashes($password);
            $sql = "update user set username='$username', password=password('$password'), first_name='$first_name', last_name='$last_name', role_id=$role_id, email='$email', phone='$phone' where id=$id";
        }
        
        $error_message = (new Executer($sql))->error;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/user/');
        }
    }
}

// Получение объекта
$row = (new Fetcher("select username, last_name, first_name, email, phone, role_id from user where id=". filter_input(INPUT_GET, 'id')))->Fetch();

$username = filter_input(INPUT_POST, 'username');
if(empty($username)) {
    $username = htmlentities($row['username']);
}

$last_name = filter_input(INPUT_POST, 'last_name');
if(empty($last_name)) {
    $last_name = htmlentities($row['last_name']);
}

$first_name = filter_input(INPUT_POST, 'first_name');
if(empty($first_name)) {
    $first_name = htmlentities($row['first_name']);
}

$email = filter_input(INPUT_POST, 'email');
if(empty($email)) {
    $email = htmlentities($row['email']);
}

$phone = filter_input(INPUT_POST, 'phone');
if(empty($phone)) {
    $phone = htmlentities($row['phone']);
}

$role_id = filter_input(INPUT_POST, 'role_id');
if(empty($role_id)) {
    $role_id = $row['role_id'];
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
            ?>
            <div class="row">
                <div class="col-12 col-md-6">
                    <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/user/">Назад</a>
                    <h1>Редактирование сотрудника</h1>
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>"/>
                        <div class="form-group">
                            <select id="role_id" name="role_id" class="form-control" required="required">
                                <option value="">...</option>
                                <?php
                                foreach (ROLES as $role) {
                                    $id = $role;
                                    $local_name = ROLE_LOCAL_NAMES[$role];
                                    $selected = '';
                                    if($role_id == $role) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$local_name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label for="first_name">Имя</label>
                                <input type="text" id="first_name" name="first_name" class="form-control<?=$first_name_valid ?>" value="<?= $first_name ?>" required="required"/>
                                <div class="invalid-feedback">Имя обязательно</div>
                            </div>
                            <div class="col-6 form-group">
                                <label for="last_name">Фамилия</label>
                                <input type="text" id="last_name" name="last_name" class="form-control<?=$last_name_valid ?>" value="<?= $last_name ?>" required="required"/>
                                <div class="invalid-feedback">Фамилия обязательно</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label for="last_name">E-Mail</label>
                                <input type="email" id="email" name="email" class="form-control<?=$email_valid ?>" value="<?= $email ?>"/>
                                <div class="invalid-feedback">Неправильный формат e-mail</div>
                            </div>
                            <div class="col-6 form-group">
                                <label for="phone">Телефон</label>
                                <input type="tel" id="phone" name="phone" class="form-control<?=$phone_valid ?>" value="<?= $phone ?>" required="required"/>
                                <div class="invalid-feedback">Телефон обязательно</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label for="username">Логин</label>
                                <input type="text" id="username" name="username" class="form-control<?=$username_valid ?>" value="<?= $username ?>" required="required"/>
                                <div class="invalid-feedback">Логин обязательно</div>
                            </div>
                            <div class="col-6 form-group">
                                <label for="password">Пароль</label>
                                <input type="password" id="password" name="password" class="form-control" value=""/>
                                <div class="invalid-feedback">Пароль обязательно</div>
                            </div>
                        </div>
                        <div class="alert alert-warning">Если оставить пустым поле &laquo;Пароль&raquo;, то останется прежний пароль.</div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-dark" id="user_edit_submit" name="user_edit_submit">Сохранить</button>
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