<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
include '../include/grafik.php';

$date_from = null;
$date_to = null;
GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);
$machineId = filter_input(INPUT_GET, 'machine_id');

$grafik = new Grafik($date_from, $date_to, $machineId);

if(in_array($machineId, [1, 2, 3, 4, 5])) {
    $grafik->user1Name = "Печатник";
}
else if(in_array($machineId, [6, 13])) {
    $grafik->user1Name = "Ламинаторщик";
}
else if(in_array($machineId, [7, 9, 10, 11, 12, 14])) {
    $grafik->user1Name = "Резчик";
}

if(in_array($machineId, [1])) {
    $grafik->user2Name = "Помощник";
}

if(in_array($machineId, [1])) {
    $grafik->userRole = 3;
}
else if(in_array($machineId, [2])) {
    $grafik->userRole = 6;
}
else if(in_array($machineId, [3])) {
    $grafik->userRole = 7;
}
else if(in_array($machineId, [4])) {
    $grafik->userRole = 8;
}
else if(in_array($machineId, [5])) {
    $grafik->userRole = 9;
}
else if(in_array($machineId, [6, 13])) {
    $grafik->userRole = 4;
}
else if(in_array($machineId, [7, 9, 10, 11, 12, 14])) {
    $grafik->userRole = 5;
}
   
if(in_array($machineId, [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14])) {
    $grafik->hasEdition = true;
}

if(in_array($machineId, [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14])) {
    $grafik->hasOrganization = true;
}

if(in_array($machineId, [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14])) {
    $grafik->hasLength = true;
}

if(in_array($machineId, [1, 2, 3, 4, 5])) {
    $grafik->hasStatus = true;
}

if(in_array($machineId, [1, 2, 3, 4, 5])) {
    $grafik->hasRoller = true;
}

if(in_array($machineId, [1, 2, 3, 4, 5, 6, 13])) {
    $grafik->hasLamination = true;
}

if(in_array($machineId, [1, 2, 3, 4, 5])) {
    $grafik->hasColoring = true;
}

if(in_array($machineId, [2, 3, 5])) {
    $grafik->coloring = 6;
}
else if(in_array($machineId, [1, 4])) {
    $grafik->coloring = 8;
}

if(in_array($machineId, [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14])) {
    $grafik->hasManager = true;
}

if(in_array($machineId, [6, 7, 9, 10, 11, 12, 13, 14])) {
    $grafik->hasComment = true;
}

if(in_array($machineId, [7, 9, 10, 11, 12, 14])) {
    $grafik->isCutter = true;
}

if(!empty($grafik->error_message)) {
    exit($grafik->error_message);
}

$grafik->ShowPage();
?>