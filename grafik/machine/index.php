<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';
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
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-2">
                <div class="p-1">
                    <h1>Машины</h1>
                </div>
                <div class="p-1">
                    <a href="create.php" title="Добавить машину" class="btn btn-outline-dark mr-sm-2">
                        <i class="fas fa-plus"></i>&nbsp;Добавить
                    </a>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Наименование</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $machines = (new Grabber("select id, name from machine order by name asc"))->result;
                    
                    foreach ($machines as $row) {
                        echo "<tr>"
                                ."<td><a href='".APPLICATION."/machine/details.php?id=".$row['id']."'>".htmlentities($row['name'])."</a></td>"
                                ."</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>