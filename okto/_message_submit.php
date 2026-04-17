<?php
include '../include/topscripts.php';

$user_id_from = filter_input(INPUT_POST, 'user_id_from');
$user_id_to = filter_input(INPUT_POST, 'user_id_to');
$message = filter_input(INPUT_POST, 'message');

$error = '';
$result = array('id' => $user_id_to, 'error' => '');

if(empty($user_id_from) || empty($user_id_to) || empty($message)) {
    $result['error'] = "Пустые исходные данные -- $user_id_from -- $user_id_to -- $message";
}
else {
    $message = addslashes($message);

    $sql = "insert into dialog (user_id_from, user_id_to, message) values ($user_id_from, $user_id_to, '$message')";
    $executer = new Executer($sql);
    $error = $executer->error;
    $insert_id = $executer->insert_id;
    
    if(!empty($error)) {
        $result['error'] = $executer->error;        
    }
    
    if(empty($error)) {
        $sql = "insert into dialog_image (dialog_id, image, pdf) select $insert_id, image, pdf from dialog_user_image where user_id = $user_id_from";
        $executer = new Executer($sql);
        $error = $executer->error;
        
        if(!empty($error)) {
            $result['error'] = $executer->error;
        }
    }
    
    if(empty($error)) {
        $sql = "delete from dialog_user_image where user_id = $user_id_from";
        $executer = new Executer($sql);
        $error = $executer->error;
        
        if(!empty($error)) {
            $result['error'] = $executer->error;
        }
    }
}

echo json_encode($result);
?>