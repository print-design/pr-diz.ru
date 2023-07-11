<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
        
// Получение личных данных
$row = (new Fetcher("select fio, username from user where id=".GetUserId()))->Fetch();
$fio = $row['fio'];
$username = $row['username'];
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
               
            $password = filter_input(INPUT_GET, 'password');
            
            if($password == 'true') {
                echo "<div class='alert alert-info'>Пароль успешно изменён</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between">
                        <div class="p-1">
                            <h1>Мои настройки</h1>
                        </div>
                        <div class="p-1">
                            <div class="btn-group">
                                <a href="<?=APPLICATION ?>/personal/edit.php" class="btn btn-outline-dark"><i class="fas fa-edit"></i>&nbsp;Редактировать</a>
                                <a href="<?=APPLICATION ?>/personal/password.php" class="btn btn-outline-dark"><i class="fas fa-key"></i>&nbsp;Сменить пароль</a>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <table class="table table-bordered">
                        <tr>
                            <th>ФИО</th>
                            <td><?=$fio ?></td>
                        </tr>
                        <tr>
                            <th>Логин</th>
                            <td><?=$username ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>