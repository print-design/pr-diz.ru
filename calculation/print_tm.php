<?php
include '../include/topscripts.php';
include './status_ids.php';
include './calculation.php';

// Печать: лицевая, оборотная
const SIDE_FRONT = 1;
const SIDE_BACK = 2;

// Бирки: Принт-Дизайн, безликие
const LABEL_PRINT_DESIGN = 1;
const LABEL_FACELESS = 2;

// Упаковка: паллетированная, россыпью
const PACKAGE_PALLETED = 1;
const PACKAGE_BULK = 2;

// Значение марки плёнки "другая"
const INDIVIDUAL = -1;

// Отходы
const WASTE_PRESS = "В пресс";
const WASTE_KAGAT = "В кагат";
const WASTE_PAPER = "В макулатуру";

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select c.date, c.customer_id, c.name calculation, c.quantity, c.unit, c.work_type_id, c.machine_id, (select shortname from machine where id = c.machine_id) machine, laminator_id, "
        . "c.film_variation_id, f.name film_name, fv.thickness thickness, fv.weight weight, c.price, c.currency, c.individual_film_name, c.individual_thickness, c.individual_density, c.customers_material, c.ski, c.width_ski, "
        . "c.lamination1_film_variation_id, lam1f.name lamination1_film_name, lam1fv.thickness lamination1_thickness, lam1fv.weight lamination1_weight, c.lamination1_price, c.lamination1_currency, c.lamination1_individual_film_name, c.lamination1_individual_thickness, c.lamination1_individual_density, c.lamination1_customers_material, c.lamination1_ski, c.lamination1_width_ski, "
        . "c.lamination2_film_variation_id, lam2f.name lamination2_film_name, lam2fv.thickness lamination2_thickness, lam2fv.weight lamination2_weight, c.lamination2_price, c.lamination2_currency, c.lamination2_individual_film_name, c.lamination2_individual_thickness, c.lamination2_individual_density, c.lamination2_customers_material, c.lamination2_ski, c.lamination2_width_ski, "
        . "c.streams_number, c.stream_width, c.length, c.raport, c.lamination_roller_width, c.ink_number, "
        . "c.ink_1, c.ink_2, c.ink_3, c.ink_4, c.ink_5, c.ink_6, c.ink_7, c.ink_8, "
        . "c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, "
        . "c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
        . "c.percent_1, c.percent_2, c.percent_3, c.percent_4, c.percent_5, c.percent_6, c.percent_7, c.percent_8, c.cliche_1, "
        . "c.cliche_2, c.cliche_3, c.cliche_4, c.cliche_5, c.cliche_6, c.cliche_7, c.cliche_8, "
        . "cus.name customer, "
        . "u.last_name, u.first_name, "
        . "wt.name work_type, "
        . "cr.width_1, cr.length_pure_1, cr.length_dirty_1, cr.width_2, cr.length_pure_2, cr.length_dirty_2, cr.width_3, cr.length_pure_3, cr.length_dirty_3, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, "
        . "tm.id techmap_id, tm.date techmap_date, tm.side, tm.winding, tm.winding_unit, tm.spool, tm.labels, tm.package, tm.roll_type, tm.comment "
        . "from calculation c "
        . "left join techmap tm on tm.calculation_id = c.id "
        . "left join film_variation fv on c.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_variation lam1fv on c.lamination1_film_variation_id = lam1fv.id "
        . "left join film lam1f on lam1fv.film_id = lam1f.id "
        . "left join film_variation lam2fv on c.lamination2_film_variation_id = lam2fv.id "
        . "left join film lam2f on lam2fv.film_id = lam2f.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join user u on c.manager_id = u.id "
        . "inner join work_type wt on c.work_type_id = wt.id "
        . "left join calculation_result cr on cr.calculation_id = c.id "
        . "where c.id = $id";

$fetcher = new Fetcher($sql);
$row = $fetcher->Fetch();

