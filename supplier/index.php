<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'delete_supplier_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from supplier where id=$id"))->error;
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
        <div class="container-fluid list-page">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto nav2">
                <div class="p-1 row">
                    <?php
                    include '../include/subheader_admin.php';
                    ?>
                </div>
                <div class="p-1">
                    <a href="create.php" title="Добавить поставщика" class="btn btn-outline-dark">
                        <i class="fas fa-plus"></i>&nbsp;Добавить поставщика
                    </a>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Название поставщика</th>
                        <th>Типы пленок</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select s.id, s.name, "
                            . "(select count(id) from film_brand where supplier_id=s.id) count, "
                            . "(select name from film_brand where supplier_id=s.id limit 1) first "
                            . "from supplier s order by s.name";
                    $fetcher = new Fetcher($sql);
                    $error_message = $fetcher->error;
                    
                    while ($row = $fetcher->Fetch()) {
                        $id = $row['id'];
                        $name = htmlentities($row['name']);
                        $count = $row['count'];
                        $first = $row['first'];
                        $products = '';
                        if($first != null) {
                            $products = htmlentities($first);
                            
                            if($count > 1) {
                                $products .= " и еще ".(intval($count) - 1);
                            }
                        }
                        echo "<tr>"
                        . "<td>$name</td>"
                                . "<td>$products</td>"
                                . "<td class='text-right'><a href='".APPLICATION."/supplier/details.php?id=$id'><image src='../images/icons/edit.svg' /></a></td>";
                        /*echo "<td class='text-right'>";
                        if($first == null) {
                            echo "<form method='post'>";
                            echo "<input type='hidden' id='id' name='id' value='$id' />";
                            echo "<button type='submit' class='btn btn-link confirmable' id='delete_supplier_submit' name='delete_supplier_submit'><i class='fas fa-trash-alt'></i></button>";
                            echo '</form>';
                        }
                        echo '</td>';*/
                        echo "</tr>";
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