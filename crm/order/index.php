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
            <h1>Заказы</h1>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Дата контакта</th>
                        <th>Менеджер</th>
                        <th>Предприятие</th>
                        <th>Товар</th>
                        <th>Количество</th>
                        <th>Цена (1 шт.)</th>
                        <th>Цена (всего)</th>
                        <th>Оплачено</th>
                        <th>Дата отгрузки</th>
                        <th>Дата закл. дог</th>
                        <th>Дата выст. сч.</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                    
                    if($conn->connect_error) {
                        die('Ошибка соединения: ' . $conn->connect_error);
                    }
                    
                    $sql = "select count(id) count from _order";
                    
                    $conn->query('set names utf8');
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        if($row = $result->fetch_assoc()) {
                            $pager_total_count = $row['count'];
                        }
                    }
                    
                    $sql = "select o.id, date_format(c.date, '%d.%m.%Y') date, org.name organization, o.product, o.number, o.price, date_format(o.shipment_date, '%d.%m.%Y') shipment_date, date_format(o.contract_date, '%d.%m.%Y') contract_date, date_format(o.bill_date, '%d.%m.%Y') bill_date, "
                            . "m.last_name, m.first_name, m.middle_name, "
                            . "(select sum(sum) from payment where order_id = o.id) total_payment "
                            . "from _order o "
                            . "left join contact c "
                            . "left join manager m on c.manager_id = m.id "
                            . "left join person p left join organization org on p.organization_id = org.id "
                            . "on c.person_id = p.id "
                            . "on o.contact_id = c.id "
                            . "order by o.id desc limit ".$pager_skip.",".$pager_take;
                    
                    $conn->query('set names utf8');
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>"
                                    ."<td class='text-nowrap'><a href='".APPLICATION."/order/details.php?id=".$row['id']."'>".$row['date']."</a></td>"
                                    ."<td>".$row['last_name'].' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['first_name'], 0, 1).'.' : $row['first_name']).' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['middle_name'], 0, 1).'.' : $row['middle_name'])."</td>"
                                    ."<td>".$row['organization']."</td>"
                                    ."<td class='newline'>".$row['product']."</td>"
                                    ."<td>".$row['number']."</td>"
                                    ."<td>".$row['price']."</td>"
                                    ."<td>".($row['price'] != "" && $row['number'] != "" ? floatval($row['price']) * floatval($row['number']) : "")."</td>"
                                    ."<td>".$row['total_payment']."</td>"
                                    ."<td>".$row['shipment_date']."</td>"
                                    ."<td>".$row['contract_date']."</td>"
                                    ."<td>".$row['bill_date']."</td>"
                                    ."<td><a href='".APPLICATION."/order/details.php?id=".$row['id']."' title='Открыть'><span class='font-awesome'>&#xf129;</span></a></td>"
                                    ."<td><a href='".APPLICATION."/order/edit.php?id=".$row['id']."' title='Редактировать'><span class='font-awesome'>&#xf044;</span></a></td>"
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