<?php
// Печать: лицевая, оборотная
const SIDE_FRONT = 1;
const SIDE_BACK = 2;

// Бирки: Принт-Дизайн, безликие
const LABEL_PRINT_DESIGN = 1;
const LABEL_FACELESS = 2;

// Упаковка: паллетированная, россыпью, европаллет, коробки
const PACKAGE_PALLETED = 1;
const PACKAGE_BULK = 2;
const PACKAGE_EUROPALLET = 3;
const PACKAGE_BOXES = 4;

// Получение объекта
$work_type_id = '';
$quantity = '';
$unit = '';
$machine_id = '';
$length = '';
$streams_number = '';
$stream_width = '';
$ink_number = '';

$film_variation_id = '';
$film_name = '';
$thickness = '';
$weight = '';
$price = '';
$currency = '';
$individual_film_name = '';
$individual_thickness = '';
$individual_density = '';

$lamination1_film_variation_id = '';
$lamination1_film_name = '';
$lamination1_thickness = '';
$lamination1_weight = '';
$lamination1_price = '';
$lamination1_currency = '';
$lamination1_individual_film_name = '';
$lamination1_individual_thickness = '';
$lamination1_individual_density = '';
$lamination1_customers_material = '';
$lamination1_ski = '';
$lamination1_width_ski = '';

$lamination2_film_variation_id = '';
$lamination2_film_name = '';
$lamination2_thickness = '';
$lamination2_weight = '';
$lamination2_price = '';
$lamination2_currency = '';
$lamination2_individual_film_name = '';
$lamination2_individual_thickness = '';
$lamination2_individual_density = '';
$lamination2_customers_material = '';
$lamination2_ski = '';
$lamination2_width_ski = '';

$width_1 = "";
$width_2 = "";
$length_pure_1 = '';
$side = '';
$winding = '';
$winding_unit = '';
$spool = '';
$labels = '';
$package = '';

$sql = "select c.work_type_id, c.quantity, c.unit, c.machine_id, c.length, c.streams_number, c.stream_width, c.ink_number, "
        . "c.film_variation_id, f.name film_name, fv.thickness thickness, fv.weight weight, c.price, c.currency, c.individual_film_name, c.individual_thickness, c.individual_density, c.customers_material, c.ski, c.width_ski, "
        . "c.lamination1_film_variation_id, lam1f.name lamination1_film_name, lam1fv.thickness lamination1_thickness, lam1fv.weight lamination1_weight, c.lamination1_price, c.lamination1_currency, c.lamination1_individual_film_name, c.lamination1_individual_thickness, c.lamination1_individual_density, c.lamination1_customers_material, c.lamination1_ski, c.lamination1_width_ski, "
        . "c.lamination2_film_variation_id, lam2f.name lamination2_film_name, lam2fv.thickness lamination2_thickness, lam2fv.weight lamination2_weight, c.lamination2_price, c.lamination2_currency, c.lamination2_individual_film_name, c.lamination2_individual_thickness, c.lamination2_individual_density, c.lamination2_customers_material, c.lamination2_ski, c.lamination2_width_ski, "
        . "cr.width_1, cr.width_2, cr.length_pure_1, tm.side, tm.winding, tm.winding_unit, tm.spool, tm.labels, tm.package "
        . "from calculation c "
        . "left join film_variation fv on c.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_variation lam1fv on c.lamination1_film_variation_id = lam1fv.id "
        . "left join film lam1f on lam1fv.film_id = lam1f.id "
        . "left join film_variation lam2fv on c.lamination2_film_variation_id = lam2fv.id "
        . "left join film lam2f on lam2fv.film_id = lam2f.id "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "inner join techmap tm on tm.calculation_id = c.id "
        . "where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $work_type_id = $row['work_type_id'];
    $quantity = $row['quantity'];
    $unit = $row['unit'];
    $machine_id = $row['machine_id'];
    $length = $row['length'];
    $streams_number = $row['streams_number'];
    $stream_width = $row['stream_width'];
    $ink_number = $row['ink_number'];
    
    $film_variation_id = $row['film_variation_id'];
    $film_name = $row['film_name'];
    $thickness = $row['thickness'];
    $weight = $row['weight'];
    $price = $row['price'];
    $currency = $row['currency'];
    $individual_film_name = $row['individual_film_name'];
    $individual_thickness = $row['individual_thickness'];
    $individual_density = $row['individual_density'];
    
    $lamination1_film_variation_id = $row['lamination1_film_variation_id'];
    $lamination1_film_name = $row['lamination1_film_name'];
    $lamination1_thickness = $row['lamination1_thickness'];
    $lamination1_weight = $row['lamination1_weight'];
    $lamination1_price = $row['lamination1_price'];
    $lamination1_currency = $row['lamination1_currency'];
    $lamination1_individual_film_name = $row['lamination1_individual_film_name'];
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
    $lamination2_individual_thickness = $row['lamination2_individual_thickness'];
    $lamination2_individual_density = $row['lamination2_individual_density'];
    $lamination2_customers_material = $row['lamination2_customers_material'];
    $lamination2_ski = $row['lamination2_ski'];
    $lamination2_width_ski = $row['lamination2_width_ski'];
    
    $width_1 = $row["width_1"];
    $width_2 = $row['width_2'];
    $length_pure_1 = $row['length_pure_1'];
    $side = $row['side'];
    $winding = $row['winding'];
    $winding_unit = $row['winding_unit'];
    $spool = $row['spool'];
    $labels = $row['labels'];
    $package = $row['package'];
}

