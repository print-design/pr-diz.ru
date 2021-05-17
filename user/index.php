<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'delete_user_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from user where id=$id"))->error;
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
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <?php
                    include '../include/subheader_admin.php';
                    ?>
                </div>
                <div class="p-1">
                    <a href="create.php" title="Добавить пользователя" class="btn btn-outline-dark">
                        <i class="fas fa-plus" style="font-size: 12px;"></i>&nbsp;&nbsp;Добавить сотрудника
                    </a>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ФИО</th>
                        <th>Должность</th>
                        <th>Логин</th>
                        <th>E-Mail</th>
                        <th>Телефон</th>
                        <!--th></th-->
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select u.id, u.first_name, u.last_name, r.local_name role, u.username, u.email, u.phone "
                            . "from user u inner join role r on u.role_id = r.id "
                            . "order by u.first_name asc";
                    $fetcher = new Fetcher($sql);
                    $error_message = $fetcher->error;
                    
                    while ($row = $fetcher->Fetch()) {
                        echo "<tr>"
                                ."<td>".$row['first_name'].' '.$row['last_name']."</td>"
                                ."<td>".$row['role']."</td>"
                                ."<td>".$row['username']."</td>"
                                ."<td>".$row['email']."</td>"
                                ."<td>".$row['phone']."</td>";
                        /*echo "<td class='text-right'>";
                        if(filter_input(INPUT_COOKIE, USER_ID) != $row['id']) {
                            echo "<a href='".APPLICATION."/user/edit.php?id=".$row['id']."'><i class='fas fa-pencil-alt'></i></a>";
                        }
                        echo '</td>';*/
                        echo "<td class='text-right'>";
                        if(filter_input(INPUT_COOKIE, USER_ID) != $row['id']) {
                            echo "<form method='post'>";
                            echo "<input type='hidden' id='id' name='id' value='".$row['id']."' />";
                            echo "<button type='submit' class='btn btn-link confirmable' id='delete_user_submit' name='delete_user_submit'><img src='../images/icons/trash.svg' /></button>";
                            echo '</form>';
                        }
                        echo '</td>';
                        echo "</tr>";
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