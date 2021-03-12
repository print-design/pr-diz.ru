<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$name_valid = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['organization_create_submit'])) {
    if($_POST['name'] == '') {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($form_valid) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if($conn->connect_error) {
            die('Ошибка соединения: '.$conn->connect_error);
        }
                
        $manager_id = $_POST['manager_id'];
        $name = addslashes($_POST['name']);
        $production = addslashes($_POST['production']);
        $address = addslashes($_POST['address']);
                
        $sql = "insert into organization"
                . "(manager_id, name, production, address) "
                . "values "
                . "($manager_id, '$name', '$production', '$address')";
                
        $conn->query('set names utf8');
        if ($conn->query($sql) === true) {
            $last_id = $conn->insert_id;
            header('Location: '.APPLICATION.'/organization/details.php?id='.$last_id);
        }
        else {
            $error_message = $conn->error;
        }
                
        $conn->close();
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
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1>Новое предприятие</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/organization/" class="btn btn-outline-dark"><span class="font-awesome">&#xf0e2;</span>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr />
                    <form method="post">
                        <input type="hidden" id="manager_id" name="manager_id" value="<?= GetManagerId() ?>"/>
                        <div class="form-group">
                            <label for="name">Наименование</label>
                            <input type="text" id="name" name="name" class="form-control<?=$name_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) ? $_POST['name'] : '' ?>" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Наименование обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="production">Продукция</label>
                            <textarea rows="5" id="production" name="production" class="form-control"><?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['production']) ? $_POST['production'] : '' ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="address">Адрес</label>
                            <textarea rows="5" id="address" name="address" class="form-control"><?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['address']) ? $_POST['address'] : '' ?></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="organization_create_submit" name="organization_create_submit">Сохранить</button>
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