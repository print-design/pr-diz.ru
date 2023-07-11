<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$name_valid = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['organization_edit_submit'])) {
    if($_POST['name'] == '') {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($form_valid) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if($conn->connect_error) {
            die('Ошибка соединения: '.$conn->connect_error);
        }
                
        $id = $_POST['id'];
        $name = addslashes($_POST['name']);
        $production = addslashes($_POST['production']);
        $address = addslashes($_POST['address']);
                
        $sql = "update organization set name='$name', production='$production', address='$address' where id=$id";
                
        $conn->query('set names utf8');
        if ($conn->query($sql) === true) {
            header('Location: '.APPLICATION.'/organization/details.php?id='.$id);
        }
        else {
            $error_message = $conn->error;
        }
                
        $conn->close();
    }
}
        
// Если нет параметра id, переход к списку
if(!isset($_GET['id'])) {
    header('Location: '.APPLICATION.'/organization/');
}
        
// Получение объекта
$date = '';
$name = '';
$production = '';
$address = '';
$email = '';
        
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
$sql = "select date, name, production, address 
    from organization where id=".$_GET['id'];
        
if($conn->connect_error) {
    die('Ошибка соединения: ' . $conn->connect_error);
}
        
$conn->query('set names utf8');
$result = $conn->query($sql);
if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
    $date = $row['date'];
    $name = $row['name'];
    $production = $row['production'];
    $address = $row['address'];
}
$conn->close();
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
                            <h1>Редактирование предприятия</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/organization/details.php?id=<?=$_GET['id'] ?>" class="btn btn-outline-dark"><span class="font-awesome">&#xf0e2;</span>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr />
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$_GET['id'] ?>"/>
                        <div class="form-group">
                            <label for="name">Наименование</label>
                            <input type="text" id="name" name="name" class="form-control<?=$name_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) ? htmlentities($_POST['name']) : htmlentities($name) ?>" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Наименование обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="production">Продукция</label>
                            <textarea rows="5" id="production" name="production" class="form-control"><?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['production']) ? htmlentities($_POST['production']) : htmlentities($production) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="address">Адрес</label>
                            <textarea rows="5" id="address" name="address" class="form-control"><?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['address']) ? htmlentities($_POST['address']) : htmlentities($address) ?></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="organization_edit_submit" name="organization_edit_submit">Сохранить</button>
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