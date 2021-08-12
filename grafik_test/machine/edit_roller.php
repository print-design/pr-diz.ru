<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$name_valid = '';
        
// Обработка отправки формы
$roller_edit_submit = filter_input(INPUT_POST, 'roller_edit_submit');
if($roller_edit_submit !== null) {
    $name = filter_input(INPUT_POST, 'name');
    if($name == '') {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $id = filter_input(INPUT_POST, 'id');
        $name = addslashes($name);
        $position = filter_input(INPUT_POST, 'position');
        if($position == '') {
            $position = 0;
        }
        $machine_id = filter_input(INPUT_POST, 'machine_id');
        $error_message = (new Executer("update roller set name='$name', position='$position' where id=$id"))->error;
                
        if($error_message == '') {
            header('Location: '.APPLICATION.'/machine/details.php?id='.$machine_id);
        }
    }
}
        

// Если нет параметра id, переход к списку
if(!isset($_GET['id'])) {
    header('Location: '.APPLICATION.'/machine/');
}
        
// Получение объекта
$id = filter_input(INPUT_GET, 'id');
$row = (new Fetcher("select name, position, machine_id from roller where id=$id"))->Fetch();
$name = htmlentities($row['name']);
$position = $row['position'];
$machine_id = $row['machine_id'];
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
                            <h1>Редактирования вала</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/machine/details.php?id=<?=$machine_id ?>" class="btn btn-outline-dark"><i class="fas fa-undo-alt"></i>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr />
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$_GET['id'] ?>"/>
                        <input type="hidden" id="machine_id" name="machine_id" value="<?=$machine_id ?>"/>
                        <div class="form-group">
                            <label for="name">Наименование</label>
                            <input type="text" id="name" name="name" class="form-control<?=$name_valid ?>" value="<?=$name ?>" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Наименование обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="position">Позиция</label>
                            <input type="number" step="0.001" id="position" name="position" class="form-control" value="<?=$position ?>" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="roller_edit_submit" name="roller_edit_submit">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>