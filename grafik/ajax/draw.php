<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
include '../include/GrafikTimetable.php';

$date_from = null;
$date_to = null;
GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);
$machineId = filter_input(INPUT_GET, 'machine_id');

$timetable = new GrafikTimetable($date_from, $date_to, $machineId);
$timetable->Show();
?>