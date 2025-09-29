<?php
include '../include/topscripts.php';

$calculation_id = 0;
$comment = '';

$sql = "select c.id, c.cut_remove_cause from calculation c where c.cut_remove_cause <> '' and (select count(id) from calculation_status_history where calculation_id = c.id and comment <> '') = 0";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $calculation_id = $row['id'];
    $comment = $row['cut_remove_cause'];
}

$result = 10000000;

if(!empty($calculation_id) && (!empty($comment) || $comment == '0')) {
    $sql = "update calculation_status_history set comment = '$comment' where calculation_id = $calculation_id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "select count(c.id) from calculation c where cut_remove_cause <> '' and (select count(id) from calculation_status_history where calculation_id = c.id and comment <> '') > 0";
        $fetcher = new Fetcher($sql);
        
        if($row = $fetcher->Fetch()) {
            $result = $row[0];
        }
    }
}

echo $result;
?>