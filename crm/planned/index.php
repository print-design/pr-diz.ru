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
        ?>
        <div class="container-fluid">
            <?php
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Запланировано</h1>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Контактное лицо</th>
                        <th>Дата</th>
                        <th>Предприятие</th>
                        <th>Телефон</th>
                        <th>E-mail</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                    $sql = "select c.id contact_id, date_format(c.next_date, '%d.%m.%Y') next_date, p.id person_id, p.name, p.phone, p.email, o.id organization_id, o.name organization "
                            . "from contact c "
                            . "inner join person p "
                            . "inner join organization o on p.organization_id = o.id "
                            . "on c.person_id = p.id "
                            . "where o.manager_id=".GetManagerId()." "
                            . "and c.next_date is not null "
                            . "and UNIX_TIMESTAMP(c.next_date) < UNIX_TIMESTAMP(DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY)) "
                            . "and (select count(id) from contact where person_id = p.id and UNIX_TIMESTAMP(date) >= UNIX_TIMESTAMP(CURRENT_DATE())) = 0 "
                            . "order by c.next_date desc"; 
                    
                    if($conn->connect_error) {
                        die('Ошибка соединения: ' . $conn->connect_error);
                    }
                    
                    $conn->query('set names utf8');
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>"
                                ."<td><a href='".APPLICATION."/contact/create.php?person=".$row['person_id']."' title='Связазаться: ".$row['name']."' class='btn btn-outline-dark btn-sm'><span class='font-awesome'>&#xf095;</span></a>&nbsp;".$row['name']."</td>"
                                ."<td>".$row['next_date']."</td>"
                                ."<td><a href='".APPLICATION."/organization/details.php?id=".$row['organization_id']."' title='".$row['organization']."'>".$row['organization']."</a></td>"
                                ."<td>".$row['phone']."</td>"
                                ."<td>".$row['email']."</td>"
                                ."</tr>";
                        }
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
            <?php
            include '../include/footer.php';
            ?>
        </div>
    </body>
</html>