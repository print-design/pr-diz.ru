<?php
include '../include/topscripts.php';
include './status_ids.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select c.date, c.customer_id, c.name name, c.work_type_id, c.quantity, c.unit, "
        . "c.brand_name, c.thickness, c.individual_brand_name, c.individual_price, c.individual_thickness, c.individual_density, c.customers_material, "
        . "c.lamination1_brand_name, c.lamination1_thickness, c.lamination1_individual_brand_name, c.lamination1_individual_price, c.lamination1_individual_thickness, c.lamination1_individual_density, c.lamination1_customers_material, "
        . "c.lamination2_brand_name, c.lamination2_thickness, c.lamination2_individual_brand_name, c.lamination2_individual_price, c.lamination2_individual_thickness, c.lamination2_individual_density, c.lamination2_customers_material, "
        . "c.width, c.length, c.stream_width, c.streams_number, c.raport, c.lamination_roller_width, c.ink_number, "
        . "c.ink_1, c.ink_2, c.ink_3, c.ink_4, c.ink_5, c.ink_6, c.ink_7, c.ink_8, "
        . "c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, "
        . "c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
        . "c.percent_1, c.percent_2, c.percent_3, c.percent_4, c.percent_5, c.percent_6, c.percent_7, c.percent_8, "
        . "c.cliche_1, c.cliche_2, c.cliche_3, c.cliche_4, c.cliche_5, c.cliche_6, c.cliche_7, c.cliche_8, "
        . "c.status_id, c.extracharge, c.no_ski, "
        . "(select id from techmap where request_calc_id = $id order by id desc limit 1) techmap_id, "
        . "cu.name customer, cu.phone customer_phone, cu.extension customer_extension, cu.email customer_email, cu.person customer_person, "
        . "wt.name work_type, "
        . "mt.name machine, mt.colorfulness, "
        . "(select count(id) from request_calc where customer_id = c.customer_id and id <= c.id) num_for_customer, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.brand_name and fbw.thickness = c.thickness limit 1) weight, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination1_brand_name and fbw.thickness = c.lamination1_thickness limit 1) lamination1_weight, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination2_brand_name and fbw.thickness = c.lamination2_thickness limit 1) lamination2_weight "
        . "from request_calc c "
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
$individual_brand_name = $row['individual_brand_name'];
$individual_price = $row['individual_price'];
$individual_thickness = $row['individual_thickness'];
$individual_density = $row['individual_density'];
$customers_material = $row['customers_material'];
$lamination1_brand_name = $row['lamination1_brand_name'];
$lamination1_thickness = $row['lamination1_thickness'];
$lamination1_weight = $row['lamination1_weight'];
$lamination1_individual_brand_name = $row['lamination1_individual_brand_name'];
$lamination1_individual_price = $row['lamination1_individual_price'];
$lamination1_individual_thickness = $row['lamination1_individual_thickness'];
$lamination1_individual_density = $row['lamination1_individual_density'];
$lamination1_customers_material = $row['lamination1_customers_material'];
$lamination2_brand_name = $row['lamination2_brand_name'];
$lamination2_thickness = $row['lamination2_thickness'];
$lamination2_weight = $row['lamination2_weight'];
$lamination2_individual_brand_name = $row['lamination2_individual_brand_name'];
$lamination2_individual_price = $row['lamination2_individual_price'];
$lamination2_individual_thickness = $row['lamination2_individual_thickness'];
$lamination2_individual_density = $row['lamination2_individual_density'];
$lamination2_customers_material = $row['lamination2_customers_material'];
$width = $row['width'];
$length = $row['length'];
$stream_width = $row['stream_width'];
$streams_number = $row['streams_number'];
$raport = rtrim(rtrim(number_format($row['raport'], 3, ",", " "), "0"), ",");
$lamination_roller_width = $row['lamination_roller_width'];
$ink_number = $row['ink_number'];

for($i=1; $i<=$ink_number; $i++) {
    $ink_var = "ink_$i";
    $$ink_var = $row[$ink_var];
    
    $color_var = "color_$i";
    $$color_var = $row[$color_var];
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = $row[$cmyk_var];
    
    $percent_var = "percent_$i";
    $$percent_var = $row[$percent_var];
    
    $cliche_var = "cliche_$i";
    $$cliche_var = $row[$cliche_var];
}

$status_id = $row['status_id'];
$extracharge = $row['extracharge'];
$no_ski = $row['no_ski'];

$customer = $row['customer'];
$customer_phone = $row['customer_phone'];
$customer_extension = $row['customer_extension'];
$customer_email = $row['customer_email'];
$customer_person = $row['customer_person'];

$work_type = $row['work_type'];

$machine = $row['machine'];
$colorfulness = $row['colorfulness'];

