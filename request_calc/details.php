<?php
include '../include/topscripts.php';
include './status_ids.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Формы
const OLD = "old";
const FLINT = "flint";
const KODAK = "kodak";
const TVER = "tver";

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select rc.date, rc.customer_id, rc.name, rc.unit, rc.quantity, wt.name work_type, "
        . "f.name film_name, fv.thickness thickness, fv.weight weight, rc.price, rc.currency, rc.individual_film_name, rc.individual_price, rc.individual_currency, rc.individual_thickness, rc.individual_density, rc.customers_material, rc.ski, "
        . "lam1f.name lamination1_film_name, lam1fv.thickness lamination1_thickness, lam1fv.weight lamination1_weight, rc.lamination1_price, rc.lamination1_currency, rc.lamination1_individual_film_name, rc.lamination1_individual_price, rc.lamination1_individual_currency, rc.lamination1_individual_thickness, rc.lamination1_individual_density, rc.lamination1_customers_material, rc.lamination1_ski, "
        . "lam2f.name lamination2_film_name, lam2fv.thickness lamination2_thickness, lam2fv.weight lamination2_weight, rc.lamination2_price, rc.lamination2_currency, rc.lamination2_individual_film_name, rc.lamination2_individual_price, rc.lamination2_individual_currency, rc.lamination2_individual_thickness, rc.lamination2_individual_density, rc.lamination2_customers_material, rc.lamination2_ski, "
        . "rc.width, rc.streams_number, m.name machine, m.colorfulness colorfulness, rc.length, rc.stream_width, rc.raport, rc.lamination_roller_width, rc.ink_number, u.first_name, u.last_name, rc.status_id, "
        . "rc.ink_1, rc.ink_2, rc.ink_3, rc.ink_4, rc.ink_5, rc.ink_6, rc.ink_7, rc.ink_8, "
        . "rc.color_1, rc.color_2, rc.color_3, rc.color_4, rc.color_5, rc.color_6, rc.color_7, rc.color_8, "
        . "rc.cmyk_1, rc.cmyk_2, rc.cmyk_3, rc.cmyk_4, rc.cmyk_5, rc.cmyk_6, rc.cmyk_7, rc.cmyk_8, "
        . "rc.percent_1, rc.percent_2, rc.percent_3, rc.percent_4, rc.percent_5, rc.percent_6, rc.percent_7, rc.percent_8, rc.cliche_1, "
        . "rc.cliche_2, rc.cliche_3, rc.cliche_4, rc.cliche_5, rc.cliche_6, rc.cliche_7, rc.cliche_8 "
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
        . "where rc.id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];
$customer_id = $row['customer_id'];
$name = $row['name'];
$unit = $row['unit'];
$quantity = $row['quantity'];
$work_type = $row['work_type'];

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