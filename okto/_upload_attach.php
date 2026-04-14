<?php
include '../include/topscripts.php';
include '../include/myimage.php';

// Размеры загружаемых картинок
const IMAGE_MINI_HEIGHT = 0;
const IMAGE_MINI_WIDTH = 100;
const IMAGE_HEIGHT = 0;
const IMAGE_WIDTH = 0;

$result = array('error' => '', 'info' => '', 'filename' => '');

$user_id = filter_input(INPUT_POST, 'user_id');
$resolution = filter_input(INPUT_POST, 'resolution');

if(!empty($user_id) && !empty($_FILES['file']) && !empty($_FILES['file']['tmp_name'])) {
    $myimage = null;
    $input_file = null;
    $file_uploaded = false;
    $pdf = '';
    $name = time();
    
    if($_FILES['file']['type'] == 'application/pdf') {
        // Если файл - PDF
        // Загружаем PDF-файл
        $pdf = $name.".pdf";
        if(move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT']. APPLICATION."/content/dialog/pdf/".$pdf)) {
            // Делаем из PDF картинку
            $imagick = new Imagick();
            $imagick->setResolution($resolution, $resolution);
            $imagick->readImage($_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/dialog/pdf/".$name.".pdf[0]");
            $imagick->setImageFormat('jpeg');
            $imagick->setCompressionQuality(95);
            $output_file = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/dialog/".$name.".jpeg";
            $imagick->writeImage($output_file);
            
            // Сохраняем мини-картинку
            $input_file = $output_file;
            $myimage = new MyImage($input_file);
            $file_uploaded = $myimage->ResizeAndSave($_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/dialog/mini/", $name, IMAGE_MINI_WIDTH, IMAGE_MINI_HEIGHT);
            if(!$file_uploaded) {
                $result['info'] .= $myimage->errorMessage;
            }
        }
        else {
            $result['error'] = "Ошибка при загрузке PDF-файла";
            $file_uploaded = false;
        }
    }
    else {
        // Если файл - не PDF
        // Сохраняем мини-картинку
        $input_file = $_FILES['file']['tmp_name'];
        $myimage = new MyImage($input_file); // $result['info'] = implode(' -- ', get_object_vars($myimage));
        $file_uploaded = $myimage->ResizeAndSave($_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/dialog/mini/", $name, IMAGE_MINI_WIDTH, IMAGE_MINI_HEIGHT);
        if(!$file_uploaded) {
            $result['info'] .= $myimage->errorMessage;
        }
    }
    
    // Сохраняем полноразмерную картинку и вносим её имя в базу данных
    if(!empty($myimage) && !empty($input_file) && $file_uploaded) {
        $myimage = new MyImage($input_file);
        $file_uploaded = $myimage->ResizeAndSave($_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/dialog/", $name, IMAGE_WIDTH, IMAGE_HEIGHT);
        if(!$file_uploaded) {
            $result['info'] .= $myimage->errorMessage;
        }
        
        $filename = '';
        
        if($file_uploaded) {
            $filename = $myimage->filename;
            $sql = "insert into dialog_user_image (user_id, image, pdf) values ($user_id, '$filename', '$pdf')";
            $executer = new Executer($sql);
            
            if(empty($executer->error)) {
                $result['filename'] = $filename;
            }
            else {
                $result['error'] = $executer->error;
            }
        }
    }
}
else {
    $result['error'] = "Данные не получены.";
}

echo json_encode($result);
?>