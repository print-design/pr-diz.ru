<?php
include '../include/topscripts.php';

$uri = urldecode(filter_input(INPUT_GET, 'uri'));
$sql = "select request_uri from user where id=". GetUserId();
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    if($uri == $row[0]) {
        echo "OK";
    }
    else {
        echo $row[0];
    }
}
?>