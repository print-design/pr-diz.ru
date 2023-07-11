<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';
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
               echo "<div class='alert alert-danger;>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-2">
                <div class="p-1">
                    <h1>Менеджеры</h1>
                </div>
                <div class="p-1">
                    <a href="create.php" title="Добавить пользователя" class="btn btn-outline-dark mr-sm-2">
                        <span class="font-awesome">&#xf067;</span>&nbsp;Добавить
                    </a>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Зарегистрирован</th>
                        <th>Логин</th>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Роли</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                    $sql = "select m.id, date_format(m.date, '%d.%m.%Y') date, m.username, m.last_name, m.first_name, m.middle_name, "
                            . "(SELECT GROUP_CONCAT(DISTINCT r.local_name SEPARATOR ', ') FROM role r inner join manager_role mr on mr.role_id = r.id where mr.manager_id = m.id) roles "
                            . "from manager m order by m.date desc";
                            
                    if($conn->connect_error) {
                        die('Ошибка соединения: ' . $conn->connect_error);
                    }
                    
                    $conn->query('set names utf8');
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>"
                                    ."<td>".$row['date']."</td>"
                                    ."<td><a href='".APPLICATION."/manager/details.php?id=".$row['id']."'>".$row['username']."</a></td>"
                                    ."<td>".$row['last_name']."</td>"
                                    ."<td>".$row['first_name']."</td>"
                                    ."<td>".$row['middle_name']."</td>"
                                    ."<td>".$row['roles']."</td>"
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