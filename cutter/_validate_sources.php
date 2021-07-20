<?php
include '../include/topscripts.php';

$cut_id = filter_input(INPUT_GET, 'cut_id');
$result = array();

for($i=1; $i<=19; $i++) {
    if(!empty(filter_input(INPUT_GET, 'source_'.$i))) {
        $result['source_'.$i] = filter_input(INPUT_GET, 'source_'.$i);
    }
}

echo json_encode($result);
?>