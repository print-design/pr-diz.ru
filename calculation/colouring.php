<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

// Переменные для валидации цвета, CMYK и процента
for($i=1; $i<=8; $i++) {
    $color_valid_var = 'color_'.$i.'_valid';
    $$color_valid_var = '';
    
    $cmyk_valid_var = 'cmyk_'.$i.'_valid';
    $$cmyk_valid_var = '';
    
    $percent_valid_var = 'percent_'.$i.'_valid';
    $$percent_valid_var = '';
}

if(null !== filter_input(INPUT_POST, 'save-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    // Проверка валидности цвета, CMYK и процента
    $paints_count = filter_input(INPUT_POST, 'paints_count');
    
    for($i=1; $i<=8; $i++) {
        if(!empty($paints_count) && is_numeric($paints_count) && $i <= $paints_count) {
            $paint_var = "paint_".$i;
            $$paint_var = filter_input(INPUT_POST, 'paint_'.$i);
            
            $color_var = "color_".$i;
            $$color_var = filter_input(INPUT_POST, 'color_'.$i);
            
            $cmyk_var = "cmyk_".$i;
            $$cmyk_var = filter_input(INPUT_POST, 'cmyk_'.$i);
            
            $percent_var = "percent_".$i;
            $$percent_var = filter_input(INPUT_POST, 'percent_'.$i);
            
            if(empty($$percent_var)) {
                $percent_valid_var = 'percent_'.$i.'_valid';
                $$percent_valid_var = ISINVALID;
                $form_valid = false;
            }
            
            if($$paint_var == 'panton' && empty($$color_var)) {
                $color_valid_var = 'color_'.$i.'_valid';
                $$color_valid_var = ISINVALID;
                $form_valid = false;
            }
            
            if($$paint_var == 'cmyk' && empty($$cmyk_var)) {
                $cmyk_valid_var = 'cmyk_'.$i.'_valid';
                $$cmyk_valid_var = ISINVALID;
                $form_valid = false;
            }
        }
    }
    
    if($form_valid) {
        $paints_count = filter_input(INPUT_POST, 'paints_count');
        if(empty($paints_count)) $paints_count = "NULL";
        
        // Данные о цвете
        for($i=1; $i<=8; $i++) {
            $paint_var = "paint_$i";
            $color_var = "color_$i";
            $cmyk_var = "cmyk_$i";
            $percent_var = "percent_$i";
            $form_var = "form_$i";
            
            $$paint_var = null;
            $$color_var = "NULL";
            $$cmyk_var = null;
            $$percent_var = "NULL";
            $$form_var = null;
            
            if(!empty($paints_count) && $paints_count >= $i) {
                $$paint_var = filter_input(INPUT_POST, "paint_$i");
            
                $$color_var = filter_input(INPUT_POST, "color_$i");
                if(empty($$color_var)) $$color_var = "NULL";
            
                $$cmyk_var = filter_input(INPUT_POST, "cmyk_$i");
            
                $$percent_var = filter_input(INPUT_POST, "percent_$i");
                if(empty($$percent_var)) $$percent_var = "NULL";
            
                $$form_var = filter_input(INPUT_POST, "form_$i");
            }
        }
        
        // Сохранение в базу
        if(empty($error_message)) {
            $sql = "update calculation set paints_count=$paints_count, "
                    . "paint_1='$paint_1', paint_2='$paint_2', paint_3='$paint_3', paint_4='$paint_4', paint_5='$paint_5', paint_6='$paint_6', paint_7='$paint_7', paint_8='$paint_8', "
                    . "color_1=$color_1, color_2=$color_2, color_3=$color_3, color_4=$color_4, color_5=$color_5, color_6=$color_6, color_7=$color_7, color_8=$color_8, "
                    . "cmyk_1='$cmyk_1', cmyk_2='$cmyk_2', cmyk_3='$cmyk_3', cmyk_4='$cmyk_4', cmyk_5='$cmyk_5', cmyk_6='$cmyk_6', cmyk_7='$cmyk_7', cmyk_8='$cmyk_8', "
                    . "percent_1=$percent_1, percent_2=$percent_2, percent_3=$percent_3, percent_4=$percent_4, percent_5=$percent_5, percent_6=$percent_6, percent_7=$percent_7, percent_8=$percent_8, "
                    . "form_1='$form_1', form_2='$form_2', form_3='$form_3', form_4='$form_4', form_5='$form_5', form_6='$form_6', form_7='$form_7', form_8='$form_8'"
                    . "where id=$id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            
            if(empty($error_message)) {
                header('Location: calculation.php?id='.$id);
            }
        }
    }
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select c.date, c.customer_id, c.name name, c.work_type_id, c.quantity, c.unit, "
        . "c.brand_name, c.thickness, other_brand_name, other_price, other_thickness, other_weight, c.customers_material, width, "
        . "c.lamination1_brand_name, c.lamination1_thickness, lamination1_other_brand_name, lamination1_other_price, lamination1_other_thickness, lamination1_other_weight, c.lamination1_customers_material, "
        . "c.lamination2_brand_name, c.lamination2_thickness, lamination2_other_brand_name, lamination2_other_price, lamination2_other_thickness, lamination2_other_weight, c.lamination2_customers_material, "
        . "c.length, c.stream_width, c.streams_count, c.machine_id, c.raport, c.lamination_roller, "
        . "c.extracharge, c.ski, c.no_ski, "
        . "cu.name customer, cu.phone customer_phone, cu.extension customer_extension, cu.email customer_email, cu.person customer_person, "
        . "wt.name work_type, "
        . "mt.name machine, mt.colorfulness, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.brand_name and fbw.thickness = c.thickness limit 1) weight, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination1_brand_name and fbw.thickness = c.lamination1_thickness limit 1) lamination1_weight, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination2_brand_name and fbw.thickness = c.lamination2_thickness limit 1) lamination2_weight "
        . "from calculation c "
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
$width = $row['width'];
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
$length = $row['length'];
$stream_width = $row['stream_width'];
$streams_count = $row['streams_count'];
$machine_id = $row['machine_id'];
$raport = $row['raport'];
$lamination_roller = $row['lamination_roller'];

$extracharge = $row['extracharge'];
$ski = $row['ski'];
$no_ski = $row['no_ski'];

$customer = $row['customer'];
$customer_phone = $row['customer_phone'];
$customer_extension = $row['customer_extension'];
$customer_email = $row['customer_email'];
$customer_person = $row['customer_person'];

$work_type = $row['work_type'];

$machine = $row['machine'];
$colorfulness = $row['colorfulness'];

// Данные о цветах
for ($i=1; $i<=8; $i++) {
    $paint_var = "paint_$i";
    $$paint_var = filter_input(INPUT_POST, "paint_$i");
    if(null === $$paint_var) {
        if(isset($row["paint_$i"])) $$paint_var = $row["paint_$i"];
        else $$paint_var = null;
    }
    
    $color_var = "color_$i";
    $$color_var = filter_input(INPUT_POST, "color_$i");
    if(null === $$color_var) {
        if(isset($row["color_$i"])) $$color_var = $row["color_$i"];
        else $$color_var = null;
    }
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = filter_input(INPUT_POST, "cmyk_$i");
    if(null === $$cmyk_var) {
        if(isset($row["cmyk_$i"])) $$cmyk_var = $row["cmyk_$i"];
        else $$cmyk_var = null;
    }
    
    $percent_var = "percent_$i";
    $$percent_var = filter_input(INPUT_POST, "percent_$i");
    if(null === $$percent_var) {
        if(isset($row["percent_$i"])) $$percent_var = $row["percent_$i"];
        else $$percent_var = null;
    }
    
    $form_var = "form_$i";
    $$form_var = filter_input(INPUT_POST, "form_$i");
    if(null === $$form_var) {
        if(isset($row["form_$i"])) $$form_var = $row["form_$i"];
        else $$form_var = null;
    }
}
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
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/calculation/calculation.php?id=<?=$id ?>">Отмена</a>
            <div class="row">
                <!-- Левая половина -->
                <div class="col-5" id="left_side">
                    <h1 style="font-size: 32px; font-weight: 600;">Добавление красочности</h1>
                    <table class="w-100 calculation-table">
                        <tr><th>Заказчик</th><td class="param-value"><?=$customer ?></td></tr>
                        <tr><th>Название заказа</th><td class="param-value"><?=$name ?></td></tr>
                        <tr><th>Тип работы</th><td class="param-value"><?=$work_type ?></td></tr>
                            <?php
                            if(!empty($quantity) && !empty($unit)):
                            ?>
                        <tr><th>Объем заказа</th><td class="param-value"><?= rtrim(rtrim(number_format($quantity, 2, ",", " "), "0"), ",") ?> <?=$unit == 'kg' ? "кг" : "шт" ?></td></tr>
                            <?php
                            endif;
                            if(!empty($machine)):
                            ?>
                        <tr><th>Печатная машина</th><td class="param-value"><?=$machine.' ('.$colorfulness.' красок)' ?></td></tr>
                            <?php
                            endif;
                            if(!empty($width)):
                            ?>
                        <tr><th>Ширина материала</th><td class="param-value"><?=$width ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($stream_width)):
                            ?>
                        <tr><th>Ширина ручья</th><td class="param-value"><?= rtrim(rtrim(number_format($stream_width, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($streams_count)):
                            ?>
                        <tr><th>Количество ручьев</th><td class="param-value"><?= $streams_count ?></td></tr>
                            <?php
                            endif;
                            if(!empty($raport)):
                            ?>
                        <tr><th>Рапорт</th><td class="param-value"><?= rtrim(rtrim(number_format($raport, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($length)):
                            ?>
                        <tr><th>Длина этикетки вдоль рапорта вала</th><td class="param-value"><?= rtrim(rtrim(number_format($length, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($lamination_roller)):
                            ?>
                        <tr><th>Ширина вала ламинации</th><td class="param-value"><?= rtrim(rtrim(number_format($lamination_roller, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($machine)):
                            ?>
                        <tr>
                            <th>Ширина лыж</th>
                            <td class="param-value">
                                <?php
                                if($no_ski) {
                                    echo "Без лыж";
                                }
                                else {
                                    echo rtrim(rtrim(number_format($ski, 2, ",", " "), "0"), ",")." м";
                                }
                                ?>
                            </td>
                        </tr>
                            <?php
                            endif;
                            if(!empty($brand_name) && !empty($thickness)):
                            ?>
                        <tr>
                            <th>Пленка</th>
                            <td class="param-value">
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
                            <td class="param-value">
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
                            <td class="param-value">
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
                            <td class="param-value">
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
                                        <td>
                                            <?php
                                            switch ($$form_var) {
                                                case "old":
                                                    echo 'Старая';
                                                    break;
                                                case "flint":
                                                    echo 'Новая Флинт';
                                                    break;
                                                case "kodak":
                                                    echo "Новая Кодак";
                                                    break;
                                                case "tver":
                                                    echo "Новая Тверь";
                                                    break;
                                                default:
                                                    echo $$form_var;
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
                
                    <form method="post">
                        <input type="hidden" name="id" value="<?=$id ?>" />
                        <div class="form-group">
                            <label for="paints_count">Количество красок</label>
                            <select id="paints_count" name="paints_count" class="form-control" required="required">
                                <option value="" hidden="hidden">Количество красок...</option>
                                    <?php
                                    if(!empty($paints_count) || !empty($machine_id)):
                                        for($i = 1; $i <= $colorfulness; $i++):
                                            $selected = "";
                                        if($paints_count == $i) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                <option<?=$selected ?>><?=$i ?></option>
                                    <?php
                                    endfor;
                                    endif;
                                    ?>
                            </select>
                        </div>
                        <!-- Каждая краска -->
                            <?php
                            for($i=1; $i<=8; $i++):
                                $block_class = " d-none";
                                $paint_required = "";

                                if(!empty($paints_count) && is_numeric($paints_count) && $i <= $paints_count) {
                                    $block_class = "";
                                    $paint_required = " required='required'";
                                }
                            ?>
                        <div class="row paint_block<?=$block_class ?>" id="paint_block_<?=$i ?>">
                            <?php
                            $paint_class = " col-12";
                            $cmyk_class = " d-none";
                            $color_class = " d-none";
                            $percent_class = " d-none";
                            $form_class = " d-none";
                            
                            $paint_var_name = "paint_$i";
                            
                            if($$paint_var_name == "white" || $$paint_var_name == "lacquer") {
                                $paint_class = " col-6";
                                $percent_class = " col-3";
                                $form_class = " col-3";
                            }
                            else if($$paint_var_name == "panton") {
                                $paint_class = " col-3";
                                $color_class = " col-3";
                                $percent_class = " col-3";
                                $form_class = " col-3";
                            }
                            else if($$paint_var_name == "cmyk") {
                                $paint_class = " col-3";
                                $cmyk_class = " col-3";
                                $percent_class = " col-3";
                                $form_class = " col-3";
                            }
                            ?>
                            <div class="form-group<?=$paint_class ?>" id="paint_group_<?=$i ?>">
                                <label for="paint_<?=$i ?>"><?=$i ?> цвет</label>
                                <select id="paint_<?=$i ?>" name="paint_<?=$i ?>" class="form-control paint" data-id="<?=$i ?>"<?=$paint_required ?>>
                                    <option value="" hidden="hidden" selected="selected">Цвет...</option>
                                        <?php
                                        $cmyk_selected = "";
                                        $panton_selected = "";
                                        $white_selected = "";
                                        $lacquer_selected = "";
                                    
                                        $selected_var_name = $$paint_var_name."_selected";
                                        $$selected_var_name = " selected='selected'";
                                        ?>
                                    <option value="cmyk"<?=$cmyk_selected ?>>CMYK</option>
                                    <option value="panton"<?=$panton_selected ?>>Пантон</option>
                                    <option value="white"<?=$white_selected ?>>Белый</option>
                                    <option value="lacquer"<?=$lacquer_selected ?>>Лак</option>
                                </select>
                                <div class="invalid-feedback">Цвет обязательно</div>
                            </div>
                            <div class="form-group<?=$color_class ?>" id="color_group_<?=$i ?>">
                                <?php
                                $color_var = "color_$i"; 
                                $color_var_valid = 'color_'.$i.'_valid'; 
                                ?>
                                <label for="color_<?=$i ?>">Номер пантона</label>
                                <div class="input-group flex-nowrap">
                                    <div class="input-group-prepend"><span class="input-group-text">P</span></div>
                                    <input type="text" 
                                           id="color_<?=$i ?>" 
                                           name="color_<?=$i ?>" 
                                           class="form-control panton color<?=$$color_var_valid ?>" 
                                           placeholder="Номер пантона..." 
                                           value="<?= empty($$color_var) ? "" : $$color_var?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'color_<?=$i ?>'); $(this).attr('name', 'color_<?=$i ?>'); $(this).attr('placeholder', 'Номер пантона...');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'color_<?=$i ?>'); $(this).attr('name', 'color_<?=$i ?>'); $(this).attr('placeholder', 'Номер пантона...');" 
                                           onfocusout="javascript: $(this).attr('id', 'color_<?=$i ?>'); $(this).attr('name', 'color_<?=$i ?>'); $(this).attr('placeholder', 'Номер пантона...');" />
                                </div>
                                <div class="invalid-feedback">Код цвета обязательно</div>
                            </div>
                            <div class="form-group<?=$cmyk_class ?>" id="cmyk_group_<?=$i ?>">
                                <?php
                                $cmyk_var = "cmyk_$i";
                                $cmyk_var_valid = 'cmyk_'.$i.'_valid';
                                ?>
                                <label for="cmyk_<?=$i ?>">CMYK</label>
                                <select id="cmyk_<?=$i ?>" name="cmyk_<?=$i ?>" class="form-control cmyk<?=$$cmyk_var_valid ?>" data-id="<?=$i ?>">
                                    <option value="" hidden="hidden" selected="selected">CMYK...</option>
                                        <?php
                                        $cyan_selected = "";
                                        $magenta_selected = "";
                                        $yellow_selected = "";
                                        $kontur_selected = "";
                                    
                                        $cmyk_var_selected = $$cmyk_var.'_selected';
                                        $$cmyk_var_selected = " selected='selected'";
                                        ?>
                                    <option value="cyan"<?=$cyan_selected ?>>Cyan</option>
                                    <option value="magenta"<?=$magenta_selected ?>>Magenta</option>
                                    <option value="yellow"<?=$yellow_selected ?>>Yellow</option>
                                    <option value="kontur"<?=$kontur_selected ?>>Kontur</option>
                                </select>
                                <div class="invalid-feedback">Выберите компонент цвета</div>
                            </div>
                            <div class="form-group<?=$percent_class ?>" id="percent_group_<?=$i ?>">
                                <?php
                                $percent_var = "percent_$i";
                                $percent_var_valid = 'percent_'.$i.'_valid';
                                ?>
                                <label for="percent_<?=$i ?>">Процент<br /></label>
                                <div class="input-group flex-nowrap">
                                    <input type="text" 
                                           id="percent_<?=$i ?>" 
                                           name="percent_<?=$i ?>" 
                                           class="form-control int-only percent<?=$$percent_var_valid ?>" 
                                           style="width: 80px;" 
                                           value="<?= empty($$percent_var) ? "" : $$percent_var ?>" 
                                           placeholder="Процент..." 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'percent_<?=$i ?>'); $(this).attr('name', 'percent_<?=$i ?>'); $(this).attr('placeholder', 'Процент...');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'percent_<?=$i ?>'); $(this).attr('name', 'percent_<?=$i ?>'); $(this).attr('placeholder', 'Процент...');" 
                                           onfocusout="javascript: $(this).attr('id', 'percent_<?=$i ?>'); $(this).attr('name', 'percent_<?=$i ?>'); $(this).attr('placeholder', 'Процент...');" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="invalid-feedback">Процент обязательно</div>
                                </div>
                            </div>
                            <div class="form-group<?=$form_class ?>" id="form_group_<?=$i ?>">
                                <label for="form_<?=$i ?>">Форма</label>
                                <select id="form_<?=$i ?>" name="form_<?=$i ?>" class="form-control form">
                                    <?php
                                    $old_selected = "";
                                    $flint_selected = "";
                                    $kodak_selected = "";
                                    $tver_selected = "";
                                    
                                    $form_var = "form_$i";
                                    $form_selected_var = $$form_var."_selected";
                                    $$form_selected_var = " selected='selected'";
                                    ?>
                                    <option value="old"<?=$old_selected ?>>Старая</option>
                                    <option value="flint"<?=$flint_selected ?>>Новая Флинт</option>
                                    <option value="kodak"<?=$kodak_selected ?>>Новая Кодак</option>
                                    <option value="tver"<?=$tver_selected ?>>Новая Тверь</option>
                                </select>
                            </div>
                        </div>
                            <?php
                            endfor;
                            ?>
                        <button type="submit" name="save-submit" class="btn btn-dark mt-5 mr-2" style="width: 200px;">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // В поле "процент" ограничиваем значения: целые числа от 1 до 100
            $('.percent').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 100)) {
                    return false;
                }
            });
    
            $(".percent").change(function(){
                ChangeLimitIntValue($(this), 100);
            });
            
            // Обработка выбора количества красок
            $('#paints_count').change(function(){
                var count = $(this).val();
                $('.paint_block').addClass('d-none');
                $('.paint').removeAttr('required');
                
                if(count != '') {
                    iCount = parseInt(count);
                    
                    for(var i=1; i<=iCount; i++) {
                        $('#paint_block_' + i).removeClass('d-none');
                        $('#paint_' + i).attr('required', 'required');
                    }
                }
            });
            
            // Обработка выбора краски
            $('.paint').change(function(){
                paint = $(this).val();
                var data_id = $(this).attr('data-id');
                
                // Устанавливаем видимость всех элементов по умолчанию, как если бы выбрали пустое значение
                $('#paint_group_' + data_id).removeClass('col-12');
                $('#paint_group_' + data_id).removeClass('col-6');
                $('#paint_group_' + data_id).removeClass('col-3');
                
                $('#color_group_' + data_id).removeClass('col-3');
                $('#color_group_' + data_id).addClass('d-none');
                
                $('#cmyk_group_' + data_id).removeClass('col-3');
                $('#cmyk_group_' + data_id).addClass('d-none');
                
                $('#percent_group_' + data_id).removeClass('col-3');
                $('#percent_group_' + data_id).addClass('d-none');
                
                $('#form_group_' + data_id).removeClass('col-3');
                $('#form_group_' + data_id).addClass('d-none');
                
                // Снимаем атрибут required с кода цвета, CMYK и процента
                $('#color_' + data_id).removeAttr('required');
                $('#cmyk_' + data_id).removeAttr('required');
                $('#percent_' + data_id).removeAttr('required');
                
                // Затем, в зависимости от выбранного значения, устанавливаем видимость нужного элемента для этого значения
                if(paint == 'lacquer')  {
                    $('#paint_group_' + data_id).addClass('col-6');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#form_group_' + data_id).addClass('col-3');
                    $('#form_group_' + data_id).removeClass('d-none');
                    
                    $('#percent_' + data_id).attr('required', 'required');
                }
                else if(paint == 'white') {
                    $('#paint_group_' + data_id).addClass('col-6');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#form_group_' + data_id).addClass('col-3');
                    $('#form_group_' + data_id).removeClass('d-none');
                    
                    $('#percent_' + data_id).attr('required', 'required');
                }
                else if(paint == 'cmyk') {
                    $('#paint_group_' + data_id).addClass('col-3');
                    $('#cmyk_group_' + data_id).addClass('col-3');
                    $('#cmyk_group_' + data_id).removeClass('d-none');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#form_group_' + data_id).addClass('col-3');
                    $('#form_group_' + data_id).removeClass('d-none');
                    
                    $('#percent_' + data_id).attr('required', 'required');
                    $('#cmyk_' + data_id).attr('required', 'required');
                }
                else if(paint == 'panton') {
                    $('#paint_group_' + data_id).addClass('col-3');
                    $('#color_group_' + data_id).addClass('col-3');
                    $('#color_group_' + data_id).removeClass('d-none');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#form_group_' + data_id).addClass('col-3');
                    $('#form_group_' + data_id).removeClass('d-none');
                    
                    $('#percent_' + data_id).attr('required', 'required');
                    $('#color_' + data_id).attr('required', 'required');
                }
                else {
                    $('#paint_group_' + data_id).addClass('col-12');
                }
            });
        </script>
    </body>
</html>