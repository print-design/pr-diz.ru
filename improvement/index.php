<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(ROLE_NAMES[ROLE_MANAGER_SENIOR]) && !IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD]))) {
    header('Location: create.php');
}
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table.typography {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 15px;
                color: #191919;
            }
            
            table.typography tr th {
                color: #68676C;
                border-top: 0;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header.php';
        include '../include/pager_top.php';
        $rowcounter = 0;
        
        $sql = "select count(id) from improvement";
        $fetcher = new Fetcher($sql);
        
        if($row = $fetcher->Fetch()) {
            $pager_total_count = $row[0];
        }
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <h1>Предложения по улучшению</h1>
                </div>
                <div class="p-1">
                    <a href="create.php" class="btn btn-dark">Новое предложение</a>
                </div>
            </div>
            <table class="table table-hover typography">
                <tr>
                    <th>Дата</th>
                    <th>Сотрудник</th>
                    <th>Коротко</th>
                    <th>Подробно</th>
                </tr>
                <?php
                $sql = "select id, DATE_FORMAT(timestamp, '%d.%m.%Y') date, employee, title, body "
                        . "from improvement "
                        . "order by id desc limit $pager_skip, $pager_take";
                $fetcher = new Fetcher($sql);
                while ($row = $fetcher->Fetch()):
                ?>
                <tr>
                    <td><?=$row['date'] ?></td>
                    <td><?= htmlentities($row['employee']) ?></td>
                    <td><?=$row['title'] ?></td>
                    <td><?=$row['body'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php include '../include/pager_bottom.php'; ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>