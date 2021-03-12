<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';

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
        
// Получение объекта
$username = '';
$fio = '';
        
$comiflex_typographer = 0;
$comiflex_assistant = 0;
$comiflex_manager = 0;
$zbs1_typographer = 0;
$zbs1_manager = 0;
$zbs2_typographer = 0;
$zbs2_manager = 0;
$zbs3_typographer = 0;
$zbs3_manager = 0;
$atlas_typographer = 0;
$atlas_manager = 0;
$lamination_laminator1 = 0;
$lamination_laminator2 = 0;
$lamination_manager = 0;
$cutting_cutter = 0;
$cutting_manager = 0;
        
$sql = "select u.username, u.fio, u.quit, "
        . "(select count(id) from workshift where machine_id = 1 and user1_id = u.id) comiflex_typographer, "
        . "(select count(id) from workshift where machine_id = 1 and user2_id = u.id) comiflex_assistant, "
        . "(select count(e.id) from workshift ws inner join edition e on e.workshift_id = ws.id where ws.machine_id = 1 and e.manager_id = u.id) comiflex_manager, "
        . "(select count(id) from workshift where machine_id = 2 and user1_id = u.id) zbs1_typographer, "
        . "(select count(e.id) from workshift ws inner join edition e on e.workshift_id = ws.id  where ws.machine_id = 2 and e.manager_id = u.id) zbs1_manager, "
        . "(select count(id) from workshift where machine_id = 3 and user1_id = u.id) zbs2_typographer, "
        . "(select count(e.id) from workshift ws inner join edition e on e.workshift_id = ws.id  where ws.machine_id = 3 and e.manager_id = u.id) zbs2_manager, "
        . "(select count(id) from workshift where machine_id = 4 and user1_id = u.id) zbs3_typographer, "
        . "(select count(e.id) from workshift ws inner join edition e on e.workshift_id = ws.id  where ws.machine_id = 4 and e.manager_id = u.id) zbs3_manager, "
        . "(select count(id) from workshift where machine_id = 5 and user1_id = u.id) atlas_typographer, "
        . "(select count(e.id) from workshift ws inner join edition e on e.workshift_id = ws.id  where ws.machine_id = 5 and e.manager_id = u.id) atlas_manager, "
        . "(select count(id) from workshift where machine_id = 6 and user1_id = u.id) lamination_laminator1, "
        . "(select count(id) from workshift where machine_id = 6 and user2_id = u.id) lamination_laminator2, "
        . "(select count(e.id) from workshift ws inner join edition e on e.workshift_id = ws.id  where ws.machine_id = 6 and e.manager_id = u.id) lamination_manager, "
        . "(select count(id) from workshift where machine_id = 7 and user1_id = u.id) cutting_cutter, "
        . "(select count(e.id) from workshift ws inner join edition e on e.workshift_id = ws.id  where ws.machine_id = 7 and e.manager_id = u.id) cutting_manager "
        . "from user u where u.id = $id";

$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;

$row = $fetcher->Fetch();
$username = $row['username'];
$fio = $row['fio'];
$quit = $row['quit'];
$comiflex_typographer = $row['comiflex_typographer'];
$comiflex_assistant = $row['comiflex_assistant'];
$comiflex_manager = $row['comiflex_manager'];
$zbs1_typographer = $row['zbs1_typographer'];
$zbs1_manager = $row['zbs1_manager'];
$zbs2_typographer = $row['zbs2_typographer'];
$zbs2_manager = $row['zbs2_manager'];
$zbs3_typographer = $row['zbs3_typographer'];
$zbs3_manager = $row['zbs3_manager'];
$atlas_typographer = $row['atlas_typographer'];
$atlas_manager = $row['atlas_manager'];
$lamination_laminator1 = $row['lamination_laminator1'];
$lamination_laminator2 = $row['lamination_laminator2'];
$lamination_manager = $row['lamination_manager'];
$cutting_cutter = $row['cutting_cutter'];
$cutting_manager = $row['cutting_manager'];

