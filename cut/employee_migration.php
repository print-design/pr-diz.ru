<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
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
            <h1>Миграция резчиков</h1>
            <?php
            $take_streams_count = 0;
            $subscribed = 0;
            $sql = "select count(id) from calculation_take_stream";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $take_streams_count = $row[0];
            }
            $sql = "select count(id) from calculation_take_stream where plan_employee_id is not null";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $subscribed = $row[0];
            }
            ?>
            <p>Всего: <?=$take_streams_count ?></p>
            <p>Подписанных: <?=$subscribed ?></p>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>