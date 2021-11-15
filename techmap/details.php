<?php
include '../include/topscripts.php';
include '../include/database_grafik.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Перенаправление при пустом id
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION.'/techmap/');
}

function DeleteFromGrafik($grafik_id) {
    if(!empty($grafik_id)) {
        $sql = "select workshift_id from edition where id = $grafik_id";
        $fetcher = new FetcherGrafik($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $workshift_id = $row[0];
            
            $sql = "delete from edition where id = $grafik_id";
            $executer = new ExecuterGrafik($sql);
            $error_message = $executer->error;
            
            if(empty($error_message)) {
                $count = (new FetcherGrafik("select count(id) from edition where workshift_id = $workshift_id"))->Fetch()[0];
                
                if($count == 0) {
                    $row = (new FetcherGrafik("select user1_id, user2_id from workshift where id = $workshift_id"))->Fetch();
                    
                    if(empty($row[0]) && empty($row[1])) {
                        $error_message = (new ExecuterGrafik("delete from workshift where id = $workshift_id"))->error;
                    }
                    else {
                        $position = 1;
                        $error_message = (new ExecuterGrafik("insert into edition (workshift_id, position) values ($workshift_id, $position)"))->error;
                    }
                }
            }
        }
    }
}

if(null !== filter_input(INPUT_POST, 'add-date-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $work_date = filter_input(INPUT_POST, 'work_date');
    $work_shift = filter_input(INPUT_POST, 'work_shift');
    
    if(!empty($work_date) && !empty($work_shift)) {
        $sql = "select grafik_id from techmap where id=$id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            DeleteFromGrafik($row[0]);
        }
        
        $sql = "update techmap set work_date='$work_date', work_shift='$work_shift' where id=$id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

if(null !== filter_input(INPUT_POST, 'remove-date-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "select grafik_id from techmap where id=$id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        DeleteFromGrafik($row[0]);
    }
    
    $sql = "update techmap set work_date=NULL, work_shift=NULL where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select t.date, t.calculation_id, t.work_date, t.work_shift, t.designer, t.printer, t.cutter, t.printings_number, t.rolls_number, "
        . "t.reverse_print, t.self_adhesive, t.spool, t.number_per_spool, t.winding, t.roll_type, "
        . "c.name name, c.unit, c.quantity, "
        . "c.brand_name, c.other_brand_name, c.lamination1_brand_name, c.lamination1_other_brand_name, c.lamination2_brand_name, c.lamination2_other_brand_name, "
        . "c.streams_count, c.length, c.raport, c.paints_count, "
        . "c.paint_1, c.paint_2, c.paint_3, c.paint_4, c.paint_5, c.paint_6, c.paint_7, c.paint_8, c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
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
$name = $row['name'];
$unit = $row['unit'];
$quantity = $row['quantity'];
$brand_name = $row['brand_name'] == 'other' ? $row['other_brand_name'] : $row['brand_name'];
$lamination1_brand_name = $row['lamination1_brand_name'] == 'other' ? $row['lamination1_other_brand_name'] : $row['lamination1_brand_name'];
$lamination2_brand_name = $row['lamination2_brand_name'] == 'other' ? $row['lamination2_other_brand_name'] : $row['lamination2_brand_name'];
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
        <?php
        include '../include/head.php';
        ?>
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
            }
            
            table.table tr th {
                font-weight: bold;
                color: black;
                font-size: 22px;
            }
            
            table.table tr td {
                font-size: 22px;
            }
            
            table.table tr td form {
                font-family: 'Arial', 'sans serif';
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/techmap/<?= IsInRole('manager') ? BuildQueryAddRemove('manager', GetUserId(), 'id') : BuildQueryRemove('id') ?>">К списку</a>
            <a class="btn btn-outline-dark ml-3 topbutton" style="width: 200px;" href="<?=APPLICATION ?>/calculation/calculation.php?id=<?=$calculation_id ?>">К расчету</a>
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
                    <th colspan="2">Размер этикетки</th>
                    <td colspan="2"><?=$length ?></td>
                </tr>
                <tr>
                    <th colspan="2">Количество ручьев</th>
                    <td colspan="2"><?=$streams_count ?></td>
                </tr>
                <tr>
                    <th colspan="2">Способ наклейки (ручная, автомат)</th>
                    <td colspan="2"><?=$self_adhesive === null ? "" : ($self_adhesive === 0 ? "ручная" : "автомат") ?></td>
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
                        Дополнительная информация
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
                    <th colspan="2">Дата печати тиража</th>
                    <td colspan="2">
                        <form method="post" class="form-inline">
                            <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                            <input type="hidden" name="scroll" />
                            <div class="form-group mr-3">
                                <input type="date" id="work_date" name="work_date" value="<?=$work_date ?>" class="form-control" />
                            </div>
                            <div class="form-group mr-3">
                                <?php
                                $day_checked = '';
                                if($work_shift == 'day') {
                                    $day_checked = " checked='checked'";
                                }
                                ?>
                                <input type="radio" class="form-check-inline" id="work_shift_day" name="work_shift" value="day"<?=$day_checked ?> />
                                <label for="work_shift_day" class="form-check-label">день</label>
                            </div>
                            <div class="form-group mr-3">
                                <?php
                                $night_checked = '';
                                if($work_shift == 'night') {
                                    $night_checked = " checked='checked'";
                                }
                                ?>
                                <input type="radio" class="form-check-inline" id="work_shift_night" name="work_shift" value="night"<?=$night_checked ?> />
                                <label for="work_shift_night" class="form-check-label">ночь</label>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-dark" name="add-date-submit">OK</button>
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