<?php
require_once './_queue.php';

$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');

$queue = new Queue($work_id, $machine_id);
$queue->Show();
?>