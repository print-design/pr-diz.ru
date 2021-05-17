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

$first_name_valid = '';
$last_name_valid = '';
$role_id_valid = '';
$email_valid = '';
$phone_valid = '';
$username_valid = '';
$password_valid = '';
        
// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'user_create_submit')) {
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
    
    $password = filter_input(INPUT_POST, 'password');
    if(empty($password)) {
        $password_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $first_name = addslashes($first_name);
        $last_name = addslashes($last_name);
        $email = addslashes($email);
        $phone = addslashes($phone);
        $username = addslashes($username);
        
        $executer = new Executer("insert into user (username, password, first_name, last_name, role_id, email, phone) values ('$username', password('$password'), '$first_name', '$last_name', $role_id, '$email', '$phone')");
        $error_message = $executer->error;
        $id = $executer->insert_id;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION."/user/");
        }
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
        <div class="container-fluid form-page">
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
            <div class="backlink">
                <a href="<?=APPLICATION ?>/user/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <div style="width:387px;">
                <h1>Добавление сотрудника</h1>
                <form method="post">
                    <div class="form-group">
                        <select id="role_id" name="role_id" class="form-control" required="required">
                            <option value="">ВЫБЕРИТЕ ДОЛЖНОСТЬ</option>
                            <?php
                            $roles = (new Grabber('select id, local_name from role order by priority'))->result;
                            foreach ($roles as $role) {
                                $id = $role['id'];
                                $local_name = $role['local_name'];
                                $selected = '';
                                if(filter_input(INPUT_POST, 'role_id') == $role['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$local_name</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="first_name">Имя</label>
                            <input type="text" id="first_name" name="first_name" class="form-control<?=$first_name_valid ?>" value="<?= filter_input(INPUT_POST, 'first_name') ?>" required="required"/>
                            <div class="invalid-feedback">Имя обязательно</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" id="last_name" name="last_name" class="form-control<?=$last_name_valid ?>" value="<?= filter_input(INPUT_POST, 'last_name') ?>" required="required"/>
                            <div class="invalid-feedback">Фамилия обязательно</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="last_name">E-Mail</label>
                            <input type="email" id="email" name="email" class="form-control<?=$email_valid ?>" value="<?= filter_input(INPUT_POST, 'email') ?>"/>
                            <div class="invalid-feedback">Неправильный формат e-mail</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="phone">Телефон</label>
                            <input type="tel" id="phone" name="phone" class="form-control<?=$phone_valid ?>" value="<?= filter_input(INPUT_POST, 'phone') ?>" required="required"/>
                            <div class="invalid-feedback">Телефон обязательно</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="username">Логин</label>
                            <input type="text" id="username" name="username" class="form-control<?=$username_valid ?>" value="<?= filter_input(INPUT_POST, 'username') ?>" required="required"/>
                            <div class="invalid-feedback">Логин обязательно</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="password">Пароль</label>
                            <input type="password" id="password" name="password" class="form-control<?=$password_valid ?>" value="" required="required"/>
                            <div class="invalid-feedback">Пароль обязательно</div>
                        </div>
                    </div>
                    <div class="form-group" style="padding-top: 24px;">
                        <button type="submit" class="btn btn-dark" id="user_create_submit" name="user_create_submit">Создать</button>
                    </div>
                </form>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>