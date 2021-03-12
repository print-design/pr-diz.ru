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
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-2">
                <div class="p-1">
                    <h1>Пользователи</h1>
                </div>
                <div class="p-1">
                    <a href="create.php" title="Добавить пользователя" class="btn btn-outline-dark mr-sm-2">
                        <i class="fas fa-plus"></i>&nbsp;Добавить
                    </a>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Зарегистрирован</th>
                        <th>Логин</th>
                        <th>ФИО</th>
                        <th>Роли</th>
                        <th>Уволился</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select u.id, date_format(u.date, '%d.%m.%Y') date, u.username, u.fio, u.quit, "
                            . "(SELECT GROUP_CONCAT(DISTINCT r.local_name SEPARATOR ', ') FROM role r inner join user_role ur on ur.role_id = r.id where ur.user_id = u.id) roles "
                            . "from user u order by u.fio asc";
                    $fetcher = new Fetcher($sql);
                    $error_message = $fetcher->error;
                    
                    while ($row = $fetcher->Fetch()) {
                        echo "<tr>"
                                . "<td>".$row['date']."</td>"
                                ."<td><a href='".APPLICATION."/user/details.php?id=".$row['id']."'>".$row['username']."</a></td>"
                                ."<td>".$row['fio']."</td>"
                                ."<td>".$row['roles']."</td>"
                                ."<td>".($row['quit'] == 0 ? '' : '<i class="fas fa-check"></i>')."</td>"
                                ."</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>