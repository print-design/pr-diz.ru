<?php
include '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');

$result = array();
$result['min_weight'] = 0;
$result['min_square'] = 0;
$result['error'] = '';

$sql = "select min_weight, min_square from norm_machine where machine_id = $machine_id order by id desc limit 1";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;

if(empty($error_message)) {
    if($row = $fetcher->Fetch()) {
        $result['min_weight'] = $row['min_weight'];
        $result['min_square'] = $row['min_square'];
    }
}
else {
    $result['error'] = $error_message;
}

echo json_encode($result);
?>