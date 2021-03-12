<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
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
        include '../include/pager_top.php';
        ?>
        <div class="container-fluid">
            <?php
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-2">
                <div class="p-1">
                    <h1>Мои предприятия</h1>
                </div>
                <div class="p-1">
                    <form class="form-inline" method="get">
                        <a href="create.php" title="Добавить предприятие" class="btn btn-outline-dark mr-sm-2">
                            <span class="font-awesome">&#xf067;</span>&nbsp;Добавить
                        </a>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Поиск" id="find" name="find" value="<?= isset($_GET['find']) ? $_GET['find'] : '' ?>" />
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-dark"><span class="font-awesome">&#xf002;</span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Создано</th>
                        <th>Наименование</th>
                        <th>Продукция</th>
                        <th>Адрес</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                    
                    if($conn->connect_error) {
                        die('Ошибка соединения: ' . $conn->connect_error);
                    }
                    
                    $find = '';
                    if(isset($_GET['find'])){
                        $find = "and name like '%".$_GET['find']."%' ";
                    }
                    
                    $sql = "select count(id) count from organization where manager_id=". GetManagerId()." "
                            . $find;
                    
                    $conn->query('set names utf8');
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        if($row = $result->fetch_assoc()) {
                            $pager_total_count = $row['count'];
                        }
                    }
                    
                    $sql = "select id, date_format(date, '%d.%m.%Y') date, name, production, address from organization where manager_id=".GetManagerId()." "
                            .$find
                            ."order by id desc";
                    
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>"
                                    ."<td class='text-nowrap'>".$row['date']."</td>"
                                    ."<td><a href='details.php?id=".$row['id']."' title='".$row['name']."'>".$row['name']."</a></td>"
                                    ."<td>".$row['production']."</td>"
                                    ."<td class='newline'>".$row['address']."</td>"
                                    ."</tr>";
                        }
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>