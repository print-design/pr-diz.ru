<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ACCOUNTANT]))) {
    include '../include/_unauthorized.php';
}

// Чтобы заголовок не выдавал ошибку
$paying = false;
$paid = false;
$production = false;
$status_id = null;
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include 'header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Поступления</h1>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>