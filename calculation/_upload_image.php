<?php
include '../include/topscripts.php';
include '../include/myimage.php';

// К каким объектам прикладываются макеты
const STREAM = "stream";
const PRINTING = "printing";

// Размеры загружаемых картинок
const IMAGE_MINI_HEIGHT = 0;
const IMAGE_MINI_WIDTH = 100;
const IMAGE_HEIGHT = 0;
const IMAGE_WIDTH = 0;

$result = array('error' => '', 'info' => '', 'filename' => '', 'to_plan_visible' => true);

$object = filter_input(INPUT_POST, 'object');
$id = filter_input(INPUT_POST, 'id');
$image = filter_input(INPUT_POST, 'image');

if(!empty($object) && !empty($id) && !empty($image) && !empty($_FILES['file']) && !empty($_FILES['file']['tmp_name'])) {
    // Загружаем файл
    $myimage = new MyImage($_FILES['file']['tmp_name']);
    $file_uploaded = $myimage->ResizeAndSave($_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/$object/mini/", $id."_".$image, IMAGE_MINI_WIDTH, IMAGE_MINI_HEIGHT);
    
    if($file_uploaded) {
        $myimage = new MyImage($_FILES['file']['tmp_name']);
        $file_uploaded = $myimage->ResizeAndSave($_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/$object/", $id."_".$image, IMAGE_WIDTH, IMAGE_HEIGHT);
        
        $database_updated = false;
        $filename = '';
        
        if($file_uploaded && $object == PRINTING) {
            $filename = $myimage->filename;
            $sql = "update calculation_quantity set image$image = '$filename' where id = $id";
            $executer = new Executer($sql);
            
            if(empty($executer->error)) {
                $database_updated = true;
                $result['filename'] = $filename;
            }
            else {
                $result['error'] = $executer->error;
            }
        }
        
        if($database_updated) {
            // Проверяем, показывать ли кнопку "Поставить в план"
        }
    }
}
else {
    $result['error'] = "Данные не получены.";
}

echo json_encode($result);
?>