$date = $row['date'];
$customer_id = $row['customer_id'];
$calculation = $row['calculation'];
$quantity = $row['quantity'];
$unit = $row['unit'];
$work_type_id = $row['work_type_id'];
$machine_id = $row['machine_id'];
$machine = $row['machine'];
$laminator_id = $row['laminator_id'];

$film_variation_id = $row['film_variation_id'];
$film_name = $row['film_name'];
$thickness = $row['thickness'];
$weight = $row['weight'];
$price = $row['price'];
$currency = $row['currency'];
$individual_film_name = $row['individual_film_name'];
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

$streams_number = $row['streams_number'];
$stream_width = $row['stream_width'];
$length = $row['length'];
$raport = $row['raport'];
$lamination_roller_width = $row['lamination_roller_width'];
$ink_number = $row['ink_number']; if(empty($ink_number)) $ink_number = 0;

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

$customer = $row['customer'];
$last_name = $row['last_name'];
$first_name = $row['first_name'];
$work_type = $row['work_type'];

$width_1 = $row['width_1'];
$length_pure_1 = $row['length_pure_1'];
$length_dirty_1 = $row['length_dirty_1'];
$width_2 = $row['width_2'];
$length_pure_2 = $row['length_pure_2'];
$length_dirty_2 = $row['length_dirty_2'];
$width_3 = $row['width_3'];
$length_pure_3 = $row['length_pure_3'];
$length_dirty_3 = $row['length_dirty_3'];

$num_for_customer = $row['num_for_customer'];

$lamination = "нет";
if(!empty($lamination1_film_name) || !empty($lamination1_individual_film_name)) $lamination = "1";
if(!empty($lamination2_film_name) || !empty($lamination2_individual_film_name)) $lamination = "2";

$techmap_id = $row['techmap_id'];
$techmap_date = $row['techmap_date']; if(empty($techmap_date)) $techmap_date = date('Y-m-d H:i:s');

$side = filter_input(INPUT_POST, 'side');
if($side === null) $side = $row['side'];

$winding = filter_input(INPUT_POST, 'winding');
if($winding === null) $winding = $row['winding'];

$winding_unit = filter_input(INPUT_POST, 'winding_unit');
if($winding_unit === null) $winding_unit = $row['winding_unit'];

$spool = filter_input(INPUT_POST, 'spool');
if($spool === null) $spool = $row['spool'];

$labels = filter_input(INPUT_POST, 'labels');
if($labels === null) $labels = $row['labels'];

$package = filter_input(INPUT_POST, 'package');
if($package === null) $package = $row['package'];

$roll_type = filter_input(INPUT_POST, 'roll_type');
if($roll_type === null) $roll_type = $row['roll_type'];

$comment = filter_input(INPUT_POST, 'comment');
if($comment === null) $comment = $row['comment'];

// ПОЛУЧЕНИЕ НОРМ
$data_priladka = new DataPriladka(0, 0, 0, 0);
$data_priladka_laminator = new DataPriladka(0, 0, 0, 0);

if(!empty($date)) {
    if(empty($machine_id)) {
        $data_priladka = new DataPriladka(0, 0, 0, 0);
    }
    else {
        $sql = "select time, length, stamp, waste_percent from norm_priladka where date <= '$date' and machine_id = $machine_id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if ($row = $fetcher->Fetch()) {
            $data_priladka = new DataPriladka($row['time'], $row['length'], $row['stamp'], $row['waste_percent']);
        }
    }
    
    if(empty($laminator_id)) {
        $data_priladka_laminator = new DataPriladka(0, 0, 0, 0);
    }
    else {
        $sql = "select time, length, waste_percent from norm_laminator_priladka where date <= '$date' and laminator_id = $laminator_id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $data_priladka_laminator = new DataPriladka($row['time'], $row['length'], 0, $row['waste_percent']);
        }
    }
}

