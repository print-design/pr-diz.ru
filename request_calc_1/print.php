<?php
include '../include/topscripts.php';
include './status_ids.php';

// Формы
const OLD = "old";
const FLINT = "flint";
const KODAK = "kodak";
const TVER = "tver";

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

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
$new_forms_number = 0;

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
    
    if(!empty($$cliche_var) && $$cliche_var != OLD) {
        $new_forms_number++;
    }
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
                padding-top: 3px;
                padding-right: 3px;
                padding-bottom: 3px;
                vertical-align: top;
            }
            
            #right-panel .value {
                font-size: 18px;
                font-weight: 700;
            }
            
            #right-panel {
                line-height: 1.3rem;
            }
        </style>
    </head>
    <body>
        <div class="row">
            <!-- Левая половина -->
            <div class="col-5" id="left_side">
                <h1 style="font-size: 26px; font-weight: 600; margin: 0; padding: 0;"><?= htmlentities($name) ?></h1>
                <h2 style="font-size: 20px; margin: 0; padding: 0;">№<?=$customer_id."-".$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></h2>
                    <?php if(!empty($techmap_id)): ?>
                <div style="width: 100%; padding: 6px; margin-top: 10p; margin-bottom: 10px; border-radius: 8px; font-weight: bold; text-align: center; border: solid 2px gray; color: gray;">
                    <i class="fas fa-file"></i>&nbsp;&nbsp;&nbsp;Составлена технологическая карта
                </div>
                    <?php else: ?>
                <div style="width: 100%; padding: 6px; margin-top: 10p; margin-bottom: 10px; border-radius: 8px; font-weight: bold; text-align: center; border: solid 2px gray; color: gray;">
                    <i class="fas fa-calculator"></i>&nbsp;&nbsp;&nbsp;Сделан расчёт
                </div>
                    <?php endif; ?>
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
                    <tr><th>Длина этикетки</th><td colspan="3"><?= rtrim(rtrim(number_format($length, 2, ",", ""), "0"), ",") ?> мм</td></tr>
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
            </div>
            <!-- Перегородка -->
            <div class="col-1" style="border-right: solid 1px lightgray;"></div>
            <!-- Правая половина -->
            <div class="col-6" id="right-panel">
                <h1 style="font-size: 26px; font-weight: 600; margin: 0; padding: 0;">Расчет</h1>
                <div class="row text-nowrap">
                    <div class="col-3">
                        <div class="p-1 pl-3" style="color: gray; border: solid 1px gray; border-radius: 8px; height: 40px; width: 100px; line-height: 0.8rem;">
                            <div class="text-nowrap" style="font-size: x-small;">Наценка</div>
                            <span class="text-nowrap"><?=$extracharge ?>%</span>
                        </div>
                    </div>
                        <?php
                        $sql = "select euro, usd from currency where date < '$date' order by id desc limit 1";
                        $fetcher = new Fetcher($sql);
                        if($row = $fetcher->Fetch()):
                        ?>
                    <div class="col-3">
                        <div class="p-1 pl-3" style="color: gray; border: solid 1px gray; border-radius: 8px; height: 40px; width: 100px; line-height: 0.8rem;">
                            <div class="text-nowrap" style="font-size: x-small;">Курс евро</div>
                            <?=number_format($row['euro'], 2, ',', ' ') ?>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-1 pl-3" style="color: gray; border: solid 1px gray; border-radius: 8px; height: 40px; width: 100px; line-height: 0.8rem;">
                            <div class="text-nowrap" style="font-size: x-small;">Курс доллара</div>
                            <?=number_format($row['usd'], 2, ',', ' ') ?>
                        </div>
                    </div>
                        <?php endif; ?>
                </div>
                <h2 style="font-size: 20px; margin: 0; padding: 0;">Стоимость</h2>
                <div class="row text-nowrap">
                    <div class="col-4 pr-4">
                        <h3>Себестоимость</h3>
                        <div>Себестоимость</div>
                        <div class="value mb-2">860 000 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">765 &#8381; за <?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
                    </div>
                    <div class="col-4 pr-4">
                        <h3>Отгрузочная стоимость</h3>
                        <div>Отгрузочная стоимость</div>
                        <div class="value">1 200 000 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">236 &#8381; за <?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
                    </div>
                    <div class="col-4" style="width: 250px;"></div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="row text-nowrap">
                    <div class="col-12">
                        <div>Себестоимость форм</div>
                        <div class="value mb-2">800 000 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;"><?=$new_forms_number ?>&nbsp;шт&nbsp;420&nbsp;мм&nbsp;<i class="fas fa-times" style="font-size: small;"></i>&nbsp;329,5&nbsp;мм</span></div>
                    </div>
                </div>
                <?php endif; ?>
                <h2 style="font-size: 20px; margin: 0; padding: 0;">Материалы&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">765 кг</span></h2>
                <div class="row text-nowrap">
                    <div class="col-4 pr-4">
                        <h3>Основная&nbsp;<span style="font-weight: normal; font-size: small;">765 кг</span></h3>
                        <div>Закупочная стоимость</div>
                        <div class="value mb-2">800 000 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">236 &#8381; за кг</span></div>
                        <div>Минимальная ширина</div>
                        <div class="value mb-2">800 000 мм</div>
                        <div>Масса без приладки</div>
                        <div class="value mb-2">7 000 кг&nbsp;<span style="font-weight: normal; font-size: small;">172 000 м</span></div>
                        <div>Масса с приладкой</div>
                        <div class="value mb-2">8 000 кг&nbsp;<span style="font-weight: normal; font-size: small;">192 000 м</span></div>
                    </div>
                        <?php if(!empty($lamination1_brand_name)): ?>
                    <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
                        <h3>Ламинация 1&nbsp;<span style="font-weight: normal; font-size: small;">765 кг</span></h3>
                        <div>Закупочная стоимость</div>
                        <div class="value mb-2">800 000 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">236 &#8381; за кг</span></div>
                        <div>Минимальная ширина</div>
                        <div class="value mb-2">800 000 мм</div>
                        <div>Масса без приладки</div>
                        <div class="value mb-2">7 000 кг&nbsp;<span style="font-weight: normal; font-size: small;">172 000 м</span></div>
                        <div>Масса с приладкой</div>
                        <div class="value mb-2">8 000 кг&nbsp;<span style="font-weight: normal; font-size: small;">192 000 м</span></div>
                    </div>
                        <?php else: ?>
                    <div class="col-4" style="width: 250px;"></div>
                        <?php endif; ?>
                        <?php if(!empty($lamination2_brand_name)): ?>
                    <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
                        <h3>Ламинация 2&nbsp;<span style="font-weight: normal; font-size: small;">765 кг</span></h3>
                        <div>Закупочная стоимость</div>
                        <div class="value mb-2">800 000 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">236 &#8381; за кг</span></div>
                        <div>Минимальная ширина</div>
                        <div class="value mb-2">800 000 мм</div>
                        <div>Масса без приладки</div>
                        <div class="value mb-2">7 000 кг&nbsp;<span style="font-weight: normal; font-size: small;">172 000 м</span></div>
                        <div>Масса с приладкой</div>
                        <div class="value mb-2">8 000 кг&nbsp;<span style="font-weight: normal; font-size: small;">192 000 м</span></div>
                    </div>
                        <?php else: ?>
                    <div class="col-4" style="width: 250px;"></div>
                        <?php endif; ?>
                </div>
                    <?php
                    if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name) || $work_type_id == 2):
                    ?>
                <div class="row text-nowrap">
                    <div class="col-4 pr-4">
                        <h2 style="font-size: 20px; margin: 0; padding: 0;">Расходы</h2>
                    </div>
                        <?php if(!empty($lamination1_brand_name)): ?>
                    <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;"></div>
                        <?php endif; ?>
                        <?php if(!empty($lamination2_brand_name)): ?>
                    <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;"></div>
                        <?php endif; ?>
                </div>
                <div class="row text-nowrap">
                    <div class="col-4 pr-4">
                        <div>Отходы</div>
                        <div class="value mb-2">1 280 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">4,5 кг</span></div>
                            <?php if($work_type_id == 2): ?>
                        <div>Краска</div>
                        <div class="value mb-2">17 500 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">17,5 кг</span></div>
                            <?php
                            endif;
                            if($work_type_id == 2):
                            ?>
                        <div>Печать тиража</div>
                        <div class="value mb-2">470 500 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">6 ч. 30 мин.</span></div>
                            <?php
                            endif;
                            ?>
                    </div>
                        <?php if(!empty($lamination1_brand_name)): ?>
                    <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
                        <div>Отходы</div>
                        <div class="value mb-2">1 280 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">4,5 кг</span></div>
                        <div>Клей</div>
                        <div class="value mb-2">800 000 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">1,0 кг</span></div>
                        <div>Работа ламинатора</div>
                        <div class="value mb-2">1 500 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">3 часа</span></div>
                    </div>
                        <?php else: ?>
                    <div class="col-4" style="width: 250px;"></div>
                        <?php endif; ?>
                        <?php if(!empty($lamination2_brand_name)): ?>
                    <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
                        <div>Отходы</div>
                        <div class="value mb-2">1 280 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">4,5 кг</span></div>
                        <div>Клей</div>
                        <div class="value mb-2">800 000 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">1,0 кг</span></div>
                        <div>Работа ламинатора</div>
                        <div class="value mb-2">1 500 &#8381;&nbsp;<span style="font-weight: normal; font-size: small;">3 часа</span></div>
                    </div>
                        <?php else: ?>
                    <div class="col-4" style="width: 250px;"></div>
                        <?php endif; ?>
                </div>
                    <?php
                    endif;
                    ?>
            </div>
        </div>
        <script>
            var css = '@page { size: landscape; margin: 8mm; }',
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
    </body>
</html>