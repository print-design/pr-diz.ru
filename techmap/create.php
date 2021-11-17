<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

const TECHMAP_CREATED_STATUS_ID = 6;

if(null !== filter_input(INPUT_POST, 'create-submit')) {
    $request_calc_id = filter_input(INPUT_POST, 'request_calc_id');
    $designer = addslashes(filter_input(INPUT_POST, 'designer'));
    $printer = addslashes(filter_input(INPUT_POST, 'printer'));
    $cutter = addslashes(filter_input(INPUT_POST, 'cutter'));
    $printings_number = filter_input(INPUT_POST, 'printings_number');
    if($printings_number === null || $printings_number === '') $printings_number = "NULL";
    $rolls_number = filter_input(INPUT_POST, 'rolls_number');
    if($rolls_number === null || $rolls_number === '') $rolls_number = "NULL";
    $reverse_print = filter_input(INPUT_POST, 'reverse_print');
    if($reverse_print === null || $reverse_print === '') $reverse_print = "NULL";
    $self_adhesive = filter_input(INPUT_POST, 'self_adhesive');
    if($self_adhesive === null || $self_adhesive === '') $self_adhesive = "NULL";
    $spool = filter_input(INPUT_POST, 'spool');
    $number_per_spool = filter_input(INPUT_POST, 'number_per_spool');
    if($number_per_spool === null || $number_per_spool === '') $number_per_spool = "NULL";
    $winding = filter_input(INPUT_POST, 'winding');
    if($winding === null || $winding === '') $winding = "NULL";
    $roll_type = filter_input(INPUT_POST, 'roll_type');
    if($roll_type === null || $roll_type === '') $roll_type = "NULL";
    
    $sql = "insert into techmap (request_calc_id, designer, printer, cutter, printings_number, rolls_number, reverse_print, "
            . "self_adhesive, spool, number_per_spool, winding, roll_type) "
            . "values($request_calc_id, '$designer', '$printer', '$cutter', $printings_number, $rolls_number, $reverse_print, "
            . "$self_adhesive, $spool, $number_per_spool, $winding, $roll_type)";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    $techmap_id = $executer->insert_id;
    
    if(empty($error_message) && !empty($techmap_id)) {
        header('Location: '.APPLICATION.'/techmap/details.php?id='.$techmap_id);
    }
}

// Открыть можно только через кнопку "Составить технологическую карту"
$request_calc_id = filter_input(INPUT_POST, 'request_calc_id');

if(empty($request_calc_id)) {
    header('Location: '.APPLICATION.'/techmap/');
}

// Получение объекта расчёта
$sql = "select c.name name, c.unit, c.quantity, "
        . "c.brand_name, c.individual_brand_name, c.lamination1_brand_name, c.lamination1_individual_brand_name, c.lamination2_brand_name, c.lamination2_individual_brand_name, "
        . "c.streams_count, c.length, c.raport, c.paints_count, "
        . "c.paint_1, c.paint_2, c.paint_3, c.paint_4, c.paint_5, c.paint_6, c.paint_7, c.paint_8, c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
        . "cus.name customer, u.last_name manager, "
        . "cr.dirty_width, cr.dirty_length "
        . "from request_calc c "
        . "inner join user u on c.manager_id = u.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join request_calc_result cr on cr.request_calc_id = c.id "
        . "where c.id = $request_calc_id";
$row = (new Fetcher($sql))->Fetch();