$laminations_number = $calculation->laminations_number;

// Отходы
$waste1 = "";
$waste2 = "";
$waste3 = "";
$waste = "";

$film_name1 = empty($film_name) ? $individual_film_name : $film_name;
$film_name2 = empty($lamination1_film_name) ? $lamination1_individual_film_name : $lamination1_film_name;
$film_name3 = empty($lamination2_film_name) ? $lamination2_individual_film_name : $lamination2_film_name;

if(in_array($film_name1, WASTE_PRESS_FILMS)) {
    $waste1 = WASTE_PRESS;
}
elseif($film_name1 == WASTE_PAPER_FILM) {
    $waste1 = WASTE_PAPER;
}
elseif(empty ($film_name1)) {
    $waste1 = "";
}
else {
    $waste1 = WASTE_KAGAT;
}

if(in_array($film_name2, WASTE_PRESS_FILMS)) {
    $waste2 = WASTE_PRESS;
}
elseif ($film_name2 == WASTE_PAPER_FILM) {
    $waste2 = WASTE_PAPER;
}
elseif(empty ($film_name2)) {
    $waste2 = "";
}
else {
    $waste2 = WASTE_KAGAT;
}

if(in_array($film_name3, WASTE_PRESS_FILMS)) {
    $waste3 = WASTE_PRESS;
}
elseif($film_name3 == WASTE_PAPER_FILM) {
    $waste3 = WASTE_PAPER;
}
elseif(empty ($film_name3)) {
    $waste3 = "";
}
else {
    $waste3 = WASTE_KAGAT;
}

