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
$row = (new Fetcher("select name from machine where id=$id"))->Fetch();
$name = htmlentities($row['name']);
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