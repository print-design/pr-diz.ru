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

// Фотометка
const PHOTOLABEL_LEFT = "left";
const PHOTOLABEL_RIGHT = "right";
const PHOTOLABEL_BOTH = "both";
const PHOTOLABEL_NONE = "none";

// Данные получены из другой тех. карты
const FROM_OTHER_TECHMAP = "from_other_techmap";

// Получение коэффициента машины
function GetMachineCoeff($machine) {
    return $machine == CalculationBase::COMIFLEX ? "1.14" : "1.7";
}

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
$photolabel_valid = '';
$roll_type_valid = '';
$cliche_valid = '';

// Создание технологической карты
if(null !== filter_input(INPUT_POST, 'techmap_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    if(empty($id)) {
        $error_message == "Не указан ID расчёта";
        $form_valid = false;
    }
    
    $techmap_id = filter_input(INPUT_POST, 'techmap_id');
    
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    
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
    
    $photolabel = filter_input(INPUT_POST, 'photolabel');
    if($photolabel != PHOTOLABEL_LEFT && $photolabel != PHOTOLABEL_RIGHT && $photolabel != PHOTOLABEL_BOTH && $photolabel != PHOTOLABEL_NONE) {
        $photolabel_valid = ISINVALID;
        $form_valid = false;
    }
    
    $roll_type = filter_input(INPUT_POST, 'roll_type');
    if(empty($roll_type)) {
        $roll_type_valid = ISINVALID;
        $form_valid = false;
    }
    
    $comment = filter_input(INPUT_POST, 'comment');
    
    // Проверяем, чтобы были заполнены формы для всех красок
    $sql = "select count(distinct cq.id) * c.ink_number - count(cc.id) "
            . "from calculation_cliche cc "
            . "right join calculation_quantity cq on cc.calculation_quantity_id = cq.id "
            . "inner join calculation c on cq.calculation_id = c.id where c.id = $id";
    
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    
    if($row[0] === null || $row[0] > 0) {
        $cliche_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        if(empty($supplier_id)) {
            $supplier_id = "NULL";
        }
        $comment = addslashes($comment);
        
        $sql = "";
        
        if(empty($techmap_id)) {
            $sql = "insert into techmap (calculation_id, supplier_id, side, winding, winding_unit, spool, labels, package, photolabel, roll_type, comment) "
                    . "values($id, $supplier_id, $side, $winding, '$winding_unit', $spool, $labels, $package, '$photolabel', $roll_type, '$comment')";
        }
        else {
            $sql = "update techmap set supplier_id = $supplier_id, side = $side, winding = $winding, winding_unit = '$winding_unit', spool = $spool, "
                    . "labels = $labels, package = $package, photolabel = '$photolabel', roll_type = $roll_type, comment = '$comment' where id = $techmap_id";
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
        . "c.streams_number, c.stream_width, c.length, c.raport, c.number_in_raport, c.lamination_roller_width, c.ink_number, "
        . "c.ink_1, c.ink_2, c.ink_3, c.ink_4, c.ink_5, c.ink_6, c.ink_7, c.ink_8, "
        . "c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, "
        . "c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
        . "c.percent_1, c.percent_2, c.percent_3, c.percent_4, c.percent_5, c.percent_6, c.percent_7, c.percent_8, c.cliche_1, "
        . "c.cliche_2, c.cliche_3, c.cliche_4, c.cliche_5, c.cliche_6, c.cliche_7, c.cliche_8, "
        . "c.knife, "
        . "c.cliches_count_flint, c.cliches_count_kodak, c.cliches_count_old, "
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

$cliches_count_flint = $row['cliches_count_flint'];
$cliches_count_kodak = $row['cliches_count_kodak'];
$cliches_count_old = $row['cliches_count_old'];

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

$supplier_id = filter_input(INPUT_POST, 'supplier_id');
if($supplier_id === null) $supplier_id = $row['supplier_id'];

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

$photolabel = filter_input(INPUT_POST, 'photolabel');
if($photolabel === null) $photolabel = $row['photolabel'];

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

$machine_coeff = GetMachineCoeff($machine);

// Тиражи и формы
$printings = array();
$cliches = array();
$repeats = array();
$cliches_used_flint = 0;
$cliches_used_kodak = 0;
$cliches_used_old = 0;

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
}
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
                padding-left: 10px;
            }
            
            .printing_title {
                font-size: large;
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
        <?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
        <div id="set_printings" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header font-weight-bold" style="font-size: x-large;">
                        <div class="d-inline-block" style="position: relative; top: -3px;"><img src="../images/icons/printing.svg" style="top: 20px;" /></div>
                        &nbsp;&nbsp;&nbsp;
                        Настроить тиражи
                        <button type="button" class="close set_printings_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                    </div>
                    <?php
                    $printing_sequence = 0;
                    foreach ($printings as $printing):
                    $printing_sequence++;
                    $display = "d-none";
                    if($printing_sequence == 1) $display = "d-block";
                    ?>
                    <div class="modal-body set_printings set_printings_<?=$printing_sequence ?> <?=$display ?>">
                        <div class="printing_title font-weight-bold"><span style="font-size: x-large;">Тираж <?=$printing_sequence ?></span>&nbsp;&nbsp;&nbsp;<span style="font-size: large;"><?= CalculationBase::Display(floatval($printing['length']), 0) ?> м</span></div>
                        <div class="d-flex justify-content-start mb-3">
                            <div class="mr-2">
                                <div>Новая Flint <?=$machine_coeff ?></div>
                                <div>Новая Kodak <?=$machine_coeff ?></div>
                                <div>Старая</div>
                            </div>
                            <div class="text-right ml-2">
                                <div class="cliches_used_flint">выбрано <span class="flint_used"><?=$cliches_used_flint ?></span> из <?=$cliches_count_flint ?></div>
                                <div class="cliches_used_kodak">выбрано <span class="kodak_used"><?=$cliches_used_kodak ?></span> из <?=$cliches_count_kodak ?></div>
                                <div class="cliches_used_old">выбрано <span class="old_used"><?=$cliches_used_old ?></span> из <?=$cliches_count_old ?></div>
                            </div>
                        </div>
                        <?php
                        for($i = 1; $i <= $ink_number; $i++):
                        $ink_var = "ink_$i";
                        $color_var = "color_$i";
                        $cmyk_var = "cmyk_$i";
                        
                        $cliche_width_style = " w-100";
                        $repeat_display_style = " d-none"; //echo "QWE"; print_r($cliches);
                        if(!empty($cliches[$printing['id']][$i]) && $cliches[$printing['id']][$i] == CalculationBase::REPEAT) {
                            $cliche_width_style = " w-50";
                            $repeat_display_style = "";
                        }
                        ?>
                        <div class="d-flex justify-content-between">
                            <div class="form-group<?=$cliche_width_style ?>">
                                <label for="select_cliche_<?=$printing['id'] ?>_<?=$i ?>">
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
                                            echo 'Pantone '.$$color_var;
                                            break;
                                        case CalculationBase::WHITE:
                                            echo 'Белая';
                                            break;
                                        case CalculationBase::LACQUER:
                                            echo 'Лак';
                                            break;
                                    }
                                    ?>
                                </label>
                                <select class="form-control select_cliche" id="select_cliche_<?=$printing['id'] ?>_<?=$i ?>" data-printing-id="<?=$printing['id'] ?>" data-sequence="<?=$i ?>">
                                    <?php
                                    // Если для этой краски назначена конкретная форма, то она выбрана в списке
                                    $flint_selected = '';
                                    if(!empty($cliches[$printing['id']][$i]) && $cliches[$printing['id']][$i] == CalculationBase::FLINT) {
                                        $flint_selected = " selected='selected'";
                                    }
                                    $kodak_selected = '';
                                    if(!empty($cliches[$printing['id']][$i]) && $cliches[$printing['id']][$i] == CalculationBase::KODAK) {
                                        $kodak_selected = " selected='selected'";
                                    }
                                    $old_selected = '';
                                    if(!empty($cliches[$printing['id']][$i]) && $cliches[$printing['id']][$i] == CalculationBase::OLD) {
                                        $old_selected = " selected='selected'";
                                    }
                                    $repeat_selected = '';
                                    if(!empty($cliches[$printing['id']][$i]) && $cliches[$printing['id']][$i] == CalculationBase::REPEAT) {
                                        $repeat_selected = " selected='selected'";
                                    }
                                
                                    $flint_hidden = '';
                                    if(empty($flint_selected) && $cliches_used_flint >= $cliches_count_flint) {
                                        $flint_hidden = " hidden='hidden'";
                                    }
                                    $kodak_hidden = '';
                                    if(empty($kodak_selected) && $cliches_used_kodak >= $cliches_count_kodak) {
                                        $kodak_hidden = " hidden='hidden'";
                                    }
                                    $old_hidden = '';
                                    if(empty($old_selected) && $cliches_used_old >= $cliches_count_old) {
                                        $old_hidden = " hidden='hidden'";
                                    }
                                    $repeat_hidden = '';
                                    if($printing_sequence == 1) {
                                        $repeat_hidden = " hidden='hidden'";
                                    }
                                    ?>
                                    <option value="">Ждем данные</option>
                                    <option class="option_flint" id="option_flint_<?=$printing['id'] ?>_<?=$i ?>" value="<?= CalculationBase::FLINT ?>"<?=$flint_selected ?><?=$flint_hidden ?>>Новая Flint <?=$machine_coeff ?></option>
                                    <option class="option_kodak" id="option_kodak_<?=$printing['id'] ?>_<?=$i ?>" value="<?= CalculationBase::KODAK ?>"<?=$kodak_selected ?><?=$kodak_hidden ?>>Новая Kodak <?=$machine_coeff ?></option>
                                    <option class="option_old" id="option_old_<?=$printing['id'] ?>_<?=$i ?>" value="<?= CalculationBase::OLD ?>"<?=$old_selected ?><?=$old_hidden ?>>Старая</option>
                                    <option class="option_repeat" id="option_repeat_<?=$printing['id'] ?>_<?=$i ?>" value="<?= CalculationBase::REPEAT ?>"<?=$repeat_selected ?><?=$repeat_hidden ?>>Повторное использование</option>
                                </select>
                            </div>
                            <div class="form-group pl-2 w-50<?=$repeat_display_style ?>">
                                <label for="repeat_from_<?=$printing['id'] ?>_<?=$i ?>">С какого тиража</label>
                                <select class="form-control repeat_from" id="repeat_from_<?=$printing['id'] ?>_<?=$i ?>" data-printing-id="<?=$printing['id'] ?>" data-sequence="<?=$i ?>">
                                    <?php
                                    for($rep_pr = 1; $rep_pr < $printing_sequence; $rep_pr++):
                                        $rep_pr_selected = (!empty($repeats[$printing['id']][$i]) && $repeats[$printing['id']][$i] == $rep_pr) ? " selected='selected'" : "";
                                    ?>
                                    <option<?=$rep_pr_selected ?>><?= $rep_pr ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <div class="modal-footer set_printings set_printings_<?=$printing_sequence ?> <?=$display ?>" style="justify-content: flex-start;">
                        <?php if($printing_sequence == 1): ?>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Закрыть</button>
                        <?php else: ?>
                        <button type="button" class="btn btn-light change_printing" data-printing="<?=($printing_sequence - 1) ?>"><i class="fas fa-chevron-left mr-2"></i>Тираж <?=($printing_sequence - 1) ?></button>
                        <?php endif; ?>
                        <?php if($printing_sequence == count($printings)): ?>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Завершить</button>
                        <?php else: ?>
                        <button type="button" class="btn btn-dark change_printing" data-printing="<?=($printing_sequence + 1) ?>">Тираж <?=($printing_sequence + 1) ?><i class="fas fa-chevron-right ml-2"></i></button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
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
            <div class="d-flex justify-content-between">
                <div><h1><?= empty($techmap_id) ? "Составление тех. карты" : "Технологическая карта" ?></h1></div>
                <div><button type="btn" class="btn btn-outline-dark" data-toggle="modal" data-target="#techmapModal">Подгрузить из другого заказа</button></div>
            </div>
            <div class="name">Заказчик: <?=$customer ?></div>
            <div class="name">Наименование: <?=$calculation ?></div>
            <div class="subtitle">№<?=$customer_id ?>-<?=$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></div>
            <h2 class="mt-2">Остальная информация</h2>
            <div class="row">
                <div class="col-5">
                    <table>
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
                        <?php if($work_type_id != CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <th>Объем заказа</th>
                            <td class="text-left"><strong><?= CalculationBase::Display(intval($quantity), 0) ?> <?=$unit == 'kg' ? 'кг' : 'шт' ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= CalculationBase::Display(floatval($length_pure_1), 0) ?> м</td>
                        </tr>
                        <?php endif; ?>
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
                    <?php if($work_type_id != CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                    <h2>Информация для ламинации</h2>
                    <div class="subtitle">Кол-во ламинаций: <?=$lamination ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-4">
                    <h2>Информация для резчика</h2>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-4">
                    <table>
                        <tr>
                            <td style="padding-top: 5px;">Машина</td>
                            <td style="padding-top: 5px;"><?= mb_stristr($machine, "zbs") ? "ZBS" : ucfirst($machine) ?></td>
                        </tr>
                        <?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Поставщик мат-ла</td>
                            <td>
                                <?php
                                if(empty($techmap_id)) {
                                    echo "Ждем данные";
                                }
                                elseif(empty ($supplier)) {
                                    echo "Любой";
                                }
                                else {
                                    echo $supplier;
                                }
                                ?>
                            </td>
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
                            <td><?= $work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? "На приладку 1 тиража" : "Метраж на приладку" ?></td>
                            <td><?= CalculationBase::Display(floatval($data_priladka->length) * floatval($ink_number), 0) ?> м</td>
                        </tr>
                        <?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Всего тиражей</td>
                            <td><?= count($printings) ?></td>
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
                            <td><?= $work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? "Ширина этикетки" : "Ширина ручья" ?></td>
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
                            <td><?=$ink_number ?> цв.</td>
                        </tr>
                        <tr>
                            <td>Штамп</td>
                            <td><?= (empty($knife) || $knife == 0) ? "Старый" : "Новый" ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-4">
                    <?php if($work_type_id != CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                    <h3>Ламинация 1</h3>
                    <table>
                        <tr>
                            <td>Марка мат-ла</td>
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
                    </table>
                    <?php endif; ?>
                </div>
                <div class="col-4">
                    <h3>Информация для резчика</h3>
                    <table>
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
                            if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE) {
                                $sql = "select gap_stream from norm_gap order by date desc limit 1";
                                $fetcher = new Fetcher($sql);
                                if($row = $fetcher->Fetch()) {
                                    $norm_stream = CalculationBase::Display($row[0], 2);
                                }
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
            <?php if($work_type_id != CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
            <div class="row mt-3">
                <div class="col-4">
                    <h3>Красочность: <?=$ink_number ?> цв.</h3>
                    <table>
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
                    <table>
                        <tr>
                            <td>Марка мат-ла</td>
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
                    </table>
                </div>
            </div>
            <?php endif; ?>
            <?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
            <div class="mt-5 mb-3">
                <button type="button" id="show_set_printings" class="btn btn-outline-dark" data-toggle="modal" data-target="#set_printings">Настроить тиражи</button>
            </div>
            <div class="row">
                <?php
                $printing_sequence = 0;
                foreach($printings as $printing):
                    $printing_sequence++;
                ?>
                <div class="col-3">
                    <div class="printing_title font-weight-bold">Тираж <?=$printing_sequence ?></div>
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
            <a name="form" />
            <div id="cliche_validation" class="text-danger<?= empty($cliche_valid) ? " d-none" : " d-block" ?>">Укажите формы для каждой краски</div>
            <form class="mt-3" method="post"<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                <input type="hidden" name="scroll" />
                <input type="hidden" name="id" value="<?= $id ?>" />
                <input type="hidden" name="techmap_id" value="<?=$techmap_id ?>" />
                <div class="row">
                    <div class="col-6">
                        <h2>Информация для резчика</h2>
                        <?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                        <div class="form-group">
                            <label for="supplier_id">Поставщик мат-ла</label>
                            <select id="supplier_id" name="supplier_id" class="form-control">
                                <option value="">Любой</option>
                                <?php
                                $sql = "select id, name from supplier where id in (select supplier_id from supplier_film_variation where film_variation_id = $film_variation_id)";
                                $fetcher = new Fetcher($sql);
                                while($row = $fetcher->Fetch()):
                                    $checked = $supplier_id == $row['id'] ? " selected='selected'" : "";
                                ?>
                                <option value="<?=$row['id'] ?>"<?=$checked ?>><?=$row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="side">Печать</label>
                            <select id="side" name="side" class="form-control<?=$side_valid ?>" required="required">
                                <?php if($lamination == "нет"): ?>
                                <option value="" hidden="hidden">...</option>
                                <option value="<?=SIDE_FRONT ?>"<?= $side == 1 ? " selected='selected'" : "" ?>>Лицевая</option>
                                <?php endif; ?>
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
                                        <option value="m"<?= $winding_unit == 'm' ? " selected='selected'" : "" ?>>Метры</option>
                                        <option value="pc"<?= $winding_unit == 'pc' ? " selected='selected'" : "" ?>>шт</option>
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
                                <option value="" hidden="hidden">...</option>
                                <option value="<?=PACKAGE_PALLETED ?>"<?= $package == 1 ? " selected='selected'" : "" ?>>Паллетирование</option>
                                <option value="<?=PACKAGE_BULK ?>"<?= $package == 2 ? " selected='selected'" : "" ?>>Россыпью</option>
                            </select>
                            <div class="invalid-feedback">Упаковка обязательно</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <h3 style="margin-top: 20px;">Выберите фотометку</h3>
                        <div class="form-group">
                            <label for="photolabel">Фотометка</label>
                            <select id="photolabel" name="photolabel" class="form-control<?=$photolabel_valid ?>" required="required">
                                <option value="<?=PHOTOLABEL_LEFT ?>"<?=$photolabel == PHOTOLABEL_LEFT ? " selected='selected'" : "" ?>>Левая</option>
                                <option value="<?=PHOTOLABEL_RIGHT ?>"<?=$photolabel == PHOTOLABEL_RIGHT ? " selected='selected'" : "" ?>>Правая</option>
                                <option value="<?=PHOTOLABEL_BOTH ?>"<?=$photolabel == PHOTOLABEL_BOTH ? " selected='selected'" : "" ?>>Две фотометки</option>
                                <option value="<?=PHOTOLABEL_NONE ?>"<?=$photolabel == PHOTOLABEL_NONE || (empty($photolabel) && $work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE) ? " selected='selected'" : "" ?>>Без фотометки</option>
                            </select>
                            <div class="invalid-feedback">Расположение фотометки обязательно</div>
                        </div>
                        <div class="form-group roll-selector">
                            <?php
                            $roll_folder = $work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? "roll" : "roll_left";
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
                            <input type="radio" class="form-check-inline" id="roll_type_1" name="roll_type" value="1"<?= $roll_type == 1 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_1" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_1_image" src="../images/<?=$roll_folder ?>/roll_type_1.png<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_2" name="roll_type" value="2"<?= $roll_type == 2 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_2" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_2_image" src="../images/<?=$roll_folder ?>/roll_type_2.png<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_3" name="roll_type" value="3"<?= $roll_type == 3 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_3" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_3_image" src="../images/<?=$roll_folder ?>/roll_type_3.png<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_4" name="roll_type" value="4"<?= $roll_type == 4 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_4" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_4_image" src="../images/<?=$roll_folder ?>/roll_type_4.png<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_5" name="roll_type" value="5"<?= $roll_type == 5 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_5" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_5_image" src="../images/<?=$roll_folder ?>/roll_type_5.png<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_6" name="roll_type" value="6"<?= $roll_type == 6 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_6" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_6_image" src="../images/<?=$roll_folder ?>/roll_type_6.png<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_7" name="roll_type" value="7"<?= $roll_type == 7 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_7" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_7_image" src="../images/<?=$roll_folder ?>/roll_type_7.png<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_8" name="roll_type" value="8"<?= $roll_type == 8 ? " checked='checked'" : "" ?> />
                            <label for="roll_type_8" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_8_image" src="../images/<?=$roll_folder ?>/roll_type_8.png<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                        </div>
                        <div id="roll_type_validation" class="text-danger<?= empty($roll_type_valid) ? " d-none" : " d-block" ?>">Выберите сторону печати</div>
                        <h3>Комментарий</h3>
                        <textarea rows="6" name="comment" class="form-control"><?= html_entity_decode($comment) ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 d-flex justify-content-between mt-3">
                        <div>
                            <?php
                            $submit_class = " d-none";
                            if(empty($techmap_id) || filter_input(INPUT_POST, FROM_OTHER_TECHMAP) !== null || !$form_valid) {
                                $submit_class = "";
                            }
                            ?>
                            <button type="submit" name="techmap_submit" id="techmap_submit" class="btn btn-dark draft<?=$submit_class ?>" style="width: 175px;">Сохранить</button>
                        </div>
                        <div>
                            <?php if(!empty($techmap_id)): ?>
                            <a href="print_tm.php?id=<?= $id ?>" target="_blank" class="btn btn-outline-dark" style="width: 175px;">Печать</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Подгрузка тех. карты из другого заказа -->
        <div class="modal fixed-left fade" id="techmapModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-aside" role="document">
                <div class="modal-content" style="padding-left: 32px; padding-right: 32px; padding-bottom: 32px; padding-top: 84px; width: 521px; overflow-y: auto;">
                    <h2><?=$customer ?></h2>
                    <?php
                    $sql = "select c.id c_id, c.date c_date, c.name c_name, "
                            . "tm.id tm_id, tm.supplier_id tm_supplier_id, tm.side tm_side, tm.winding tm_winding, tm.winding_unit tm_winding_unit, tm.spool tm_spool, tm.labels tm_labels, tm.package tm_package, tm.photolabel tm_photolabel, tm.roll_type tm_roll_type, tm.comment tm_comment "
                            . "from calculation c "
                            . "inner join techmap tm on tm.calculation_id = c.id "
                            . "where customer_id = $customer_id "
                            . "and work_type_id = $work_type_id ";
                    if(!empty($techmap_id)) {
                        $sql .= "and tm.id <> $techmap_id ";
                    }
                    switch($lamination) {
                        case "нет":
                            $sql .= "and c.lamination1_film_variation_id is null and c.lamination1_individual_film_name = '' ";
                            break;
                        case "1";
                            $sql .= "and (c.lamination1_film_variation_id is not null or c.lamination1_individual_film_name <> '') "
                                    . "and c.lamination2_film_variation_id is null and c.lamination2_individual_film_name = '' ";
                            break;
                        case "2";
                            $sql .= "and (c.lamination2_film_variation_id is not null or c.lamination2_individual_film_name <> '') ";
                            break;
                        default :
                            $sql .= "and false ";
                            break;
                    }
                    $sql .= "order by c.date";
                    $fetcher = new Fetcher($sql);
                    
                    while($row = $fetcher->Fetch()):
                    $c_id = $row['c_id'];
                    $c_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['c_date'])->format('d.m.Y');
                    $c_name = $row['c_name'];
                    $tm_id = $row['tm_id'];
                    $tm_supplier_id = $row['tm_supplier_id'];
                    $tm_side = $row['tm_side'];
                    $tm_winding = $row['tm_winding'];
                    $tm_winding_unit = $row['tm_winding_unit'];
                    $tm_spool = $row['tm_spool'];
                    $tm_labels = $row['tm_labels'];
                    $tm_package = $row['tm_package'];
                    $tm_photolabel = $row['tm_photolabel'];
                    $tm_roll_type = $row['tm_roll_type'];
                    $tm_comment = $row['tm_comment'];
                    ?>
                    <div class="border-bottom mb-2">
                        <div class="d-flex justify-content-between">
                            <div class="pt-2 font-weight-bold" style="font-size: large;"><?='ТК от '.$c_date ?></div>
                            <div>
                                <form method="post" action="#form">
                                    <input type="hidden" name="<?=FROM_OTHER_TECHMAP ?>" value="1" />
                                    <input type="hidden" name="id" value="<?= $c_id ?>" />
                                    <input type="hidden" name="techmap_id" value="<?=$tm_id ?>" />
                                    <input type="hidden" name="supplier_id" value="<?=$tm_supplier_id ?>" />
                                    <input type="hidden" name="side" value="<?=$tm_side ?>" />
                                    <input type="hidden" name="winding" value="<?= $tm_winding ?>" />
                                    <input type="hidden" name="winding_unit" value="<?=$tm_winding_unit ?>" />
                                    <input type="hidden" name="spool" value="<?=$tm_spool ?>" />
                                    <input type="hidden" name="labels" value="<?=$tm_labels ?>" />
                                    <input type="hidden" name="package" value="<?=$tm_package ?>" />
                                    <input type="hidden" name="photolabel" value="<?=$tm_photolabel ?>" />
                                    <input type="hidden" name="roll_type" value="<?=$tm_roll_type ?>" />
                                    <input type="hidden" name="comment" value="<?=$tm_comment ?>" />
                                    <button type="submit" class="btn btn-light">+ Подцепить</button>
                                </form>
                            </div>
                        </div>
                        <div style="font-size: large;"><?=$c_name ?></div>
                    </div>
                    <?php endwhile; ?>
                    <button type="button" class="close" data-dismiss='modal' style="position: absolute; right: 34px; top: 34px; z-index: 2000;"><img src="../images/icons/close_modal_red.svg" /></button>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_find.php';
        ?>
        <script>
            // Скрываем сообщение о невалидном значении стороны печати
            $('.roll-selector input').change(function(){
                $('#roll_type_validation').removeClass('d-block');
                $('#roll_type_validation').addClass('d-none');
            });
            
            // Скрываем сообщение о невалидном заполнении форм
            $('button#show_set_printings').click(function() {
                $('#cliche_validation').removeClass('d-block');
                $('#cliche_validation').addClass('d-none');
            });
            
            // Показываем кнопку "Сохранить" при внесении изменений
            <?php if(!empty($techmap_id)): ?>
                $('select').change(function() {
                    $('#techmap_submit').removeClass('d-none');
                });
                
                $('input').not('.color_input').change(function() {
                    $('#techmap_submit').removeClass('d-none');
                });
                
                $('input').not('.color_input').keydown(function() {
                    $('#techmap_submit').removeClass('d-none');
                });
                
                $('textarea').keydown(function() {
                    $('#techmap_submit').removeClass('d-none');
                });
                
                $('textarea').change(function() {
                    $('#techmap_submit').removeClass('d-none');
                });
            <?php endif; ?>
            
            // Редактируем пантоны
            $('.color_input').keydown(function(e) {
                if(e.which == 13) {
                    e.preventDefault();
                    SavePanton($(this).attr('data-id'), $(this).attr('data-i'));
                }
            });
            
            // Изменение рисунка роликов при выборе фотометки
            $('select#photolabel').change(function() {
                switch($(this).val()) {
                    case '<?=PHOTOLABEL_LEFT ?>':
                        for(var i = 1; i <= 8; i++) {
                            $('img#roll_type_' + i + '_image').attr('src', '../images/roll_left/roll_type_' + i + '.png<?='?'. time() ?>');
                        }
                        break;
                    case '<?=PHOTOLABEL_RIGHT ?>':
                        for(var i = 1; i <= 8; i++) {
                            $('img#roll_type_' + i + '_image').attr('src', '../images/roll_right/roll_type_' + i + '.png<?='?'. time() ?>');
                        }
                        break;
                    case '<?=PHOTOLABEL_BOTH ?>':
                        for(var i = 1; i <= 8; i++) {
                            $('img#roll_type_' + i + '_image').attr('src', '../images/roll_both/roll_type_' + i + '.png<?='?'. time() ?>');
                        }
                        break;
                    default :
                        for(var i = 1; i <= 8; i++) {
                            $('img#roll_type_' + i + '_image').attr('src', '../images/roll/roll_type_' + i + '.png<?='?'. time() ?>');
                        }
                        break;
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
            
            <?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
                
            // Переход между страницами редактирования форм тиражей
            $('.change_printing').click(function() {
                $('.set_printings').removeClass('d-block');
                $('.set_printings').addClass('d-none');
                $('.set_printings_' + $(this).attr('data-printing')).removeClass('d-none');
                $('.set_printings_' + $(this).attr('data-printing')).addClass('d-block');
            });
            
            // Обработка выбора формы
            $('.select_cliche').change(function() {
                if($(this).val() == '<?= CalculationBase::REPEAT ?>') {
                    $(this).parent().removeClass('w-100');
                    $(this).parent().addClass('w-50');
                    $(this).parent().next().removeClass('d-none');
                }
                else {
                    $(this).parent().removeClass('w-50');
                    $(this).parent().addClass('w-100');
                    $(this).parent().next().addClass('d-none');
                }
                
                $.ajax({ dataType: 'JSON', url: '_edit_cliche.php?printing_id=' + $(this).attr('data-printing-id') + '&sequence=' + $(this).attr('data-sequence') + '&cliche=' + $(this).val() + '&machine_coeff=<?=$machine_coeff ?>&repeat_from=' + $('select#repeat_from_' + $(this).attr('data-printing-id') + '_' + $(this).attr('data-sequence')).val() })
                        .done(function(data) {
                            if(data.error != '') {
                                alert(data.error);
                            }
                            else {
                                var cliche = '';
                                switch(data.cliche) {
                                    case '<?= CalculationBase::FLINT ?>':
                                        cliche = 'Новая Flint ' + data.machine_coeff;
                                        break;
                                    case '<?= CalculationBase::KODAK ?>':
                                        cliche = 'Новая Kodak ' + data.machine_coeff;
                                        break;
                                    case '<?= CalculationBase::OLD ?>':
                                        cliche = 'Старая';
                                        break;
                                    case '<?= CalculationBase::REPEAT ?>':
                                        cliche = 'Повт. исп. с тир. ' + $('select#repeat_from_' + data.printing_id + '_' + data.sequence).val();
                                        break;
                                    default :
                                        cliche = 'Ждем данные';
                                        break;
                                }
                                $('#cliche_' + data.printing_id + '_' + data.sequence).text(cliche);
                                
                                $('span.flint_used').text(data.flint_used);
                                $('span.kodak_used').text(data.kodak_used);
                                $('span.old_used').text(data.old_used);
                                
                                $('option.option_flint').removeAttr('hidden');
                                $('option.option_kodak').removeAttr('hidden');
                                $('option.option_old').removeAttr('hidden');
                                if(data.flint_used >= <?=$cliches_count_flint ?>) {
                                    $('option.option_flint').attr('hidden', 'hidden');
                                }
                                if(data.kodak_used >= <?=$cliches_count_kodak ?>) {
                                    $('option.option_kodak').attr('hidden', 'hidden');
                                }
                                if(data.old_used >= <?=$cliches_count_old ?>) {
                                    $('option.option_old').attr('hidden', 'hidden');
                                }
                                $('option#option_' + data.cliche + '_' + data.printing_id + '_' + data.sequence).removeAttr('hidden');
                            }
                        })
                        .fail(function() {
                            alert("Ошибка при выборе формы");
                        });
            });
            
            // Обработка выбора, с какого тиража повторное использование
            $('.repeat_from').change(function() {
                $.ajax({ dataType: 'JSON', url: '_edit_repeat.php?printing_id=' + $(this).attr('data-printing-id') + '&sequence=' + $(this).attr('data-sequence') + '&repeat_from=' + $(this).val() })
                        .done(function(data) {
                            if(data.error != '') {
                                alert(data.error);
                            }
                            else {
                                cliche = 'Повт. исп. с тир. ' + data.repeat_from;
                                $('#cliche_' + data.printing_id + '_' + data.sequence).text(cliche);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при изменении с какого тиража повторное использование');
                        });
            });
            
            <?php endif; ?>
        </script>
    </body>
</html>