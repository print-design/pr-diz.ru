<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Смена статуса
if(null !== filter_input(INPUT_POST, 'change_status_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $status_id = filter_input(INPUT_POST, 'status_id');
    $extracharge = filter_input(INPUT_POST, 'extracharge');
    if(empty($extracharge)) {
        $sql = "update request_calc set status_id=$status_id where id=$id";
    }
    else {
        $sql = "update request_calc set status_id=$status_id, extracharge=$extracharge where id=$id";
    }
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    
    
    if(empty($error_message)) {
        // Составление технологической карты
        if($status_id == 6) {
            $sql = "insert into techmap set request_calc_id = $id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/request_calc/request_calc.php'. BuildQuery('id', $id));
        }
    }
}

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select c.date, c.customer_id, c.name name, c.work_type_id, c.quantity, c.unit, "
        . "c.brand_name, c.thickness, other_brand_name, other_price, other_thickness, other_weight, c.customers_material, "
        . "c.lamination1_brand_name, c.lamination1_thickness, lamination1_other_brand_name, lamination1_other_price, lamination1_other_thickness, lamination1_other_weight, c.lamination1_customers_material, "
        . "c.lamination2_brand_name, c.lamination2_thickness, lamination2_other_brand_name, lamination2_other_price, lamination2_other_thickness, lamination2_other_weight, c.lamination2_customers_material, "
        . "c.width, c.length, c.stream_width, c.streams_count, c.raport raport_value, c.paints_count, "
        . "c.paint_1, c.paint_2, c.paint_3, paint_4, paint_5, paint_6, paint_7, paint_8, "
        . "c.color_1, c.color_2, c.color_3, color_4, color_5, color_6, color_7, color_8, "
        . "c.cmyk_1, c.cmyk_2, c.cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
        . "c.percent_1, c.percent_2, c.percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
        . "c.form_1, c.form_2, c.form_3, form_4, form_5, form_6, form_7, form_8, "
        . "c.status_id, c.extracharge, c.no_ski, "
        . "(select count(id) from techmap where request_calc_id = $id) techmaps_count, "
        . "cs.name status, cs.colour, cs.colour2, cs.image, "
        . "cu.name customer, cu.phone customer_phone, cu.extension customer_extension, cu.email customer_email, cu.person customer_person, "
        . "wt.name work_type, "
        . "mt.name machine, mt.colorfulness, "
        . "(select name from raport where value = c.raport and machine_id = c.machine_id) raport_name, "
        . "(select count(id) from request_calc where customer_id = c.customer_id and id <= c.id) num_for_customer, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.brand_name and fbw.thickness = c.thickness limit 1) weight, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination1_brand_name and fbw.thickness = c.lamination1_thickness limit 1) lamination1_weight, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination2_brand_name and fbw.thickness = c.lamination2_thickness limit 1) lamination2_weight "
        . "from request_calc c "
        . "left join request_calc_status cs on c.status_id = cs.id "
        . "left join customer cu on c.customer_id = cu.id "
        . "left join work_type wt on c.work_type_id = wt.id "
        . "left join machine mt on c.machine_id = mt.id "
        . "where c.id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];
$customer_id = $row['customer_id'];
$name = $row['name'];
$work_type_id = $row['work_type_id'];
$quantity = $row['quantity'];
$unit = $row['unit'];
$brand_name = $row['brand_name'];
$thickness = $row['thickness'];
$weight = $row['weight'];
$other_brand_name = $row['other_brand_name'];
$other_price = $row['other_price'];
$other_thickness = $row['other_thickness'];
$other_weight = $row['other_weight'];
$customers_material = $row['customers_material'];
$lamination1_brand_name = $row['lamination1_brand_name'];
$lamination1_thickness = $row['lamination1_thickness'];
$lamination1_weight = $row['lamination1_weight'];
$lamination1_other_brand_name = $row['lamination1_other_brand_name'];
$lamination1_other_price = $row['lamination1_other_price'];
$lamination1_other_thickness = $row['lamination1_other_thickness'];
$lamination1_other_weight = $row['lamination1_other_weight'];
$lamination1_customers_material = $row['lamination1_customers_material'];
$lamination2_brand_name = $row['lamination2_brand_name'];
$lamination2_thickness = $row['lamination2_thickness'];
$lamination2_weight = $row['lamination2_weight'];
$lamination2_other_brand_name = $row['lamination2_other_brand_name'];
$lamination2_other_price = $row['lamination2_other_price'];
$lamination2_other_thickness = $row['lamination2_other_thickness'];
$lamination2_other_weight = $row['lamination2_other_weight'];
$lamination2_customers_material = $row['lamination2_customers_material'];
$width = $row['width'];
$length = $row['length'];
$stream_width = $row['stream_width'];
$streams_count = $row['streams_count'];
$raport = (empty($row['raport_name']) ? "" : $row['raport_name']." ").(rtrim(rtrim(number_format($row['raport_value'], 3, ",", " "), "0"), ","));
$paints_count = $row['paints_count'];

for($i=1; $i<=$paints_count; $i++) {
    $paint_var = "paint_$i";
    $$paint_var = $row[$paint_var];
    
    $color_var = "color_$i";
    $$color_var = $row[$color_var];
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = $row[$cmyk_var];
    
    $percent_var = "percent_$i";
    $$percent_var = $row[$percent_var];
    
    $form_var = "form_$i";
    $$form_var = $row[$form_var];
}

$status_id = $row['status_id'];
$extracharge = $row['extracharge'];
$no_ski = $row['no_ski'];
$techmaps_count = $row['techmaps_count'];

$status = $row['status'];
$colour = $row['colour'];
$colour2 = $row['colour2'];
$image = $row['image'];

$customer = $row['customer'];
$customer_phone = $row['customer_phone'];
$customer_extension = $row['customer_extension'];
$customer_email = $row['customer_email'];
$customer_person = $row['customer_person'];

$work_type = $row['work_type'];

$machine = $row['machine'];
$colorfulness = $row['colorfulness'];

$techmaps_count = $row['techmaps_count'];
$num_for_customer = $row['num_for_customer'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table.calculation-table tr th, table.calculation-table tr td {
                padding-top: 5px;
                padding-right: 5px;
                padding-bottom: 5px;
                vertical-align: top;
            }
        </style>
    </head>
    <body>
        <?php
        include './right_panel.php';
        include '../include/header_zakaz.php';
        ?>
        <div id="calculation_cancel" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <input type="hidden" id="change_status_submit" name="change_status_submit" />
                        <div class="modal-header">
                            <div style="font-size: x-large;">Отмена заказа</div>
                            <button type="button" class="close calculation_cancel_dismiss" data-dismiss="modal"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            Вы уверены, что хотите отменить заказ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-dark" style="width: 120px;" data-dismiss="modal">Нет</button>
                            <button type="submit" class="btn btn-dark" style="width: 120px;" name="status_id" value="8">Да</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/request_calc/<?= BuildQueryRemove("id") ?>">Назад</a>
            <div class="row">
                <!-- Левая половина -->
                <div class="col-5" id="left_side">
                    <h1 style="font-size: 32px; font-weight: 600;"><?= htmlentities($name) ?></h1>
                    <h2 style="font-size: 26px;">№<?=$customer_id."-".$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></h2>
                    <div style="width: 100%; padding: 12px; margin-top: 40px; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; background-color: <?=$colour2 ?>; border: solid 2px <?=$colour ?>; color: <?=$colour ?>">
                        <?=$image ?>&nbsp;&nbsp;&nbsp;<?=$status ?>
                    </div>
                    <table class="w-100 calculation-table">
                        <tr>
                            <th>Заказчик</th>
                            <td><?=$customer ?></td>
                        </tr>
                        <tr>
                            <th>Название заказа</th>
                            <td><?=$name ?></td>
                        </tr>
                        <tr><th>Тип работы</th><td><?=$work_type ?></td></tr>
                            <?php
                            if(!empty($quantity) && !empty($unit)):
                            ?>
                        <tr><th>Объем заказа</th><td><?= rtrim(rtrim(number_format($quantity, 2, ",", " "), "0"), ",") ?> <?=$unit == 'kg' ? "кг" : "шт" ?></td></tr>
                            <?php
                            endif;
                            if(!empty($machine)):
                            ?>
                        <tr><th>Печатная машина</th><td><?=$machine.' ('.$colorfulness.' красок)' ?></td></tr>
                            <?php
                            endif;
                            if(!empty($width)):
                            ?>
                        <tr><th>Обрезная ширина</th><td><?= rtrim(rtrim(number_format($width, 2, ",", " "), "0"), ",") ?></td></tr>
                            <?php
                            endif;
                            if(!empty($length)):
                            ?>
                        <tr><th>Длина от метки до метки</th><td><?= rtrim(rtrim(number_format($length, 2, ",", ""), "0"), ",") ?></td></tr>
                            <?php
                            endif;
                            if(!empty($stream_width)):
                            ?>
                        <tr><th>Ширина ручья</th><td><?= rtrim(rtrim(number_format($stream_width, 2, ",", ""), "0"), ",") ?></td></tr>
                            <?php
                            endif;
                            if(!empty($raport)):
                            ?>
                        <tr><th>Рапорт</th><td><?= $raport ?></td></tr>
                            <?php
                            endif;
                            if(!empty($streams_count)):
                            ?>
                        <tr><th>Количество ручьев</th><td><?= $streams_count ?></td></tr>
                            <?php
                            endif;
                            if(!empty($machine)):
                            ?>
                        <tr>
                            <th>Печать без лыж</th>
                            <td><?=$no_ski == 1 ? "ДА" : "НЕТ" ?></td>
                        </tr>
                            <?php
                            endif;
                            if(!empty($brand_name) && !empty($thickness)):
                            ?>
                        <tr>
                            <th>Пленка</th>
                            <td>
                                <table class="w-100">
                                    <tr>
                                        <td><?=$brand_name ?></td>
                                        <td><?= number_format($thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                            <?php elseif(!empty($other_brand_name)): ?>
                        <tr>
                            <th>Пленка</th>
                            <td>
                                <table class="w-100">
                                    <tr>
                                        <td><?=$other_brand_name ?></td>
                                        <td><?= number_format($other_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($other_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                            <?php endif; ?>
                        <tr>
                            <?php
                            $lamination = "нет";
                            if(!empty($lamination1_brand_name)) $lamination = "1";
                            if(!empty($lamination2_brand_name)) $lamination = "2";
                            ?>
                            <th>Ламинация: <?=$lamination ?></th>
                            <td>
                                <?php if(!empty($lamination1_brand_name) && !empty($lamination1_thickness)): ?>
                                <table class="w-100">
                                    <tr>
                                        <td><?=$lamination1_brand_name ?></td>
                                        <td><?= number_format($lamination1_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination1_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination1_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php
                                    if(!empty($lamination2_brand_name) && !empty($lamination2_thickness)):
                                    ?>
                                    <tr>
                                        <td><?=$lamination2_brand_name ?></td>
                                        <td><?= number_format($lamination2_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php elseif(!empty($lamination2_other_brand_name)): ?>
                                    <tr>
                                        <td><?=$lamination2_other_brand_name ?></td>
                                        <td><?= number_format($lamination2_other_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_other_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                                <?php elseif(!empty($lamination1_other_brand_name)): ?>
                                <table class="w-100">
                                    <tr>
                                        <td><?=$lamination1_other_brand_name ?></td>
                                        <td><?= number_format($lamination1_other_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination1_other_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination1_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php
                                    if(!empty($lamination2_brand_name) && !empty($lamination2_thickness)):
                                    ?>
                                    <tr>
                                        <td><?=$lamination2_brand_name ?></td>
                                        <td><?= number_format($lamination2_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php elseif(!empty($lamination2_other_brand_name)): ?>
                                    <tr>
                                        <td><?=$lamination2_other_brand_name ?></td>
                                        <td><?= number_format($lamination2_other_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_other_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                                <?php endif; ?>
                            </td>
                        </tr>
                            <?php
                            if(!empty($paints_count)):
                            ?>
                        <tr>
                            <th>Красочность: <?=$paints_count ?></th>
                            <td>
                                <table class="w-100">
                                    <?php
                                    for($i=1; $i<=$paints_count; $i++):
                                    $paint_var = "paint_$i";
                                    $color_var = "color_$i";
                                    $cmyk_var = "cmyk_$i";
                                    $percent_var = "percent_$i";
                                    $form_var = "form_$i";
                                    ?>
                                    <tr>
                                        <td><?=$i ?></td>
                                        <td>
                                            <?php
                                            switch ($$paint_var) {
                                                case 'cmyk':
                                                    echo "CMYK";
                                                    break;
                                                case 'panton':
                                                    echo 'Пантон';
                                                    break;
                                                case 'lacquer':
                                                    echo 'Лак';
                                                    break;
                                                case  'white':
                                                    echo 'Белый';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if($$paint_var == "cmyk") {
                                                echo $$cmyk_var;
                                            }
                                            elseif($$paint_var == "panton") {
                                                echo 'P '.$$color_var;
                                            }
                                            ?>
                                        </td>
                                        <td><?=$$percent_var ?>%</td>
                                        <td><?=$$form_var ?></td>
                                    </tr>
                                        <?php
                                        endfor;
                                        ?>
                                </table>
                            </td>
                        </tr>
                            <?php
                            endif;
                            ?>
                    </table>
                    <?php if($status_id == 3): ?>
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <input type="hidden" id="change_status_submit" name="change_status_submit" />
                        <button type="submit" id="status_id" name="status_id" value="5" class="btn btn-outline-dark mt-5 mr-2" style="width: 200px;">Отклонить</button>
                        <button type="submit" id="status_id" name="status_id" value="4" class="btn btn-dark mt-5 mr-2" style="width: 200px;">Одобрить</button>
                    </form>
                    <?php endif; if ($status_id == 1 || $status_id == 2 || $status_id == 4 || $status_id == 5 || $status_id == 6 || $status_id == 7 || $status_id == 8): ?>
                    <a href="create.php<?= BuildQuery("mode", "recalc") ?>" class="btn btn-dark mt-5 mr-2" style="width: 200px;">Пересчитать</a>
                    <?php endif; if ($status_id == 4 || $status_id == 6): ?>
                    <button type="button" class="btn btn-outline-dark mt-5" style="width: 200px;" data-toggle="modal" data-target="#calculation_cancel">Отменить заказ</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // Показ расходов
            function ShowCosts() {
                $("#costs").removeClass("d-none");
                $("#show_costs").addClass("d-none");
                AdjustFixedBlock($('#calculation'));
            }
            
            // Скрытие расходов
            function HideCosts() {
                $("#costs").addClass("d-none");
                $("#show_costs").removeClass("d-none");
                AdjustFixedBlock($('#calculation'));
            }
            
            // Ограницение значений наценки
            $('#extracharge').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('#extracharge').change(function(){
                ChangeLimitIntValue($(this), 999);
                
                // Сохранение значения в базе
                EditExtracharge($(this));
            });
            
            // Отображение полностью блока с фиксированной позицией, не умещающегося полностью в окне
            AdjustFixedBlock($('#calculation'));
            
            $(window).on("scroll", function(){
                AdjustFixedBlock($('#calculation'));
            });
        </script>
    </body>
</html>