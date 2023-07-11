<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
        
if(IsInRole('admin')) {
    // Смена менеджера
    if($_SERVER['REQUEST_METHOD'] == 'POST'
        && isset($_POST['change_manager_submit'])
        && isset($_POST['manager_id']) && $_POST['manager_id'] != ''
        && isset($_POST['id']) && $_POST['id'] != '') {
                
    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                
    if($conn->connect_error) {
        die('Ошибка соединения: '.$conn->connect_error);
    }
                
    $id = $_POST['id'];
    $manager_id = $_POST['manager_id'];
                
    $sql = "update organization set manager_id=$manager_id where id=$id";
                
    $conn->query('set names utf8');
    if ($conn->query($sql) === true) {
        header('Location: '.APPLICATION.'/organization/details.php?id='.$id);
    }
    else {
        $error_message = $conn->error;
    }
                
    $conn->close();
    }
}
        
// Удаление перспективного планирования
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_perspective_planning_submit'])) {
    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
            
    if($conn->connect_error) {
        die('Ошибка соединения: '.$conn->connect_error);
    }
            
    $id = $_POST['id'];
    $sql = "delete from perspective_planning where id=$id";
            
    $conn->query('set names utf8');
    if (!$conn->query($sql) === true) {
        $error_message = $conn->error;
    }
            
    $conn->close();
}
        
// Если нет параметра id, переход к списку
if(!isset($_GET['id'])) {
    header('Location: '.APPLICATION.'/organization/');
}
        
// Получение объекта
$date = '';
$name = '';
$production = '';
$address = '';
$manager_id = '';
$last_name = '';
$first_name = '';
$middle_name = '';
        
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
$sql = "select date_format(o.date, '%d.%m.%Y') date, o.name, o.production, o.address, o.manager_id, m.last_name, m.first_name, m.middle_name 
    from organization o inner join manager m on o.manager_id = m.id where o.id=".$_GET['id'];
        
if($conn->connect_error) {
    die('Ошибка соединения: ' . $conn->connect_error);
}
        
