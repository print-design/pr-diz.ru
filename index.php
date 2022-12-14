<?php
include 'include/topscripts.php';

// Карщика и ревизора перенаправляем в раздел car
if(IsInRole(array('electrocarist', 'auditor'))) {
    header('Location: '.APPLICATION.'/car/');
}

// Резчика по раскрою перенаправляем в раздел cut
if(IsInRole(array('cutter'))) {
    header('Location: '.APPLICATION.'/cutter/');
}

// Маркиратора перенаправляем в раздел marker
if(IsInRole(array('marker'))) {
    header('Location: '.APPLICATION.'/marker/');
}
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <?php
        include 'include/head.php';
        ?>
        <style>
            #topmost {
                height: 85px;
            }
        </style>
    </head>
    <body>
        <?php
        include 'include/header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger mt-3'>$error_message</div>";
            }
            ?>
            <h1>Принт-Дизайн</h1>
            <h2>Управление ресурсами предприятия</h2>
        </div>
        <?php
        include 'include/footer.php';
        ?>
    </body>
</html>