$waste = $waste1;
if(!empty($waste2) && $waste2 != $waste1) $waste = WASTE_KAGAT;
if(!empty($waste3) && $waste3 != $waste2) $waste = WASTE_KAGAT;
?>
<div class="cutter_info">
    <div class="subtitle">Хар-ки</div>
    <div class="subtitle">ИНФОРМАЦИЯ ПО ПЕЧАТИ</div>
    <table>
        <tr>
            <td><?= empty($machine_id) ? "" : PRINTER_NAMES[$machine_id] ?> Марка мат-ла</td>
            <td><?= (empty($film_name) ? "" : $film_name).(empty($individual_film_name) ? "" : $individual_film_name) ?></td>
        </tr>
        <tr>
            <td>Толщина</td>
            <td>
                <?php
                if(!empty($thickness)) {
                    echo DisplayNumber(floatval($thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($weight), 2), "0").' г/м<sup>2</sup>';
                }
                elseif(!empty($individual_thickness)) {
                    echo DisplayNumber(floatval($individual_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($individual_density), 2), "0").' г/м<sup>2</sup>';
                }
                else {
                    echo "0 мкм&nbsp;&ndash;&nbsp;0 г/м<sup>2</sup>";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Ширина мат-ла</td>
            <td><?= DisplayNumber(floatval($width_1), 0) ?> мм</td>
        </tr>
        <tr>
            <td>Метраж на тираж</td>
            <td><?= DisplayNumber(floatval($length_pure_1), 0) ?> м</td>
        </tr>
        <tr>
            <td>Печать</td>
            <td>
                <?php
                switch ($side) {
                    case SIDE_FRONT:
                        echo 'Лицевая';
                        break;
                    case SIDE_BACK:
                        echo 'Оборотная';
                        break;
                    default :
                        echo "Ждем данные";
                        break;
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td>Длина этикетки</td>
            <td><?= rtrim(rtrim(DisplayNumber(floatval($length), 2), "0"), ",").(empty($length) ? "" : " мм") ?></td>
        </tr>
        <tr>
            <td>Кол-во ручьёв</td>
            <td><?=$streams_number ?></td>
        </tr>
        <tr>
            <td>Красочность</td>
            <td><?=$ink_number ?> кр.</td>
        </tr>
    </table>
    <div class="subtitle">ИНФОРМАЦИЯ ПО ЛАМИНАЦИИ 1</div>
    <table>
        <tr>
            <td>Кол-во ламинаций</td>
            <td><?= $laminations_number == 2 ? "2 ламинации" : ($laminations_number == 1 ? "1 ламинация" : "нет") ?></td>
        </tr>
        <tr>
            <td>Марка пленки</td>
            <td><?= (empty($lamination1_film_name) ? "" : $lamination1_film_name).(empty($lamination1_individual_film_name) ? "" : $lamination1_individual_film_name) ?></td>
        </tr>
        <tr>
            <td>Толщина</td>
            <td>
                <?php
                if(!empty($lamination1_thickness)) {
                    echo DisplayNumber(floatval($lamination1_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($lamination1_weight), 2), "0").' г/м<sup>2</sup>';
                }
                elseif(!empty($lamination1_individual_thickness)) {
                    echo DisplayNumber(floatval($lamination1_individual_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($lamination1_individual_density), 2), "0").' г/м<sup>2</sup>';
                }
                else {
                    echo "0 мкм&nbsp;&ndash;&nbsp;0 г/м<sup>2</sup>";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Ширина мат-ла</td>
            <td><?= DisplayNumber(floatval($width_2), 0) ?> мм</td>
        </tr>
    </table>
    <div class="subtitle">ИНФОРМАЦИЯ ПО ЛАМИНАЦИИ 2</div>
    <table>
        <tr>
            <td>Марка пленки</td>
            <td><?= (empty($lamination2_film_name) ? "" : $lamination2_film_name).(empty($lamination2_individual_film_name) ? "" : $lamination2_individual_film_name) ?></td>
        </tr>
        <tr>
            <td>Толщина</td>
            <td>
                <?php
                if(!empty($lamination2_thickness)) {
                    echo DisplayNumber(floatval($lamination2_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($lamination2_weight), 2), "0").' г/м<sup>2</sup>';
                }
                elseif(!empty($lamination2_individual_thickness)) {
                    echo DisplayNumber(floatval($lamination2_individual_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($lamination2_individual_density), 2), "0").' г/м<sup>2</sup>';
                }
                else {
                    echo "0 мкм&nbsp;&ndash;&nbsp;0 г/м<sup>2</sup>";
                }
                ?>
            </td>
        </tr>
    </table>
    <div class="subtitle">ИНФОРМАЦИЯ ДЛЯ РЕЗЧИКА</div>
    <table>
        <tr>
            <td>Объем заказа</td>
            <td><?= DisplayNumber(intval($quantity), 0) ?> <?=$unit == 'kg' ? 'кг' : 'шт' ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($length_pure_1), 0) ?> м</td>
        </tr>
        <tr>
            <td>Отгрузка в</td>
            <td><?=$unit == 'kg' ? 'Кг' : 'Шт' ?></td>
        </tr>
        <tr>
            <td><?=$work_type_id == WORK_TYPE_SELF_ADHESIVE ? "Обр. шир. / Гор. зазор" : "Обрезная ширина" ?></td>
            <?php
            $norm_stream = "";
            if($work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                $sql = "select gap_stream from norm_gap order by date desc limit 1";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    $norm_stream = DisplayNumber($row[0], 2);
                }
            }
            ?>
            <td>
                <?php
                if($work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                    if(empty($norm_stream)) {
                        echo DisplayNumber(intval($stream_width), 0)." мм";
                    }
                    else {
                        echo DisplayNumber(floatval($stream_width) + floatval($norm_stream), 2)." / ".DisplayNumber(floatval($norm_stream), 2)." мм";
                    }
                }
                else {
                    echo DisplayNumber(intval($stream_width), 0)." мм";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Намотка до</td>
            <td>
                <?php
                if(empty($winding)) {
                    echo 'Ждем данные';
                }
                elseif(empty ($winding_unit)) {
                    echo 'Нет данных по кг/мм/м/шт';
                }
                elseif($winding_unit == 'pc') {
                    if(empty($length)) {
                        echo 'Нет данных по длине этикетки';
                    }
                    else {
                        echo DisplayNumber(floatval($winding) * floatval($length) / 1000, 0);
                    }
                }
                else {
                    echo DisplayNumber(floatval($winding), 0);
                }                    
                        
                switch ($winding_unit) {
                    case 'kg':
                        echo " кг";
                        break;
                    case 'mm':
                        echo " мм";
                        break;
                    case 'm':
                        echo " м";
                        break;
                    case 'pc':
                        echo " м";
                        break;
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Прим. метраж намотки</td>
            <td>
                <?php
                /* 1) Если намотка до =«кг», то Примерный метраж = (намотка до *1000*1000)/((уд вес пленка 1 + уд вес пленка 2 + уд вес пленка 3)*обрезная ширина))
                * 1) Если намотка до =«кг», то Примерный метраж = (намотка до *1000*1000)/((уд вес пленка 1 + уд вес пленка 2 + уд вес пленка 3)*обрезная ширина))-200
                * 2) Если намотка до = «мм» , то значение = "Нет"
                * 3) Если намотка до = «м», то значение = "Нет"
                * 4) Если намотка до = «шт» , то значение = "Нет" */
                if(empty($winding) || empty($winding_unit)) {
                    echo 'Ждем данные';
                }
                elseif(empty ($weight) && empty($individual_density)) {
                    echo 'Нет данных по уд. весу пленки';
                }
                elseif(empty ($width_1)) {
                    echo 'Нет данных по ширине мат-ла';
                }
                elseif($winding_unit == 'kg') {
                    $final_density = empty($weight) ? $individual_density : $weight;
                    $lamination1_final_density = empty($lamination1_weight) ? $lamination1_individual_density : $lamination1_weight;
                    $lamination2_final_density = empty($lamination2_weight) ? $lamination2_individual_density : $lamination2_weight;
                    echo DisplayNumber((floatval($winding) * 1000 * 1000) / ((floatval($final_density) + ($lamination1_final_density === null ? 0 : floatval($lamination1_final_density)) + ($lamination2_final_density === null ? 0 : floatval($lamination2_final_density))) * floatval($stream_width)) - 200, 0)." м";
                }
                else {
                    echo 'Нет';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Шпуля</td>
            <td><?= empty($spool) ? "Ждем данные" : $spool." мм" ?></td>
        </tr>
        <tr>
            <td>Этикеток в 1 м. пог.</td>
            <td>
                <?php
                if(empty($length)) {
                    echo "";
                }
                elseif($work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                    // Делаем новый расчёт (необходимо для получения параметра "количество этикеток в рапорте чистое")
                    echo DisplayNumber(floatval($calculation->number_in_raport_pure) / floatval($calculation->raport) * 1000.0, 4);
                }
                else {
                    echo DisplayNumber(1 / floatval($length) * 1000, 4);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Бирки</td>
            <td>
                <?php
                switch ($labels) {
                    case LABEL_PRINT_DESIGN:
                        echo "Принт-Дизайн";
                        break;
                    case LABEL_FACELESS:
                        echo "Безликие";
                        break;
                    default :
                        echo "Ждем данные";
                        break;
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Склейки</td>
            <td>Помечать</td>
        </tr>
        <tr>
            <td>Отходы</td>
            <td><?=$waste ?></td>
        </tr>
        <tr>
            <td>Упаковка</td>
            <td>
                <?php
                switch ($package) {
                    case PACKAGE_PALLETED:
                        echo "Паллетирование";
                        break;
                    case PACKAGE_BULK:
                        echo "Россыпью";
                        break;
                    case PACKAGE_EUROPALLET:
                        echo "Европаллет";
                        break;
                    case PACKAGE_BOXES:
                        echo "Коробки";
                        break;
                    default :
                        echo "Ждем данные";
                        break;
                }
                ?>
            </td>
        </tr>
    </table>
</div>