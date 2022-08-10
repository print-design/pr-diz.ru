<?php
$source_width = filter_input(INPUT_GET, 'source_width');
$cut_length = filter_input(INPUT_GET, 'cut_length');
echo json_encode(array("source_width" => $source_width));
?>