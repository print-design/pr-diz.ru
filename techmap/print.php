<?php
include '../include/topscripts.php';

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select t.date, t.calculation_id, t.work_date, t.work_shift, t.designer, t.printer, t.cutter, t.printings_number, t.rolls_number, t.information, "
        . "t.reverse_print, t.self_adhesive, t.spool, t.number_per_spool, t.winding, t.roll_type, "
        . "c.name name, c.unit, c.quantity, c.work_type_id, "
        . "c.brand_name, c.individual_brand_name, c.lamination1_brand_name, c.lamination1_individual_brand_name, c.lamination2_brand_name, c.lamination2_individual_brand_name, "
        . "c.streams_number, c.label_length, c.raport, c.ink_number, "
        . "c.ink_1, c.ink_2, c.ink_3, c.ink_4, c.ink_5, c.ink_6, c.ink_7, c.ink_8, c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
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
$work_shift = $row['work_shift'];
$designer = $row['designer'];
$printer = $row['printer'];
$cutter = $row['cutter'];
$printings_number = $row['printings_number'];
$rolls_number = $row['rolls_number'];
$information = $row['information'];
$name = $row['name'];
$unit = $row['unit'];
$quantity = $row['quantity'];
$work_type_id = $row['work_type_id'];
$brand_name = $row['brand_name'] == INDIVIDUAL ? $row['individual_brand_name'] : $row['brand_name'];
$lamination1_brand_name = $row['lamination1_brand_name'] == INDIVIDUAL ? $row['lamination1_individual_brand_name'] : $row['lamination1_brand_name'];
$lamination2_brand_name = $row['lamination2_brand_name'] == INDIVIDUAL ? $row['lamination2_individual_brand_name'] : $row['lamination2_brand_name'];
$streams_number = $row['streams_number'];
$label_length = $row['label_length'];
$raport = $row['raport'];
$ink_number = $row['ink_number'];
$inks = array();
for($i=1; $i<=$ink_number; $i++) {
    $ink = '';
    
    if($row['ink_'.$i] == 'cmyk') {
        $ink = $row['cmyk_'.$i];
    }
    elseif($row['ink_'.$i] == 'panton') {
        $ink = 'P'.$row['color_'.$i];
    }
    elseif($row['ink_'.$i] == 'white') {
        $ink = "белила";
    }
    elseif($row['ink_'.$i] == 'lacquer') {
        $ink = "лак";
    }
    
    array_push($inks, $ink);
}
$customer = $row['customer'];
$manager = $row['manager'];
$dirty_width = $row['dirty_width'];
$dirty_length = $row['dirty_length'];
$reverse_print = $row['reverse_print'];
$self_adhesive = $row['self_adhesive'];
$spool = $row['spool'];
$number_per_spool = $row['number_per_spool'];
$winding = $row['winding'];
$roll_type = $row['roll_type'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Технологическая карта</title>
        <style> 
            .roll_type {
                display: inline-block;
                border: solid 3px white;
            }
            
            .roll_type.selected {
                border: solid 3px darkgray;
            }
            
            table.table {
                font-family: 'Times New Roman', 'serif';
                border-collapse: collapse;
            }
            
            table.table tr th {
                text-align: left;
                font-weight: bold;
                color: black;
                font-size: 20px;
                border: solid 1px lightgray;
                padding: 5px;
            }
            
            table.table tr td {
                font-size: 20px;
                border: solid 1px lightgray;
                padding: 5px;
            }
            
            table.table tr td form {
                font-family: 'Arial', 'sans serif';
            }
        </style>
    </head>
    <body>
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
                <th colspan="2">Красочность</th>
                <td colspan="2"><?=implode(' + ', $inks) ?></td>
            </tr>
            <tr>
                <th colspan="2">Печать</th>
                <td colspan="2"><?=$reverse_print === null ? "" : ($reverse_print == 0 ? "прямая" : "оборотная") ?></td>
            </tr>
            <tr>
                <th colspan="2">Рапорт, число зубьев</th>
                <td colspan="2"><?=$raport ?></td>
            </tr>
            <tr>
                <th colspan="2">Размер этикетки</th>
                <td colspan="2"><?=$work_type_id == 1 ? "" : $label_length ?></td>
            </tr>
            <tr>
                <th colspan="2">Количество ручьев</th>
                <td colspan="2"><?=$streams_number ?></td>
            </tr>
            <tr>
                <th colspan="2">Способ наклейки (ручная, автомат)</th>
                <td colspan="2"><?=$self_adhesive === null ? "" : ($self_adhesive == 0 ? "ручная" : "автомат") ?></td>
            </tr>
            <tr>
                <th rowspan="3">Резка и размотка продукции</th>
                <th>Размер шпули (внутренний диаметр)</th>
                <td colspan="2"><?=$spool ?></td>
            </tr>
            <tr>
                <th>К-во этикеток на шпуле</th>
                <td colspan="2"><?=$number_per_spool ?></td>
            </tr>
            <tr>
                <th>Намотка, в метрах</th>
                <td colspan="2"><?=$winding ?></td>
            </tr>
            <tr>
                <th colspan="4">
                    <div class="roll-selector mt-3">
                        <div class="roll_type<?=$roll_type == 1 ? " selected" : "" ?>"><image src="../images/rolls/2-50.gif" style="height: 50px; width: auto;" /></div>
                        <div class="roll_type<?=$roll_type == 2 ? " selected" : "" ?>"><image src="../images/rolls/2-50.gif" style="height: 50px; width: auto;" /></div>
                        <div class="roll_type<?=$roll_type == 3 ? " selected" : "" ?>"><image src="../images/rolls/3-50.gif" style="height: 50px; width: auto;" /></div>
                        <div class="roll_type<?=$roll_type == 4 ? " selected" : "" ?>"><image src="../images/rolls/4-50.gif" style="height: 50px; width: auto;" /></div>
                        <div class="roll_type<?=$roll_type == 5 ? " selected" : "" ?>"><image src="../images/rolls/5-50.gif" style="height: 50px; width: auto;" /></div>
                        <div class="roll_type<?=$roll_type == 6 ? " selected" : "" ?>"><image src="../images/rolls/6-50.gif" style="height: 50px; width: auto;" /></div>
                        <div class="roll_type<?=$roll_type == 7 ? " selected" : "" ?>"><image src="../images/rolls/7-50.gif" style="height: 50px; width: auto;" /></div>
                        <div class="roll_type<?=$roll_type == 8 ? " selected" : "" ?>"><image src="../images/rolls/8-50.gif" style="height: 50px; width: auto;" /></div>
                    </div>
                </th>
            </tr>
            <tr>
                <th colspan="2">Дополнительная информация</th>
                <td colspan="2"><?=$information ?></td>
            </tr>
        </table>
    </body>
    <script>
        var css = '@page { size: portrait; margin: 8mm; }',
                head = document.head || document.getElementsByTagName('head')[0],
                style = document.createElement('style');
            
        style.type = 'text/css';
        style.media = 'print';
            
        if (style.styleSheet){
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }
            
        head.appendChild(style);
        
        window.print();
    </script>
</html>