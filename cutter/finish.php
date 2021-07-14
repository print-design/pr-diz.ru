<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
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
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start"></nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1 class="text-center">Заявка закрыта</h1>
            <p class="text-center" style="font-size: x-large; color: green;">Молодец:)</p>
        </div>
    </body>
</html>