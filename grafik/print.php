<?php
include 'include/topscripts.php';
include 'include/GrafikTimetable.php';

$error_message = '';

if(null !== filter_input(INPUT_POST, 'print_submit')) {
    $date_from = null;
    $date_to = null;
    $diff3Days = new DateInterval('P3D');
    $machine_id = filter_input(INPUT_POST, 'machine');
    $from = filter_input(INPUT_POST, 'from');
    
    if($from != null) {
        $date_from = DateTime::createFromFormat("Y-m-d", $from);
    }
    else {
        $date_from = new DateTime();
    }
    
    $date_to = clone $date_from;
    $date_to->add($diff3Days);
    
    $timetable = new GrafikTimetable($date_from, $date_to, $machine_id);
}
else {
    $error_message = 'Для печати нажмите кнопку &nbsp;Печать&nbsp; в верхней правой части графика';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>График, печать</title>
        <?php
        include 'include/head.php';
        ?>
    </head>
    <body class="print">
        <div class="container-fluid">
            <?php
            if(isset($error_message) && $error_message != '') {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            $timetable->Print();
            ?>
        </div>
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
    </body>
</html>