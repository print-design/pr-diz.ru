<?php
require_once './_plan_timetable.php';

$date_from = null;
$date_to = null;
GetDateFromDateTo(filter_input(INPUT_GET, 'from'), null, $date_from, $date_to);
$machine_id = filter_input(INPUT_GET, 'machine_id');

$timetable = new PlanTimetable($machine_id, $date_from, $date_to);
$timetable->Show();
?>