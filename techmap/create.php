<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

const TECHMAP_CREATED_STATUS_ID = 6;

if(null !== filter_input(INPUT_POST, 'create-submit')) {
    $calculation_id = filter_input(INPUT_POST, 'calculation_id');
    $designer = addslashes(filter_input(INPUT_POST, 'designer'));
    $printer = addslashes(filter_input(INPUT_POST, 'printer'));
    $cutter = addslashes(filter_input(INPUT_POST, 'cutter'));
    $printings_number = filter_input(INPUT_POST, 'printings_number');
    if(is_nan($printings_number)) $printings_number = "NULL";
    
    $sql = "insert into techmap (calculation_id, designer, printer, cutter, printings_number) "
            . "values($calculation_id, '$designer', '$printer', '$cutter', $printings_number)";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    $techmap_id = $executer->insert_id;
    
    if(empty($error_message) && !empty($techmap_id)) {
        $sql = "update calculation set status_id = ".TECHMAP_CREATED_STATUS_ID." where id = $calculation_id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/techmap/details.php?id='.$techmap_id);
        }
    }
}

// Открыть можно только через кнопку "Составить технологическую карту"
$calculation_id = filter_input(INPUT_POST, 'calculation_id');

if(empty($calculation_id)) {
    header('Location: '.APPLICATION.'/techmap/');
}

// Получение объекта расчёта
$name = '';
$unit = '';
$quantity = '';
$customer = '';
$manager = '';

$sql = "select c.name name, c.unit, c.quantity, cus.name customer, u.last_name manager "
        . "from calculation c "
        . "inner join user u on c.manager_id = u.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "where c.id = $calculation_id";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $name = $row['name'];
    $unit = $row['unit'];
    $quantity = $row['quantity'];
    $customer = $row['customer'];
    $manager = $row['manager'];
}
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
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/calculation/calculation.php?id=<?=$calculation_id ?>">Отмена</a>
            <h1 style="font-size: 32px; font-weight: 600;">Новая заявка на флекс-печать</h1>
            <form method="post">
                <input type="hidden" name="calculation_id" value="<?=$calculation_id ?>" />
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 25%;">Менеджер</th>
                        <th style="width: 25%;">Дизайнер</th>
                        <th style="width: 25%;">Печатник</th>
                        <th style="width: 25%;">Резчик</th>
                    </tr>
                    <tr>
                        <td><?=$manager ?></td>
                        <td><input type="text" name="designer" value="<?= filter_input(INPUT_POST, 'designer') ?>" class="form-control" /></td>
                        <td><input type="text" name="printer" value="<?= filter_input(INPUT_POST, 'printer') ?>" class="form-control" /></td>
                        <td><input type="text" name="cutter" value="<?= filter_input(INPUT_POST, 'cutter') ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <th colspan="2">Наименование заказа</th>
                        <td colspan="2"><?= $customer.', '.$name ?></td>
                    </tr>
                    <tr>
                        <th colspan="2">Общий тираж</th>
                        <td colspan="2"><?=$quantity.' '.($unit == 'kg' ? 'кг' : 'шт') ?></td>
                    </tr>
                    <tr>
                        <th colspan="2">Количество тиражей</th>
                        <td colspan="2"><input type="number" min="1" step="1" name="printings_number" class="form-control" style="width: 150px;" value="<?= filter_input(INPUT_POST, 'printings_number') ?>" /></td>
                    </tr>
                </table>
                <button type="submit" name="create-submit" class="btn btn-dark" style="width: 200px;">Создать</button>
            </form>
        </div>
    </body>
</html>