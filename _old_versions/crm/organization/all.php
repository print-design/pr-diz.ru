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
                    <h1>Все предприятия</h1>
                </div>
                <div class="p-1">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <form class="form-inline" method="get">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Поиск" id="find" name="find" value="<?= isset($_GET['find']) ? $_GET['find'] : '' ?>" />
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-dark"><span class="font-awesome">&#xf002;</span></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div>
                            <form class="form-inline" method="get" action="<?=APPLICATION ?>/organization/all.php">
                                <select id="manager" name="manager" class="form-control" onchange="this.form.submit();">
                                    <option value="">...</option>
                                    <?php
                                    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                                    $sql = "select id, last_name, first_name, middle_name from manager order by last_name";
                                    
                                    if($conn->connect_error) {
                                        die('Ошибка соединения: ' . $conn->connect_error);
                                    }
                                    
                                    $conn->query('set names utf8');
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            $selected = isset($_GET['manager']) && $_GET['manager'] == $row['id'] ? " selected='selected'" : '';
                                            echo '<option value='.$row['id'].$selected.'>'.$row['last_name'].' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['first_name'], 0, 1).'.' : $row['first_name']).' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['middle_name'], 0, 1).'.' : $row['middle_name']).'</option>';
                                        }
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Создано</th>
                        <th>Наименование</th>
                        <th>Продукция</th>
                        <th>Адрес</th>
                        <th>Менеджер</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                    
                    if($conn->connect_error) {
                        die('Ошибка соединения: ' . $conn->connect_error);
                    }
                    
                    $find = '';
                    if(isset($_GET['find'])) {
                        $find = "where name like '%".$_GET['find']."%'";
                    }
                    
                    $manager = '';
                    if(isset($_GET['manager']) && $_GET['manager'] != '') {
                        $manager = "where manager_id = ".$_GET['manager'];
                    }
                    
                    $sql = "select count(id) count "
                            . "from organization "
                            . $find
                            . $manager;
                    
                    $conn->query('set names utf8');
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        if($row = $result->fetch_assoc()) {
                            $pager_total_count = $row['count'];
                        }
                    }
                    
                    $sql = "select o.id, date_format(o.date, '%d.%m.%Y') date, o.name, o.production, o.address, m.last_name, m.first_name, m.middle_name "
                            . "from organization o inner join manager m on o.manager_id = m.id "
                            . $find
                            . $manager
                            . " order by id desc limit ".$pager_skip.",".$pager_take;
                    
                    $conn->query('set names utf8');
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>"
                                    ."<td class='text-nowrap'>".$row['date']."</td>"
                                    ."<td><a href='details.php?id=".$row['id']."' title='".$row['name']."'>".$row['name']."</a></td>"
                                    ."<td>".$row['production']."</td>"
                                    ."<td class='newline'>".$row['address']."</td>"
                                    ."<td class='text-nowrap'>".$row['last_name'].' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['first_name'], 0, 1).'.' : $row['first_name']).' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['middle_name'], 0, 1).'.' : $row['middle_name'])."</td>"
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