<?php
include '../include/topscripts.php';

if(null !== filter_input(INPUT_POST, 'image_submit')) {
    if($_FILES['file']['error'] == 0) {
        if(exif_imagetype($_FILES['file']['tmp_name'])) {
            if(copy($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].APPLICATION.'/temp/images/'.$_FILES['file']['name'])) {
                $sql = "insert into myimages (name) values ('".$_FILES['file']['name']."')";
                $executer = new Executer($sql);
                $error_message = $executer->error;
                $insert_id = $executer->insert_id;
                
                if(empty($error_message) && !empty($insert_id)) {
                    header('Location: details.php?id='.$insert_id);
                }
            }
            else {
                $error_message = "Ошибка при загрузке файла";
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
        <div class="container-fluid">
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