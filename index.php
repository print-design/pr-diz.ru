<?php
include 'include/topscripts.php';
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
    </head>
    <body>
        <?php
        // put your code here
        include 'include/header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Принт-дизайн</h1>
            <h2>График печати</h2>
        </div>
        <?php
        include 'include/footer.php';
        ?>
    </body>
</html>
