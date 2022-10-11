<?php
include '../include/topscripts.php';

$foldername = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/myimages/images/";

if(null !== filter_input(INPUT_POST, 'delete_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    if(!empty($id)) {
        $sql = "select name from myimages where id = $id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $name = $row['name'];
            if(is_dir($foldername.$name) || (file_exists($foldername.$name) && unlink($foldername.$name))) {
                $sql = "delete from myimages where id = $id";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
            else {
                $error_message = "Ошибка при удалении файла";
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
            <a href="create.php" class="btn btn-outline-dark">Создать</a>
            <h1>Мои картинки</h1>
            <div class="row">
                <?php
                $sql = "select id, name from myimages order by id desc";
                $fetcher = new Fetcher($sql);
                while ($row = $fetcher->Fetch()):
                ?>
                <div class="col-3" style="position: relative;">
                    <a href="details.php?id=<?=$row['id'] ?>"><img src="images/<?=$row['name'] ?>" title="<?=$row['name'] ?>" class="img-fluid" /></a>
                    <div style="position: absolute; top: 5px; right: 5px;">
                        <form method="post">
                            <input type="hidden" name="id" value="<?=$row['id'] ?>" />
                            <button type="submit" name="delete_submit" class="btn btn-dark">X</button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_find.php';
        ?>
    </body>
</html>