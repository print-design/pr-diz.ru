<?php
include '../include/topscripts.php';
include '../include/restrict_accountant.php';

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_payment_submit'])) {
    if($form_valid) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if($conn->connect_error) {
            die('Ошибка соединения: '.$conn->connect_error);
        }

        $id = $_POST['id'];
        $order_id = $_POST['order_id'];
                
        $sql = "delete from payment where id = ".$id;
                
        $conn->query('set names utf8');
        if ($conn->query($sql) === true) {
            header('Location: '.APPLICATION.'/order/details.php?id='.$order_id);
        }
        else {
            $error_message = $conn->error;
        }
                
        $conn->close();
    }
}
        
// Если нет параметра id, переход к списку
if(!isset($_GET['id'])) {
    header('Location: '.APPLICATION.'/order/');
}
        
// Получение заказа и оплаты
$order_id = '';
$contact_date = '';
$organization = '';
$last_name = '';
$first_name = '';
$middle_name = '';
$product = '';
$number = '';
$price = '';
        
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
$sql = "select o.id, date_format(c.date, '%d.%m.%Y') contact_date, org.name organization, m.last_name, m.first_name, m.middle_name, "
        . "o.product, o.number, o.price, date_format(p.date, '%d.%m.%Y') date, p.sum "
        . "from _order o "
        . "left join contact c "
        . "left join manager m on c.manager_id = m.id "
        . "left join phone ph left join organization org on ph.organization_id = org.id "
        . "on c.phone_id = ph.id "
        . "on o.contact_id = c.id "
        . "inner join payment p on p.order_id = o.id "
        . "where p.id=".$_GET['id'];
        
if($conn->connect_error) {
    die('Ошибка соединения: ' . $conn->connect_error);
}
        
$conn->query('set names utf8');
$result = $conn->query($sql);
if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
    $order_id = $row['id'];
    $contact_date = $row['date'];
    $organization = $row['organization'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $middle_name = $row['middle_name'];
    $product = $row['product'];
    $number = $row['number'];
    $price = $row['price'];
    $date = $row['date'];
    $sum = $row['sum'];
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
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1>Удаление оплаты</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/order/details.php?id=<?=$order_id ?>" title="Отмена" class="btn btn-outline-dark"><span class="font-awesome">&#xf0e2;</span>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <h2 class="text-danger">Вы действительно хотите удалить эту оплату?</h2>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th>Дата заказа</th>
                            <td><?=$contact_date ?></td>
                        </tr>
                        <tr>
                            <th>Менеджер</th>
                            <td><?=$last_name." ".$first_name." ".$middle_name ?></td>
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
                            <td><?=$number ?></td>
                        </tr>
                        <tr>
                            <th>Цена (за 1 шт.)</th>
                            <td><?=$price ?></td>
                        </tr>
                        <tr>
                            <th>Цена (всего)</th>
                            <td><?=(floatval($price) * floatval($number)) ?></td>
                        </tr>
                    </table>
                    <table class="table table-bordered">
                        <tr>
                            <th>Дата</th>
                            <th>Сумма</th>
                        </tr>
                        <tr>
                            <td><?=$date ?></td>
                            <td><?=$sum ?></td>
                        </tr>
                    </table>
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$_GET['id'] ?>"/>
                        <input type="hidden" id="order_id" name="order_id" value="<?=$order_id ?>"/>
                        <div class="form-group">
                            <button type="submit" id="delete_payment_submit" name="delete_payment_submit" class="btn btn-outline-dark">Удалить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>