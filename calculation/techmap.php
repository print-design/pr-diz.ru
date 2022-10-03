<?php
include '../include/topscripts.php';
include 'status_ids.php';
include 'calculation.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id, направляем к списку технических карт
if(null === filter_input(INPUT_GET, 'id')) {
    header('Location: '.APPLICATION.'/calculation/');
}

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

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$side_valid = '';
$winding_valid = '';
$winding_unit_valid = '';
$spool_valid = '';
$labels_valid = '';
$package_valid = '';
$roll_type_valid = '';

// Создание технологической карты
if(null !== filter_input(INPUT_POST, 'techmap_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    if(empty($id)) {
        $error_message == "Не указан ID расчёта";
        $form_valid = false;
    }
    
    $techmap_id = filter_input(INPUT_POST, 'techmap_id');
    
    $side = filter_input(INPUT_POST, 'side');
    if(empty($side)) {
        $side_valid = ISINVALID;
        $form_valid = false;
    }
    
    $winding = filter_input(INPUT_POST, 'winding');
    if(empty($winding)) {
        $winding_valid = ISINVALID;
        $form_valid = false;
    }
    
    $winding_unit = filter_input(INPUT_POST, 'winding_unit');
    if(empty($winding_unit)) {
        $winding_unit_valid = ISINVALID;
        $form_valid = false;
    }
    
    $spool = filter_input(INPUT_POST, 'spool');
    if(empty($spool)) {
        $spool_valid = ISINVALID;
        $form_valid = false;
    }
    
    $labels = filter_input(INPUT_POST, 'labels');
    if(empty($labels)) {
        $labels_valid = ISINVALID;
        $form_valid = false;
    }
    
    $package = filter_input(INPUT_POST, 'package');
    if(empty($package)) {
        $package_valid = ISINVALID;
        $form_valid = false;
    }
    
    $roll_type = filter_input(INPUT_POST, 'roll_type');
    if(empty($roll_type)) {
        $roll_type_valid = ISINVALID;
        $form_valid = false;
    }
    
    $comment = filter_input(INPUT_POST, 'comment');
    
    if($form_valid) {
        $comment = addslashes($comment);
        
        $sql = "";
        
        if(empty($techmap_id)) {
            $sql = "insert into techmap (calculation_id, side, winding, winding_unit, spool, labels, package, roll_type, comment) "
                    . "values($id, $side, $winding, '$winding_unit', $spool, $labels, $package, $roll_type, '$comment')";
        }
        else {
            $sql = "update techmap set side = $side, winding = $winding, winding_unit = '$winding_unit', spool = $spool, "
                    . "labels = $labels, package = $package, roll_type = $roll_type, comment = '$comment' where id = $techmap_id";
        }
        
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($techmap_id) && empty($error_message)) {
            $sql = "update calculation set status_id = ".TECHMAP." where id = $id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
    }
}

