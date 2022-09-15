<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole('admin')) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$role_id_valid = '';
        
// Обработка отправки формы
$create_user_role_submit = filter_input(INPUT_POST, 'create_user_role_submit');
if($create_user_role_submit !== null) {
    $role_id = filter_input(INPUT_POST, 'role_id');
    if($role_id == '') {
        $role_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $user_id = filter_input(INPUT_POST, 'user_id');
        $error_message = (new Executer("insert into user_role (user_id, role_id) values ($user_id, $role_id)"))->error;
    }
}

$delete_user_role_submit = filter_input(INPUT_POST, 'delete_user_role_submit');
if($delete_user_role_submit !== null) {
    $user_id = filter_input(INPUT_POST, 'user_id');
    $role_id = filter_input(INPUT_POST, 'role_id');
    $error_message = (new Executer("delete from user_role where user_id = $user_id and role_id = $role_id"))->error;
}

// Если нет параметра id, переход к списку
$id = filter_input(INPUT_GET, 'id');
if($id === null) {
    header('Location: '.APPLICATION.'/user/');
}
        
// Список всех машин
$sql = "select id, name, user1_name, user2_name from machine order by name";
$grabber = new Grabber($sql);
$error_message = $grabber->error;
$machines = $grabber->result;

// Полная информация о пользователе, включая все отработанные смены
$username = '';
$user_info_array = array();

if(empty($error_message)) {
    $sql = "select u.username, u.fio, u.quit";
    foreach($machines as $machine) {
        $sql .= ", (select count(id) from workshift where machine_id = ".$machine['id']." and user1_id = u.id) user1_".$machine['id'];
        if(!empty($machine['user2_name'])) {
            $sql .= ", (select count(id) from workshift where machine_id = ".$machine['id']." and user2_id = u.id) user2_".$machine['id'];
        }
        $sql .= ", (select count(e.id) from workshift ws inner join edition e on e.workshift_id = ws.id where ws.machine_id = ".$machine['id']." and e.manager_id = u.id) manager_".$machine['id'];
    }
    $sql .= " from user u where u.id = $id";
    $grabber = new Grabber($sql);
    $error_message = $grabber->error;
    $user_info_array = $grabber->result;
}

// Количество отработанных смен
$shifts_count = 0;

foreach($user_info_array as $user_info) {
    $username = $user_info['username'];
    foreach ($machines as $machine) {
        $shifts_count += intval($user_info['user1_'.$machine['id']]);
        if(!empty($machine['user2_name'])) {
            $shifts_count += intval($user_info['user2_'.$machine['id']]);
        }
        $shifts_count += intval($user_info['manager_'.$machine['id']]);
    }
}

// Все роли
$roles = (new Grabber("select id, local_name from role where id not in (select role_id from user_role where user_id = $id) order by local_name"))->result;

// Роли текущего пользователя
$myroles = (new Grabber("select ur.user_id, ur.role_id, r.local_name from role r inner join user_role ur on r.id = ur.role_id where ur.user_id = $id order by local_name"))->result;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            body {
                padding-left: 0;
            }
        </style>
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
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1><?=$username ?></h1>
                        </div>
                        <div class="p-1">
                            <div class="btn-group">
                                <a href="<?=APPLICATION ?>/user/" class="btn btn-outline-dark"><i class="fas fa-undo"></i>&nbsp;К списку</a>
                                <a href="<?=APPLICATION ?>/user/edit.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-edit"></i>&nbsp;Редактировать</a>
                                <?php
                                if($shifts_count === 0 && filter_input(INPUT_COOKIE, USERNAME) != $username) :
                                ?>
                                <a href="<?=APPLICATION ?>/user/delete.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-trash-alt"></i>&nbsp;Удалить</a>
                                <?php
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover">
                        <?php foreach($user_info_array as $user_info): ?>
                        <tr>
                            <th>ФИО</th>
                            <td><?=$user_info['fio'] ?></td>
                        </tr>
                        <tr>
                            <th>Логин</th>
                            <td><?=$user_info['username'] ?></td>
                        </tr>
                        <tr>
                            <th>Уволился</th>
                            <td><?=($user_info['quit'] == 0 ? 'Нет' : 'Да') ?></td>
                        </tr>
                        <?php foreach($machines as $machine): ?>
                        <tr>
                            <th><?=$machine['name'].', '.$machine['user1_name'] ?></th>
                            <td><?=$user_info['user1_'.$machine['id']] ?></td>
                        </tr>
                        <?php if(!empty($machine['user2_name'])): ?>
                        <tr>
                            <th><?=$machine['name'].', '.$machine['user2_name'] ?></th>
                            <td><?=$user_info['user2_'.$machine['id']] ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th><?=$machine['name'].', Менеджер' ?></th>
                            <td><?=$user_info['manager_'.$machine['id']] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h2>Роли</h2>
                        </div>
                        <div class="p-1">
                            <form method="post" class="form-inline">
                                <input type="hidden" id="user_id" name="user_id" value="<?=$_GET['id'] ?>"/>
                                <div class="form-group">
                                    <select id="role_id" name="role_id" class="form-control<?=$role_id_valid ?>" required="required">
                                        <option value="">...</option>
                                        <?php
                                        foreach ($roles as $row) {
                                            $id = $row['id'];
                                            $local_name = $row['local_name'];
                                            echo "<option value='$id'>$local_name</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">*</div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="form-control" id="create_user_role_submit" name="create_user_role_submit">
                                        <i class="fas fa-plus"></i>&nbsp;Добавить
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tbody>
                            <?php
                            foreach ($myroles as $row) {
                                $user_id = $row['user_id'];
                                $role_id = $row['role_id'];
                                $local_name = $row['local_name'];
                                echo <<<ROLE
                                <tr>
                                    <td>$local_name</td><td style='width:15%';>
                                        <form method='post'>
                                            <input type='hidden' id='user_id' name='user_id' value='$user_id' />
                                            <input type='hidden' id='role_id' name='role_id' value='$role_id' />
                                            <button type='submit' id='delete_user_role_submit' name='delete_user_role_submit' class='form-control confirmable text-nowrap'><i class='fas fa-trash-alt'></i>&nbsp;Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                                ROLE;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>