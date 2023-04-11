<?php
require_once './_queue.php';

$machine = filter_input(INPUT_GET, 'machine');

$queue = new Queue($machine);
$queue->Show();
?>