<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(CUTTER_USERS) && !IsInRole(ROLE_NAMES[ROLE_TECHNOLOGIST])) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            /* Таблица */
            table.typography {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 15px;
                color: #191919;
            }
            
            table.typography tr th {
                color: #68676C;
                border-top: 0;
                font-weight: bold;
            }
            
            table.typography tr td {
                background-color: white;
            }
            
            table.typography tr td.night {
                background-color: #F0F1FA;
            }
            
            thead#grafik-thead {
                background-color: lightcyan;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_cut.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_cut.php';
        ?>
    </body>
</html>