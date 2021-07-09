<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';

// Обработка отправки формы
$delete_machine_submit = filter_input(INPUT_POST, 'delete_machine_submit');
if($delete_machine_submit !== null) {
    $machine_id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from machine where id=$machine_id"))->error;
        
    if($error_message == '') {
        header('Location: '.APPLICATION.'/machine/');
    }
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
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1 class="text-danger">Дествительно удалить?</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/machine/details.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-undo-alt"></i>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr><th>Наименование</th><td><?=$name ?></td></tr>
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
                        <tr><th>Красочность</th><td><?=$coloring == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть менеджер</th><td><?=$has_manager == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Есть комментарий</th><td><?=$has_comment == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                        <tr><th>Это резка?</th><td><?=$is_cutter == true ? '<i class="fas fa-check"></i>' : '' ?></td></tr>
                    </table>
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$id ?>"/>
                        <button type="submit" id="delete_machine_submit" name="delete_machine_submit" class="btn btn-outline-dark">Удалить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>