$conn->query('set names utf8');
$result = $conn->query($sql);
if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
    $date = $row['date'];
    $name = $row['name'];
    $production = $row['production'];
    $address = $row['address'];
    $manager_id = $row['manager_id'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $middle_name = $row['middle_name'];
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
                <div class="col-12 col-md-5">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1><?=$name ?></h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/organization/edit.php?id=<?=$_GET['id'] ?>" class="btn btn-outline-dark"><span class="font-awesome">&#xf044;</span>&nbsp;Редактировать</a>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Менеджер</th>
                            <td>
                                <?=$last_name." ".$first_name." ".$middle_name ?>
                                <?php
                                if(IsInRole('admin')) {
                                ?>
                                <br/><br/>
                                <form class="form-inline" method="post">
                                    <input type="hidden" id="id" name="id" value="<?=$_GET['id'] ?>"/>
                                    <div class="form-group">
                                        <select class="form-control" id="manager_id" name="manager_id" required="required">
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
                                                        if($manager_id != $row['id']) {
                                                            echo '<option value='.$row['id'].'>'.$row['last_name'].' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['first_name'], 0, 1).'.' : $row['first_name']).' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['middle_name'], 0, 1).'.' : $row['middle_name']).'</option>';
                                                        }
                                                    }
                                                }
                                                $conn->close();
                                                ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-outline-dark" id="change_manager_submit" name="change_manager_submit">Сменить менеджера</button>
                                </form>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Создано</th>
                            <td><?=$date ?></td>
                        </tr>
                        <tr>
                            <th>Продукция</th>
                            <td class="newline"><?=$production ?></td>
                        </tr>
                        <tr>
                            <th>Адрес</th>
                            <td class="newline"><?=$address ?></td>
                        </tr>
                    </table>
                    <h2>Контактные лица&nbsp;<a href="<?=APPLICATION ?>/person/create.php?id=<?=$_GET['id'] ?>" class="btn btn-outline-dark btn-sm"><span class="font-awesome">&#xf067;</span></a></h2>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>ФИО</th>
                                <th>Должность (роль)</th>
                                <th>Телефон</th>
                                <th>E-mail</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                            $sql = "select id, name, position, phone, email from person where organization_id=".$_GET['id'];
                                    
                            if($conn->connect_error) {
                                die('Ошибка соединения: ' . $conn->connect_error);
                            }
                            
                            $conn->query('set names utf8');
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>"
                                        ."<td class='text-nowrap'><a href='".APPLICATION."/contact/create.php?person=".$row['id']."' title='Новый контакт ".$row['name']."' class='btn btn-outline-dark btn-sm'><span class='font-awesome'>&#xf095;</span></a>&nbsp;".$row['name']."</td>"
                                            ."<td>".$row['position']."</td>"
                                            ."<td>".$row['phone']."</td>"
                                            ."<td>".$row['email']."</td>"
                                            ."<td><a href='".APPLICATION."/person/edit.php?id=".$row['id']."' title='Редактировать' class='btn btn-outline-dark btn-sm'><span class='font-awesome'>&#xf044;</span></a></td>"
                                            ."</tr>";
                                }
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 col-md-7">
                    <h2>Контакты</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Менеджер</th>
                                <th>Конт. лицо</th>
                                <th>Должность</th>
                                <th>Результат</th>
                                <th>Действ.</th>
                                <th>След.</th>
                                <th>Комментарий</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $organization_id = $_GET['id'];
                            $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                            $sql = "select c.id id, date_format(c.date, '%d.%m.%Y') date, u.first_name, u.middle_name, u.last_name, r.name result, r.efficient, date_format(c.next_date, '%d.%m.%Y') next_date, "
                                    . "p.name, p.position, p.phone, p.email, c.comment "
                                    . "from person p "
                                    . "inner join contact c on c.person_id = p.id "
                                    . "inner join manager u on c.manager_id = u.id "
                                    . "inner join contact_result r on c.result_id = r.id "
                                    . "where p.organization_id=".$_GET['id']." "
                                    . "order by c.id desc";
                            
                            if($conn->connect_error) {
                                die('Ошибка соединения: ' . $conn->connect_error);
                            }
                            
                            $conn->query('set names utf8');
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>"
                                        ."<td>".$row['date']."</td>"
                                        ."<td>".$row['last_name'].' '.mb_substr($row['first_name'], 0, 1, 'UTF-8').'. '.mb_substr($row['middle_name'], 0, 1, 'UTF-8').".</td>"
                                        ."<td>".$row['name']."</td>"
                                        ."<td>".$row['position']."</td>"
                                        ."<td>".$row['result']."</td>"
                                        ."<td>".($row['efficient'] == '1' ? '&#x2713;' : '')."</td>"
                                        ."<td>".$row['next_date']."</td>"
                                        ."<td>".$row['comment']."</td>"
                                        ."<td><a href='".APPLICATION."/contact/edit.php?id=".$row['id']."' class='btn btn-outline-dark'><span class='font-awesome'>&#xf044;</span></a></td>"
                                        ."</tr>";
                                }
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <br/>
            <hr/>
            <br/>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <h2 id="perspective_planning">Перспективное планирование</h2>
                </div>
                <div class="p-1">
                    <a href="<?=APPLICATION ?>/perspective_planning/create.php?organization_id=<?=$_GET['id'] ?>" class="btn btn-outline-dark"><span class="font-awesome">&#xf067;</span>&nbsp;Добавить</a>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Дата&nbsp;&ndash;</th>
                        <th>Дата</th>
                        <th>Дата&nbsp;+</th>
                        <th>Затраты</th>
                        <th>Тип плёнки</th>
                        <th>Толщина плёнки</th>
                        <th>Ширина плёнки</th>
                        <th>Длина плёнки</th>
                        <th>Вес плёнки</th>
                        <th>Цена плёнки</th>
                        <th>Вероятность (%)</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $organization_id = $_GET['id'];
                    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                    $sql = "select pp.id, pp.date, date_format(pp.date, '%d.%m.%Y') fdate, pp.date_minus, date_format(pp.date_minus, '%d.%m.%Y') fdate_minus, pp.date_plus, date_format(pp.date_plus, '%d.%m.%Y') fdate_plus, pp.expenses, f.name film, pp.film_thickness, pp.film_width, pp.film_length, pp.film_weight, pp.film_price, pp.probability "
                            . "from perspective_planning pp left join film f on pp.film_id = f.id "
                            . "where pp.organization_id=$organization_id "
                            . "order by pp.date desc";
                    
                    if($conn->connect_error) {
                        die('Ошибка соединения: ' . $conn->connect_error);
                    }
                    
                    $conn->query('set names utf8');
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>"
                            ."<td>".$row['fdate_minus']."</td>"
                            ."<td>".$row['fdate']."</td>"
                            ."<td>".$row['fdate_plus']."</td>"
                            ."<td>".$row['expenses']."</td>"
                            ."<td>". htmlentities($row['film'])."</td>"
                            ."<td>".$row['film_thickness']."</td>"
                            ."<td>".$row['film_width']."</td>"
                            ."<td>".$row['film_length']."</td>"
                            ."<td>".$row['film_weight']."</td>"
                            ."<td>".$row['film_price']."</td>"
                            ."<td>".$row['probability']."</td>"
                            ."<td><a href='".APPLICATION."/perspective_planning/edit.php?id=".$row['id']."' class='btn btn-outline-dark'><span class='font-awesome'>&#xf044;</span></a></td>"
                            ."<td>"
                            ."<form method='post'>"
                            ."<input type='hidden' id='id' name='id' value='".$row['id']."' />"
                            ."<button type='submit' id='delete_perspective_planning_submit' name='delete_perspective_planning_submit' class='btn btn-outline-dark' onclick='javascript: return confirm(\"Действительно удалить?\");'><span class='font-awesome'>&#xf1f8;</span></button>"
                            ."</form>"
                            ."</td>"
                            ."</tr>";
                        }
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>