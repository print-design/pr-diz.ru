<?php
include '../include/topscripts.php';
include './status_ids.php';

// Лыжи
const NO_SKI = 0;
const STANDARD_SKI = 1;
const NONSTANDARD_SKI = 2;

// Формы
const OLD = "old";
const FLINT = "flint";
const KODAK = "kodak";
const TVER = "tver";

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select rc.date, rc.customer_id, rc.name, rc.unit, rc.quantity, rc.work_type_id, wt.name work_type, "
        . "rc.film_variation_id, f.name film_name, fv.thickness thickness, fv.weight weight, rc.price, rc.currency, rc.individual_film_name, rc.individual_price, rc.individual_currency, rc.individual_thickness, rc.individual_density, rc.customers_material, rc.ski, rc.width_ski, "
        . "rc.lamination1_film_variation_id, lam1f.name lamination1_film_name, lam1fv.thickness lamination1_thickness, lam1fv.weight lamination1_weight, rc.lamination1_price, rc.lamination1_currency, rc.lamination1_individual_film_name, rc.lamination1_individual_price, rc.lamination1_individual_currency, rc.lamination1_individual_thickness, rc.lamination1_individual_density, rc.lamination1_customers_material, rc.lamination1_ski, rc.lamination1_width_ski, "
        . "rc.lamination2_film_variation_id, lam2f.name lamination2_film_name, lam2fv.thickness lamination2_thickness, lam2fv.weight lamination2_weight, rc.lamination2_price, rc.lamination2_currency, rc.lamination2_individual_film_name, rc.lamination2_individual_price, rc.lamination2_individual_currency, rc.lamination2_individual_thickness, rc.lamination2_individual_density, rc.lamination2_customers_material, rc.lamination2_ski, rc.lamination2_width_ski, "
        . "rc.width, rc.streams_number, m.name machine, m.colorfulness colorfulness, rc.length, rc.stream_width, rc.raport, rc.lamination_roller_width, rc.ink_number, u.first_name, u.last_name, rc.status_id, "
        . "rc.ink_1, rc.ink_2, rc.ink_3, rc.ink_4, rc.ink_5, rc.ink_6, rc.ink_7, rc.ink_8, "
        . "rc.color_1, rc.color_2, rc.color_3, rc.color_4, rc.color_5, rc.color_6, rc.color_7, rc.color_8, "
        . "rc.cmyk_1, rc.cmyk_2, rc.cmyk_3, rc.cmyk_4, rc.cmyk_5, rc.cmyk_6, rc.cmyk_7, rc.cmyk_8, "
        . "rc.percent_1, rc.percent_2, rc.percent_3, rc.percent_4, rc.percent_5, rc.percent_6, rc.percent_7, rc.percent_8, rc.cliche_1, "
        . "rc.cliche_2, rc.cliche_3, rc.cliche_4, rc.cliche_5, rc.cliche_6, rc.cliche_7, rc.cliche_8, "
        . "cus.name customer, cus.phone customer_phone, cus.extension customer_extension, cus.email customer_email, cus.person customer_person, "
        . "(select count(id) from request_calc where customer_id = rc.customer_id and id <= rc.id) num_for_customer "
        . "from request_calc rc "
        . "left join film_variation fv on rc.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_variation lam1fv on rc.lamination1_film_variation_id = lam1fv.id "
        . "left join film lam1f on lam1fv.film_id = lam1f.id "
        . "left join film_variation lam2fv on rc.lamination2_film_variation_id = lam2fv.id "
        . "left join film lam2f on lam2fv.film_id = lam2f.id "
        . "left join machine m on rc.machine_id = m.id "
        . "left join user u on rc.manager_id = u.id "
        . "left join work_type wt on rc.work_type_id = wt.id "
        . "left join customer cus on rc.customer_id = cus.id "
        . "where rc.id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];
$customer_id = $row['customer_id'];
$name = $row['name'];
$unit = $row['unit'];
$quantity = $row['quantity'];
$work_type_id = $row['work_type_id'];
$work_type = $row['work_type'];

