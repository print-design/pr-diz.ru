<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';

// Обработка отправки формы
$delete_user_submit = filter_input(INPUT_POST, 'delete_user_submit');
if($delete_user_submit !== null) {
    $user_id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from user_role where user_id=$user_id"))->error;
    
    if($error_message == '') {
        $error_message = (new Executer("delete from user where id=$user_id"))->error;
        
        if($error_message == '') {
            header('Location: '.APPLICATION.'/user/');
        }
    }
}
        
// Если нет параметра id, переход к списку
$id = filter_input(INPUT_GET, 'id');
if($id === null) {
    header('Location: '.APPLICATION.'/user/');
}
        
// Получение объекта
$row = (new Fetcher("select username, fio, quit from user where id=$id"))->Fetch();
$username = $row['username'];
$fio = $row['fio'];
$quit = $row['quit'];
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
                            <a href="<?=APPLICATION ?>/user/details.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-undo-alt"></i>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Логин</th>
                            <td><?=$username ?></td>
                        </tr>
                        <tr>
                            <th>ФИО</th>
                            <td><?=$fio ?></td>
                        </tr>
                        <tr>
                            <th>Уволился</th>
                            <td><?=($quit == 0 ? 'Нет' : 'Да') ?></td>
                        </tr>
                    </table>
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$id ?>"/>
                        <button type="submit" id="delete_user_submit" name="delete_user_submit" class="btn btn-outline-dark">Удалить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>