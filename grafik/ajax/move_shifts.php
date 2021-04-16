<?php
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$shift_from = filter_input(INPUT_GET, 'shift_from');
$to = filter_input(INPUT_GET, 'to');
$shift_to = filter_input(INPUT_GET, 'shift_to');
$days = filter_input(INPUT_GET, 'days');
$half = filter_input(INPUT_GET, 'half');

print_r($_GET);

//echo $machine_id.' -- '.$from.' -- '.$shift_from.' -- '.$to.' -- '.$shift_to.' -- '.$days.' -- '.$half;
?>