$techmap_id = $row['techmap_id'];
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
                    <?php
                    $real_status_id = null;
                    
                    if(!empty($techmap_id)):
                        $real_status_id = TECHMAP;
                    ?>
                    <div style="width: 100%; padding: 12px; margin-top: 40p; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; border: solid 2px green; color: green;">
                        <i class="fas fa-file"></i>&nbsp;&nbsp;&nbsp;Составлена технологическая карта
                    </div>
                    <?php
                    else:
                        $real_status_id = CALCULATION;
                    ?>
                    <div style="width: 100%; padding: 12px; margin-top: 40p; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; border: solid 2px blue; color: blue;">
                        <i class="fas fa-calculator"></i>&nbsp;&nbsp;&nbsp;Сделан расчёт
                    </div>
                    <?php
                    endif;
                    
                    // Обновляем поле status_id (оно нужно для сортировки по статусу на странице списка)
                    if(!empty($real_status_id) && $status_id != $real_status_id) {
                        $sql = "update request_calc set status_id = $real_status_id where id = $id";
                        $executer = new Executer($sql);
                    }
                    ?>
                    <table class="w-100 calculation-table">
                        <tr><th>Заказчик</th><td colspan="3"><?=$customer ?></td></tr>
                        <tr><th>Название заказа</th><td colspan="3"><?=$name ?></td></tr>
                        <tr><th>Тип работы</th><td colspan="3"><?=$work_type ?></td></tr>
                            <?php
                            if(!empty($quantity) && !empty($unit)):
                            ?>
                        <tr><th>Объем заказа</th><td colspan="3"><?= rtrim(rtrim(number_format($quantity, 2, ",", " "), "0"), ",") ?> <?=$unit == 'kg' ? "кг" : "шт" ?></td></tr>
                            <?php
                            endif;
                            if(!empty($machine)):
                            ?>
                        <tr><th>Печатная машина</th><td colspan="3"><?=$machine.' ('.$colorfulness.' красок)' ?></td></tr>
                            <?php
                            endif;
                            if(!empty($width)):
                            ?>
                        <tr><th>Обрезная ширина</th><td colspan="3"><?= rtrim(rtrim(number_format($width, 2, ",", " "), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($length)):
                            ?>
                        <tr><th>Длина от метки до метки</th><td colspan="3"><?= rtrim(rtrim(number_format($length, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($stream_width)):
                            ?>
                        <tr><th>Ширина ручья</th><td colspan="3"><?= rtrim(rtrim(number_format($stream_width, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($raport)):
                            ?>
                        <tr><th>Рапорт</th><td colspan="3"><?= $raport ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($lamination_roller_width)):
                            ?>
                        <tr><th>Ширина ламинирующего вала</th><td colspan="3"><?= $lamination_roller_width ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($streams_count)):
                            ?>
                        <tr><th>Количество ручьев</th><td colspan="3"><?= $streams_number ?></td></tr>
                            <?php
                            endif;
                            if(!empty($machine)):
                            ?>
                        <tr><th>Печать без лыж</th><td colspan="3"><?=$no_ski == 1 ? "ДА" : "НЕТ" ?></td></tr>
                            <?php
                            endif;
                            if($brand_name == INDIVIDUAL):
                            ?>
                        <tr>
                            <th>Пленка</th>
                            <td><?=$individual_brand_name ?></td>
                            <td><?= number_format($individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                            <td><?=$customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                        </tr>
                            <?php
                            else:
                            ?>
                        <tr>
                            <th>Пленка</th>
                            <td><?=$brand_name ?></td>
                            <td><?= number_format($thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                            <td><?=$customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                        </tr>
                            <?php
                            endif;
                            $lamination = "нет";
                            if(!empty($lamination1_brand_name)) $lamination = "1";
                            if(!empty($lamination2_brand_name)) $lamination = "2";
                            
                            if(!empty($lamination1_brand_name) && $lamination1_brand_name == INDIVIDUAL):
                            ?>
                        <tr>
                            <th<?=(empty($lamination2_brand_name) ? "" : " rowspan='2'") ?>>Ламинация: <?=$lamination ?></th>
                            <td><?=$lamination1_individual_brand_name ?></td>
                            <td><?= number_format($lamination1_individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination1_individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                            <td><?=$lamination1_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                        </tr>
                            <?php
                            elseif(!empty($lamination1_brand_name)):
                            ?>
                        <tr>
                            <th<?=(empty($lamination2_brand_name) ? "" : " rowspan='2'") ?>>Ламинация: <?=$lamination ?></th>
                            <td><?=$lamination1_brand_name ?></td>
                            <td><?= number_format($lamination1_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination1_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                            <td><?=$lamination1_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                        </tr>
                            <?php
                            endif;
                            if(!empty($lamination2_brand_name) && $lamination2_brand_name == INDIVIDUAL):
                            ?>
                        <tr>
                            <td><?=$lamination2_individual_brand_name ?></td>
                            <td><?= number_format($lamination2_individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                            <td><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                        </tr>
                            <?php
                            elseif(!empty($lamination2_brand_name)):
                            ?>
                        <tr>
                            <td><?=$lamination2_brand_name ?></td>
                            <td><?= number_format($lamination2_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                            <td><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                        </tr>
                            <?php
                            endif;
                            if(!empty($ink_number)):
                            ?>
                        <tr>
                            <th>Красочность: <?=$ink_number ?></th>
                            <td colspan="3">
                                <table class="w-100">
                                    <?php
                                    for($i=1; $i<=$ink_number; $i++):
                                    $ink_var = "ink_$i";
                                    $color_var = "color_$i";
                                    $cmyk_var = "cmyk_$i";
                                    $percent_var = "percent_$i";
                                    $cliche_var = "cliche_$i";
                                    ?>
                                    <tr>
                                        <td><?=$i ?></td>
                                        <td>
                                            <?php
                                            switch ($$ink_var) {
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
                                            if($$ink_var == "cmyk") {
                                                echo $$cmyk_var;
                                            }
                                            elseif($$ink_var == "panton") {
                                                echo 'P '.$$color_var;
                                            }
                                            ?>
                                        </td>
                                        <td><?=$$percent_var ?>%</td>
                                        <td>
                                            <?php
                                            switch ($$cliche_var) {
                                                case 'old':
                                                    echo 'Старая';
                                                    break;
                                                case 'flint':
                                                    echo 'Флинт';
                                                    break;
                                                case 'kodak';
                                                    echo 'Кодак';
                                                    break;
                                                case 'tver';
                                                    echo 'Тверь';
                                                    break;
                                            }
                                            ?>
                                        </td>
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
                    <a href="create.php<?= BuildQuery("mode", "recalc") ?>" class="btn btn-dark mt-5 mr-2" style="width: 200px;">Пересчитать</a>
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