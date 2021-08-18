<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

include_once '_redirects.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '../include/style_mobile.php';
        ?>
        <script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
        <script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script src="<?=APPLICATION ?>/js/popper.min.js"></script>
        <script src="<?=APPLICATION ?>/js/jquery.maskedinput.js"></script>
        <script src="<?=APPLICATION ?>/js/calculation.js?version=100"></script>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-between">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="">Склад</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" id="logout-submit" href="logout.php"><i class="fa fa-cog" aria-hidden="true""></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-dark w-100 mt-4" href="material.php">Приступить к раскрою</a>
        </div>
        <?php
        include '_footer.php';
        ?>
    </body>
</html>