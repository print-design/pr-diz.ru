<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на список
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION.'/supplier/');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$name_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'supplier_edit_submit')) {
    $name = filter_input(INPUT_POST, 'name');
    if(empty($name)) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $name = addslashes($name);    
        $error_message = (new Executer("update supplier set name='$name' where id=".filter_input(INPUT_POST, 'id')))->error;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION."/supplier/details.php?id=".filter_input(INPUT_POST, 'id'));
        }
    }
}

// Получение объекта
$row = (new Fetcher("select name from supplier where id=". filter_input(INPUT_GET, 'id')))->Fetch();

$name = filter_input(INPUT_POST, 'name');
if(empty($name)) {
    $name = htmlentities($row['name']);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/jquery-ui.css" rel="stylesheet"/>
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid form-page">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-2 nav2">
                <div class="p-1 row">
                    <?php
                    include '../include/subheader_admin.php';
                    ?>
                </div>
                <div class="p-1"></div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="backlink">
                        <a href="<?=APPLICATION ?>/supplier/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </div>
                    <h1>Редактирование поставщика</h1>
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>"/>
                        <div class="form-group">
                            <label for="name">Название поставщика</label>
                            <input type="text" id="name" name="name" class="form-control<?=$name_valid ?>" value="<?= $name ?>" required="required"/>
                            <div class="invalid-feedback">Название поставщика обязательно</div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-dark" id="supplier_edit_submit" name="supplier_edit_submit">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
    </body>
</html>