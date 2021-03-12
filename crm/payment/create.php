<?php
include '../include/topscripts.php';
include '../include/restrict_accountant.php';

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$date_valid = '';
$sum_valid = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_payment_submit'])) {
    if($_POST['date'] == '') {
        $date_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($_POST['sum'] == '' || is_nan($_POST['sum'])) {
        $sum_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($form_valid) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if($conn->connect_error) {
            die('Ошибка соединения: '.$conn->connect_error);
        }
                
        $accountant_id = $_POST['accountant_id'];
        $order_id = $_POST['order_id'];
        $sum = $_POST['sum'];
        $date = $_POST['date'] == '' ? 'NULL' : DateTime::createFromFormat("d.m.Y", $_POST['date']);
        $timestamp = $_POST['date'] == '' ? 'NULL' : "from_unixtime(".$date->gettimestamp().")";
                
        $sql = "insert into payment (date, accountant_id, order_id, sum) values ($timestamp, $accountant_id, $order_id, $sum)";
                
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
        
// Если нет параметра order_id, переход к списку
if(!isset($_GET['order_id'])) {
    header('Location: '.APPLICATION.'/order/');
}
        
// Получение заказа
$contact_date = '';
$organization = '';
$last_name = '';
$first_name = '';
$middle_name = '';
$product = '';
$number = '';
$price = '';
        
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
$sql = "select date_format(c.date, '%d.%m.%Y') date, org.name organization, m.last_name, m.first_name, m.middle_name, "
        . "o.product, o.number, o.price "
        . "from _order o "
        . "left join contact c "
        . "left join manager m on c.manager_id = m.id "
        . "left join phone ph left join organization org on ph.organization_id = org.id "
        . "on c.phone_id = ph.id "
        . "on o.contact_id = c.id "
        . "where o.id=".$_GET['order_id'];
        
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
}
$conn->close();
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/jquery-ui.css" rel="stylesheet"/>
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
                            <h1>Оплата заказа</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/order/details.php?id=<?=$_GET['order_id'] ?>" title="Отмена" class="btn btn-outline-dark"><span class="font-awesome">&#xf0e2;</span>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <table class="table table-bordered">
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
                    <form method="post">
                        <input type="hidden" id="order_id" name="order_id" value="<?=$_GET['order_id'] ?>"/>
                        <input type="hidden" id="accountant_id" name="accountant_id" value="<?= GetManagerId() ?>"/>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="date">Дата</label>
                                    <input type="text" id="date" name="date" class="form-control<?=$date_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date']) ? $_POST['date'] : '' ?>" autocomplete="off" required="required"/>
                                    <div class="invalid-feedback">Дата обязательно</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="price">Сумма</label>
                                    <input type="number" step="0.01" id="sum" name="sum" class="form-control<?=$sum_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sum']) ? $_POST['sum'] : '' ?>" autocomplete="off" required="required"/>
                                    <div class="invalid-feedback">Сумма обязательно, число</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" id="create_payment_submit" name="create_payment_submit" class="btn btn-outline-dark">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script>
            $( function() {
                $.datepicker.regional['ru'] = {
                    closeText: 'Закрыть', // set a close button text
                    currentText: 'Сегодня', // set today text
                    monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'], // set month names
                    monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'], // set short month names
                    dayNames: ['Воскресенье','Понедельник','Вторник','Среда','Четверг','Пятница','Суббота'], // set days names
                    dayNamesShort: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'], // set short day names
                    dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'], // set more short days names
                    dateFormat: 'dd.mm.yy' // set format date
                };

                $.datepicker.setDefaults($.datepicker.regional['ru']);

                $("#date").datepicker();
            });
        </script>
    </body>
</html>