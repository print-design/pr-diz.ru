<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
include '../include/GrafikMachine.php';

$date_from = null;
$date_to = null;
GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);
$machineId = filter_input(INPUT_GET, 'machine_id');

$machine = new GrafikMachine($date_from, $date_to, $machineId);
$machine->Show();
?>