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
$row = (new Fetcher("select name from machine where id=$id"))->Fetch();
$name = $row['name'];
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
                        <tr>
                            <th>Наименование</th>
                            <td><?=$name ?></td>
                        </tr>
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