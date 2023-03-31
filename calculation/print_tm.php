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

// Упаковка: паллетированная, россыпью, европаллет, коробки
const PACKAGE_PALLETED = 1;
const PACKAGE_BULK = 2;
const PACKAGE_EUROPALLET = 3;
const PACKAGE_BOXES = 4;

// Значение марки плёнки "другая"
const INDIVIDUAL = -1;

// Отходы
const WASTE_PRESS = "В пресс";
const WASTE_KAGAT = "В кагат";
const WASTE_PAPER = "В макулатуру";

// Фотометка
const PHOTOLABEL_LEFT = "left";
const PHOTOLABEL_RIGHT = "right";
const PHOTOLABEL_BOTH = "both";
const PHOTOLABEL_NONE = "none";

// Получение коэффициента машины
function GetMachineCoeff($machine) {
    return $machine == CalculationBase::COMIFLEX ? "1.14" : "1.7";
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select c.date, c.customer_id, c.name calculation, c.quantity, c.unit, c.work_type_id, c.machine_id, (select shortname from machine where id = c.machine_id) machine, laminator_id, "
        . "c.film_variation_id, f.name film_name, fv.thickness thickness, fv.weight weight, c.price, c.currency, c.individual_film_name, c.individual_thickness, c.individual_density, c.customers_material, c.ski, c.width_ski, "
        . "c.lamination1_film_variation_id, lam1f.name lamination1_film_name, lam1fv.thickness lamination1_thickness, lam1fv.weight lamination1_weight, c.lamination1_price, c.lamination1_currency, c.lamination1_individual_film_name, c.lamination1_individual_thickness, c.lamination1_individual_density, c.lamination1_customers_material, c.lamination1_ski, c.lamination1_width_ski, "
        . "c.lamination2_film_variation_id, lam2f.name lamination2_film_name, lam2fv.thickness lamination2_thickness, lam2fv.weight lamination2_weight, c.lamination2_price, c.lamination2_currency, c.lamination2_individual_film_name, c.lamination2_individual_thickness, c.lamination2_individual_density, c.lamination2_customers_material, c.lamination2_ski, c.lamination2_width_ski, "
        . "c.streams_number, c.stream_width, c.length, c.raport, c.number_in_raport, c.lamination_roller_width, c.ink_number, "
        . "c.ink_1, c.ink_2, c.ink_3, c.ink_4, c.ink_5, c.ink_6, c.ink_7, c.ink_8, "
        . "c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, "
        . "c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
        . "c.percent_1, c.percent_2, c.percent_3, c.percent_4, c.percent_5, c.percent_6, c.percent_7, c.percent_8, c.cliche_1, "
        . "c.cliche_2, c.cliche_3, c.cliche_4, c.cliche_5, c.cliche_6, c.cliche_7, c.cliche_8, "
        . "c.knife, "
        . "cus.name customer, sup.name supplier, "
        . "u.last_name, u.first_name, "
        . "wt.name work_type, "
        . "cr.width_1, cr.length_pure_1, cr.length_dirty_1, cr.width_2, cr.length_pure_2, cr.length_dirty_2, cr.width_3, cr.length_pure_3, cr.length_dirty_3, gap, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, "
        . "tm.id techmap_id, tm.date techmap_date, tm.supplier_id, tm.side, tm.winding, tm.winding_unit, tm.spool, tm.labels, tm.package, tm.photolabel, tm.roll_type, tm.comment "
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
        . "left join supplier sup on tm.supplier_id = sup.id "
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
$number_in_raport = $row['number_in_raport'];
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

$knife = $row['knife'];

$customer = $row['customer'];
$supplier = $row['supplier'];
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
$gap = $row['gap'];

$num_for_customer = $row['num_for_customer'];

$lamination = "нет";
if(!empty($lamination1_film_name) || !empty($lamination1_individual_film_name)) $lamination = "1";
if(!empty($lamination2_film_name) || !empty($lamination2_individual_film_name)) $lamination = "2";

$techmap_id = $row['techmap_id'];
$techmap_date = $row['techmap_date']; if(empty($techmap_date)) $techmap_date = date('Y-m-d H:i:s');

$side = $row['side'];
$winding = $row['winding'];
$winding_unit = $row['winding_unit'];
$spool = $row['spool'];
$labels = $row['labels'];
$package = $row['package'];
$photolabel = $row['photolabel'];
$roll_type = $row['roll_type'];
$comment = $row['comment'];

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

$machine_coeff = GetMachineCoeff($machine);

// Тиражи и формы
$printings = array();
$cliches = array();
$repeats = array();
$cliches_used_flint = 0;
$cliches_used_kodak = 0;
$cliches_used_old = 0;
$quantities_sum = 0;
$lengths_sum = 0;

if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE) {
    $sql = "select id, quantity, length from calculation_quantity where calculation_id = $id";
    $grabber = new Grabber($sql);
    $error_message = $grabber->error;
    $printings = $grabber->result;
    
    $sql = "select calculation_quantity_id, sequence, name, repeat_from from calculation_cliche where calculation_quantity_id in (select id from calculation_quantity where calculation_id = $id)";
    $fetcher = new Fetcher($sql);
    $error_message = $fetcher->error;
    while($row = $fetcher->Fetch()) {
        $cliches[$row['calculation_quantity_id']][$row['sequence']] = $row['name'];
        $repeats[$row['calculation_quantity_id']][$row['sequence']] = $row['repeat_from'];
        
        switch ($row['name']) {
            case CalculationBase::FLINT:
                $cliches_used_flint++;
                break;
            case CalculationBase::KODAK:
                $cliches_used_kodak++;
                break;
            case CalculationBase::OLD:
                $cliches_used_old++;
                break;
        }
    }
    
    foreach($printings as $printing) {
        $quantities_sum += intval($printing['quantity']);
        $lengths_sum += intval($printing['length']);
    }
}

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
                height: 80px;
                width: 80px;
            }
            
            .header_qr img {
                height: 80px;
                width: 80px;
            }
            
            .header_title {
                font-size: 18px;
                vertical-align: middle;
            }
            
            .right_logo {
                padding-right: 10px;
            }
            
            #main, #fixed_top {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            #title {
                font-weight: bold;
                font-size: 30px;
                margin-top: 10px;
            }
            
            #subtitle {
                font-weight: bold;
                font-size: 24px;
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
                line-height: 20px;
                padding-top: 7px;
                padding-bottom: 7px;
                border-bottom: solid 1px #cccccc;
            }
            
            tr td:nth-child(2) {
                text-align: right;
                padding-left: 10px;
                font-weight: bold;
            }
            
            tr.left td:nth-child(2) {
                text-align: left;
            }
            
            table.fotometka {
                margin-top: 10px;
                margin-bottom: 10px;
            }
            
            table.fotometka tr td {
                border: solid 1px #dddddd;
                padding-left: 4px;
                padding-top: 4px;
                padding-right: 20px;
                text-align: right;
                vertical-align: top;
            }
            
            td.fotometka img:nth-child(1) {
                 height: 50px;
                 width: auto;
            }
            
            .photolable {
                margin-top: 10px;
                margin-bottom: 10px;
                font-size: 18px;
            }
            
            .border-bottom-2 {
                border-bottom: solid 2px gray;
            }
            
            .printing_title {
                font-size: large;
            }
            
            @media print {
                #fixed_top {
                    position: fixed;
                    top: 0px;
                    left: 0px;
                    width: 100%;
                }
                
                #fixed_bottom {
                    position: fixed;
                    bottom: 0px;
                    left: 0px;
                    width: 100%;
                }
                
                #placeholder_top {
                    height: 210px;
                }
                
                .break_page {
                    page-break-before: always;
                    height: 210px;
                }
            }
            
            #fixed_bottom table tbody tr td {
                font-size: 18px;
                font-weight: bold;
                height: 50px;
                border: solid 2px #cccccc;
                padding-left: 5px;
            }
        </style>
    </head>
    <body>
        <div id="fixed_top">
            <div class="d-flex justify-content-between">
                <div>
                    <?php
                    include '../qr/qrlib.php';
                    $errorCorrectionLevel = 'L'; // 'L','M','Q','H'
                    $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/calculation/details.php?id='.$id;
                    $filename = "../temp/$current_date_time.png";
                
                    do {
                        QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 3, 0, true);
                    } while (!file_exists($filename));
                    ?>
                    <div class="d-inline-block header_qr"><img src='<?=$filename ?>' /></div>
                    <div class="d-inline-block header_title font-weight-bold mr-3">
                        Заказ №<?=$customer_id ?>-<?=$num_for_customer ?><br />
                        от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?>
                    </div>
                    <div class="d-inline-block header_title font-weight-bold mr-2">
                        Карта составлена:
                        <br />
                        Менеджер:
                    </div>
                    <div class="d-inline-block header_title">
                        <?= DateTime::createFromFormat('Y-m-d H:i:s', $techmap_date)->format('d.m.Y H:i') ?>
                        <br />
                        <?=$first_name ?> <?=$last_name ?>
                    </div>
                </div>
                <div>
                    <div class="d-inline-block right_logo"><img src="../images/logo_with_label.svg" /></div>
                </div>
            </div>
            <div id="title">Заказчик: <?=$customer ?></div>
            <div id="subtitle">Наименование: <?=$calculation ?></div>
            <div class="row">
                <div class="col-6 topproperty">
                    <strong>Объем заказа:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? CalculationBase::Display(intval($quantities_sum), 0)." шт" : CalculationBase::Display(intval($quantity), 0).($unit == CalculationBase::KG ? " кг" : " шт") ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? CalculationBase::Display(floatval($lengths_sum), 0)." м" : CalculationBase::Display(floatval($length_pure_1), 0)." м" ?>
                </div>
                <div class="col-6 topproperty">
                    <strong>Тип работы:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$work_type ?>
                </div>
            </div>
        </div>
        <div id="placeholder_top"></div>
        <div id="main">
            <div class="row">
                <div class="col-4 border-right">
                    <table class="w-100">
                        <tr>
                            <td colspan="2" class="table-header font-weight-bold">ИНФОРМАЦИЯ ДЛЯ ПЕЧАТИ</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Печать</td>
                        </tr>
                        <tr>
                            <td>Машина</td>
                            <td><?= mb_stristr($machine, "zbs") ? "ZBS" : ucfirst($machine) ?></td>
                        </tr>
                        <?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Поставщик мат-ла</td>
                            <td><?= empty($supplier) ? "Любой" : $supplier ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Марка мат-ла</td>
                            <td><?= empty($film_name) ? $individual_film_name : $film_name ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td class="text-nowrap"><?= empty($film_name) ? CalculationBase::Display(floatval($individual_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(CalculationBase::Display(floatval($individual_density), 2), "0").' г/м<sup>2</sup>' : CalculationBase::Display(floatval($thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(CalculationBase::Display(floatval($weight), 2), "0").' г/м<sup>2</sup>' ?></td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= CalculationBase::Display(floatval($width_1), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td><?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? "На приладку 1 тиража" : "Метраж на приладку" ?></td>
                            <td><?= CalculationBase::Display(floatval($data_priladka->length) * floatval($ink_number), 0) ?> м</td>
                        </tr>
                        <?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Всего тиражей</td>
                            <td><?=count($printings) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($work_type_id != CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= CalculationBase::Display(floatval($length_pure_1), 0) ?> м</td>
                        </tr>
                        <?php endif; ?>
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
                            <td><?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? "Ширина этикетки" : "Ширина ручья" ?></td>
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
                        <?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Этикеток в рапорте</td>
                            <td><?=$number_in_raport ?></td>
                        </tr>
                        <tr>
                            <td>Красочность</td>
                            <td><?=$ink_number ?> красок</td>
                        </tr>
                        <tr>
                            <td>Штамп</td>
                            <td><?= (empty($knife) || $knife == 0) ? "Старый" : "Новый" ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($work_type_id != CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td colspan="2" class="font-weight-bold border-bottom-2">Красочность: <?=$ink_number ?> красок</td>
                        </tr>
                        <?php
                        for($i = 1; $i <= $ink_number; $i++):
                        $ink_var = "ink_$i";
                        $color_var = "color_$i";
                        $cmyk_var = "cmyk_$i";
                        $percent_var = "percent_$i";
                        $cliche_var = "cliche_$i";
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
                        <?php endfor; ?>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-6 border-right">
                            <table class="w-100">
                                <tr>
                                    <td colspan="2" class="table-header font-weight-bold"><?php if($work_type_id != CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?> ИНФОРМАЦИЯ ДЛЯ ЛАМИНАЦИИ<?php else: echo "<br /> "; endif; ?></td>
                                </tr>
                                <?php if($work_type_id != CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
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
                                    <td class="text-nowrap"><?= empty($lamination1_film_name) ? CalculationBase::Display(floatval($lamination1_individual_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(CalculationBase::Display(floatval($lamination1_individual_density), 2), "0").' г/м<sup>2</sup>' : CalculationBase::Display(floatval($lamination1_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(CalculationBase::Display(floatval($lamination1_weight), 2), "0").' г/м<sup>2</sup>' ?></td>
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
                                <tr>
                                    <td colspan="2" class="font-weight-bold border-bottom-2">Ламинация 2</td>
                                </tr>
                                <tr>
                                    <td>Марка пленки</td>
                                    <td><?= empty($lamination2_film_name) ? $lamination2_individual_film_name : $lamination2_film_name ?></td>
                                </tr>
                                <tr>
                                    <td>Толщина</td>
                                    <td class="text-nowrap"><?= empty($lamination2_film_name) ? CalculationBase::Display(floatval($lamination2_individual_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(CalculationBase::Display(floatval($lamination2_individual_density), 2), "0").' г/м<sup>2</sup>' : CalculationBase::Display(floatval($lamination2_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(CalculationBase::Display(floatval($lamination2_weight), 2), "0").' г/м<sup>2</sup>' ?></td>
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
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="w-100">
                                <tr>
                                    <td colspan="2" class="table-header font-weight-bold">ИНФОРМАЦИЯ ДЛЯ РЕЗЧИКА</td>
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
                                    <td><?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? "Обр. шир. / Гор. зазор" : "Обрезная ширина" ?></td>
                                    <?php
                                    $norm_stream = "";
                                    $sql = "select gap_stream from norm_gap order by date desc limit 1";
                                    $fetcher = new Fetcher($sql);
                                    if($row = $fetcher->Fetch()) {
                                        $norm_stream = CalculationBase::Display($row[0], 2);
                                    }
                                    ?>
                                    <td>
                                        <?php
                                        if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE) {
                                            if(empty($norm_stream)) {
                                                echo CalculationBase::Display(intval($stream_width), 0)." мм";
                                            }
                                            else {
                                                echo CalculationBase::Display(floatval($stream_width) + floatval($norm_stream), 2)." / ".CalculationBase::Display(floatval($norm_stream), 2)." мм";
                                            }
                                        }
                                        else {
                                            echo CalculationBase::Display(intval($stream_width), 0)." мм";
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
                                            echo CalculationBase::Display((floatval($winding) * 1000 * 1000) / ((floatval($final_density) + ($lamination1_final_density === null ? 0 : floatval($lamination1_final_density)) + ($lamination2_final_density === null ? 0 : floatval($lamination2_final_density))) * floatval($stream_width)) - 200, 0)." м";
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
                    </div>
                    <div class="photolable">
                <span class="font-weight-bold">Фотометка:</span>&nbsp;
                <?php
                switch ($photolabel) {
                    case PHOTOLABEL_LEFT:
                        echo "Левая";
                        break;
                    case PHOTOLABEL_RIGHT:
                        echo "Правая";
                        break;
                    case PHOTOLABEL_BOTH:
                        echo "Две фотометки";
                        break;
                    case PHOTOLABEL_NONE:
                        echo "Без фотометки";
                        break;
                    default :
                        echo ($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? "Без фотометки" : "Левая");
                        break;
                }
                ?>
            </div>
            <?php
            $roll_folder = ($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? "roll" : "roll_left");
            switch ($photolabel) {
                case PHOTOLABEL_LEFT:
                    $roll_folder = "roll_left";
                    break;
                case PHOTOLABEL_RIGHT:
                    $roll_folder = "roll_right";
                    break;
                case PHOTOLABEL_BOTH:
                    $roll_folder = "roll_both";
                    break;
                case PHOTOLABEL_NONE:
                    $roll_folder = "roll";
                    break;
            }
            ?>
            <table class="fotometka">
                <tr>
                    <td class="fotometka<?= $roll_type == 1 ? " fotochecked" : "" ?>">
                        <img src="../images/<?=$roll_folder ?>/roll_type_1.png<?='?'. time() ?>" />
                        <?php if($roll_type == 1): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 2 ? " fotochecked" : "" ?>">
                        <img src="../images/<?=$roll_folder ?>/roll_type_2.png<?='?'. time() ?>" />
                        <?php if($roll_type == 2): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 3 ? " fotochecked" : "" ?>">
                        <img src="../images/<?=$roll_folder ?>/roll_type_3.png<?='?'. time() ?>" />
                        <?php if($roll_type == 3): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 4 ? " fotochecked" : "" ?>">
                        <img src="../images/<?=$roll_folder ?>/roll_type_4.png<?='?'. time() ?>" />
                        <?php if($roll_type == 4): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 5 ? " fotochecked" : "" ?>">
                        <img src="../images/<?=$roll_folder ?>/roll_type_5.png<?='?'. time() ?>" />
                        <?php if($roll_type == 5): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 6 ? " fotochecked" : "" ?>">
                        <img src="../images/<?=$roll_folder ?>/roll_type_6.png<?='?'. time() ?>" />
                        <?php if($roll_type == 6): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 7 ? " fotochecked" : "" ?>">
                        <img src="../images/<?=$roll_folder ?>/roll_type_7.png<?='?'. time() ?>" />
                        <?php if($roll_type == 7): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                    <td class="fotometka<?= $roll_type == 8 ? " fotochecked" : "" ?>">
                        <img src="../images/<?=$roll_folder ?>/roll_type_8.png<?='?'. time() ?>" />
                        <?php if($roll_type == 8): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                    </td>
                </tr>
            </table>
                </div>
            </div>
            
            <div class="font-weight-bold" style="font-size: 18px; margin-top: 10px;">Комментарий:</div>
            <div style="white-space: pre-wrap; font-size: 24px;"><?=$comment ?></div>
            <?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
            <div class="break_page"></div>
            <div class="row">
                <?php
                $printing_sequence = 0;
                $counter = 0;
                foreach($printings as $printing):
                    $printing_sequence++;
                    $counter++;
                    
                if($counter > 4) {
                    echo "</div>";
                    
                    if($printing_sequence == 13) {
                        echo "<div class='break-page'></div>";
                    }
                    
                    echo "<div class='row'>";
                    $counter = 1;
                }
                ?>
                <div class="col-3">
                    <div class="mt-4 mb-2 printing_title font-weight-bold">Тираж <?=$printing_sequence ?></div>
                    <div class="d-flex justify-content-between font-italic border-bottom">
                        <div><?= CalculationBase::Display(intval($printing['quantity']), 0) ?> шт</div>
                        <div><?= CalculationBase::Display(floatval($printing['length']), 0) ?> м</div>
                    </div>
                    <table class="mb-3 w-100">
                    <?php
                    for($i = 1; $i <= $ink_number; $i++):
                    $ink_var = "ink_$i";
                    $color_var = "color_$i";
                    $cmyk_var = "cmyk_$i";
                    ?>
                        <tr>
                            <td>
                                <?php
                                switch($$ink_var) {
                                    case CalculationBase::CMYK:
                                        switch ($$cmyk_var) {
                                            case CalculationBase::CYAN:
                                                echo 'Cyan';
                                                break;
                                            case CalculationBase::MAGENDA:
                                                echo 'Magenda';
                                                break;
                                            case CalculationBase::YELLOW:
                                                echo 'Yellow';
                                                break;
                                            case CalculationBase::KONTUR:
                                                echo 'Kontur';
                                                break;
                                        }
                                        break;
                                    case CalculationBase::PANTON:
                                        echo "P".$$color_var;
                                        break;
                                    case CalculationBase::WHITE:
                                        echo 'Белая';
                                        break;
                                    case CalculationBase::LACQUER:
                                        echo 'Лак';
                                        break;
                                }
                                ?>
                            </td>
                            <td id="cliche_<?=$printing['id'] ?>_<?=$i ?>">
                                <?php
                                if(empty($cliches[$printing['id']][$i])) {
                                    echo 'Ждем данные';
                                }
                                else {
                                    switch ($cliches[$printing['id']][$i]) {
                                        case CalculationBase::FLINT:
                                            echo "Новая Flint $machine_coeff";
                                            break;
                                        case CalculationBase::KODAK:
                                            echo "Новая Kodak $machine_coeff";
                                            break;
                                        case CalculationBase::OLD:
                                            echo "Старая";
                                            break;
                                        case CalculationBase::REPEAT:
                                            echo "Повт. исп. с тир. ".$repeats[$printing['id']][$i];
                                            break;
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endfor; ?>
                    </table>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div id="fixed_bottom">
                <table class="w-100">
                    <tr class="left">
                        <td>Дизайнер:</td>
                        <td>Менеджер:</td>
                    </tr>
                </table>
            </div>
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