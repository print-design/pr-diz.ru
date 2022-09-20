<?php
include '../include/topscripts.php';
include '../calculation/status_ids.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан calculation_id, направляем к списку технических карт
if(null === filter_input(INPUT_GET, 'calculation_id')) {
    header('Location: '.APPLICATION.'/techmap/');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

// Создание технологической карты
if(null !== filter_input(INPUT_POST, 'techmap_submit')) {
    if(empty(filter_input(INPUT_GET, 'calculation_id'))) {
        $error_message == "Не указан ID расчёта";
        $form_valid = false;
    }
    
    if($form_valid) {
        $calculation_id = filter_input(INPUT_GET, 'calculation_id');
        
        $sql = "insert into techmap (calculation_id) values($calculation_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $id = $executer->insert_id;
        
        if(empty($error_message)) {
            $sql = "update calculation set status_id = ".TECHMAP." where id = $calculation_id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message) && !empty($id)) {
            header("Location: details.php?id=$id");
        }
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
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/calculation/details.php?id=<?= filter_input(INPUT_GET, 'calculation_id') ?>">К расчету</a>
            <h1>Создание технологической карты (заглушка)</h1>
            <form method="post">
                <input type="hidden" name="calculation_id" value="<?= filter_input(INPUT_GET, 'calculation_id') ?>" />
                <button type="submit" name="techmap_submit" class="btn btn-outline-dark draft mt-3" style="width: 200px;">Сохранить</button>
            </form>
        </div> 
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>