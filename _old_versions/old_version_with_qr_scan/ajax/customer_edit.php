<?php
include '../include/topscripts.php';

$sql = "";
$id = filter_input(INPUT_GET, 'id');

$person = filter_input(INPUT_GET, 'person');

if(null !== $id && null !== $person) {
    $str_person = addslashes($person);
    $sql = "update customer set person = '$str_person' where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        echo $person;
    }
    else {
        echo "ERROR";
    }
}

$phone = filter_input(INPUT_GET, 'phone');
$extension = filter_input(INPUT_GET, 'extension');

if(null !== $id && null !== $phone) {
    $sql = "update customer set phone = '$phone', extension = '$extension' where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        echo json_encode(array("phone" => $phone, "extension" => $extension));
    }
    else {
        echo json_encode(array("phone" => "ERROR", "extension" => ""));
    }
}

$email = filter_input(INPUT_GET, 'email');

if(null !== $id && null !== $email) {
    $sql = "update customer set email = '$email' where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        echo $email;
    }
    else {
        echo "ERROR";
    }
}

$manager_id = filter_input(INPUT_GET, 'manager_id');

if(null !== $id && null !== $manager_id) {
    $sql = "update customer set manager_id = $manager_id where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "select last_name, first_name from user where id = $manager_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            echo json_encode(array("id" => $manager_id, "last_name" => $row['last_name'], "first_name" => $row['first_name']));
        }
        else {
            echo json_encode(array("id" => 0, "last_name" => "ERROR", "first_name" => ""));
        }
    }
    else {
        echo json_encode(array("id" => 0, "last_name" => "ERROR", "first_name" => ""));
    }
}
?>