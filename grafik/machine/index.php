<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole('admin')) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            body {
                padding-left: 0;
            }
        </style>
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
                    <h1>Машины</h1>
                </div>
                <div class="p-1">
                    <a href="create.php" title="Добавить машину" class="btn btn-outline-dark mr-sm-2">
                        <i class="fas fa-plus"></i>&nbsp;Добавить
                    </a>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Наименование</th>
                        <th>Позиция</th>
                        <th>Пользователь 1</th>
                        <th>Пользователь 2</th>
                        <th>Роль</th>
                        <th>Заказчик</th>
                        <th>Наименование</th>
                        <th>Длина</th>
                        <th>Статус</th>
                        <th>Вал</th>
                        <th>Ламинация</th>
                        <th>Красочность</th>
                        <th>Менеджер</th>
                        <th>Комментарий</th>
                        <th>Резка</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select m.id, m.name, m.position, m.user1_name, m.user2_name, m.role_id, m.has_organization, m.has_edition, m.has_length, m.has_status, m.has_roller, m.has_lamination, m.has_coloring, m.coloring, m.has_manager, m.has_comment, m.is_cutter, r.local_name role "
                            . "from machine m "
                            . "left join role r on m.role_id = r.id "
                            . "order by m.position asc";
                    $machines = (new Grabber($sql))->result;
                    foreach ($machines as $row):
                    ?>
                    <tr>
                        <td><a href='<?= APPLICATION."/machine/details.php?id=".$row['id'] ?>'><?=$row['name'] ?></a></td>
                        <td><?=$row['position'] ?></td>
                        <td><?=$row['user1_name'] ?></td>
                        <td><?=$row['user2_name'] ?></td>
                        <td><?=$row['role'] ?></td>
                        <td><?=$row['has_organization'] == true ? '<i class="fas fa-check"></i>' : '' ?></td>
                        <td><?=$row['has_edition'] == true ? '<i class="fas fa-check"></i>' : '' ?></td>
                        <td><?=$row['has_length'] == true ? '<i class="fas fa-check"></i>' : '' ?></td>
                        <td><?=$row['has_status'] == true ? '<i class="fas fa-check"></i>' : '' ?></td>
                        <td><?=$row['has_roller'] == true ? '<i class="fas fa-check"></i>' : '' ?></td>
                        <td><?=$row['has_lamination'] == true ? '<i class="fas fa-check"></i>' : '' ?></td>
                        <td><?=$row['has_coloring'] == true ? '<i class="fas fa-check"></i> ('.$row['coloring'].')' : '' ?></td>
                        <td><?=$row['has_manager'] == true ? '<i class="fas fa-check"></i>' : '' ?></td>
                        <td><?=$row['has_comment'] == true ? '<i class="fas fa-check"></i>' : '' ?></td>
                        <td><?=$row['is_cutter'] == true ? '<i class="fas fa-check"></i>' : '' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>