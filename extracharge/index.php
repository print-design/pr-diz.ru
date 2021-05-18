<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Список типов наценки
$sql = "select id, name from extracharge_type";
$grabber = new Grabber($sql);
$error_message = $grabber->error;

$extracharge_types = array();

foreach ($grabber->result as $row) {
    $extracharge_types[$row['id']] = $row['name'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-start">
                <div class="p-1">
                    <?php
                    include '../include/subheader_admin.php';
                    ?>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-12 col-md-6">
                    <?php
                    $extracharge_type_id = 1;
                    ?>
                    <h2><?=$extracharge_types[$extracharge_type_id] ?></h2>
                    <form method="post">
                        <button type="submit" class="btn btn-light"><i class="fas fa-plus"></i>&nbsp;&nbsp;Добавить</button>
                    </form>
                    <br />
                    <?php
                    $extracharge_type_id = 2;
                    ?>
                    <h2><?=$extracharge_types[$extracharge_type_id] ?></h2>
                    <?php
                    $extracharge_type_id = 3;
                    ?>
                    <h2><?=$extracharge_types[$extracharge_type_id] ?></h2>
                    <?php
                    $extracharge_type_id = 4;
                    ?>
                    <h2><?=$extracharge_types[$extracharge_type_id] ?></h2>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>