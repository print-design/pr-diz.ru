<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';

// Если нет параметра id
// переходим к списку предприятий
if(!isset($_GET['id'])) {
    header('Location: '.APPLICATION.'/organization/');
}
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$result_id_valid = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contact_edit_submit'])) {
    if($_POST['result_id'] == '') {
        $result_id_valid = ISINVALID;
        $form_valid = false;
    }
            
    if($form_valid) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if($conn->connect_error) {
            die('Ошибка соединения: '.$conn->connect_error);
        }
                
        $id = $_POST['id'];
        $result_id = $_POST['result_id'];
        $next_date = $_POST['next_date'] == '' ? 'NULL' : DateTime::createFromFormat("d.m.Y", $_POST['next_date']);
        $next_timestamp = $_POST['next_date'] == '' ? 'NULL' : "from_unixtime(".$next_date->gettimestamp().")";
        $comment = addslashes($_POST['comment']);
                
        $sql = "update contact set result_id=$result_id, next_date=$next_timestamp, comment='$comment' where id=$id";
                
        $conn->query('set names utf8');
        if ($conn->query($sql) === true) {
            $is_order = 0;
            $organization_id = '';
                    
            $sql_order = 'select p.organization_id, r.is_order from contact c '
                    . 'inner join person p on c.person_id = p.id '
                    . 'inner join contact_result r on c.result_id = r.id where c.id='.$id;
                    
            $result_order = $conn->query($sql_order);
                    
            if($result_order->num_rows > 0 && $row_order = $result_order->fetch_assoc()) {
                $organization_id = $row_order['organization_id'];
                $is_order = $row_order['is_order'];
            }
                    
            if($is_order > 0) {
                $sql_order = "insert into _order (contact_id) values ($contact_id)";
                        
                if($conn->query($sql_order) === true) {
                    $order_id = $conn->insert_id;
                    header('Location: '.APPLICATION.'/order/edit.php?id='.$order_id);
                }
            }
            else if($organization_id != '') {
                header('Location: '.APPLICATION.'/organization/details.php?id='.$organization_id);
            }
        }
        else {
            $error_message = $conn->error;
        }
                
        $conn->close();
    }
}
        
// Получение объекта
$person = '';
$organization_id = '';
$organization = '';
$production = '';
$position = '';
$phone = '';
$email = '';
$result_id = '';
$next_date = '';
$comment = '';
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
if($conn->connect_error) {
    die('Ошибка соединения: ' . $conn->connect_error);
}
        
$sql = "select o.id organization_id, o.name organization, o.production, p.name person, p.position, p.phone, p.email, c.result_id, date_format(c.next_date, '%d.%m.%Y') next_date, c.comment "
        . "from person p "
        . "inner join organization o on p.organization_id = o.id "
        . "inner join contact c on c.person_id = p.id "
        . "where c.id=".$_GET['id'];
        
$conn->query('set names utf8');
$result = $conn->query($sql);
        
if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
    $person = $row['person'];
    $organization_id = $row['organization_id'];
    $organization = $row['organization'];
    $production = $row['production'];
    $position = $row['position'];
    $phone = $row['phone'];
    $email = $row['email'];
    $result_id = $row['result_id'];
    $next_date = $row['next_date'];
    $comment = htmlentities($row['comment']);
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
                            <h1>Контакт: <?=$person ?></h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/organization/details.php?id=<?=$organization_id ?>" class="btn btn-outline-dark"><span class="font-awesome">&#xf060;</span>&nbsp;К предприятию</a>
                        </div>
                    </div>
                    <table class='table'>
                        <tbody>
                            <tr><th>Предприятие</th><td><?=$organization ?></td></tr>
                            <tr><th>Продукция</th><td><?=$production ?></td></tr>
                            <tr><th>Контактное лицо</th><td><?=$person ?></td></tr>
                            <tr><th>Должность (роль)</th><td><?=$position ?></td></tr>
                            <tr><th>Телефон</th><td><?=$phone ?></td></tr>
                            <tr><th>E-mail</th><td><?=$email ?></td></tr>
                        </tbody>
                    </table>
                    <hr />
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$_GET['id'] ?>"/>
                        <div class="form-group">
                            <label for="result_id">Результат контакта</label>
                            <select id="result_id" name="result_id" class="form-control<?=$result_id_valid ?>" required="required">
                                <option value="">...</option>
                                <?php
                                $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                                if($conn->connect_error) {
                                    die('Ошибка соединения: ' . $conn->connect_error);
                                }
                                $conn->query('set names utf8');
                                $result = $conn->query("select id, name from contact_result order by ordinal");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $selected = '';
                                        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['result_id'])) {
                                            $result_id = $_POST['result_id'];
                                        }
                                        if($result_id == $row['id']) {
                                            $selected = " selected='selected'";
                                        }
                                        echo "<option value='".$row['id']."'".$selected.">".$row["name"]."</option>";
                                    }
                                }
                                $conn->close();
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="next_date">Дата следующего контакта</label>
                            <input type="text" id="next_contact_date" name="next_date" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['next_date']) ? $_POST['next_date'] : $next_date ?>" autocomplete="off" />
                            <div class="invalid-feedback">Дата следующего звонка обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Комментарий</label>
                            <textarea id="comment" name="comment" class="form-control" rows="5"><?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) ? $_POST['comment'] : $comment ?></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="contact_create_submit" name="contact_edit_submit">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
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

                $("#next_contact_date").datepicker();
            });
        </script>
    </body>
</html>