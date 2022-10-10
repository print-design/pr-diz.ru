<?php
include '../include/topscripts.php';
include './myimage.php';

if(null !== filter_input(INPUT_POST, 'image_submit')) {
    if($_FILES['file']['error'] == 0) {
        if(exif_imagetype($_FILES['file']['tmp_name'])) {
            $myimage = new MyImage($_FILES['file']['tmp_name']);
            $file_uploaded = $myimage->ResizeAndSave($_SERVER['DOCUMENT_ROOT'].APPLICATION.'/myimages/images/', 0, 0);
            
            if($file_uploaded) {
                $sql = "insert into myimages (name) values ('$myimage->filename')";
                $executer = new Executer($sql);
                $error_message = $executer->error;
                $insert_id = $executer->insert_id;
                
                if(empty($error_message) && !empty($insert_id)) {
                    header('Location: details.php?id='.$insert_id);
                }
            }
            else {
                $error_message = "ошибка при загрузке файла";
            }
        }
    }
}
?>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <div class="container">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a href="./" class="btn btn-outline-dark">Отмена</a>
            <h1>Новая картинка</h1>
            <form method="post" enctype="multipart/form-data">
                <input type="file" id="file" name="file" class="form-control" />
                <br /><br />
                <button type="submit" name="image_submit" class="btn btn-dark">Загрузить</button>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_find.php';
        ?>
    </body>
</html>