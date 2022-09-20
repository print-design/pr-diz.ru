<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select calculation_id from techmap where id = $id";
$fetcher = new Fetcher($sql);
$row = $fetcher->Fetch();

$calculation_id = $row['calculation_id'];
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/calculation/details.php?id=<?= $calculation_id ?>">К расчету</a>
            <h1>Технологическая карта (заглушка)</h1>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>