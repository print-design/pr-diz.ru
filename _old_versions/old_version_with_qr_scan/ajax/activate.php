<?php
include '../include/topscripts.php';

$type = filter_input(INPUT_GET, 'type');
$id = filter_input(INPUT_GET, 'id');

if($type == "laminator_roller" && !empty($id)) {
    $sql = "update norm_laminator_roller set active = not active where id = $id";
    $executer = new Executer($sql);
    if(!empty($executer->error)) {
        exit(-1);
    }
    else {
        $sql = "select active from norm_laminator_roller where id = $id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            exit($row[0]);
        }
        else {
            exit(-1);
        }
    }
}

if($type == "raport" && !empty($id)) {
    $sql = "update raport set active = not active where id = $id";
    $executer = new Executer($sql);
    if(!empty($executer->error)) {
        exit(-1);
    }
    else {
        $sql = "select active from raport where id = $id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            exit($row[0]);
        }
        else {
            exit(-1);
        }
    }
}
?>