$roles = (new Grabber("select id, local_name from role where id not in (select role_id from user_role where user_id = $id) order by local_name"))->result;
$myroles = (new Grabber("select ur.user_id, ur.role_id, r.local_name from role r inner join user_role ur on r.id = ur.role_id where ur.user_id = $id order by local_name"))->result;
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
                                $shifts_count = intval($comiflex_typographer) +
                                        intval($comiflex_assistant) +
                                        intval($comiflex_manager) +
                                        intval($zbs1_typographer) +
                                        intval($zbs1_manager) +
                                        intval($zbs2_typographer) +
                                        intval($zbs2_manager) +
                                        intval($zbs3_typographer) +
                                        intval($zbs3_manager) +
                                        intval($atlas_typographer) +
                                        intval($atlas_manager) +
                                        intval($lamination_laminator1) +
                                        intval($lamination_laminator2) +
                                        intval($lamination_manager) +
                                        intval($cutting_cutter) +
                                        intval($cutting_manager);
                                if($shifts_count === 0 && filter_input(INPUT_COOKIE, USERNAME) != $username) :
                                ?>
                                <a href="<?=APPLICATION ?>/user/delete.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-trash-alt"></i>&nbsp;Удалить</a>
                                <?php
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>ФИО</th>
                            <td><?=$fio ?></td>
                        </tr>
                        <tr>
                            <th>Логин</th>
                            <td><?=$username ?></td>
                        </tr>
                        <tr>
                            <th>Уволился</th>
                            <td><?=($quit == 0 ? 'Нет' : 'Да') ?></td>
                        </tr>
                        <tr>
                            <th>Comiflex, печатник</th>
                            <td><?=$comiflex_typographer ?></td>
                        </tr>
                        <tr>
                            <th>Comiflex, ассистент</th>
                            <td><?=$comiflex_assistant ?></td>
                        </tr>
                        <tr>
                            <th>Comiflex, менеджер</th>
                            <td><?=$comiflex_manager ?></td>
                        </tr>
                        <tr>
                            <th>ZBS-1, печатник</th>
                            <td><?=$zbs1_typographer ?></td>
                        </tr>
                        <tr>
                            <th>ZBS-1, менеджер</th>
                            <td><?=$zbs1_manager ?></td>
                        </tr>
                        <tr>
                            <th>ZBS-2, печатник</th>
                            <td><?=$zbs2_typographer ?></td>
                        </tr>
                        <tr>
                            <th>ZBS-2, менеджер</th>
                            <td><?=$zbs2_manager ?></td>
                        </tr>
                        <tr>
                            <th>ZBS-3, печатник</th>
                            <td><?=$zbs3_typographer ?></td>
                        </tr>
                        <tr>
                            <th>ZBS-3, менеджер</th>
                            <td><?=$zbs3_manager ?></td>
                        </tr>
                        <tr>
                            <th>Атлас, печатник</th>
                            <td><?=$atlas_typographer ?></td>
                        </tr>
                        <tr>
                            <th>Атлас, менеджер</th>
                            <td><?=$atlas_manager ?></td>
                        </tr>
                        <tr>
                            <th>Ламинация, ламинаторщик 1</th>
                            <td><?=$lamination_laminator1 ?></td>
                        </tr>
                        <tr>
                            <th>Ламинация, ламинаторщик 2</th>
                            <td><?=$lamination_laminator2 ?></td>
                        </tr>
                        <tr>
                            <th>Ламинация, менеджер</th>
                            <td><?=$lamination_manager ?></td>
                        </tr>
                        <tr>
                            <th>Резка, резчик</th>
                            <td><?=$cutting_cutter ?></td>
                        </tr>
                        <tr>
                            <th>Резка, менеджер</th>
                            <td><?=$cutting_manager ?></td>
                        </tr>
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