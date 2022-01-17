<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$name_valid = '';
        
// Обработка отправки формы
$add_roller_submit = filter_input(INPUT_POST, 'add_roller_submit');
if($add_roller_submit !== null) {
    $name = filter_input(INPUT_POST, 'name');
    if($name == '') {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $name = addslashes($name);
        $position = filter_input(INPUT_POST, 'position');
        if($position == '') {
            $position = 0;
        }
        $machine_id = filter_input(INPUT_POST, 'machine_id');
        $error_message = (new Executer("insert into roller (name, position, machine_id) values ('$name', '$position', $machine_id)"))->error;
    }
}

$delete_roller_submit = filter_input(INPUT_POST, 'delete_roller_submit');
if($delete_roller_submit !== null) {
    $roller_id = filter_input(INPUT_POST, 'roller_id');
    $error_message = (new Executer("delete from roller where id=$roller_id"))->error;
}
        
// Если нет параметра id, переход к списку
$id = filter_input(INPUT_GET, 'id');
if($id === null) {
    header('Location: '.APPLICATION.'/machine/');
}
        
// Получение объекта
$sql = "select m.name, m.user1_name, m.user2_name, m.role_id, m.has_organization, m.has_edition, m.has_length, m.has_status, m.has_roller, m.has_lamination, m.has_coloring, m.coloring, m.has_manager, m.has_comment, m.is_cutter, r.local_name role "
        . "from machine m "
        . "left join role r on m.role_id = r.id "
        . "where m.id=$id";
$row = (new Fetcher($sql))->Fetch();
$name = $row['name'];
$user1_name = $row['user1_name'];
$user2_name = $row['user2_name'];
$role_id = $row['role_id'];
$has_organization = $row['has_organization'];
$has_edition = $row['has_edition'];
$has_length = $row['has_length'];
$has_status = $row['has_status'];
$has_roller = $row['has_roller'];
$has_lamination = $row['has_lamination'];
$has_coloring = $row['has_coloring'];
$coloring = $row['coloring'];
$has_manager = $row['has_manager'];
$has_comment = $row['has_comment'];
$is_cutter = $row['is_cutter'];
$role = $row['role'];
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
                            <h1><?=$name ?></h1>
                        </div>
                        <div class="p-1">
                            <div class="btn-group">
                                <a href="<?=APPLICATION ?>/machine/" class="btn btn-outline-dark"><i class="fas fa-undo-alt"></i>&nbsp;К списку</a>
                                <a href="<?=APPLICATION ?>/machine/edit.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-edit"></i>&nbsp;Редактировать</a>
                                <a href="<?=APPLICATION ?>/machine/delete.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-trash-alt"></i>&nbsp;Удалить</a>
                            </div>
                        </div>
                    </div>
                    <table class="table table-hover">
                        <tr><th>Пользователь 1</th><td><?=$user1_name ?></td></tr>
                        <tr><th>Пользователь 2</th><td><?=$user2_name ?></td></tr>
                        <tr><th>Роль</th><td><?=$role ?></td></tr>
                        <tr><th>Есть организация</th><td><?=$has_organization == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть тираж</th><td><?=$has_edition == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть длина</th><td><?=$has_length == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть статус</th><td><?=$has_status == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть вал</th><td><?=$has_roller == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть ламинация</th><td><?=$has_lamination == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть красочность</th><td><?=$has_coloring == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Красочность</th><td><?=$coloring ?></td></tr>
                        <tr><th>Есть менеджер</th><td><?=$has_manager == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть комментарий</th><td><?=$has_comment == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Это резка?</th><td><?=$is_cutter == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                    </table>
                    <hr/>
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h2>Валы</h2>
                        </div>
                        <div class="p-1">
                            <form class="form-inline" method="post">
                                <div class="input-group">
                                    <input type="hidden" id="machine_id" name="machine_id" value="<?=$id ?>"/>
                                    <input type="text" class="form-control<?=$name_valid ?>" placeholder="Наименование вала" id="name" name="name" required="required" />
                                    <input type="number" class="form-control<?=$name_valid ?>" placeholder="Позиция" id="position" name="position" />
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-dark" id="add_roller_submit" name="add_roller_submit">
                                            <i class="fas fa-plus"></i>&nbsp;Добавить
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <th></th>
                                <th class="w-25">Наименование</th>
                                <th class="w-25">Позиция</th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php
                            $rollers = (new Grabber("select id, name, position from roller where machine_id=$id order by position, name"))->result;
                            $roller_num = 0;
                            
                            foreach ($rollers as $row) {
                                $roller_id = $row['id'];
                                echo "<tr>"
                                        ."<td>".(++$roller_num)."</td>"
                                        ."<td>".htmlentities($row['name'])."</td>"
                                        . "<td>".$row['position']."</td>"
                                        ."<td class='text-right'>"
                                        . "<a class='btn btn-outline-dark' title='Редактировать' href='edit_roller.php?id=".$row['id']."'><i class='fas fa-edit'></i>&nbsp;Редактировать</a>"
                                        . "</td>"
                                        . "<td class='text-right'>"
                                        . "<form method='post'>"
                                        . "<input type='hidden' id='roller_id' name='roller_id' value='$roller_id' />"
                                        . "<button type='submit' class='btn btn-outline-dark confirmable' id='delete_roller_submit' name='delete_roller_submit'><i class='fas fa-trash-alt'></i>&nbsp;Удалить</button>"
                                        . "</form>"
                                        . "</td>"
                                        ."</tr>";
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