<?php
include '../include/topscripts.php';

$error_message = '';

$edition = filter_input(INPUT_GET, 'edition');
if($edition !== null) {
    $sql = "select name, organization, length, status_id, lamination_id, coloring, roller_id, manager_id, comment from edition where id=$edition";
    $fetcher = new Fetcher($sql);
    $error_message = $fetcher->error;
    
    if($row = $fetcher->Fetch()) {
        $name = addslashes($row['name']);
        $organization = addslashes($row['organization']);
        $length = empty($row['length']) ? 'NULL' : $row['length'];
        $status_id = empty($row['status_id']) ? 'NULL' : $row['status_id'];
        $lamination_id = empty($row['lamination_id']) ? 'NULL' : $row['lamination_id'];
        $coloring = empty($row['coloring']) ? 'NULL' : $row['coloring'];
        $roller_id = empty($row['roller_id']) ? 'NULL' : $row['roller_id'];
        $manager_id = empty($row['manager_id']) ? 'NULL' : $row['manager_id'];
        $comment = addslashes($row['comment']);
        $origin_id = $edition;
        $origin_name = addslashes($row['organization']).': '.addslashes($row['name']);
        
        $sql = "insert into clipboard (name, organization, length, status_id, lamination_id, coloring, roller_id, manager_id, comment, origin_id, origin_name) "
                . "values ('$name', '$organization', $length, $status_id, $lamination_id, $coloring, $roller_id, $manager_id, '$comment', $origin_id, '$origin_name')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

if($error_message != '') {
    echo $error_message;
}
?>