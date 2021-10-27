<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Открыть можно только через кнопку "Составить технологическую карту"
$calculation_id = filter_input(INPUT_POST, 'calculation_id');

if(empty($calculation_id)) {
    header('Location: '.APPLICATION.'/techmap/');
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
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/calculation/calculation.php?id=<?=$calculation_id ?>">Отмена</a>
            <h1 style="font-size: 32px; font-weight: 600;">Новая технологическая карта</h1>
        </div>
    </body>
</html>