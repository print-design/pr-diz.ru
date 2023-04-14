<?php
require_once './_queue.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');
$machine = filter_input(INPUT_GET, 'machine');

$queue = new Queue($machine_id, $machine);
$queue->Show();
?>