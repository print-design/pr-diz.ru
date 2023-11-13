<?php
include '../include/topscripts.php';
include './_cut_timetable.php';

// Авторизация
if(!IsInRole(CUTTER_USERS) && !IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

$machine_id = 0;
if(IsInRole(CUTTER_USERS)) {
    $machine_id = GetUserId();
}
else {
    $machine_id = filter_input(INPUT_GET, 'machine_id');
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
            <h1>Список работ <?= IsInRole(CUTTER_USERS) ? filter_input(INPUT_COOKIE, ROLE_LOCAL) : (empty($machine_id) ? "" : CUTTER_NAMES[$machine_id]) ?></h1>
            <?php
            
            
            if(!empty($machine_id)) {
                $date_from = null;
                $date_to = null;
                GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);
            
                $timetable = new CutTimetable($machine_id, $date_from, $date_to);
                $timetable->Show();
            }
            ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_cut.php';
        ?>
    </body>
</html>