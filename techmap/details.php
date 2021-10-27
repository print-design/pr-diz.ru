<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Перенаправление при пустом id
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION.'/techmap/');
}

if(null !== filter_input(INPUT_POST, 'add-date-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $work_date = filter_input(INPUT_POST, 'work_date');
    
    if(!empty($work_date)) {
        $sql = "update techmap set work_date='$work_date' where id=$id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

if(null !== filter_input(INPUT_POST, 'remove-date-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "update techmap set work_date=NULL where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select t.date, t.calculation_id, t.work_date, t.designer, t.printer, t.cutter, "
        . "c.name name, c.unit, c.quantity, cus.name customer, u.last_name manager "
        . "from techmap t "
        . "inner join calculation c on t.calculation_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join user u on c.manager_id = u.id "
        . "where t.id = $id";
$row = (new Fetcher($sql))->Fetch();

$date = DateTime::createFromFormat("Y-m-d H:i:s", $row['date']);
$calculation_id = $row['calculation_id'];
$work_date = $row['work_date'];
$designer = $row['designer'];
$printer = $row['printer'];
$cutter = $row['cutter'];
$name = $row['name'];
$unit = $row['unit'];
$quantity = $row['quantity'];
$customer = $row['customer'];
$manager = $row['manager'];
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/techmap/<?= BuildQueryRemove("id") ?>">К списку</a>
            <a class="btn btn-outline-dark ml-3" href="<?=APPLICATION ?>/calculation/calculation.php?id=<?=$calculation_id ?>">Расчет</a>
            <h1 style="font-size: 32px; font-weight: 600;">Заявка на флекс-печать от <?= $date->format('d').' '.$GLOBALS['months_genitive'][intval($date->format('m'))].' '.$date->format('Y') ?> г</h1>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 25%;">Менеджер</th>
                    <th style="width: 25%;">Дизайнер</th>
                    <th style="width: 25%;">Печатник</th>
                    <th style="width: 25%;">Резчик</th>
                </tr>
                <tr>
                    <td><?=$manager ?></td>
                    <td><?=$designer ?></td>
                    <td><?=$printer ?></td>
                    <td><?=$cutter ?></td>
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
                    <th colspan="2">Дата печати тиража</th>
                    <td colspan="2">
                        <form method="post" class="form-inline">
                            <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="date" id="work_date" name="work_date" value="<?=$work_date ?>" class="form-control" />
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-dark" name="add-date-submit">OK</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ml-3">
                                <button type="submit" class="btn btn-outline-dark" name="remove-date-submit"<?= empty($work_date) ? " disabled='disabled'" : "" ?>>В черновики</button>
                            </div>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>