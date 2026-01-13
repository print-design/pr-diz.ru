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
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>