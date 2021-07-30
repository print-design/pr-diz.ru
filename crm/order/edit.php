<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
       
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$number_valid = '';
$price_valid = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_edit_submit'])) {
    if($_POST['number'] != '' && !is_numeric($_POST['number'])) {
        $number_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($_POST['price'] != '' && is_nan($_POST['price'])) {
        $price_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($form_valid) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if($conn->connect_error) {
            die('Ошибка соединения: '.$conn->connect_error);
        }
                
        $id = $_POST['id'];
        $product = addslashes($_POST['product']);
        $number = $_POST['number'] == '' ? 'NULL' : $_POST['number'];
        $price = $_POST['price'] == '' ? 'NULL' : $_POST['price'];
        $shipment_date = $_POST['shipment_date'] == '' ? 'NULL' : DateTime::createFromFormat("d.m.Y", $_POST['shipment_date']);
        $shipment_timestamp = $_POST['shipment_date'] == '' ? 'NULL' : "from_unixtime(".$shipment_date->gettimestamp().")";
        $contract_date = $_POST['contract_date'] == '' ? 'NULL' : DateTime::createFromFormat("d.m.Y", $_POST['contract_date']);
        $contract_timestamp = $_POST['contract_date'] == '' ? 'NULL' : "from_unixtime(".$contract_date->gettimestamp().")";
        $bill_date = $_POST['bill_date'] == '' ? 'NULL' : DateTime::createFromFormat("d.m.Y", $_POST['bill_date']);
        $bill_timestamp = $_POST['bill_date'] == '' ? 'NULL' : "from_unixtime(".$bill_date->gettimestamp().")";
                
        $sql = "update _order set product='$product', number=$number, price=$price, shipment_date=$shipment_timestamp, contract_date=$contract_timestamp, bill_date=$bill_timestamp where id=$id";
                
        $conn->query('set names utf8');
        if ($conn->query($sql) === true) {
            header('Location: '.APPLICATION.'/order/details.php?id='.$id);
        }
        else {
            $error_message = $conn->error;
        }
                
        $conn->close();
    }
}
        
// Если нет параметра id, переход к списку
if(!isset($_GET['id'])) {
    header('Location; '.APPLICATION.'/order/');
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
        
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
$sql = "select date_format(c.date, '%d.%m.%Y') date, org.name organization, m.last_name, m.first_name, m.middle_name, "
        . "o.product, o.number, o.price, "
        . "date_format(o.shipment_date, '%d.%m.%Y') shipment_date, date_format(o.contract_date, '%d.%m.%Y') contract_date, date_format(o.bill_date, '%d.%m.%Y') bill_date "
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
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                    <h1>Редактирование заказа</h1>
                    </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/order/details.php?id=<?=$_GET['id'] ?>" class="btn btn-outline-dark"><span class="font-awesome">&#xf0e2;</span>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr/>
                    <p><strong>Дата контакта:</strong> <?=$contact_date ?>.</p>
                    <p><strong>Менеджер:</strong> <?=$last_name." ".$first_name." ".$middle_name ?>.</p>
                    <p><strong>Предприятие:</strong> <?=$organization ?></p>
                    <hr/>
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$_GET['id'] ?>"/>
                        <div class="form-group">
                            <label for="product">Товар</label>
                            <textarea rows="5" id="product" name="product" class="form-control"><?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product']) ? htmlentities($_POST['product']) : htmlentities($product) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="number">Количество</label>
                                    <input type="number" step="1" id="number" name="number" class="form-control<?=$number_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['number']) ? $_POST['number'] : $number ?>" autocomplete="off"/>
                                    <div class="invalid-feedback">Количество должно быть целым числом</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="price">Цена (за 1 шт.)</label>
                                    <input type="number" step="0.01" id="price" name="price" class="form-control<?=$price_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['price']) ? $_POST['price'] : $price ?>" autocomplete="off"/>
                                    <div class="invalid-feedback">Цена должна быть числом</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="shipment_date">Дата отгрузки</label>
                                    <input type="text" id="shipment_date" name="shipment_date" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['shipment_date']) ? $_POST['shipment_date'] : $shipment_date ?>" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="contract_date">Дата заключения договора</label>
                                    <input type="text" id="contract_date" name="contract_date" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contract_date']) ? $_POST['contract_date'] : $contract_date ?>" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="bill_date">Дата выставления счёта</label>
                                    <input type="text" id="bill_date" name="bill_date" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bill_date']) ? $_POST['bill_date'] : $bill_date ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="order_edit_submit" name="order_edit_submit">Сохранить</button>
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

                $("#shipment_date").datepicker();
                $("#contract_date").datepicker();
                $("#bill_date").datepicker();
            });
        </script>
    </body>
</html>