<?php
include '../include/topscripts.php';
include '../include/pager_top.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
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
            <div class="d-flex justify-content-start">
                <div class="p-1">
                    <?php
                    include '../include/subheader_admin.php';
                    ?>
                </div>
            </div>
            <?php
            include '../include/subheader_history.php';
            ?>
            <hr class="pb-0 mb-0" />
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Пользователь</th>
                        <th>Дата</th>
                        <th>Предыдущая</th>
                        <th>Текущая</th>
                        <th>
                            Ошибка&nbsp;
                            <?php if(filter_input(INPUT_GET, 'error') == 1): ?>
                            <a href="cut.php"><i class="far fa-times-circle"></i></a>
                            <?php else: ?>
                            <a href="cut.php?error=1"><i class="fa fa-filter"></i></a>
                            <?php endif; ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $where = "";
                    if(filter_input(INPUT_GET, 'error') == 1) {
                        $where = " where ch.valid = 0";
                    }
                    $sql = "select count(ch.id) from cut_history ch$where";
                    $fetcher = new Fetcher($sql);
                    
                    if($row = $fetcher->Fetch()) {
                        $pager_total_count = $row[0];
                    }
                    
                    $sql = "select u.first_name, u.last_name, ch.datetime, ch.page_db, ch.page_real, ch.valid from cut_history ch inner join user u on ch.user_id = u.id$where order by ch.id desc limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    while($row = $fetcher->Fetch()) {
                        ?>
                    <tr>
                        <td><?=$row['first_name'].' '.$row['last_name'] ?></td>
                        <td class="text-nowrap"><?=$row['datetime'] ?></td>
                        <td style="word-break: break-all;"><?=$row['page_db'] ?></td>
                        <td style="word-break: break-all;"><?=$row['page_real'] ?></td>
                        <td style="background-color: <?=$row['valid'] ? "green" : "red" ?>; color: white; font-weight: bold;"><?=$row['valid'] ? "OK" : "Ошибка" ?></td>
                    </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>