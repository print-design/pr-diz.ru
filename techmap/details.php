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

$sql = "select t.date, t.calculation_id, t.work_date, t.designer, t.printer, t.cutter, t.printings_number, t.rolls_number, t.reverse_print, "
        . "c.name name, c.unit, c.quantity, c.raport, "
        . "c.brand_name, c.other_brand_name, c.lamination1_brand_name, c.lamination1_other_brand_name, c.lamination2_brand_name, c.lamination2_other_brand_name, c.paints_count, "
        . "c.paint_1, c.paint_2, c.paint_3, c.paint_4, c.paint_5, c.paint_6, c.paint_7, c.paint_8, c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, c.percent_1, c.percent_2, c.percent_3, c.percent_4, c.percent_5, c.percent_6, c.percent_7, c.percent_8, "
        . "cus.name customer, u.last_name manager, "
        . "cr.dirty_width, cr.dirty_length "
        . "from techmap t "
        . "inner join calculation c on t.calculation_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join user u on c.manager_id = u.id "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "where t.id = $id";
$row = (new Fetcher($sql))->Fetch();

$date = DateTime::createFromFormat("Y-m-d H:i:s", $row['date']);
$calculation_id = $row['calculation_id'];
$work_date = $row['work_date'];
$designer = $row['designer'];
$printer = $row['printer'];
$cutter = $row['cutter'];
$printings_number = $row['printings_number'];
$rolls_number = $row['rolls_number'];
$name = $row['name'];
$unit = $row['unit'];
$quantity = $row['quantity'];
$raport = $row['raport'];
$brand_name = $row['brand_name'] == 'other' ? $row['other_brand_name'] : $row['brand_name'];
$lamination1_brand_name = $row['lamination1_brand_name'] == 'other' ? $row['lamination1_other_brand_name'] : $row['lamination1_brand_name'];
$lamination2_brand_name = $row['lamination2_brand_name'] == 'other' ? $row['lamination2_other_brand_name'] : $row['lamination2_brand_name'];
$paints_count = $row['paints_count'];
$paints = array();
for($i=1; $i<=$paints_count; $i++) {
    $paint = '';
    
    if($row['paint_'.$i] == 'cmyk') {
        $paint = $row['cmyk_'.$i];
    }
    elseif($row['paint_'.$i] == 'panton') {
        $paint = 'P'.$row['color_'.$i];
    }
    elseif($row['paint_'.$i] == 'white') {
        $paint = "белила";
    }
    elseif($row['paint_'.$i] == 'lacquer') {
        $paint = "лак";
    }
    
    $paint .= ' ('.$row['percent_'.$i].'%)';
    array_push($paints, $paint);
}
$customer = $row['customer'];
$manager = $row['manager'];
$dirty_width = $row['dirty_width'];
$dirty_length = $row['dirty_length'];
$reverse_print = $row['reverse_print'];
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
                    <td colspan="2"><?=rtrim(rtrim(number_format($quantity, 2, ",", " "), "0"), ",").' '.($unit == 'kg' ? 'кг' : 'шт') ?></td>
                </tr>
                <tr>
                    <th colspan="2">Количество тиражей</th>
                    <td colspan="2"><?=$printings_number ?></td>
                </tr>
                <tr>
                    <th rowspan="4">Бумага ролевая</th>
                    <th>Ширина роля (бумаги)</th>
                    <td colspan="2"><?=rtrim(rtrim(number_format($dirty_width, 2, ",", " "), "0"), ",") ?></td>
                </tr>
                <tr>
                    <th>Количество бумаги в метрах</th>
                    <td colspan="2"><?=rtrim(rtrim(number_format($dirty_length, 2, ",", " "), "0"), ",") ?></td>
                </tr>
                <tr>
                    <th>Количество ролей</th>
                    <td colspan="2"><?=$rolls_number ?></td>
                </tr>
                <tr>
                    <th>Наименование, маркировка бумаги</th>
                    <td colspan="2"><?=$brand_name.' '.(empty($lamination1_brand_name) ? '' : '+ '.$lamination1_brand_name).(empty($lamination2_brand_name) ? '' : '+ '.$lamination2_brand_name) ?></td>
                </tr>
                <tr>
                    <td class="p-0">
                        <table class="table mb-0">
                            <tr>
                                <td rowspan="2">Печать</td>
                                <td>прямая</td>
                                <td><?php if($reverse_print == 0): ?><i class="fas fa-check"></i><?php endif; ?></td>
                            </tr>
                            <tr>
                                <td>оборотная</td>
                                <td><?php if($reverse_print == 1): ?><i class="fas fa-check"></i><?php endif; ?></td>
                            </tr>
                        </table>
                    </td>
                    <th>Красочность</th>
                    <td colspan="2"><?=implode(' + ', $paints) ?></td>
                </tr>
                <tr>
                    <th colspan="2">Рапорт, число зубьев</th>
                    <td colspan="2"><?=$raport ?></td>
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