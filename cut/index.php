<?php
include '../include/topscripts.php';
include './_cut_timetable.php';

// Авторизация
if(!IsInRole(CUTTER_USERS) && !IsInRole(ROLE_NAMES[ROLE_TECHNOLOGIST])) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

$machine_id = 0;
if(IsInRole(CUTTER_USERS)) {
    $machine_id = GetUserId();
}
else {
    $machine_id = filter_input(INPUT_GET, 'machine_id');
}

if(empty($machine_id)) {
    header('Location: '.APPLICATION.'/cut/?machine_id='.CUTTER_1);
}

// Ошибки при расчётах (если есть)
if(null !== filter_input(INPUT_GET, 'error_message')) {
    $error_message = filter_input(INPUT_GET, 'error_message');
}

//*******************************************************************
// Добавление наименования ручьёв в тех резках, где они не именованы.
if($machine_id == CUTTER_SOMA || $machine_id == CUTTER_3) {
    $today = date('Y-m-d');
    $sql = "select id, name, streams_number from calculation where (id in "
            . "(select calculation_id from plan_edition where date >= '$today' and work_id = ".WORK_CUTTING." and machine_id = $machine_id) "
            . "or id in "
            . "(select calculation_id from plan_edition where id in (select plan_edition_id from plan_continuation where date >= '$today' and work_id = ".WORK_CUTTING." and machine_id = $machine_id))) "
            . "and id not in (select calculation_id from calculation_stream)";
    $grabber = new Grabber($sql);
    $slits = $grabber->result;
    foreach ($slits as $slit) {
        $sql = "select id, position, name from calculation_stream where calculation_id = ".$slit['id'];
        $grabber = new Grabber($sql);
        $result = $grabber->result;
        
        $stream_position_ids_names = array();
        
        foreach ($result as $position_id_name) {
            $stream_position_ids_names[$position_id_name['position']] = array('id' => $position_id_name['id'], 'name' => $position_id_name['name']);
        }
        
        $streams_number = intval($slit['streams_number']);
        
        for($stream_i = 1; $stream_i <= $streams_number; $stream_i++) {
            if(empty($stream_position_ids_names[$stream_i])) {
                $sql = "insert into calculation_stream (calculation_id, position, name) values (".$slit['id'].", $stream_i, 'ручей $stream_i')";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
    }
}
//*******************************************************************
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
            
            if(!empty($machine_id)):
                
            $diff5Days = new DateInterval('P5D');
            $diff3Months = new DateInterval('P3M');
            
            // Работы отображаются, начиная с дня 5 сутками ранее.
            $now = new DateTime();
            $date_from = clone $now;
            $date_from->sub($diff5Days);
            
            $start_work_date = new DateTime('2020-01-01 00:00:00');
            
            // Для резки 3 работу начинаем только с 16 мая 2024 г.
            if($machine_id == CUTTER_3) {
                $start_work_date->setDate(2024, 5, 16);
                
                if($date_from < $start_work_date) {
                    $date_from = $start_work_date;
                }
            }
            
            // Для резки ZTM1 работу начинаем с 8 июня 2024 г.
            if($machine_id == CUTTER_ZTM_1) {
                $start_work_date->setDate(2024, 7, 8);
                
                if($date_from < $start_work_date) {
                    $date_from = $start_work_date;
                }
            }
            
            // Если за рамками 5 суток присутствует работа в статусе "В плане резки", "Приладка на резке", "Режется", "Сняли с резки"
            // отображаем список, начиная с этой работы.
            $sql = "select e.date "
                    . "from plan_edition e inner join calculation c on e.calculation_id = c.id "
                    . "where (c.status_id = ".ORDER_STATUS_PLAN_CUT." or c.status_id = ".ORDER_STATUS_CUT_PRILADKA." or c.status_id = ".ORDER_STATUS_CUTTING." or c.status_id = ".ORDER_STATUS_CUT_REMOVED.") "
                    . "and e.work_id = ".WORK_CUTTING." and e.machine_id = $machine_id and "
                    . "e.date between '".$start_work_date->format('Y-m-d')."' and '".$date_from->format('Y-m-d')."' "
                    . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
                    . "order by e.date asc";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $date_from = DateTime::createFromFormat('Y-m-d', $row['date']);
            }
            
            $date_to = clone $date_from;
            $date_to->add($diff3Months);
            $timetable = new CutTimetable($machine_id, $date_from, $date_to);
            
            $count = 0;
            
            foreach($timetable->editions as $key1 => $value1) {
                foreach($value1 as $key2 => $value2) {
                    $count += count($value2);
                }
            }
            ?>
            <h1>Список работ "<?= IsInRole(CUTTER_USERS) ? filter_input(INPUT_COOKIE, ROLE_LOCAL) : (empty($machine_id) ? "" : CUTTER_NAMES[$machine_id]) ?>"&nbsp;&nbsp;<span style="font-size: smaller; color: #999999;"><?=$count ?></span></h1>
            <?php
            $timetable->Show();
            endif;
            ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_cut.php';
        ?>
    </body>
</html>