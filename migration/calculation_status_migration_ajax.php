<?php
include '../include/topscripts.php';

$calculation_id = 0;
$date = null;
$status_id = 0;
$comment = '';
$user_id = 0;

$sql = "select c.id, c.date, c.status_id, c.cut_remove_cause, c.manager_id "
        . "from calculation c "
        . "where c.status_id <> (select status_id from calculation_status_history where calculation_id = c.id order by date desc limit 1)";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $calculation_id = $row['id'];
    $date = $row['date'];
    $status_id = $row['status_id'];
    $comment = addslashes($row['cut_remove_cause']);
    $user_id = $row['manager_id'];
}

$result = 10000000;

if(!empty($calculation_id) && !empty($date) && !empty($status_id) && !empty($user_id)) {
    $error_message = SetCalculationStatus($calculation_id, $status_id, $comment);
    
    if(empty($error_message)) {
        $sql = "select count(c.id) "
                . "from calculation c "
                . "where c.status_id = (select status_id from calculation_status_history where calculation_id = c.id order by date desc limit 1)";
        $fetcher = new Fetcher($sql);
        
        if($row = $fetcher->Fetch()) {
            $result = $row[0];
        }
    }
}

echo $result;
?>