// УРАВНИВАЮЩИЕ КОЭФФИЦИЕНТЫ
$uk2 = !empty($lamination1_film_name) || !empty($lamination1_individual_film_name) ? 1 : 0; // "нет ламинации - 0, есть ламинация - 1"
$uk3 = !empty($lamination2_film_name) || !empty($lamination2_individual_film_name) ? 1 : 0; // "нет второй ламинации - 0, есть вторая ламинация - 1"

// Отходы
$waste1 = "";
$waste2 = "";
$waste3 = "";
$waste = "";

$film_name1 = empty($film_name) ? $individual_film_name : $film_name;
$film_name2 = empty($lamination1_film_name) ? $lamination1_individual_film_name : $lamination1_film_name;
$film_name3 = empty($lamination2_film_name) ? $lamination2_individual_film_name : $lamination2_film_name;

$waste_press_films = array("CPP cast", "CPP LA", "HGPL прозрачка", "HMIL.M металл", "HOHL жемчуг", "HWHL белая", "LOBA жемчуг", "LOHM.M", "MGS матовая");
$waste_paper_film = "Офсет БДМ-7";

if(in_array($film_name1, $waste_press_films)) {
    $waste1 = WASTE_PRESS;
}
elseif($film_name1 == $waste_paper_film) {
    $waste1 = WASTE_PAPER;
}
elseif(empty ($film_name1)) {
    $waste1 = "";
}
else {
    $waste1 = WASTE_KAGAT;
}

if(in_array($film_name2, $waste_press_films)) {
    $waste2 = WASTE_PRESS;
}
elseif ($film_name2 == $waste_paper_film) {
    $waste2 = WASTE_PAPER;
}
elseif(empty ($film_name2)) {
    $waste2 = "";
}
else {
    $waste2 = WASTE_KAGAT;
}