$name = $row['name'];
$unit = $row['unit'];
$quantity = $row['quantity'];
$customer = $row['customer'];
$brand_name = $row['brand_name'] == 'other' ? $row['individual_brand_name'] : $row['brand_name'];
$lamination1_brand_name = $row['lamination1_brand_name'] == 'other' ? $row['lamination1_individual_brand_name'] : $row['lamination1_brand_name'];
$lamination2_brand_name = $row['lamination2_brand_name'] == 'other' ? $row['lamination2_individual_brand_name'] : $row['lamination2_brand_name'];
$streams_count = $row['streams_count'];
$length = $row['length'];
$raport = $row['raport'];
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
    
    array_push($paints, $paint);
}
$manager = $row['manager'];
$dirty_width = $row['dirty_width'];
$dirty_length = $row['dirty_length'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            .roll-selector input {
                margin:0;padding:0;
                -webkit-appearance:none;
                -moz-appearance:none;
                appearance:none;
            }
            
            .roll-selector label {
                cursor:pointer;
                border: solid 3px white;
            }
            
            .roll-selector label:hover {
                border: solid 3px lightgray;
            }
            
            .roll-selector input[type="radio"]:checked + label {
                border: solid 3px darkgray;
            }
            
            table.table {
                font-family: 'Times New Roman', 'serif';
            }
            
            table.table tr th {
                font-weight: bold;
                color: black;
                font-size: 22px;
            }
            
            table.table tr td {
                font-size: 22px;
            }
        </style>
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/request_calc/request_calc.php?id=<?=$request_calc_id ?>">Отмена</a>
            <h1 style="font-size: 32px; font-weight: 600;">Новая заявка на флекс-печать</h1>
            <form method="post">
                <input type="hidden" name="request_calc_id" value="<?=$request_calc_id ?>" />
                <input type="hidden" name="scroll" />
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
                        <td colspan="2"><?=rtrim(rtrim(number_format($quantity, 2, ",", " "), "0"), ",").' '.($unit == 'kg' ? 'кг' : 'шт') ?></td>
                    </tr>
                    <tr>
                        <th colspan="2">Количество тиражей</th>
                        <td colspan="2"><input type="number" min="1" step="1" name="printings_number" class="form-control int-only" style="width: 150px;" value="<?= filter_input(INPUT_POST, 'printings_number') ?>" /></td>
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
                        <td colspan="2"><input type="number" min="1" step="1" name="rolls_number" class="form-control int-only" style="width: 150px;" value="<?= filter_input(INPUT_POST, 'rolls_number') ?>" /></td>
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
                                    <?php
                                    $reverse_print_0_checked = '';
                                    if(filter_input(INPUT_POST, 'reverse_print') === '0') {
                                        $reverse_print_0_checked = " checked='checked'";
                                    }
                                    ?>
                                    <td><input type="radio" name="reverse_print" value="0" class="form-check-inline"<?=$reverse_print_0_checked ?> /></td>
                                </tr>
                                <tr>
                                    <td>оборотная</td>
                                    <?php
                                    $reverse_print_1_checked = '';
                                    if(filter_input(INPUT_POST, 'reverse_print') === '1') {
                                        $reverse_print_1_checked = " checked='checked'";
                                    }
                                    ?>
                                    <td><input type="radio" name="reverse_print" value="1" class="form-check-inline"<?=$reverse_print_1_checked ?> /></td>
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
                        <th colspan="2">Размер этикетки</th>
                        <td colspan="2"><?=$length ?></td>
                    </tr>
                    <tr>
                        <th colspan="2">Количество ручьев</th>
                        <td colspan="2"><?=$streams_count ?></td>
                    </tr>
                    <tr>
                        <th colspan="2">Способ наклейки (ручная, автомат)</th>
                        <td colspan="2">
                            <div class="form-group">
                                <input type="radio" class="form-check-inline" id="self_adhesive_0" name="self_adhesive" value="0" />
                                <label for="self_adhesive_0" class="form-check-label">ручная</label>
                                <input type="radio" class="form-check-inline ml-3" id="self_adhesive_1" name="self_adhesive" value="1" />
                                <label for="self_adhesive_1" class="form-check-label">автомат</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="3">Резка и размотка продукции</th>
                        <th>Размер шпули (внутренний диаметр)</th>
                        <td colspan="2">
                            <select class="form-control" name="spool" style="width: 150px;">
                                <option value="" hidden="hidden">...</option>
                                <?php $post_spool = filter_input(INPUT_POST, 'spool'); ?>
                                <option value="40"<?=$post_spool == 40 ? " selected='selected'" : "" ?>>40</option>
                                <option value="50"<?=$post_spool == 50 ? " selected='selected'" : "" ?>>50</option>
                                <option value="76"<?=$post_spool == 76 ? " selected='selected'" : "" ?>>76</option>
                                <option value="152"<?=$post_spool == 152 ? " selected='selected'" : "" ?>>152</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>К-во этикеток на шпуле</th>
                        <td colspan="2"><input type="number" min="1" step="1" name="number_per_spool" class="form-control int-only" style="width: 150px;" value="<?= filter_input(INPUT_POST, 'number_per_spool') ?>" /></td>
                    </tr>
                    <tr>
                        <th>Намотка, в метрах</th>
                        <td colspan="2"><input type="number" min="1" step="1" name="winding" class="form-control int-only" style="width: 150px;" value="<?= filter_input(INPUT_POST, 'winding') ?>" /></td>
                    </tr>
                    <tr>
                        <th colspan="4">
                            Дополнительная информация
                            <div class="form-group roll-selector mt-3">
                                <?php $roll_type = filter_input(INPUT_POST, 'roll_type'); ?>
                                <input type="radio" class="form-check-inline mr-3 mt-3" id="roll_type_1" name="roll_type" value="1"<?=$roll_type == 1 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_1"><image src="../images/rolls/2-50.gif" style="height: 50px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline mr-3 mt-3" id="roll_type_2" name="roll_type" value="2"<?=$roll_type == 2 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_2"><image src="../images/rolls/2-50.gif" style="height: 50px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline mr-3 mt-3" id="roll_type_3" name="roll_type" value="3"<?=$roll_type == 3 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_3"><image src="../images/rolls/3-50.gif" style="height: 50px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline mr-3 mt-3" id="roll_type_4" name="roll_type" value="4"<?=$roll_type == 4 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_4"><image src="../images/rolls/4-50.gif" style="height: 50px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline mr-3 mt-3" id="roll_type_5" name="roll_type" value="5"<?=$roll_type == 5 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_5"><image src="../images/rolls/5-50.gif" style="height: 50px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline mr-3 mt-3" id="roll_type_6" name="roll_type" value="6"<?=$roll_type == 6 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_6"><image src="../images/rolls/6-50.gif" style="height: 50px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline mr-3 mt-3" id="roll_type_7" name="roll_type" value="7"<?=$roll_type == 7 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_7"><image src="../images/rolls/7-50.gif" style="height: 50px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline mr-3 mt-3" id="roll_type_8" name="roll_type" value="8"<?=$roll_type == 8 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_8"><image src="../images/rolls/8-50.gif" style="height: 50px; width: auto;" /></label>
                            </div>
                        </th>
                    </tr>
                </table>
                <button type="submit" name="create-submit" class="btn btn-dark" style="width: 200px;">Создать</button>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>