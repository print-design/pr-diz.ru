<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_MANAGER_SENIOR], ROLE_NAMES[ROLE_TECHNOLOGIST]))) {
    header('Location: create.php');
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
                <div>
                    <h1>Предложения по улучшению</h1>
                </div>
                <div>
                    <a href="create.php" class="btn btn-dark">Новое предложение</a>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Сотрудник</th>
                        <th>Предложение</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select id, DATE_FORMAT(timestamp, '%d.%m.%Y') date, employee, title "
                            . "from improvement "
                            . "order by id desc limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    while ($row = $fetcher->Fetch()):
                    ?>
                    <tr>
                        <td><?=$row['date'] ?></td>
                        <td><?=$row['employee'] ?></td>
                        <td><?=$row['title'] ?></td>
                        <td></td>
                        <td>
                            <a href="details.php?id=<?=$row['id'] ?>"><img src="<?=APPLICATION ?>/images/icons/vertical-dots.svg" /></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php include '../include/pager_bottom.php'; ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>