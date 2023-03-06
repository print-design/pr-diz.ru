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
$sql = "select m.name, m.position, m.user1_name, m.user2_name, m.role_id, m.has_organization, m.has_edition, m.has_material, m.has_thickness, m.has_width, m.has_length, m.has_status, m.has_roller, m.has_lamination, m.has_coloring, m.coloring, m.has_manager, m.has_comment, m.is_cutter, r.local_name role, "
        . "(select count(id) from workshift where machine_id = m.id) workshifts_count, "
        . "(select count(id) from roller where machine_id = m.id) rollers_count "
        . "from machine m "
        . "left join role r on m.role_id = r.id "
        . "where m.id=$id";
$row = (new Fetcher($sql))->Fetch();
$name = $row['name'];
$position = $row['position'];
$user1_name = $row['user1_name'];
$user2_name = $row['user2_name'];
$role_id = $row['role_id'];
$has_organization = $row['has_organization'];
$has_edition = $row['has_edition'];
$has_material = $row['has_material'];
$has_thickness = $row['has_thickness'];
$has_width = $row['has_width'];
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
$workshifts_count = $row['workshifts_count'];
$rollers_count = $row['rollers_count'];
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
                            <p>Отработанных смен: <?=$workshifts_count ?>. Валов: <?=$rollers_count ?>.</p>
                        </div>
                        <div class="p-1">
                            <div class="btn-group">
                                <a href="<?=APPLICATION ?>/machine/" class="btn btn-outline-dark"><i class="fas fa-undo-alt"></i>&nbsp;К списку</a>
                                <a href="<?=APPLICATION ?>/machine/edit.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-edit"></i>&nbsp;Редактировать</a>
                                <?php if($rollers_count == 0 && $workshifts_count == 0): ?>
                                <a href="<?=APPLICATION ?>/machine/delete.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-trash-alt"></i>&nbsp;Удалить</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <table class="table table-hover">
                        <tr><th>Позиция</th><td><?=$position ?></td></tr>
                        <tr><th>Пользователь 1</th><td><?=$user1_name ?></td></tr>
                        <tr><th>Пользователь 2</th><td><?=$user2_name ?></td></tr>
                        <tr><th>Роль</th><td><?=$role ?></td></tr>
                        <tr><th>Есть организация</th><td><?=$has_organization == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть тираж</th><td><?=$has_edition == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть материал</th><td><?=$has_material == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть толщина</th><td><?=$has_thickness == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть ширина</th><td><?=$has_width == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
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
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <th></th>
                                <th class="w-25">Наименование</th>
                                <th class="w-25">Позиция</th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php
                            $sql = "select r.id, r.name, r.position, (select count(id) from edition where roller_id = r.id) editions_count "
                                    . "from roller r where r.machine_id=$id order by r.position, r.name";
                            $grabber = new Grabber($sql);
                            $rollers = $grabber->result;
                            $roller_num = 0;
                            
                            foreach($rollers as $roller):
                            ?>
                            <tr>
                                <td><?=(++$roller_num) ?></td>
                                <td><?=$roller['name'] ?></td>
                                <td><?=$roller['position'] ?></td>
                                <td class='text-right'>
                                    <a class='btn btn-outline-dark' title='Редактировать' href='edit_roller.php?id=<?=$roller['id'] ?>'><i class='fas fa-edit'></i>&nbsp;Редактировать</a>
                                </td>
                                <td class='text-right'>
                                    <?php if($roller['editions_count'] == 0): ?>
                                    <form method='post'><input type='hidden' id='roller_id' name='roller_id' value='<?=$roller['id'] ?>' />
                                        <button type='submit' class='btn btn-outline-dark confirmable' id='delete_roller_submit' name='delete_roller_submit'><i class='fas fa-trash-alt'></i>&nbsp;Удалить</button>
                                    </form>
                                    <?php else: ?>
                                    <p><?=$roller['editions_count'] ?> тир.</p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
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