// ПОЛУЧЕНИЕ ОБЪЕКТА
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
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            .row {
                width: 900px;
            }
            
            h1 {
                font-size: 33px;
            }
            
            h2, .name {
                font-size: 26px;
                font-weight: bold;
                line-height: 45px;
            }
            
            h3 {
                font-size: 20px;
            }
            
            .subtitle {
                font-weight: bold;
                font-size: 20px;
                line-height: 40px
            }
            
            table {
                width: 100%;
            }
            
            tr {
                border-bottom: solid 1px #e3e3e3;
            }
            
            th {
                white-space: nowrap;
                padding-right: 30px;
                vertical-align: top;
            }
            
            td {
                line-height: 25px;
            }
            
            tr td:nth-child(2) {
                text-align: right;
            }
            
            .roll-selector input {
                margin:0;
                padding:0;
                -webkit-appearance:none;
                -moz-appearance:none;
                appearance:none;
            }
            
            .roll-selector label {
                cursor:pointer;
                border: solid 5px white;
            }
            
            .roll-selector label:hover {
                border: solid 5px lightblue;
            }
            
            .roll-selector input[type="radio"]:checked + label {
                background-image: url(../images/icons/check.svg);
                background-position-x: 100%;
                background-position-y: 100%;
                background-repeat: no-repeat;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between">
                <div><a class="btn btn-outline-dark backlink" href="details.php?id=<?= $id ?>">К расчету</a></div>
                <div>
                    <?php if(!empty($techmap_id)): ?>
                    <a class="btn btn-outline-dark mt-2" target="_blank" style="width: 3rem;" title="Печать" href="print_tm.php?id=<?= $id ?>"><i class="fa fa-print"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <h1><?= empty($techmap_id) ? "Составление тех. карты" : "Технологическая карта" ?></h1>
            <div class="name">Заказчик: <?=$customer ?></div>
            <div class="name">Наименование: <?=$calculation ?></div>
            <div class="subtitle">№<?=$customer_id ?>-<?=$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></div>
            <h2 class="mt-2">Остальная информация</h2>
            <div class="row">
                <div class="col-5">
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                        <tr>
                            <th>Карта составлена</th>
                            <td class="text-left"><?= DateTime::createFromFormat('Y-m-d H:i:s', $techmap_date)->format('d.m.Y H:i') ?></td>
                        </tr>
                        <tr>
                            <th>Заказчик</th>
                            <td class="text-left"><?=$customer ?></td>
                        </tr>
                        <tr>
                            <th>Название заказа</th>
                            <td class="text-left"><?=$calculation ?></td>
                        </tr>
                        <tr>
                            <th>Объем заказа</th>
                            <td class="text-left"><strong><?= CalculationBase::Display(intval($quantity), 0) ?> <?=$unit == 'kg' ? 'кг' : 'шт' ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= CalculationBase::Display(floatval($length_pure_1), 0) ?> м</td>
                        </tr>
                        <tr>
                            <th>Менеджер</th>
                            <td class="text-left"><?=$first_name ?> <?=$last_name ?></td>
                        </tr>
                        <tr>
                            <th>Тип работы</th>
                            <td class="text-left"><?=$work_type ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-4">
                    <h2>Информация для печатника</h2>
                    <div class="subtitle">Печать</div>
                </div>
                <div class="col-4">
                    <h2>Информация для ламинации</h2>
                    <div class="subtitle">Кол-во ламинаций: <?=$lamination ?></div>
                </div>
                <div class="col-4">
                    <h2>Информация для резчика</h2>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-4">
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                        <tr>
                            <td style="padding-top: 5px;">Машина</td>
                            <td style="padding-top: 5px;"><?= empty($machine) ? "" : ($machine == CalculationBase::COMIFLEX ? "Comiflex" : "ZBS") ?></td>
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
                <div class="col-4">
                    <h3>Ламинация 1</h3>
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
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
                    <h3>Информация для резчика</h3>
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
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
                                <?= empty($winding) ? "Ждем данные" : CalculationBase::Display(intval($winding), 0) ?>
                                <?php
                                switch ($winding_unit) {
                                    case 'kg':
                                        echo " кг";
                                        break;
                                    case 'mm':
                                        echo " мм";
                                        break;
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
            <div class="row mt-3">
                <div class="col-4">
                    <h3>Красочность: <?=$ink_number ?> цв.</h3>
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
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
                                <div class="color_label d-inline" id="color_label_<?=$i ?>">
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
                                </div>
                                <?php
                                if($$ink_var == CalculationBase::PANTON):
                                ?>
                                <div class="edit_panton_link d-inline" id="edit_panton_link_<?=$i ?>"><a class="edit_panton" href="javascript: void(0);" onclick="javascript: EditPanton(<?=$i ?>);"><img class="ml-2" src="../images/icons/edit1.svg" /></a></div>
                                <div class="edit_panton_form d-none" id="edit_panton_form_<?=$i ?>">
                                    <form class="panton_form form-inline">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">P</span></div>
                                            <input type="text" class="form-control color_input" name="color" id="color_input_<?=$i ?>" value="<?=$$color_var ?>" data-id="<?=$id ?>" data-i="<?=$i ?>">
                                            <div class="input-group-append">
                                                <a class="btn btn-outline-dark" href="javascript: void(0);" onclick="javascript: SavePanton(<?=$id ?>, <?=$i ?>);">OK</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <?php endif; ?>
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
                    </table>
                </div>
                <div class="col-4">
                    <h3>Ламинация 2</h3>
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
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
            <form class="mt-5" method="post"<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                <input type="hidden" name="scroll" />
                <input type="hidden" name="id" value="<?= $id ?>" />
                <input type="hidden" name="techmap_id" value="<?=$techmap_id ?>" />
                <div class="row">
                    <div class="col-6">
                        <h2>Информация для резчика</h2>
                        <div class="form-group">
                            <label for="side">Печать</label>
                            <select id="side" name="side" class="form-control<?=$side_valid ?>" required="required">
                                <option value="" hidden="hidden">...</option>
                                <option value="<?=SIDE_FRONT ?>"<?= $side == 1 ? " selected='selected'" : "" ?>>Лицевая</option>
                                <option value="<?=SIDE_BACK ?>"<?= $side == 2 ? " selected='selected'" : "" ?>>Оборотная</option>
                            </select>
                            <div class="invalid-feedback">Сторона обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="winding">Намотка до</label>
                            <div class="input-group">
                                <input type="text" 
                                       id="winding" 
                                       name="winding" 
                                       class="form-control int-only<?=$winding_valid ?>" 
                                       placeholder="Намотка до" 
                                       value="<?= $winding ?>" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('name', 'winding'); $(this).attr('placeholder', 'Намотка до');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('name', 'winding'); $(this).attr('placeholder', 'Намотка до');" 
                                       onfocusout="javascript: $(this).attr('name', 'winding'); $(this).attr('placeholder', 'Намотка до');" />
                                <div class="input-group-append">
                                    <select id="winding_unit" name="winding_unit" required="required">
                                        <option value="" hidden="hidden">...</option>
                                        <option value="kg"<?= $winding_unit == 'kg' ? " selected='selected'" : "" ?>>Кг</option>
                                        <option value="mm"<?= $winding_unit == 'mm' ? " selected='selected'" : "" ?>>мм</option>
                                    </select>
                                </div>
                                <div class="invalid-feedback">Намотка обязательно</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="spool">Шпуля</label>
                            <select id="spool" name="spool" class="form-control<?=$spool_valid ?>" required="required">
                                <option value="" hidden="hidden">...</option>
                                <option<?= $spool == 40 ? " selected='selected'" : "" ?>>40</option>
                                <option<?= $spool == 76 ? " selected='selected'" : "" ?>>76</option>
                                <option<?= $spool == 152 ? " selected='selected'" : "" ?>>152</option>
                            </select>
                            <div class="invalid-feedback">Шпуля обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="labels">Бирки</label>
                            <select id="labels" name="labels" class="form-control<?=$labels_valid ?>" required="required">
                                <option value="" hidden="hidden">...</option>
                                <option value="<?=LABEL_PRINT_DESIGN ?>"<?= $labels == 1 ? " selected='selected'" : "" ?>>Принт-Дизайн</option>
                                <option value="<?=LABEL_FACELESS ?>"<?= $labels == 2 ? " selected='selected'" : "" ?>>Безликие</option>
                            </select>
                            <div class="invalid-feedback">Бирки обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="package">Упаковка</label>
                            <select id="package" name="package" class="form-control<?=$package_valid ?>" required="required">
                                <option value="" hidden="">...</option>
                                <option value="<?=PACKAGE_PALLETED ?>"<?= $package == 1 ? " selected='selected'" : "" ?>>Паллетирование</option>
                                <option value="<?=PACKAGE_BULK ?>"<?= $package == 2 ? " selected='selected'" : "" ?>>Россыпью</option>
                            </select>
                            <div class="invalid-feedback">Упаковка обязательно</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <h3>Выберите фотометку</h3>
                        <div class="form-group">
                            <label for="x"></label>
                            <input type="text" id="x" style="visibility: hidden;" />
                        </div>
                        <div class="form-group roll-selector">
                            <input type="radio" class="form-check-inline" id="roll_type_1" name="roll_type" value="1"<?= $roll_type == 1 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_1" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img src="../images/roll/roll_type_1.png" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_2" name="roll_type" value="2"<?= $roll_type == 2 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_2" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img src="../images/roll/roll_type_2.png" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_3" name="roll_type" value="3"<?= $roll_type == 3 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_3" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img src="../images/roll/roll_type_3.png" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_4" name="roll_type" value="4"<?= $roll_type == 4 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_4" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img src="../images/roll/roll_type_4.png" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_5" name="roll_type" value="5"<?= $roll_type == 5 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_5" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img src="../images/roll/roll_type_5.png" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_6" name="roll_type" value="6"<?= $roll_type == 6 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_6" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img src="../images/roll/roll_type_6.png" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_7" name="roll_type" value="7"<?= $roll_type == 7 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_7" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img src="../images/roll/roll_type_7.png" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_8" name="roll_type" value="8"<?= $roll_type == 8 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_8" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img src="../images/roll/roll_type_8.png" style="height: 30px; width: auto;" /></label>
                        </div>
                        <div id="roll_type_validation" class="text-danger<?= empty($roll_type_valid) ? " d-none" : " d-block" ?>">Укажите фотометку</div>
                        <h3>Комментарий</h3>
                        <textarea rows="6" name="comment" class="form-control"><?= html_entity_decode($comment) ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 d-flex justify-content-between mt-3">
                        <div><button type="submit" name="techmap_submit" class="btn btn-dark draft" style="width: 175px;">Сохранить</button></div>
                        <div>
                            <?php if(!empty($techmap_id)): ?>
                            <a href="print_tm.php?id=<?= $id ?>" target="_blank" class="btn btn-outline-dark" style="width: 175px;">Печать</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('.roll-selector input').change(function(){
                $('#roll_type_validation').removeClass('d-block');
                $('#roll_type_validation').addClass('d-none');
            });
            
            $('.color_input').keydown(function(e) {
                if(e.which == 13) {
                    e.preventDefault();
                    SavePanton($(this).attr('data-id'), $(this).attr('data-i'));
                }
            });
            
            function EditPanton(i) {
                $('.color_label').removeClass('d-none');
                $('.color_label').addClass('d-inline');
                $('#color_label_' + i).removeClass('d-inline');
                $('#color_label_' + i).addClass('d-none');
                
                $('.edit_panton_link').removeClass('d-none');
                $('.edit_panton_link').addClass('d-inline');
                $('#edit_panton_link_' + i).removeClass('d-inline');
                $('#edit_panton_link_' + i).addClass('d-none');
                
                $('.edit_panton_form').removeClass('d-inline');
                $('.edit_panton_form').addClass('d-none');
                $('#edit_panton_form_' + i).removeClass('d-none');
                $('#edit_panton_form_' + i).addClass('d-inline');
                
                $('#color_input_' + i).focus();
            }
            
            function SavePanton(id, i) {
                $.ajax({ url: "edit_panton.php?id=" + id + "&i=" + i + "&value=" + $('#color_input_' + i).val() })
                        .done(function(data) {
                            $('#color_label_' + i).text('P' + data);
                            
                            $('#edit_panton_form_' + i).removeClass('d-inline');
                            $('#edit_panton_form_' + i).addClass('d-none');
                            
                            $('#color_label_' + i).removeClass('d-none');
                            $('#color_label_' + i).addClass('d-inline');
                            
                            $('#edit_panton_link_' + i).removeClass('d-none');
                            $('#edit_panton_link_' + i).addClass('d-inline');
                        })
                        .fail(function() {
                            alert('Ошибка при редактировании пантона');
                        });
            }
        </script>
    </body>
</html>