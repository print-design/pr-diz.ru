<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');

if(empty($id)) {
    header('Location: ./');
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
            <a href="./" class="btn btn-outline-dark">К списку</a>
            <h1>Картинка</h1>
            <?php
            $sql = "select name from myimages where id = $id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()):
            ?>
            <img src="images/<?=$row['name'] ?>" title="<?=$row['name'] ?>" class="img-fluid" />
            <?php endif; ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_find.php';
        ?>
    </body>
</html>