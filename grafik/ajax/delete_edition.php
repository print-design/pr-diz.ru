<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');

// Определяем смену
$sql = "select workshift_id from edition where id = $id";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;

if($row = $fetcher->Fetch()) {
    $workshift_id = $row[0];
    
    $sql = "delete from edition where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $count = (new Fetcher("select count(id) from edition where workshift_id = $workshift_id"))->Fetch()[0];
        
        if($count == 0) {
            $row = (new Fetcher("select user1_id, user2_id from workshift where id = $workshift_id"))->Fetch();
            
            if(empty($row[0]) && empty($row[1])) {
                $error_message = (new Executer("delete from workshift where id = $workshift_id"))->error;
            }
            else {
                $position = 1;
                $error_message = (new Executer("insert into edition (workshift_id, position) values ($workshift_id, $position)"))->error;
            }
        }
    }
}
?>