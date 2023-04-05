<?php
include 'include/topscripts.php';
include 'include/GrafikTimetableReadonly.php';

// Авторизация
if(!IsInRole(array('technologist', 'storekeeper', 'dev', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан параметр id, переводим на начальную страницу
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION);
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
            //$grafik_machine->Show();
            ?>
        </div>
        <?php
        include 'include/footer.php';
        ?>
    </body>
</html>