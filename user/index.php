<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы - удаление пользователя
if(null !== filter_input(INPUT_POST, 'delete_user_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from user where id=$id"))->error;
}

// Обработка отправки формы - смена пароля
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$user_change_password_old_valid = '';
$user_change_password_new_valid = '';
$user_change_password_confirm_valid = '';

if(null !== filter_input(INPUT_POST, 'user_change_password_submit')) {
    if(empty(filter_input(INPUT_POST, "user_change_password_old"))) {
        $user_change_password_old_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'user_change_password_new'))) {
        $user_change_password_new_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'user_change_password_confirm'))) {
        $user_change_password_confirm_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'user_change_password_new') != filter_input(INPUT_POST, 'user_change_password_confirm')) {
        $user_change_password_confirm_valid = ISINVALID;
        $form_valid = false;
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
        <div id="user_change_password" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <input type="hidden" id="user_change_password_id" name="user_change_password_id" />
                        <div class="modal-header">
                            <div style="font-size: xx-large;">Изменение пароля</div>
                            <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div style="font-size: x-large;">Сотрудник: <span id="user_change_password_fio"></span></div>
                            <div class="form-group">
                                <label for="user_change_password_old">Текущий пароль</label>
                                <input type="password" id="user_change_password_old" name="user_change_password_old" class="form-control<?=$user_change_password_old_valid ?>" required="required" />
                                <div class="invalid-feedback">Текущий пароль обязательно</div>
                            </div>
                            <div class="form-group">
                                <label for="user_change_password_new">Новый пароль</label>
                                <input type="password" id="user_change_password_new" name="user_change_password_new" class="form-control<?=$user_change_password_new_valid ?>" required="required" />
                                <div class="invalid-feedback">Новый пароль обязательно</div>
                            </div>
                            <div class="form-group">
                                <label for="user_change_password_confirm">Новый пароль ещё раз</label>
                                <input type="password" id="user_change_password_confirm" name="user_change_password_confirm" class="form-control<?=$user_change_password_confirm_valid ?>" required="required" />
                                <div class="invalid-feedback">Новый пароль и его подтверждение не совпадают</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="user_change_password_submit" name="user_change_password_submit">Изменить пароль</button>
                            <button type="button" class="btn" data-dismiss="modal">Отменить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <?php
            if(null !== filter_input(INPUT_POST, 'user_change_password_submit') && $form_valid) {
                echo "<div class='alert alert-success'>Пароль изменен успешно</div>";
            }
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <?php
                    include '../include/subheader_admin.php';
                    ?>
                </div>
                <div class="p-1">
                    <a href="create.php" title="Добавить пользователя" class="btn btn-outline-dark">
                        <i class="fas fa-plus" style="font-size: 12px;"></i>&nbsp;&nbsp;Добавить сотрудника
                    </a>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ФИО</th>
                        <th>Должность</th>
                        <th>Логин</th>
                        <th>E-Mail</th>
                        <th>Телефон</th>
                        <th style="width: 80px;"></th>
                        <th style="width: 80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select u.id, u.first_name, u.last_name, r.local_name role, u.username, u.email, u.phone "
                            . "from user u inner join role r on u.role_id = r.id "
                            . "order by u.first_name asc";
                    $fetcher = new Fetcher($sql);
                    $error_message = $fetcher->error;
                    
                    while ($row = $fetcher->Fetch()):
                    ?>
                    <tr>
                        <td><?=$row['first_name'].' '.$row['last_name'] ?></td>
                        <td><?=$row['role'] ?></td>
                        <td><?=$row['username'] ?></td>
                        <td><?=$row['email'] ?></td>
                        <td><?=$row['phone'] ?></td>
                        <td class='text-right'>
                            <button type="button" class="btn btn-link user_change_password_open" data-id="<?=$row['id'] ?>" data-fio="<?=$row['last_name'].' '.$row['first_name'] ?>" data-toggle="modal" data-target="#user_change_password">
                                <image src='../images/icons/edit.svg' />
                            </button>
                        </td>
                        <td class='text-right'>
                            <?php if(filter_input(INPUT_COOKIE, USER_ID) != $row['id']): ?>
                            <form method='post'>
                                <input type='hidden' id='id' name='id' value='<?=$row['id'] ?>' />
                                <button type='submit' class='btn btn-link confirmable' id='delete_user_submit' name='delete_user_submit'><i class="fas fa-trash-alt"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        echo $error_message."<br />";
        echo null !== filter_input(INPUT_POST, 'user_change_password_submit');
        echo !empty($error_message);
        print_r($_POST);
        ?>
        <script>
            // Открытие формы изменения пароля при нажатии на "карандаш"
            $('.user_change_password_open').click(function(){
                $('#user_change_password_id').val($(this).attr('data-id'));
                $('#user_change_password_fio').text($(this).attr('data-fio'));
            });
            
            // Открытие формы изменения пароля, если изменение пароля не было удачным
            <?php if(null !== filter_input(INPUT_POST, 'user_change_password_submit') && !$form_valid): ?>
            $('#user_change_password').modal('show');
            <?php endif; ?>
        </script>
    </body>
</html>