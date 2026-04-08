<?php
include '../include/topscripts.php';

$user_id_from = filter_input(INPUT_POST, 'user_id_from');
$user_id_to = filter_input(INPUT_POST, 'user_id_to');
$message = filter_input(INPUT_POST, 'message');

$result = array();
$result['error'] = '';

if(empty($user_id_from) || empty($user_id_to) || empty($message)) {
    $result['error'] = "Пустые исходные данные";
}
else {
    $message = addslashes($message);

    $sql = "insert into dialog (user_id_from, user_id_to, message) values ($user_id_from, $user_id_to, '$message')";
    $executer = new Executer($sql);
    if(!empty($executer->error)) {
        $result['error'] = $executer->error;        
    }
    else {
        $result['id'] = $user_id_to;
    }
}

echo json_encode($result);
?>