$film_variation_id = $row['film_variation_id'];
$film_name = $row['film_name'];
$thickness = $row['thickness'];
$weight = $row['weight'];
$price = $row['price'];
$currency = $row['currency'];
$individual_film_name = $row['individual_film_name'];
$individual_price = $row['individual_price'];
$individual_currency = $row['individual_currency'];
$individual_thickness = $row['individual_thickness'];
$individual_density = $row['individual_density'];
$customers_material = $row['customers_material'];
$ski = $row['ski'];
$width_ski = $row['width_ski'];

$lamination1_film_variation_id = $row['lamination1_film_variation_id'];
$lamination1_film_name = $row['lamination1_film_name'];
$lamination1_thickness = $row['lamination1_thickness'];
$lamination1_weight = $row['lamination1_weight'];
$lamination1_price = $row['lamination1_price'];
$lamination1_currency = $row['lamination1_currency'];
$lamination1_individual_film_name = $row['lamination1_individual_film_name'];
$lamination1_individual_price = $row['lamination1_individual_price'];
$lamination1_individual_currency = $row['lamination1_individual_currency'];
$lamination1_individual_thickness = $row['lamination1_individual_thickness'];
$lamination1_individual_density = $row['lamination1_individual_density'];
$lamination1_customers_material = $row['lamination1_customers_material'];
$lamination1_ski = $row['lamination1_ski'];
$lamination1_width_ski = $row['lamination1_width_ski'];

$lamination2_film_variation_id = $row['lamination2_film_variation_id'];
$lamination2_film_name = $row['lamination2_film_name'];
$lamination2_thickness = $row['lamination2_thickness'];
$lamination2_weight = $row['lamination2_weight'];
$lamination2_price = $row['lamination2_price'];
$lamination2_currency = $row['lamination2_currency'];
$lamination2_individual_film_name = $row['lamination2_individual_film_name'];
$lamination2_individual_price = $row['lamination2_individual_price'];
$lamination2_individual_currency = $row['lamination2_individual_currency'];
$lamination2_individual_thickness = $row['lamination2_individual_thickness'];
$lamination2_individual_density = $row['lamination2_individual_density'];
$lamination2_customers_material = $row['lamination2_customers_material'];
$lamination2_ski = $row['lamination2_ski'];
$lamination2_width_ski = $row['lamination2_width_ski'];

$width = $row['width'];
$streams_number = $row['streams_number'];
$machine = $row['machine'];
$colorfulness = $row['colorfulness'];
$length = $row['length'];
$stream_width = $row['stream_width'];
$raport = rtrim(rtrim(number_format($row['raport'], 3, ",", " "), "0"), ",");
$lamination_roller_width = $row['lamination_roller_width'];
$ink_number = $row['ink_number'];
$first_name = $row['first_name'];
$last_name = $row['last_name'];
$status_id = $row['status_id'];

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

