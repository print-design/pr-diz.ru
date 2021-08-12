<?php
include 'include/topscripts.php';
include 'include/grafik.php';

$error_message = '';
$print_submit = filter_input(INPUT_POST, 'print_submit');

if($print_submit !== null) {
    $date_from = null;
    $date_to = null;
    $diff3Days = new DateInterval('P3D');
    $machine = filter_input(INPUT_POST, 'machine');
    $from = filter_input(INPUT_POST, 'from');
    
    if($from != null) {
        $date_from = DateTime::createFromFormat("Y-m-d", $from);
    }
    else {
        $date_from = new DateTime();
    }
    
    $date_to = clone $date_from;
    $date_to->add($diff3Days);
    $grafik = new Grafik($date_from, $date_to, $machine);
    
    $grafik->name = filter_input(INPUT_POST, 'name');
    $grafik->user1Name = filter_input(INPUT_POST, 'user1Name');
    $grafik->user2Name = filter_input(INPUT_POST, 'user2Name');
    $grafik->userRole = filter_input(INPUT_POST, 'userRole');
    $grafik->hasEdition = filter_input(INPUT_POST, 'hasEdition');
    $grafik->hasOrganization = filter_input(INPUT_POST, 'hasOrganization');
    $grafik->hasLength = filter_input(INPUT_POST, 'hasLength');
    $grafik->hasRoller = filter_input(INPUT_POST, 'hasRoller');
    $grafik->hasLamination = filter_input(INPUT_POST, 'hasLamination');
    $grafik->hasColoring = filter_input(INPUT_POST, 'hasColoring');
    $grafik->hasManager = filter_input(INPUT_POST, 'hasManager');
    $grafik->hasComment = filter_input(INPUT_POST, 'hasComment');
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
            if(isset($grafik)) {
                $grafik->Print();
            }
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