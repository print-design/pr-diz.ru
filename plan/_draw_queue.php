<?php
require_once './_queue.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');

$queue = new Queue($machine_id);
$queue->Show();
?>