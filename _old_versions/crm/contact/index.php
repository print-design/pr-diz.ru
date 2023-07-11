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
            
            if(IsInRole('admin')) {
            ?>
            <div class="d-flex justify-content-between">
                <div class="p-1">
                    <h1>Первичные действенные контакты</h1>
                </div>
                <div class="p-1">
                    <form class="form-inline" method="get" action="<?=APPLICATION ?>/contact/">
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
            <?php
            }
            else
            {
            ?>
            <h1>Первичные действенные контакты</h1>
            <?php
            }
            ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Дата</th>
                        <?php
                        if(IsInRole('admin')) {
                        ?>
                        <th>Менеджер</th>
                        <?php
                        }
                        ?>
                        <th>Предприятие</th>
                        <th>Контактное лицо</th>
                        <th>Результат</th>
                        <th>След. зв.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(IsInRole('admin')){
                        $date = '';
                        $i = 0;
                        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                        
                        if($conn->connect_error) {
                            die('Ошибка соединения: ' . $conn->connect_error);
                        }
                        
                        $sql = "select count(c.id) count "
                                . "from contact c "
                                . "inner join contact_result r on c.result_id = r.id "
                                . "inner join person p "
                                . "inner join organization o on p.organization_id = o.id "
                                . "on c.person_id = p.id "
                                . "where r.efficient = 1 "
                                . "and (select count(c1.id) from contact c1 inner join person p1 inner join organization o1 on p1.organization_id = o1.id on c1.person_id = p1.id where o1.id = o.id and c1.manager_id = c.manager_id and c1.id < c.id) = 0"
                                . (isset($_GET['manager']) && $_GET['manager'] != '' ? " and c.manager_id=".$_GET['manager'] : "");
                        
                        $conn->query('set names utf8');
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            if($row = $result->fetch_assoc()) {
                                $pager_total_count = $row['count'];
                            }
                        }
                        
                        $sql = "select c.id id, date_format(c.date, '%d.%m.%Y') date, o.id organization_id, o.name organization, r.name result, date_format(c.next_date, '%d.%m.%Y') next_date, "
                                . "p.name person, "
                                . "m.last_name, m.first_name, m.middle_name "
                                . "from contact c "
                                . "inner join manager m on c.manager_id = m.id "
                                . "inner join contact_result r on c.result_id = r.id "
                                . "inner join person p "
                                . "inner join organization o on p.organization_id = o.id "
                                . "on c.person_id = p.id "
                                . "where r.efficient = 1 "
                                . "and (select count(c1.id) from contact c1 inner join person p1 inner join organization o1 on p1.organization_id = o1.id on c1.person_id = p1.id where o1.id = o.id and c1.manager_id = c.manager_id and c1.id < c.id) = 0 "
                                . (isset($_GET['manager']) && $_GET['manager'] != '' ? "and c.manager_id=".$_GET['manager']." " : " ")
                                . "order by id desc limit ".$pager_skip.", ".$pager_take;
                        
                        $conn->query('set names utf8');
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                if ($row['date'] != $date) {
                                    $date = $row['date'];
                                    $i = 0;
                                }
                                echo "<tr>"
                                        ."<td>".(++$i)."</td>"
                                        ."<td class='text-nowrap'>".$row['date']."</td>"
                                        ."<td class='text-nowrap'>".$row['last_name'].' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['first_name'], 0, 1).'.' : $row['first_name']).' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['middle_name'], 0, 1).'.' : $row['middle_name'])."</td>"
                                        ."<td><a href='".APPLICATION."/organization/details.php?id=".$row['organization_id']."' title='".$row['organization']."'>".$row['organization']."</a></td>"
                                        ."<td>".$row['person']."</td>"
                                        ."<td>".$row['result']."</td>"
                                        ."<td>".$row['next_date']."</td>"
                                        ."</tr>";
                            }
                        }
                        $conn->close();
                    }
                    else {
                        $date = '';
                        $i = 0;
                        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                        
                        if($conn->connect_error) {
                            die('Ошибка соединения: ' . $conn->connect_error);
                        }
                        
                        $sql = "select count(c.id) count "
                                . "from contact c "
                                . "inner join contact_result r on c.result_id = r.id "
                                . "inner join person p "
                                . "inner join organization o on p.organization_id = o.id "
                                . "on c.person_id = p.id "
                                . "where c.manager_id=". GetManagerId()." "
                                . "and r.efficient = 1 "
                                . "and (select count(c1.id) from contact c1 inner join person p1 inner join organization o1 on p1.organization_id = o1.id on c1.person_id = p1.id where o1.id = o.id and c1.manager_id = c.manager_id and c1.id < c.id) = 0";
                        
                        $conn->query('set names utf8');
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            if($row = $result->fetch_assoc()) {
                                $pager_total_count = $row['count'];
                            }
                        }
                        
                        $sql = "select c.id id, date_format(c.date, '%d.%m.%Y') date, o.id organization_id, o.name organization, r.name result, date_format(c.next_date, '%d.%m.%Y') next_date, "
                                . "p.name person "
                                . "from contact c "
                                . "inner join contact_result r on c.result_id = r.id "
                                . "inner join person p "
                                . "inner join organization o on p.organization_id = o.id "
                                . "on c.person_id = p.id "
                                . "where c.manager_id=". GetManagerId()." "
                                . "and r.efficient = 1 "
                                . "and (select count(c1.id) from contact c1 inner join person p1 inner join organization o1 on p1.organization_id = o1.id on c1.person_id = p1.id where o1.id = o.id and c1.manager_id = c.manager_id and c1.id < c.id) = 0 "
                                . "order by id desc limit ".$pager_skip.", ".$pager_take;
                        
                        $conn->query('set names utf8');
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                if ($row['date'] != $date) {
                                    $date = $row['date'];
                                    $i = 0;
                                }
                                echo "<tr>"
                                        ."<td>".(++$i)."</td>"
                                        ."<td class='text-nowrap'>".$row['date']."</td>"
                                        ."<td><a href='".APPLICATION."/organization/details.php?id=".$row['organization_id']."' title='".$row['organization']."'>".$row['organization']."</a></td>"
                                        ."<td>".$row['person']."</td>"
                                        ."<td>".$row['result']."</td>"
                                        ."<td>".$row['next_date']."</td>"
                                        ."</tr>";
                            }
                        }
                        $conn->close();
                    }
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