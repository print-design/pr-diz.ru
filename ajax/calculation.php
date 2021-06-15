<?php
include '../include/topscripts.php';

$error_message = '';
$id = filter_input(INPUT_GET, 'id');

$extracharge = filter_input(INPUT_GET, 'extracharge');
if($extracharge !== null) {
    $error_message = (new Executer("update calculation set extracharge=$extracharge where id=$id"))->error;
    
    if(empty($error_message)) {
        $fetcher = new Fetcher("select extracharge from calculation where id=$id");
        $row = $fetcher->Fetch();
        $error_message = $fetcher->error;
        
        if(empty($error_message)) {
            echo $row['extracharge'];
        }
    }
}

if(!empty($error_message)) {
    echo $error_message;
}
?>