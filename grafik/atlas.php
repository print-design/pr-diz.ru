<?php
include '../include/topscripts.php';
include '../include/grafik_readonly.php';

// Авторизация
if(!IsInRole(array('technologist', 'storekeeper', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

$date_from = null;
$date_to = null;
GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);

$grafik = new GrafikReadonly($date_from, $date_to, 5);
$grafik->user1Name = "Печатник";
$grafik->userRole = 9;

$grafik->hasEdition = true;
$grafik->hasOrganization = true;
$grafik->hasLength = true;
$grafik->hasStatus = true;
$grafik->hasRoller = true;
$grafik->hasLamination = true;
$grafik->hasColoring = true;
$grafik->coloring = 6;
$grafik->hasManager = true;

$error_message = $grafik->error_message;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>График - Атлас</title>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_grafik.php';
        ?>
        <div style="position: fixed; top: 100px; left: 100px; z-index: 1000;" id="waiting"></div>
        <div class="container-fluid" id="maincontent">
            <?php
            if(isset($error_message) && $error_message != '') {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            $grafik->ShowPage();
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>