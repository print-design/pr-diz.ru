<?php
include '../include/topscripts.php';

// Если нет параметра id, переходим к списку предпиятий
if(!isset($_GET['id'])) {
    header('Location: '.APPLICATION.'/organization/');
}
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$name_valid = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['person_edit_submit'])) {
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
        $organization_id = $_POST['organization_id'];
        $name = addslashes($_POST['name']);
        $position = addslashes($_POST['position']);
        $phone = addslashes($_POST['phone']);
        $email = addslashes($_POST['email']);
                
        $sql = "update person set organization_id=$organization_id, name='$name', position='$position', phone='$phone', email='$email' where id=".$id;
                
        $conn->query('set names utf8');
        if ($conn->query($sql) === true) {
            header('Location: '.APPLICATION.'/organization/details.php?id='.$organization_id);
        }
        else {
            $error_message = $conn->error;
        }
                
        $conn->close();
    }
}
        
// Получение контактного лица и предприятия
$name = '';
$position = '';
$phone = '';
$email = '';
$organization_id = '';
$organization_name = '';
        
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
$sql = "select p.name, p.position, p.phone, p.email, o.id organization_id, o.name organization_name 
    from person p inner join organization o on p.organization_id=o.id 
    where p.id=".$_GET['id'];
        
if($conn->connect_error) {
    die('Ошибка соединения: ' . $conn->connect_error);
}
$result = $conn->query($sql);
if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
    $name = $row['name'];
    $position = $row['position'];
    $phone = $row['phone'];
    $email = $row['email'];
    $organization_id = $row['organization_id'];
    $organization_name = $row['organization_name'];
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
            <div class="col-12 col-md-6">
                <div class="d-flex justify-content-between mb-2">
                    <div class="p-1">
                        <h1>Редактирование контактного лица для &laquo;<?=$organization_name ?>&raquo;</h1>
                    </div>
                    <div class="p-1">
                        <a href="<?=APPLICATION ?>/organization/details.php?id=<?=$organization_id ?>" class="btn btn-outline-dark"><span class="font-awesome">&#xf0e2;</span>&nbsp;Отмена</a>
                    </div>
                </div>
                <hr />
                <form method="post">
                    <input type="hidden" id="id" name="id" value="<?=$_GET['id'] ?>"/>
                    <input type="hidden" id="organization_id" name="organization_id" value="<?=$organization_id ?>" />
                    <div class="form-group">
                        <label for="name">ФИО</label>
                        <input type="text" id="number" name="name" class="form-control<?=$name_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) ? $_POST['name'] : $name ?>" required="required" autocomplete="off" />
                        <div class="invalid-feedback">ФИО обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="position">Должность (роль)</label>
                        <textarea rows="5" id="position" name="position" class="form-control"><?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['position']) ? $_POST['position'] : $position ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['phone']) ? $_POST['phone'] : $phone ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="text" id="email" name="email" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['phone']) ? $_POST['phone'] : $email ?>"/>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-outline-dark" id="person_edit_submit" name="person_edit_submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>