if(in_array($film_name3, $waste_press_films)) {
    $waste3 = WASTE_PRESS;
}
elseif($film_name3 == $waste_paper_film) {
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

// Текущее время
$current_date_time = date("dmYHis");
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            body {
                padding-left: 0;
                font-family: 'SF Pro Display';
                font-size: 16px;
            }
            
            .header_qr {
                margin-right: 15px;
            }
            
            .header_title {
                font-weight: bold;
                font-size: 18px;
                vertical-align: middle;
            }
            
            .right_logo {
                padding-right: 10px;
            }
            
            #main {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            #title {
                font-weight: bold;
                font-size: 30px;
                margin-top: 20px;
            }
            
            #subtitle {
                font-weight: bold;
                font-size: 24px;
                margin-top: 8px;
            }
            
            .topproperty {
                font-size: 18px;
                margin-top: 6px;
            }
            
            .table-header {
                color: #cccccc;
                padding-top: 6px;
                border-bottom: solid 2px gray;
            }
            
            td {
                line-height: 30px;
                border-bottom: solid 1px #cccccc;
            }
            
            tr td:nth-child(2) {
                text-align: right;
            }
            
            tr.left td:nth-child(2) {
                text-align: left;
            }
            
            table.fotometka tr td {
                border: solid 1px #dddddd;
                padding-left: 4px;
                padding-top: 4px;
                padding-right: 20px;
                text-align: right;
                vertical-align: top;
            }
        </style>
    </head>
    <body>
        <div class="d-flex justify-content-between">
            <div>
                <?php
                include '../qr/qrlib.php';
                $errorCorrectionLevel = 'L'; // 'L','M','Q','H'
                $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/calculation/details.php?id='.$id;
                $filename = "../temp/$current_date_time.png";
                
                do {
                    QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 3, 4, true);
                } while (!file_exists($filename));
                ?>
                <div class="d-inline-block header_qr"><img src='<?=$filename ?>' style='height: 100px; width: 100px;' /></div>
                <div class="d-inline-block header_title">
                    Заказ №<?=$customer_id ?>-<?=$num_for_customer ?><br />
                    от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?>
                </div>
            </div>
            <div>
                <div class="d-inline-block right_logo"><img src="../images/logo_with_label.svg" /></div>
            </div>
        </div>
        <div id="main">
            <div id="title">Заказчик: <?=$customer ?></div>
            <div id="subtitle">Наименование: <?=$calculation ?></div>
            <div class="row">
                <div class="col-6 topproperty">
                    <strong>Карта составлена:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DateTime::createFromFormat('Y-m-d H:i:s', $techmap_date)->format('d.m.Y H:i') ?>
                </div>
                <div class="col-6 topproperty">
                    <strong>Менеджер:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$first_name ?> <?=$last_name ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6 topproperty">
                    <strong>Объем заказа:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= CalculationBase::Display(intval($quantity), 0) ?> <?=$unit == 'kg' ? 'кг' : 'шт' ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= CalculationBase::Display(floatval($length_pure_1), 0) ?> м
                </div>
                <div class="col-6 topproperty">
                    <strong>Тип работы:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$work_type ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4" style="border-right: solid 1px #cccccc;">
                    <table class="w-100">
                        <tr>
                            <td colspan="2" class="table-header">Информация для печатника</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Печать</td>
                        </tr>
                        <tr>
                            <td>Машина</td>
                            <td><?= empty($machine) ? "" : ($machine == CalculationBase::COMIFLEX ? "Comiflex" : "ZBS") ?></td>
                        </tr>
                        <tr>
                            <td>Марка пленки</td>
                            <td><?= empty($film_name) ? $individual_film_name : $film_name ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td><?= empty($film_name) ? CalculationBase::Display(floatval($individual_thickness), 0) : CalculationBase::Display(floatval($thickness), 0) ?> мкм</td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= CalculationBase::Display(floatval($width_1), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Метраж на приладку</td>
                            <td><?= CalculationBase::Display(floatval($data_priladka->length) * floatval($ink_number), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= CalculationBase::Display(floatval($length_pure_1), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Всего мат-ла</td>
                            <td><?= CalculationBase::Display(floatval($length_dirty_1), 0) ?> м</td>
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
                            <td>Рапорт</td>
                            <td><?= CalculationBase::Display(floatval($raport), 3) ?></td>
                        </tr>
                        <tr>
                            <td>Растяг</td>
                            <td>Нет</td>
                        </tr>
                        <tr>
                            <td>Ширина ручья</td>
                            <td><?=$stream_width.(empty($stream_width) ? "" : " мм") ?></td>
                        </tr>
                        <tr>
                            <td>Длина этикетки</td>
                            <td><?= CalculationBase::Display(floatval($length), 0).(empty($length) ? "" : " мм") ?></td>
                        </tr>
                        <tr>
                            <td>Кол-во ручьёв</td>
                            <td><?=$streams_number ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-4" style="border-right: solid 1px #cccccc;">
                    <table class="w-100">
                        <tr>
                            <td colspan="2" class="table-header">Информация для ламинации</td>
                        </tr>
                        <tr>
                            <td>Кол-во ламинаций</td>
                            <td><?=$lamination ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Ламинация 1</td>
                        </tr>
                        <tr>
                            <td>Марка пленки</td>
                            <td><?= empty($lamination1_film_name) ? $lamination1_individual_film_name : $lamination1_film_name ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td><?= empty($lamination1_film_name) ? CalculationBase::Display(floatval($lamination1_individual_thickness), 0) : CalculationBase::Display(floatval($lamination1_thickness), 0) ?> мкм</td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= CalculationBase::Display(floatval($width_2), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Метраж на приладку</td>
                            <td><?= CalculationBase::Display(floatval($data_priladka_laminator->length) * $uk2, 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= CalculationBase::Display(floatval($length_pure_2), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Всего мат-ла</td>
                            <td><?= CalculationBase::Display(floatval($length_dirty_2), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Ламинационный вал</td>
                            <td><?= CalculationBase::Display(floatval($lamination_roller_width), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Анилокс</td>
                            <td>Нет</td>
                        </tr>
                    </table>
                </div>
                <div class="col-4">
                    <table class="w-100">
                        <tr>
                            <td colspan="2" class="table-header">Информация для резчика</td>
                        </tr>
                        <tr>
                            <td>Отгрузка в</td>
                            <td><?=$unit == 'kg' ? 'Кг' : 'Шт' ?></td>
                        </tr>
                        <tr>
                            <td>Готовая продукция</td>
                            <td><?=$unit == 'kg' ? 'Взвешивать' : 'Записывать метраж' ?></td>
                        </tr>
                        <tr>
                            <td>Обрезная ширина</td>
                            <td><?=$stream_width.(empty($stream_width) ? "" : " мм") ?></td>
                        </tr>
                        <tr>
                            <td>Намотка до</td>
                            <td>
                                <?php
                                if(empty($winding)) {
                                    echo 'Ждем данные';
                                }
                                elseif(empty ($winding_unit)) {
                                    echo 'Нет данных по кг/мм/м';
                                }
                                elseif($winding_unit == 'pc') {
                                    if(empty($length)) {
                                        echo 'Нет данных по длине этикетки';
                                    }
                                    else {
                                        echo CalculationBase::Display(floatval($winding) * floatval($length) / 1000, 0);
                                    }
                                }
                                else {
                                    echo CalculationBase::Display(floatval($winding), 0);
                                }
                                
                                switch ($winding_unit) {
                                    case 'kg':
                                        echo " кг";
                                        break;
                                    case 'mm':
                                        echo " мм";
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
                                 * 2) Если намотка до = «мм» , то значение = "Нет"
                                 * 3) Если намотка до = «шт» , то значение = "Нет" */
                                if(empty($winding) || empty($winding_unit)) {
                                    echo 'Ждем данные';
                                }
                                elseif(empty ($weight)) {
                                    echo 'Нет данных по уд. весу пленки';
                                }
                                elseif(empty ($width_1)) {
                                    echo 'Нет данных по ширине мат-ла';
                                }
                                elseif($winding_unit == 'kg') {
                                    echo CalculationBase::Display((floatval($winding) * 1000 * 1000) / ((floatval($weight) + ($lamination1_weight === null ? 0 : floatval($lamination1_weight)) + ($lamination2_weight === null ? 0 : floatval($lamination2_weight))) * floatval($stream_width)), 0)." м";
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
                            <td><?= empty($length) ? "" : CalculationBase::Display(1 / floatval($length) * 1000, 4) ?></td>
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
                                        echo "Ждём данные";
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
                                    default :
                                        echo "Ждем данные";
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-4" style="border-right: solid 1px #cccccc;">
                    <table class="w-100">
                        <tr>
                            <td colspan="2" class="font-weight-bold" style="border-bottom: solid 2px gray;">Красочность: <?=$ink_number ?> цв.</td>
                        </tr>
                        <?php
                        for($i = 1; $i <= $ink_number; $i++):
                        $ink_var = "ink_$i";
                        $color_var = "color_$i";
                        $cmyk_var = "cmyk_$i";
                        $percent_var = "percent_$i";
                        $cliche_var = "cliche_$i";
                        
                        $machine_coeff = $machine == CalculationBase::COMIFLEX ? "1.14" : "1.7";
                        ?>
                        <tr>
                            <td>
                                <?php
                                switch ($$ink_var) {
                                    case CalculationBase::CMYK:
                                        switch ($$cmyk_var) {
                                            case CalculationBase::CYAN:
                                                echo "Cyan";
                                                break;
                                            case CalculationBase::MAGENDA:
                                                echo "Magenda";
                                                break;
                                            case CalculationBase::YELLOW:
                                                echo "Yellow";
                                                break;
                                            case CalculationBase::KONTUR:
                                                echo "Kontur";
                                                break;
                                        }
                                        break;
                                    case CalculationBase::PANTON:
                                        echo "P".$$color_var;
                                        break;
                                    case CalculationBase::WHITE;
                                        echo "Белая";
                                        break;
                                    case CalculationBase::LACQUER;
                                        echo "Лак";
                                        break;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                switch ($$cliche_var) {
                                    case CalculationBase::OLD:
                                        echo "Старая";
                                        break;
                                    case CalculationBase::FLINT:
                                        echo "Новая Flint $machine_coeff";
                                        break;
                                    case CalculationBase::KODAK:
                                        echo "Новая Kodak $machine_coeff";
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        endfor;
                        ?>
                    </table>
                </div>
                <div class="col-4" style="border-right: solid 1px #cccccc;">
                    <table class="w-100">
                        <tr>
                            <td colspan="2" class="font-weight-bold" style="border-bottom: solid 2px gray;">Ламинация 2</td>
                        </tr>
                        <tr>
                            <td>Марка пленки</td>
                            <td><?= empty($lamination2_film_name) ? $lamination2_individual_film_name : $lamination2_film_name ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td><?= empty($lamination2_film_name) ? CalculationBase::Display(floatval($lamination2_individual_thickness), 0) : CalculationBase::Display(floatval($lamination2_thickness), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= CalculationBase::Display(floatval($width_3), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Метраж на приладку</td>
                            <td><?= CalculationBase::Display(floatval($data_priladka_laminator->length) * $uk3, 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= CalculationBase::Display(floatval($length_pure_3), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Всего мат-ла</td>
                            <td><?= CalculationBase::Display(floatval($length_dirty_3), 0) ?> м</td>
                        </tr>
                    </table>
                </div>
            </div>
            <table class="fotometka" style="margin-top: 10px; margin-bottom: 10px;">
                <tr>
                    <td class="fotometka<?= $roll_type == 1 ? " fotochecked" : "" ?>">
                        <img src="../images/roll/roll_type_1.png" style="height: 50px; width: auto;" />
                        <?php if($roll_type == 1): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 2 ? " fotochecked" : "" ?>">
                        <img src="../images/roll/roll_type_2.png" style="height: 50px; width: auto;" />
                        <?php if($roll_type == 2): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 3 ? " fotochecked" : "" ?>">
                        <img src="../images/roll/roll_type_3.png" style="height: 50px; width: auto;" />
                        <?php if($roll_type == 3): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 4 ? " fotochecked" : "" ?>">
                        <img src="../images/roll/roll_type_4.png" style="height: 50px; width: auto;" />
                        <?php if($roll_type == 4): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 5 ? " fotochecked" : "" ?>">
                        <img src="../images/roll/roll_type_5.png" style="height: 50px; width: auto;" />
                        <?php if($roll_type == 5): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 6 ? " fotochecked" : "" ?>">
                        <img src="../images/roll/roll_type_6.png" style="height: 50px; width: auto;" />
                        <?php if($roll_type == 6): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 7 ? " fotochecked" : "" ?>">
                        <img src="../images/roll/roll_type_7.png" style="height: 50px; width: auto;" />
                        <?php if($roll_type == 7): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 8 ? " fotochecked" : "" ?>">
                        <img src="../images/roll/roll_type_8.png" style="height: 50px; width: auto;" />
                        <?php if($roll_type == 8): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                </tr>
            </table>
            <div style="margin-bottom: 50px;"><span style="font-size: 18px; font-weight: bold;">Комментарий:</span> <?=$comment ?></div>
            <table class="w-100">
                <tr class="left">
                    <td style="font-size: 18px; font-weight: bold; height: 50px; border: solid 2px #cccccc; padding-left: 5px;">Дизайнер:</td>
                    <td style="font-size: 18px; font-weight: bold; height: 50px; border: solid 2px #cccccc; padding-left: 5px;">Менеджер:</td>
                </tr>
            </table>
            <?php
            // Удаление всех файлов, кроме текущих (чтобы диск не переполнился).
            $files = scandir("../temp/");
            foreach ($files as $file) {
                $created = filemtime("../temp/".$file);
                $now = time();
                $diff = $now - $created;
            
                if($diff > 20 &&
                        $file != "$current_date_time.png" &&
                        !is_dir($file)) {
                    unlink("../temp/$file");
                }
            }
            ?>
        </div>
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
    </body>
</html>