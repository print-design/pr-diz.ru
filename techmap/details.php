<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select c.name "
        . "from techmap t "
        . "inner join calculation c on t.calculation_id = c.id "
        . "where t.id = $id";
$row = (new Fetcher($sql))->Fetch();

$name = $row['name'];
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
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/techmap/<?= BuildQueryRemove("id") ?>">К списку</a>
            <h1 style="font-size: 32px; font-weight: 600;"><?= htmlentities($name) ?></h1>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>