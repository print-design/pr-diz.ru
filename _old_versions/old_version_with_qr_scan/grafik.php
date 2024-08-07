<?php
include 'include/topscripts.php';
include 'include/GrafikTimetableReadonly.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан параметр id, переводим на страницу с id=1
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: ?id=1');
}

$date_from = null;
$date_to = null;
GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);

$grafik_machine = new GrafikTimetableReadonly($date_from, $date_to, filter_input(INPUT_GET, 'id'));
$error_message = $grafik_machine->error_message;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include 'include/head.php';;
        ?>
        <style>
            table.typography {
    font-size: smaller;
}

table.typography tbody tr td {
    background-color: white;
    padding-top: 15px;
    padding-bottom: 15px;
}

table.typography tbody tr td.top {
    border-top: solid 2px darkgray;
}

table.print tr td.top {
    border-top: solid 2px darkgray;
}

table.typography tbody tr td.night {
    background-color: #F2F2F2;
}
        </style>
    </head>
    <body>
        <?php
        include 'include/header_grafik.php';
        ?>
        <div style="position: fixed; top: 100px; left: 100px; z-index: 1000;" id="waiting"></div>
        <div class="container-fluid" id="maincontent">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            $grafik_machine->Show();
            ?>
        </div>
        <?php
        include 'include/footer.php';
        ?>
    </body>
</html>