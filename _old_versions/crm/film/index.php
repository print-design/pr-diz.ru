<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$first_name_valid = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['film_type_submit'])) {
    if($_POST['name'] == '') {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($form_valid) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if($conn->connect_error) {
            die('Ошибка соединения: '.$conn->connect_error);
        }
                
        $name = addslashes($_POST['name']);
                
        $sql = "insert into film (name) values ('$name')";
                
        $conn->query('set names utf8');
        if(!$conn->query($sql) === true) {
            $error_message = $conn->error;
        }
                
        $conn->close();
    }
}
        
// Удаление типа пленки
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_film_submit'])) {
    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
            
    if($conn->connect_error) {
        die('Ошибка соединения: '.$conn->connect_error);
    }
            
    $id = $_POST['id'];
    $sql = "delete from film where id=$id";
            
    $conn->query('set names utf8');
    if (!$conn->query($sql) === true) {
        $error_message = $conn->error;
    }
            
    $conn->close();
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
               echo <<<ERROR
               <div class="alert alert-danger">$error_message</div>
               ERROR;
            }
            ?>
            <div class="d-flex justify-content-between mb-2">
                <div class="p-1">
                    <h1>Типы плёнки</h1>
                </div>
                <div class="p-1">
                    <form class="form-inline" method="post">
                        <div class="input-group">
                            <input type="text" id="name" name="name" placeholder="Наименование" class="form-control" required="required"/>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-dark" id="film_type_submit" name="film_type_submit"><span class="font-awesome">&#xf067;</span>&nbsp;Добавить</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Наименование</th>
                        <th style="width: 80px;"></th>
                        <th style="width: 80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                    $sql = "select id, name from film order by name";
                            
                    if($conn->connect_error) {
                        die('Ошибка соединения: ' . $conn->connect_error);
                    }
                    
                    $conn->query('set names utf8');
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>"
                                    ."<td>".$row['name']."</td>"
                                    ."<td><a href='".APPLICATION."/film/edit.php?id=".$row['id']."' class='btn btn-outline-dark'><span class='font-awesome'>&#xf044;</span></a></td>"
                                    ."<td>"
                                    ."<form method='post'>"
                                    ."<input type='hidden' id='id' name='id' value='".$row['id']."' />"
                                    ."<button type='submit' id='delete_film_submit' name='delete_film_submit' class='btn btn-outline-dark' onclick='javascript: return confirm(\"Действительно удалить?\");'><span class='font-awesome'>&#xf1f8;</span></button>"
                                    ."</form>"
                                    ."</td>"
                                    ."</tr>";
                        }
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>