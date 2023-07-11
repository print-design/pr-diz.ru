<?php
include '../include/topscripts.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include '../include/restrict_admin.php';
        
        // Валидация формы
        define('ISINVALID', ' is-invalid');
        $form_valid = true;
        $error_message = '';
        
        $name_valid = '';
        
        // Обработка отправки формы
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_lamination_submit'])) {
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
                
                $sql = "insert into lamination (name) values ('$name')";
                
                if ($conn->query($sql) === true) {
                    header('Location: '.APPLICATION.'/lamination/');
                }
                else {
                    $error_message = $conn->error;
                }
                
                $conn->close();
            }
        }
        ?>
    </head>
    <body>
        <?php
        include '../include/header.php';
        include '../include/pager_top.php';
        ?>
        <div class="container-fluid">
            <?php
            if(isset($error_message) && $error_message != '') {
               echo <<<ERROR
               <div class="alert alert-danger">$error_message</div>
               ERROR;
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1>Ламинация</h1>
                        </div>
                        <div class="p-1">
                            <form class="form-inline" method="post">
                                <div class="input-group">
                                    <input type="name" class="form-control<?=$name_valid ?>" placeholder="Наименование ламинация" id="name" name="name" required="required" />
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-dark" id="add_lamination_submit" name="add_lamination_submit">
                                            <span class="font-awesome">&#xf067;</span>&nbsp;Добавить
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <tbody>
                            <?php
                            $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                            
                            if($conn->connect_error) {
                                die('Ошибка соединения: ' . $conn->connect_error);
                            }
                            
                            $sql = "select name from lamination order by name";
                            
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>"
                                            ."<td>".$row['name']."</td>"
                                            ."</tr>";
                                }
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                    <?php
                    include '../include/pager_bottom.php';
                    ?>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>