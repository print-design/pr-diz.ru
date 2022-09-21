<?php
include '../include/topscripts.php';
include '../calculation/status_ids.php';
include '../calculation/calculation.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан calculation_id, направляем к списку технических карт
if(null === filter_input(INPUT_GET, 'calculation_id')) {
    header('Location: '.APPLICATION.'/techmap/');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

// Создание технологической карты
if(null !== filter_input(INPUT_POST, 'techmap_submit')) {
    if(empty(filter_input(INPUT_GET, 'calculation_id'))) {
        $error_message == "Не указан ID расчёта";
        $form_valid = false;
    }
    
    if($form_valid) {
        $calculation_id = filter_input(INPUT_GET, 'calculation_id');
        
        $sql = "insert into techmap (calculation_id) values($calculation_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $id = $executer->insert_id;
        
        if(empty($error_message)) {
            $sql = "update calculation set status_id = ".TECHMAP." where id = $calculation_id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message) && !empty($id)) {
            header("Location: details.php?id=$id");
        }
    }
}

// Получение объекта
$calculation_id = filter_input(INPUT_GET, 'calculation_id');

$sql = "select c.date, c.customer_id, c.name calculation, c.quantity, c.unit, "
        . "lam1f.name lamination1_film_name, c.lamination1_individual_film_name, lam2f.name lamination2_film_name, c.lamination2_individual_film_name, "
        . "cus.name customer, "
        . "u.last_name, u.first_name, "
        . "wt.name work_type, "
        . "cr.length_pure_1, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
        . "from calculation c "
        . "left join film_variation lam1fv on c.lamination1_film_variation_id = lam1fv.id "
        . "left join film lam1f on lam1fv.film_id = lam1f.id "
        . "left join film_variation lam2fv on c.lamination2_film_variation_id = lam2fv.id "
        . "left join film lam2f on lam2fv.film_id = lam2f.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join user u on c.manager_id = u.id "
        . "inner join work_type wt on c.work_type_id = wt.id "
        . "left join calculation_result cr on cr.calculation_id = c.id "
        . "where c.id = $calculation_id";
$fetcher = new Fetcher($sql);
$row = $fetcher->Fetch();

$calculation_date = $row['date'];
$customer_id = $row['customer_id'];
$calculation = $row['calculation'];
$quantity = $row['quantity'];
$unit = $row['unit'];

$lamination1_film_name = $row['lamination1_film_name'];
$lamination1_individual_film_name = $row['lamination1_individual_film_name'];
$lamination2_film_name = $row['lamination2_film_name'];
$lamination2_individual_film_name = $row['lamination2_individual_film_name'];

$customer = $row['customer'];
$last_name = $row['last_name'];
$first_name = $row['first_name'];
$work_type = $row['work_type'];
$length_pure_1 = $row['length_pure_1'];
$num_for_customer = $row['num_for_customer'];

$lamination = "нет";
if(!empty($lamination1_film_name) || !empty($lamination1_individual_film_name)) $lamination = "1";
if(!empty($lamination2_film_name) || !empty($lamination2_individual_film_name)) $lamination = "2";
$date = date('Y-m-d H:i:s');
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
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/calculation/details.php?id=<?= filter_input(INPUT_GET, 'calculation_id') ?>">К расчету</a>
            <h1>Составление тех. карты</h1>
            <div class="name">Заказчик: <?=$customer ?></div>
            <div class="name">Наименование: <?=$calculation ?></div>
            <div class="subheader">№<?=$customer_id ?>-<?=$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation_date)->format('d.m.Y') ?></div>
            <h2>Остальная информация</h2>
            <table>
                <tr>
                    <th>Карта составлена</th>
                    <td><?= DateTime::createFromFormat('Y-d-m H:i:s', $date)->format('d.m.Y H:i') ?></td>
                </tr>
                <tr>
                    <th>Заказчик</th>
                    <td><?=$customer ?></td>
                </tr>
                <tr>
                    <th class="pb-3">Название заказа</th>
                    <td class="pb-3"><?=$calculation ?></td>
                </tr>
                <tr>
                    <th>Объем заказа</th>
                    <td><strong><?=$quantity ?> <?=$unit == 'kg' ? 'кг' : 'шт' ?></strong> <?= CalculationBase::Display($length_pure_1, 2) ?> м</td>
                </tr>
                <tr>
                    <th>Менеджер</th>
                    <td><?=$first_name ?> <?=$last_name ?></td>
                </tr>
                <tr>
                    <th>Тип работы</th>
                    <td><?=$work_type ?></td>
                </tr>
            </table>
            <div class="row">
                <div class="col-4">
                    <h2>Информация для печатника</h2>
                </div>
                <div class="col-4">
                    <h2>Информация для ламинации</h2>
                    <div class="subheader">Кол-во ламинаций: <?=$lamination ?></div>
                </div>
                <div class="col-4">
                    <h2>Информация для резчика</h2>
                </div>
            </div>
            <form method="post">
                <input type="hidden" name="calculation_id" value="<?= filter_input(INPUT_GET, 'calculation_id') ?>" />
                <button type="submit" name="techmap_submit" class="btn btn-outline-dark draft mt-3" style="width: 200px;">Сохранить</button>
            </form>
        </div> 
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>