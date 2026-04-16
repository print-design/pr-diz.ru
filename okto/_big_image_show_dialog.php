<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$is_user_image = filter_input(INPUT_GET, 'is_user_image');

$result = array('error' => '');

// Картинка пользователя
if(!empty($id) && $is_user_image !== null && $is_user_image == 1) {
    $sql = "select image from dialog_user_image where id = $id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result['name'] = "Изображение";
        $result['filename'] = $row['image'];
        $result['id'] = $id;
        $result['is_user_image'] = 1;
    }
}

// Картинка сообщения
if(!empty($id) && $is_user_image !== null && $is_user_image == 0) {
    $sql = "select image from dialog_image where id = $id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result['name'] = "Изображение";
        $result['filename'] = $row['image'];
        $result['id'] = $id;
        $result['is_user_image'] = 0;
    }
}

echo json_encode($result);
?>