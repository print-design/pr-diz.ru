<?php
$calculation_id = filter_input(INPUT_GET, 'calculation_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$before = filter_input(INPUT_GET, 'before');
$error = '';

echo json_encode(array('machine_id' => $machine_id, 'from' => $from, 'error' => $error));
?>