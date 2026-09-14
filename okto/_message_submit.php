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
    // Само сообщение, перенос вложений из "черновика" (dialog_user_image) в сообщение,
    // и очистка этого черновика -- одна связанная цепочка, выполняется в рамках одной
    // транзакции: либо сообщение уходит со всеми вложениями, либо не уходит вообще
    $transaction = new Transaction();
    
    $insert_id = $transaction->Execute("insert into dialog (user_id_from, user_id_to, message) values (?, ?, ?)", [$user_id_from, $user_id_to, $message]);
    $transaction->Execute("insert into dialog_image (dialog_id, image, pdf) select ?, image, pdf from dialog_user_image where user_id = ?", [$insert_id, $user_id_from]);
    $transaction->Execute("delete from dialog_user_image where user_id = ?", [$user_id_from]);
    
    $error = $transaction->error;
    
    if(empty($error)) {
        $transaction->Commit();
    }
    else {
        $transaction->Rollback();
        $result['error'] = $error;
    }
}

echo json_encode($result);
?>