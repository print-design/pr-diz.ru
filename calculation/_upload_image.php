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

$result = array('error' => '', 'info' => '', 'filename' => '', 'to_plan_visible' => false);

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
        
        if($file_uploaded && $object == STREAM) {
            $filename = $myimage->filename;
            $sql = "update calculation_stream set image$image = '$filename' where id = $id";
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
            $status_id = null;
            $work_type_id = null;
            $streams = array();
            
            $sql = "select c.status_id, c.work_type_id from calculation_stream cs inner join calculation c on cs.calculation_id = c.id where cs.id = $id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $status_id = $row['status_id'];
                $work_type_id = $row['work_type_id'];
            }
            
            $sql = "select image1, image2 from calculation_stream where calculation_id = (select calculation_id from calculation_stream where id = $id)";
            $grabber = new Grabber($sql);
            $streams = $grabber->result;
            $result['error'] = $grabber->error;
            
            if($status_id == ORDER_STATUS_TECHMAP && $work_type_id == WORK_TYPE_NOPRINT) {
                $result['to_plan_visible'] = true;
            }
            elseif($status_id == ORDER_STATUS_TECHMAP && $work_type_id == WORK_TYPE_PRINT && count(array_filter($streams, function($x) { return empty($x["image1"]) || empty($x["image2"]); })) == 0) {
                $result['to_plan_visible'] = true;
            }
        }
    }
}
else {
    $result['error'] = "Данные не получены.";
}

echo json_encode($result);
?>