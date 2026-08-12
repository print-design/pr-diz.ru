<?php
require_once './_queue.php';

$work_id = filter_input(INPUT_GET, 'work_id', FILTER_VALIDATE_INT);
$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);

$queue = new Queue($work_id, $machine_id);
$queue->Show();
?>