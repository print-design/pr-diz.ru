<?php
include '../include/topscripts.php';

$error_message = '';

$edition = filter_input(INPUT_GET, 'edition');
if($edition !== null) {
    $sql = "select e.name, e.organization, e.length, e.status_id, e.lamination_id, e.coloring, e.roller_id, e.manager_id, e.comment, ws.user1_id, ws.user2_id "
            . "from edition e "
            . "inner join workshift ws on e.workshift_id=ws.id "
            . "where e.id=$edition";
    
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    $error_message = $fetcher->Fetch();
    if($fetcher->error == '') {
        $json = json_encode($row);
    
        echo $json;
    }
}

if($error_message != '') {
    echo $error_message;
}
?>