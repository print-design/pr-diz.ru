<?php
include '../include/topscripts.php';
include './_plan_timetable.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR], ROLE_NAMES[ROLE_SCHEDULER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');

// Если не указаны work_id или machine_id, перенаправляем на печать/Comiflex
if(empty($work_id) || empty($machine_id)) {
    if(empty($work_id)) {
        $work_id = WORK_PRINTING;
    }
    
    if(empty($machine_id)) {
        switch ($work_id) {
            case WORK_PRINTING:
                $machine_id = PRINTER_COMIFLEX;
                break;
            case WORK_LAMINATION:
                $machine_id = LAMINATOR_SOLVENT;
                break;
            case WORK_CUTTING:
                $machine_id = CUTTER_1;
                break;
        }
    }
    
    header("Location: ?work_id=$work_id&machine_id=$machine_id");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            body {
                padding: 0;
            }
            
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
            
            /*table.typography tbody tr td.night {
                background-color: #F2F2F2;
            }*/
            
            <?php if($work_id == WORK_CUTTING): ?>
            .cutting_hidden {
                display: none;
            }
            <?php elseif($work_id == WORK_LAMINATION): ?>
            .lamination_hidden {
                display: none;
            }
            <?php endif; ?>
        </style>
    </head>
    <body>
        <h1>
            <?php
            switch ($work_id) {
                case WORK_PRINTING:
                    echo PRINTER_NAMES[$machine_id];
                    break;
                case WORK_LAMINATION:
                    echo LAMINATOR_NAMES[$machine_id];
                    break;
                case WORK_CUTTING:
                    echo CUTTER_NAMES[$machine_id];
                    break;
            }
            ?>
        </h1>
        <?php
        $date_from = null;
        $date_to = null;
        GetDateFromDateTo(filter_input(INPUT_GET, 'from'), null, $date_from, $date_to);
        $diff3Days = new DateInterval('P3D');
        $date_to = clone $date_from;
        $date_to->add($diff3Days);
                        
        $timetable = new PlanTimetable($work_id, $machine_id, $date_from, $date_to);
        $timetable->Print();
        ?>
    </body>
    <script>
            var css = '@page { size: landscape; margin: 8mm; }',
                    head = document.head || document.getElementsByTagName('head')[0],
                    style = document.createElement('style');
            
            style.type = 'text/css';
            style.media = 'print';
            
            if (style.styleSheet){
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            
            head.appendChild(style);
            
            window.print();
        </script>
</html>