<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
        
// Если нет параметра id, переход к списку
if(!isset($_GET['id'])) {
    header('Location: '.APPLICATION.'/order/');
}
        
// Получение объекта
$contact_date = '';
$organization = '';
$last_name = '';
$first_name = '';
$middle_name = '';
$product = '';
$number = '';
$price = '';
$shipment_date = '';
$contract_date = '';
$bill_date = '';
$total_payment = '';
        
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
$sql = "select date_format(c.date, '%d.%m.%Y') date, org.name organization, m.last_name, m.first_name, m.middle_name, "
        . "o.product, o.number, o.price, "
        . "date_format(o.shipment_date, '%d.%m.%Y') shipment_date, date_format(o.contract_date, '%d.%m.%Y') contract_date, date_format(o.bill_date, '%d.%m.%Y') bill_date, "
        . "(select sum(sum) from payment where order_id = o.id) as total_payment "
        . "from _order o "
        . "left join contact c "
        . "left join manager m on c.manager_id = m.id "
        . "left join person p left join organization org on p.organization_id = org.id "
        . "on c.person_id = p.id "
        . "on o.contact_id = c.id "
        . "where o.id=".$_GET['id'];
        
if($conn->connect_error) {
    die('Ошибка соединения: ' . $conn->connect_error);
}
        
$conn->query('set names utf8');
$result = $conn->query($sql);
if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
    $contact_date = $row['date'];
    $organization = $row['organization'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $middle_name = $row['middle_name'];
    $product = $row['product'];
    $number = $row['number'];
    $price = $row['price'];
    $shipment_date = $row['shipment_date'];
    $contract_date = $row['contract_date'];
    $bill_date = $row['bill_date'];
    $total_payment = $row['total_payment'];
}
$conn->close();
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
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1>Заказ</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/order/edit.php?id=<?=$_GET['id'] ?>" class="btn btn-outline-dark"><span class="font-awesome">&#xf044;</span>&nbsp;Редактировать</a>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Дата контакта</th>
                            <td><?=$contact_date ?></td>
                        </tr>
                        <tr>
                            <th>Менеджер</th>
                            <td><?=$last_name.' '.$first_name.' '.$middle_name ?></td>
                        </tr>
                        <tr>
                            <th>Предприятие</th>
                            <td><?=$organization ?></td>
                        </tr>
                        <tr>
                            <th>Товар</th>
                            <td class="newline"><?=$product ?></td>
                        </tr>
                        <tr>
                            <th>Количество</th>
                            <td><?=$number ?> шт.</td>
                        </tr>
                        <tr>
                            <th>Цена (1 шт.)</th>
                            <td><?=$price ?> руб.</td>
                        </tr>
                        <tr>
                            <th>Цена (всего)</th>
                            <td><?=$number != '' && $price != '' ? floatval($price) * floatval($number) : '' ?> руб.</td>
                        </tr>
                        <tr>
                            <th>Дата отгрузки</th>
                            <td><?=$shipment_date ?></td>
                        </tr>
                        <tr>
                            <th>Дата заключения договора</th>
                            <td><?=$contract_date ?></td>
                        </tr>
                        <tr>
                            <th>Дата выставления счёта</th>
                            <td><?=$bill_date ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-12 col-md-6">
                    <?php
                    if(IsInRole('accountant')) {
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h2>Оплата заказа</h2>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/payment/create.php?order_id=<?=$_GET['id'] ?>" title="Добавить оплату заказа" class="btn btn-outline-dark"><span class="font-awesome">&#xf067;</span>&nbsp;Добавить</a>
                        </div>
                    </div>
                    <?php
                    }
                    else {
                    ?>
                    <h2>Оплата заказа</h2>
                    <?php
                    }
                    ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th class="text-right">Сумма оплаты</th>
                                <?php
                                if(IsInRole('accountant')) {
                                    echo '<th></th>';
                                }
                                ?>
                            </tr>
                        </thead>
                        <?php
                        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                        
                        if($conn->connect_error) {
                            die('Ошибка соединения: ' . $conn->connect_error);
                        }
                        
                        $sql = "select id, date_format(date, '%d.%m.%Y') date, sum from payment where order_id = ".$_GET['id'];
                        
                        $conn->query('set names utf8');
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $id = $row['id'];
                                $date = $row['date'];
                                $sum = $row['sum'];
                                $application = APPLICATION;
                                echo <<<PAYMENT
                                <tr>
                                <td>$date</td>
                                <td class='text-right'>$sum руб.</td>
                                PAYMENT;
                                if(IsInRole('accountant')) {
                                echo <<<PAYMENT
                                <td class='text-right'>
                                    <a href='$application/payment/edit.php?id=$id' title='Редактировать'><span class='font-awesome'>&#xf044;</span></a>
                                    &nbsp;
                                    <a href='$application/payment/delete.php?id=$id' title='Удалить'><span class='font-awesome'>&#xf1f8;</span></a>
                                </td>
                                PAYMENT;
                                }
                                echo '</tr>';
                            }
                        }
                        $conn->close();
                        ?>
                        <tfoot>
                            <tr>
                                <th>Итого</th>
                                <th class="text-right"><?=$total_payment ?> руб.</th>
                                <?php
                                if(IsInRole('accountant')) {
                                    echo '<td></td>';
                                }
                                ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>