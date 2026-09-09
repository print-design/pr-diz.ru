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
    $sql = "insert into dialog (user_id_from, user_id_to, message) values (?, ?, ?)";
    $executer = new Executer($sql, [$user_id_from, $user_id_to, $message]);
    $error = $executer->error;
    $insert_id = $executer->insert_id;
    
    if(!empty($error)) {
        $result['error'] = $executer->error;        
    }
    
    if(empty($error)) {
        $sql = "insert into dialog_image (dialog_id, image, pdf) select ?, image, pdf from dialog_user_image where user_id = ?";
        $executer = new Executer($sql, [$insert_id, $user_id_from]);
        $error = $executer->error;
        
        if(!empty($error)) {
            $result['error'] = $executer->error;
        }
    }
    
    if(empty($error)) {
        $sql = "delete from dialog_user_image where user_id = ?";
        $executer = new Executer($sql, [$user_id_from]);
        $error = $executer->error;
        
        if(!empty($error)) {
            $result['error'] = $executer->error;
        }
    }
}

echo json_encode($result);
?>