$customer = $row['customer'];
$customer_phone = $row['customer_phone'];
$customer_extension = $row['customer_extension'];
$customer_email = $row['customer_email'];
$customer_person = $row['customer_person'];

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
                        if(!empty($last_name) || !empty($first_name)):
                        ?>
                    <tr><th>Менеджер</th><td colspan="3"><?=$last_name.(empty($last_name) ? "" : " ").$first_name ?></td></tr>
                        <?php
                        endif;
                        if(empty($film_name)):
                        ?>
                    <tr>
                        <th>Пленка</th>
                        <td><?=$individual_film_name ?></td>
                        <td><?= number_format($individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                        <td><?=$customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                    </tr>
                        <?php
                        else:
                        ?>
                    <tr>
                        <th>Пленка</th>
                        <td><?=$film_name ?></td>
                        <td><?= number_format($thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                        <td><?=$customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                    </tr>
                        <?php
                        endif;
                        ?>
                    <tr>
                        <th></th>
                        <td>
                            <?php
                            switch ($ski) {
                                case STANDARD_SKI:
                                    echo "Стандартные лыжи";
                                    break;
                                case NONSTANDARD_SKI:
                                    echo "Нестандартные лыжи";
                                    break;
                                default :
                                    echo 'Без лыж';
                                    break;
                            }
                            ?>
                        </td>
                        <td colspan="2"><?=($ski == NONSTANDARD_SKI ? $width_ski.' мм' : '') ?></td>
                    </tr>
                        <?php
                        $lamination = "нет";
                        if(!empty($lamination1_film_name) || !empty($lamination1_individual_film_name)) $lamination = "1";
                        if(!empty($lamination2_film_name) || !empty($lamination2_individual_film_name)) $lamination = "2";
                            
                        if(!empty($lamination1_individual_film_name)):
                        ?>
                    <tr>
                        <th>Ламинация: <?=$lamination ?></th>
                        <td><?=$lamination1_individual_film_name ?></td>
                        <td><?= number_format($lamination1_individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination1_individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                        <td><?=$lamination1_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                    </tr>
                        <?php
                        elseif(!empty($lamination1_film_name)):
                        ?>
                    <tr>
                        <th>Ламинация: <?=$lamination ?></th>
                        <td><?=$lamination1_film_name ?></td>
                        <td><?= number_format($lamination1_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination1_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                        <td><?=$lamination1_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                    </tr>
                        <?php
                        endif;
                        if(!empty($lamination1_individual_film_name) || !empty($lamination1_film_name)):
                        ?>
                    <tr>
                        <th></th>
                        <td>
                            <?php
                            switch ($lamination1_ski) {
                                case STANDARD_SKI:
                                    echo "Стандартные лыжи";
                                    break;
                                case NONSTANDARD_SKI:
                                    echo "Нестандартные лыжи";
                                    break;
                                default :
                                    echo 'Без лыж';
                                    break;
                            }
                            ?>
                        </td>
                        <td colspan="2"><?=($lamination1_ski == NONSTANDARD_SKI ? $lamination1_width_ski.' мм' : '') ?></td>
                    </tr>
                        <?php
                        endif;
                        if(!empty($lamination2_individual_film_name)):
                        ?>
                    <tr>
                        <th></th>
                        <td><?=$lamination2_individual_film_name ?></td>
                        <td><?= number_format($lamination2_individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                        <td><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                    </tr>
                        <?php
                        elseif(!empty($lamination2_film_name)):
                        ?>
                    <tr>
                        <th></th>
                        <td><?=$lamination2_film_name ?></td>
                        <td><?= number_format($lamination2_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                        <td><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                    </tr>
                        <?php
                        endif;
                        if(!empty($lamination2_individual_film_name) || !empty($lamination2_film_name)):
                        ?>
                    <tr>
                        <th></th>
                        <td>
                            <?php
                            switch ($lamination2_ski) {
                                case STANDARD_SKI:
                                    echo 'Стандартные лыжи';
                                    break;
                                case NONSTANDARD_SKI:
                                    echo 'Нестандартные лыжи';
                                    break;
                                default :
                                    echo 'Без лыж';
                                    break;
                            }
                            ?>
                        </td>
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
                                            case OLD:
                                                echo 'Старая';
                                                break;
                                            case FLINT:
                                                echo 'Флинт';
                                                break;
                                            case KODAK;
                                                echo 'Кодак';
                                                break;
                                            case TVER;
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
                            <span class="text-nowrap">30 %</span>
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
                        <?php if(!empty($lamination1_film_variation_id) || !empty($lamination1_individual_film_name)): ?>
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
                        <?php if(!empty($lamination2_film_variation_id) || !empty($lamination2_individual_film_name)): ?>
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
                    if(!empty($lamination1_film_variation_id) || !empty($lamination1_individual_film_name) || !empty($lamination2_film_variation_id) || !empty($lamination2_individual_film_name) || $work_type_id == 2):
                    ?>
                <div class="row text-nowrap">
                    <div class="col-4 pr-4">
                        <h2 style="font-size: 20px; margin: 0; padding: 0;">Расходы</h2>
                    </div>
                        <?php if(!empty($lamination1_film_variation_id) || !empty($lamination1_individual_film_name)): ?>
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
                        <?php if(!empty($lamination1_film_variation_id) || !empty($lamination1_individual_film_name)): ?>
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
                        <?php if(!empty($lamination2_film_variation_id) || !empty($lamination2_individual_film_name)): ?>
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