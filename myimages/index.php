<?php
include '../include/topscripts.php';
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
                <div class="col-3">
                    <a href="details.php?id=<?=$row['id'] ?>"><img src="images/<?=$row['name'] ?>" title="<?=$row['name'] ?>" class="img-fluid" /></a>
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