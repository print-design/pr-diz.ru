<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$error_message = '';
$result = array('error' => '', 'id' => $id);

if(!empty($id)) {
    $sql = "select image, pdf from dialog_user_image where id = $id";
    $fetcher = new Fetcher($sql);
    $error_message = $fetcher->error;
    
    if(empty($error_message) && $row = $fetcher->Fetch()) {
        $filename = $row['image'];
        $filepath = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/dialog/mini/$filename";
        if(file_exists($filepath)) {
            unlink($filepath);
        }
        
        $filepath = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/dialog/$filename";
        if(file_exists($filepath)) {
            unlink($filepath);
        }
        
        $filename = $row['pdf'];
        if(!empty($filename)) {
            $filepath = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/dialog/pdf/$filename";
            if(file_exists($filepath)) {
                unlink($filepath);
            }
            
            $filename = $row['image'];
            $filepath = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/dialog/pdf/$filename";
            if(file_exists($filepath)) {
                unlink($filepath);
            }
        }
        
        if(empty($error_message)) {
            $sql = "delete from dialog_user_image where id = $id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
    }
}

$result['error'] = $error_message;

echo json_encode($result);
?>