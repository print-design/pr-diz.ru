<?php
include '../include/topscripts.php';
include './_plan_timetable.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_PACKER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');

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
    
    header("Location: ?work_id=$work_id&machine_id=$machine_id".(empty($from) ? "" : "&from=$from"));
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
                font-size: 16px;
            }
            
            table.typography, table.typography tbody, table.typography tbody tr, table.typography tbody tr td {
                page-break-before: avoid;
            }
            
            table.typography tbody tr td.top {
                border-top: solid 2px darkgray;
            }
            
            table.typography tbody tr td.top {
                border-top: solid 2px darkgray;
            }
            
            <?php if($work_id == WORK_CUTTING): ?>
            .cutting_hidden {
                display: none;
            }
            <?php elseif($work_id == WORK_LAMINATION): ?>
            .lamination_hidden {
                display: none;
            }
            <?php endif; ?>
            
            <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))): ?>
            .comment_invisible {
                display: none;
            }
            .notforedit {
                display: none;
            }
            <?php else: ?>
            th.fordrag, td.fordrag {
                display: none;
            }
            .foredit {
                display: none;
            }
            <?php endif; ?>
            
            <?php if(IsInRole(ROLE_NAMES[ROLE_STOREKEEPER])): ?>
            .storekeeper_hidden {
                display: none;
            }
            <?php else: ?>
            .not_storekeeper_hidden {
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
        GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);
        $datediff = date_diff($date_from, $date_to, true);
        
        if($datediff->days > 3) {
            $diff3Days = new DateInterval('P3D');
            $date_to = clone $date_from;
            $date_to->add($diff3Days);
        }
                        
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