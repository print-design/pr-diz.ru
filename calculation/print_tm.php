<?php
include '../include/topscripts.php';
include './calculation.php';
include './calculation_result.php';

// Получение коэффициента машины
function GetMachineCoeff($printer) {
    return $printer == PRINTER_COMIFLEX ? "1.14" : "1.7";
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);

if(!empty($calculation->ink_number)) {
    for($i=1; $i<=$calculation->ink_number; $i++) {
        $ink_var = "ink_$i";
        $$ink_var = $calculation->$ink_var;
    
        $color_var = "color_$i";
        $$color_var = $calculation->$color_var;
    
        $cmyk_var = "cmyk_$i";
        $$cmyk_var = $calculation->$cmyk_var;
        
        $lacquer_var = "lacquer_$i";
        $$lacquer_var = $calculation->$lacquer_var;
    
        $percent_var = "percent_$i";
        $$percent_var = $calculation->$percent_var;
    
        $cliche_var = "cliche_$i";
        $$cliche_var = $calculation->$cliche_var;
    }
}

$lamination = (empty($calculation->laminations_number) || $calculation->laminations_number == 0) ? "нет" : $calculation->laminations_number;

// Отходы
$waste1 = "";
$waste2 = "";
$waste3 = "";
$waste = "";

if(in_array($calculation->film_1, WASTE_PRESS_FILMS)) {
    $waste1 = WASTE_PRESS;
}
elseif($calculation->film_1 == WASTE_PAPER_FILM) {
    $waste1 = WASTE_PAPER;
}
elseif(empty ($calculation->film_1)) {
    $waste1 = "";
}
else {
    $waste1 = WASTE_KAGAT;
}

if(in_array($calculation->film_2, WASTE_PRESS_FILMS)) {
    $waste2 = WASTE_PRESS;
}
elseif ($calculation->film_2 == WASTE_PAPER_FILM) {
    $waste2 = WASTE_PAPER;
}
elseif(empty ($calculation->film_2)) {
    $waste2 = "";
}
else {
    $waste2 = WASTE_KAGAT;
}

if(in_array($calculation->film_3, WASTE_PRESS_FILMS)) {
    $waste3 = WASTE_PRESS;
}
elseif($calculation->film_3 == WASTE_PAPER_FILM) {
    $waste3 = WASTE_PAPER;
}
elseif(empty ($calculation->film_3)) {
    $waste3 = "";
}
else {
    $waste3 = WASTE_KAGAT;
}

$waste = $waste1;
if(!empty($waste2) && $waste2 != $waste1) $waste = WASTE_KAGAT;
if(!empty($waste3) && $waste3 != $waste2) $waste = WASTE_KAGAT;

$machine_coeff = null;

if(!empty($calculation->machine_id)) {
    $machine_coeff = GetMachineCoeff($calculation->machine_id);
}

// Тиражи и формы
$printings = array();
$cliches = array();
$repeats = array();
$cliches_used_flint = 0;
$cliches_used_kodak = 0;
$cliches_used_old = 0;
$quantities_sum = 0;
$lengths_sum = 0;

if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
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
        <link href="<?=APPLICATION ?>/fontawesome-free-5.15.1-web/css/all.min.css" rel="stylesheet" />
        <link href="<?=APPLICATION ?>/css/jquery-ui.css" rel="stylesheet"/>
        <link href="<?=APPLICATION ?>/css/main.css?version=73" rel="stylesheet">
        <link rel="shortcut icon" type="image/x-icon" href="<?=APPLICATION ?>/favicon.ico" />
        <style>
            img {
                vertical-align: middle;
                border-style: none;
            }
            
            body {
                margin: 0;
                line-height: 1.5;
                
                padding-left: 0;
                font-family: 'SF Pro Display';
                font-size: 16px;
            }
            
            table {
                border-collapse: collapse;
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
            
            td {
                line-height: 20px;
                padding-top: 7px;
                padding-bottom: 7px;
                border-bottom: solid 1px #cccccc;
            }
            
            tr td:nth-child(2), tr td:nth-child(5) {
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
                padding-left: 4px;
                padding-top: 4px;
                padding-right: 20px;
                text-align: right;
                vertical-align: top;
                border-bottom: 0;
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
                body {
                    font-size: 12px;
                }
                
                .header_qr {
                    margin-right: 10px;
                    height: 55px;
                    width: 55px;
                }
                
                .header_qr img {
                    height: 55px;
                    width: 55px;
                }
                
                .header_title {
                    font-size: 13px;
                }
                
                #title {
                    font-size: 23px;
                    margin-top: 7px;
                }
                
                #subtitle {
                    font-size: 18px;
                }
            
                .topproperty {
                    font-size: 14px;
                    margin-top: 4px;
                }
            
                td {
                    line-height: 16px;
                    padding-top: 4px;
                    padding-bottom: 4px;
                }
            
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
                    height: 150px;
                }
                
                .break_page {
                    page-break-before: always;
                    height: 150px;
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
            <div class="d-flex justify-content-between" style="display: flex; justify-content: space-between;">
                <div>
                    <?php
                    include_once '../qr/qrlib.php';
                    $errorCorrectionLevel = 'L'; // 'L','M','Q','H'
                    $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/calculation/details.php?id='.$id;
                    $filename = "../temp/$current_date_time.png";
                
                    do {
                        QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 3, 0, true);
                    } while (!file_exists($filename));
                    ?>
                    <div class="d-inline-block header_qr" style="display: inline-block;"><img src='<?=$filename ?>' /></div>
                    <div class="d-inline-block header_title font-weight-bold mr-3" style="display: inline-block; font-weight: 700; margin-right: 1rem;">
                        Заказ №<?=$calculation->customer_id ?>-<?=$calculation->num_for_customer ?><br />
                        от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?>
                    </div>
                    <div class="d-inline-block header_title font-weight-bold mr-2" style="display: inline-block; font-weight: 700; margin-right: 0.5rem;">
                        Карта составлена:
                        <br />
                        Менеджер:
                    </div>
                    <div class="d-inline-block header_title" style="display: inline-block;">
                        <?= empty($calculation_result->techmap_date) ? date('d.m.Y H:i') : DateTime::createFromFormat('Y-m-d H:i:s', $calculation_result->techmap_date)->format('d.m.Y H:i') ?>
                        <br />
                        <?=$calculation->first_name ?> <?=$calculation->last_name ?>
                    </div>
                </div>
                <div>
                    <div class="d-inline-block right_logo" style="display: inline-block;"><img src="../images/logo_with_label.svg" /></div>
                </div>
            </div>
            <div id="title">Заказчик: <?=$calculation->customer ?></div>
            <div id="subtitle">Наименование: <?=$calculation->name ?></div>
            <div class="row" style="display: flex; flex-wrap: wrap;">
                <div class="col-6 topproperty" style="-webkit-box-flex: 0; flex: 0 0 50%; max-width: 50%;">
                    <strong>Объем заказа:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? DisplayNumber(intval($quantities_sum), 0)." шт" : DisplayNumber(intval($calculation->quantity), 0).($calculation->unit == KG ? " кг" : " шт") ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? DisplayNumber(floatval($lengths_sum), 0)." м" : DisplayNumber(floatval($calculation_result->length_pure_1), 0)." м" ?>
                </div>
                <div class="col-6 topproperty" style="-webkit-box-flex: 0; flex: 0 0 50%; max-width: 50%;">
                    <strong>Тип работы:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=WORK_TYPE_NAMES[$calculation->work_type_id] ?>
                </div>
            </div>
        </div>
        <div id="placeholder_top"></div>
        <div id="main">
            <div class="row" style="display: flex; flex-wrap: wrap;">
                <div class="col-4 border-right" style="-webkit-box-flex: 0; flex: 0 0 33%; max-width: 33%; border-right: 1px solid  #dee2e6; padding-right: 5px;">
                    <table class="w-100" style="width: 100%;">
                        <tr>
                            <td colspan="2" class="font-weight-bold border-bottom-2" style="font-size: 18px; font-weight: 700;">Печать</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold" style="font-weight: 700; border-bottom: 0;">Печать</td>
                        </tr>
                        <tr>
                            <td>Машина</td>
                            <td>
                                <?php
                                if(!empty($calculation->machine_id)) {
                                    echo mb_stristr(PRINTER_SHORTNAMES[$calculation->machine_id], "zbs") ? "ZBS" : ucfirst(PRINTER_SHORTNAMES[$calculation->machine_id]);
                                }
                                ?>
                            </td>
                        </tr>
                        <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Поставщик мат-ла</td>
                            <td><?= empty($calculation_result->supplier) ? "Любой" : $calculation_result->supplier ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Марка мат-ла</td>
                            <td><?= $calculation->film_1 ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td class="text-nowrap" style="white-space: nowrap;"><?= DisplayNumber(floatval($calculation->thickness_1), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(rtrim(DisplayNumber(floatval($calculation->density_1), 2), "0"), ",").' г/м<sup>2</sup>' ?></td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= DisplayNumber(floatval($calculation_result->width_1), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td><?=$calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? "На приладку 1 тиража" : "Метраж на приладку" ?></td>
                            <td><?= DisplayNumber(floatval($calculation->data_priladka->length) * floatval($calculation->ink_number), 0) ?> м</td>
                        </tr>
                        <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Всего тиражей</td>
                            <td><?=count($printings) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= DisplayNumber(floatval($calculation_result->length_pure_1), 0) ?> м</td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Всего мат-ла</td>
                            <td><?= DisplayNumber(floatval($calculation_result->length_dirty_1), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Печать</td>
                            <td>
                                <?php
                                switch ($calculation_result->side) {
                                    case CalculationResult::SIDE_FRONT:
                                        echo 'Лицевая';
                                        break;
                                    case CalculationResult::SIDE_BACK:
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
                            <td><?= DisplayNumber(floatval($calculation->raport), 3) ?></td>
                        </tr>
                        <tr>
                            <td>Растяг</td>
                            <td>Нет</td>
                        </tr>
                        <tr>
                            <td><?=$calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? "Ширина этикетки" : "Ширина ручья" ?></td>
                            <td><?=$calculation->stream_width.(empty($calculation->stream_width) ? "" : " мм") ?></td>
                        </tr>
                        <tr>
                            <td>Длина этикетки</td>
                            <td><?= DisplayNumber(floatval($calculation->length), 0).(empty($calculation->length) ? "" : " мм") ?></td>
                        </tr>
                        <tr>
                            <td>Кол-во ручьёв</td>
                            <td><?=$calculation->streams_number ?></td>
                        </tr>
                        <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Этикеток в рапорте</td>
                            <td><?=$calculation->number_in_raport ?></td>
                        </tr>
                        <tr>
                            <td>Красочность</td>
                            <td><?=$calculation->ink_number ?> красок</td>
                        </tr>
                        <tr>
                            <td>Штамп</td>
                            <td><?= (empty($calculation->knife) || $calculation->knife == 0) ? "Старый" : "Новый" ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Требование по материалу</td>
                            <td><?=$calculation->requirement1 ?></td>
                        </tr>
                    </table>
                    <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?>
                    <p class="font-weight-bold" style="font-weight: bold;">Красочность: <?=$calculation->ink_number ?> красок</p>
                    <table class="w-100" style="width: 100%;">
                        <?php
                        for($i = 1; $i <= $calculation->ink_number; $i++):
                        $ink_var = "ink_$i";
                        $color_var = "color_$i";
                        $cmyk_var = "cmyk_$i";
                        $lacquer_var = "lacquer_$i";
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
                                        switch($$lacquer_var) {
                                            case CalculationBase::LACQUER_GLOSSY:
                                                echo 'Лак глянцевый';
                                                break;
                                            case CalculationBase::LACQUER_MATTE:
                                                echo 'Лак матовый';
                                                break;
                                            default :
                                                echo "Лак";
                                                break;
                                        }
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
                    </table>
                    <?php endif; ?>
                </div>
                <div class="col-8" style="-webkit-box-flex: 0; flex: 0 0 66%; max-width: 66%;">
                    <div class="row" style="display: flex; flex-wrap: wrap;">
                        <div class="col-6 border-right" style="-webkit-box-flex: 0; flex: 0 0 48%; max-width: 48%; border-right: 1px solid #dee2e6; padding-left: 5px; padding-right: 5px;">
                            <table class="w-100" style="width: 100%;">
                                <tr>
                                    <td colspan="2" class="font-weight-bold border-bottom-2" style="font-size: 18px; font-weight: 700;"><?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?> Ламинация<?php else: echo "<br /> "; endif; ?></td>
                                </tr>
                                <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?>
                                <tr>
                                    <td>Кол-во ламинаций</td>
                                    <td><?=$lamination ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="font-weight-bold" style="font-weight: 700; border-bottom: 0;">Ламинация 1</td>
                                </tr>
                                <tr>
                                    <td>Марка пленки</td>
                                    <td><?= $calculation->film_2 ?></td>
                                </tr>
                                <tr>
                                    <td>Толщина</td>
                                    <td class="text-nowrap" style="white-space: nowrap;"><?= DisplayNumber(floatval($calculation->thickness_2), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(rtrim(DisplayNumber(floatval($calculation->density_2), 2), "0"), ",").' г/м<sup>2</sup>' ?></td>
                                </tr>
                                <tr>
                                    <td>Ширина мат-ла</td>
                                    <td><?= DisplayNumber(floatval($calculation_result->width_2), 0) ?> мм</td>
                                </tr>
                                <tr>
                                    <td>Метраж на приладку</td>
                                    <td><?= DisplayNumber(floatval($calculation->data_priladka_laminator->length) * $calculation->uk2, 0) ?> м</td>
                                </tr>
                                <tr>
                                    <td>Метраж на тираж</td>
                                    <td><?= DisplayNumber(floatval($calculation_result->length_pure_2), 0) ?> м</td>
                                </tr>
                                <tr>
                                    <td>Всего мат-ла</td>
                                    <td><?= DisplayNumber(floatval($calculation_result->length_dirty_2), 0) ?> м</td>
                                </tr>
                                <tr>
                                    <td>Ламинационный вал</td>
                                    <td><?= DisplayNumber(floatval($calculation->lamination_roller_width), 0) ?> мм</td>
                                </tr>
                                <tr>
                                    <td>Анилокс</td>
                                    <td>Нет</td>
                                </tr>
                                <tr>
                                    <td>Требование по материалу</td>
                                    <td><?=$calculation->requirement2 ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="font-weight-bold" style="font-weight: 700; border-bottom: 0;">Ламинация 2</td>
                                </tr>
                                <tr>
                                    <td>Марка пленки</td>
                                    <td><?= $calculation->film_3 ?></td>
                                </tr>
                                <tr>
                                    <td>Толщина</td>
                                    <td class="text-nowrap" style="white-space: nowrap;"><?= DisplayNumber(floatval($calculation->thickness_3), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(rtrim(DisplayNumber(floatval($calculation->density_3), 2), "0"), ",").' г/м<sup>2</sup>' ?></td>
                                </tr>
                                <tr>
                                    <td>Ширина мат-ла</td>
                                    <td><?= DisplayNumber(floatval($calculation_result->width_3), 0) ?> мм</td>
                                </tr>
                                <tr>
                                    <td>Метраж на приладку</td>
                                    <td><?= DisplayNumber(floatval($calculation->data_priladka_laminator->length) * $calculation->uk3, 0) ?> м</td>
                                </tr>
                                <tr>
                                    <td>Метраж на тираж</td>
                                    <td><?= DisplayNumber(floatval($calculation_result->length_pure_3), 0) ?> м</td>
                                </tr>
                                <tr>
                                    <td>Всего мат-ла</td>
                                    <td><?= DisplayNumber(floatval($calculation_result->length_dirty_3), 0) ?> м</td>
                                </tr>
                                <tr>
                                    <td>Требование по материалу</td>
                                    <td><?=$calculation->requirement3 ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-6" style="-webkit-box-flex: 0; flex: 0 0 48%; max-width: 48%; padding-left: 5px;">
                            <table class="w-100" style="width: 100%;">
                                <tr>
                                    <td colspan="2" class="font-weight-bold border-bottom-2" style="font-size: 18px; font-weight: 700;">Резка</td>
                                </tr>
                                <tr>
                                    <td>Отгрузка в</td>
                                    <td><?=$calculation->unit == KG ? 'Кг' : 'Шт' ?></td>
                                </tr>
                                <tr>
                                    <td>Готовая продукция</td>
                                    <td><?=$calculation->unit == 'kg' ? 'Взвешивать' : 'Записывать метраж' ?></td>
                                </tr>
                                <tr>
                                    <td><?=$calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? "Обр. шир. / Гор. зазор" : "Обрезная ширина" ?></td>
                                    <td>
                                        <?php
                                        if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                                            if(empty($calculation->data_gap->gap_stream)) {
                                                echo DisplayNumber(intval($calculation->stream_width), 0)." мм";
                                            }
                                            else {
                                                echo DisplayNumber(floatval($calculation->stream_width) + floatval($calculation->data_gap->gap_stream), 2)." / ".DisplayNumber(floatval($calculation->data_gap->gap_stream), 2)." мм";
                                            }
                                        }
                                        else {
                                            echo DisplayNumber(intval($calculation->stream_width), 0)." мм";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Намотка до</td>
                                    <td>
                                        <?php
                                        if(empty($calculation_result->winding)) {
                                            echo 'Ждем данные';
                                        }
                                        elseif(empty ($calculation_result->winding_unit)) {
                                            echo 'Нет данных по кг/мм/м/шт';
                                        }
                                        elseif($calculation_result->winding_unit == 'pc') {
                                            if(empty($calculation->length)) {
                                                echo 'Нет данных по длине этикетки';
                                            }
                                            else {
                                                echo DisplayNumber(floatval($calculation_result->winding) * floatval($calculation->length) / 1000, 0);
                                            }
                                        }
                                        else {
                                            echo DisplayNumber(floatval($calculation_result->winding), 0);
                                        }
                                        
                                        switch ($calculation_result->winding_unit) {
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
                                        if(empty($calculation_result->winding) || empty($calculation_result->winding_unit)) {
                                            echo 'Ждем данные';
                                        }
                                        elseif(empty ($calculation->density_1)) {
                                            echo 'Нет данных по уд. весу пленки';
                                        }
                                        elseif(empty ($calculation_result->width_1)) {
                                            echo 'Нет данных по ширине мат-ла';
                                        }
                                        elseif($calculation_result->winding_unit == 'kg') {
                                            echo DisplayNumber((floatval($calculation_result->winding) * 1000 * 1000) / ((floatval($calculation->density_1) + ($calculation->density_2 === null ? 0 : floatval($calculation->density_2)) + ($calculation->density_3 === null ? 0 : floatval($calculation->density_3))) * floatval($calculation->stream_width)) - 200, 0)." м";
                                        }
                                        else {
                                            echo 'Нет';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Шпуля</td>
                                    <td><?= empty($calculation_result->spool) ? "Ждем данные" : $calculation_result->spool." мм" ?></td>
                                </tr>
                                <tr>
                                    <td>Этикеток в 1 м. пог.</td>
                                    <td><?= DisplayNumber($calculation->number_in_meter, 4) ?></td>
                                </tr>
                                <tr>
                                    <td>Бирки</td>
                                    <td>
                                        <?php
                                        switch ($calculation_result->labels) {
                                            case CalculationResult::LABEL_PRINT_DESIGN:
                                                echo "Принт-Дизайн";
                                                break;
                                            case CalculationResult::LABEL_FACELESS:
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
                                        switch ($calculation_result->package) {
                                            case CalculationResult::PACKAGE_PALLETED:
                                                echo "Паллетирование";
                                                break;
                                            case CalculationResult::PACKAGE_BULK:
                                                echo "Россыпью";
                                                break;
                                            case CalculationResult::PACKAGE_EUROPALLET:
                                                echo "Европаллет";
                                                break;
                                            case CalculationResult::PACKAGE_BOXES:
                                                echo "Коробки";
                                                break;
                                            default :
                                                echo "Ждем данные";
                                                break;
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $sql = "select name from calculation_stream where calculation_id = $id order by position";
                                $grabber = new Grabber($sql);
                                $streams = $grabber->result;
                                $i = 0;
                                
                                if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE):
                                ?>
                                <tr><td colspan="2" class="font-weight-bold border-bottom-2 pt-5" style="font-size: 18px; padding-top: 30px; font-weight: 700;">Наименования, <?= count($streams) ?> шт.</td></tr>
                                <?php
                                foreach($streams as $stream):
                                ?>
                                <tr><td colspan="2"><?=(++$i).'. '.$stream['name'] ?></td></tr>
                                <?php
                                endforeach;
                                
                                endif;
                                ?>
                            </table>
                        </div>
                    </div>
                    <div style="padding-left: 5px;">
                    <div class="border-bottom-2" style="font-size: 18px; margin-top: 10px; font-weight: 700;">
                        Фотометка:&nbsp;
                        <?php
                        switch ($calculation_result->photolabel) {
                            case CalculationResult::PHOTOLABEL_LEFT:
                                echo "Левая";
                                break;
                            case CalculationResult::PHOTOLABEL_RIGHT:
                                echo "Правая";
                                break;
                            case CalculationResult::PHOTOLABEL_BOTH:
                                echo "Две фотометки";
                                break;
                            case CalculationResult::PHOTOLABEL_NONE:
                                echo "Без фотометки";
                                break;
                            default :
                                echo ($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? "Без фотометки" : "Левая");
                                break;
                        }
                        ?>
                    </div>
                    <?php
                    $roll_folder = ($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? "roll" : "roll_left");
                    switch ($calculation_result->photolabel) {
                        case CalculationResult::PHOTOLABEL_LEFT:
                            $roll_folder = "roll_left";
                            break;
                        case CalculationResult::PHOTOLABEL_RIGHT:
                            $roll_folder = "roll_right";
                            break;
                        case CalculationResult::PHOTOLABEL_BOTH:
                            $roll_folder = "roll_both";
                            break;
                        case CalculationResult::PHOTOLABEL_NONE:
                            $roll_folder = "roll";
                            break;
                    }
                    ?>
                    <table class="fotometka">
                        <tr>
                            <td class="fotometka<?= $calculation_result->roll_type == 1 ? " fotochecked" : "" ?>">
                                <img src="../images/<?=$roll_folder ?>/roll_type_1_black.svg<?='?'. time() ?>" />
                                <?php if($calculation_result->roll_type == 1): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                            </td>
                            <td class="fotometka<?= $calculation_result->roll_type == 2 ? " fotochecked" : "" ?>">
                                <img src="../images/<?=$roll_folder ?>/roll_type_2_black.svg<?='?'. time() ?>" />
                                <?php if($calculation_result->roll_type == 2): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                            </td>
                            <td class="fotometka<?= $calculation_result->roll_type == 3 ? " fotochecked" : "" ?>">
                                <img src="../images/<?=$roll_folder ?>/roll_type_3_black.svg<?='?'. time() ?>" />
                                <?php if($calculation_result->roll_type == 3): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                            </td>
                            <td class="fotometka<?= $calculation_result->roll_type == 4 ? " fotochecked" : "" ?>">
                                <img src="../images/<?=$roll_folder ?>/roll_type_4_black.svg<?='?'. time() ?>" />
                                <?php if($calculation_result->roll_type == 4): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                            </td>
                            <td class="fotometka<?= $calculation_result->roll_type == 5 ? " fotochecked" : "" ?>">
                                <img src="../images/<?=$roll_folder ?>/roll_type_5_black.svg<?='?'. time() ?>" />
                                <?php if($calculation_result->roll_type == 5): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                            </td>
                            <td class="fotometka<?= $calculation_result->roll_type == 6 ? " fotochecked" : "" ?>">
                                <img src="../images/<?=$roll_folder ?>/roll_type_6_black.svg<?='?'. time() ?>" />
                                <?php if($calculation_result->roll_type == 6): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                            </td>
                            <td class="fotometka<?= $calculation_result->roll_type == 7 ? " fotochecked" : "" ?>">
                                <img src="../images/<?=$roll_folder ?>/roll_type_7_black.svg<?='?'. time() ?>" />
                                <?php if($calculation_result->roll_type == 7): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                            </td>
                            <td class="fotometka<?= $calculation_result->roll_type == 8 ? " fotochecked" : "" ?>">
                                <img src="../images/<?=$roll_folder ?>/roll_type_8_black.svg<?='?'. time() ?>" />
                                <?php if($calculation_result->roll_type == 8): ?><br /><img src="../images/icons/check_black.svg" /><?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    </div>
                </div>
            </div>
            <div class="font-weight-bold border-bottom-2" style="font-size: 18px; margin-top: 10px; font-weight: 700;">Комментарий</div>
            <div style="white-space: pre-wrap; font-size: 24px;"><?=$calculation_result->comment ?></div>
            <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
            <div class="break_page"></div>
            <div class="row" style="display: flex; flex-wrap: wrap;">
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
                <div class="col-3" style="-webkit-box-flex: 0; flex: 0 0 22%; max-width: 20%; padding-right: 30px;">
                    <div class="mt-4 mb-2 printing_title font-weight-bold" style="margin-top: 1.5rem; margin-bottom: 0.5rem; font-weight: 700;">Тираж <?=$printing_sequence ?></div>
                    <div class="d-flex justify-content-between font-italic border-bottom" style="display: flex; -webkit-box-pack: 0; justify-content: space-between; font-style: italic; border-bottom: 1px solid #dee2e6;">
                        <div><?= DisplayNumber(intval($printing['quantity']), 0) ?> шт</div>
                        <div><?= DisplayNumber(floatval($printing['length']), 0) ?> м</div>
                    </div>
                    <table class="mb-3 w-100" style="margin-bottom: 1rem; width: 100%;">
                    <?php
                    for($i = 1; $i <= $calculation->ink_number; $i++):
                    $ink_var = "ink_$i";
                    $color_var = "color_$i";
                    $cmyk_var = "cmyk_$i";
                    $lacquer_var = "lacquer_$i";
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
                                        switch($$lacquer_var) {
                                            case CalculationBase::LACQUER_GLOSSY:
                                                echo 'Лак глянцевый';
                                                break;
                                            case CalculationBase::LACQUER_MATTE:
                                                echo 'Лак матовый';
                                                break;
                                            default :
                                                echo 'Лак';
                                                break;
                                        }
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
                <div style="display: flex; justify-content: space-between;">
                    <div class="border-bottom-2" style="width: 48%; font-size: 18px; font-weight: 700; margin-bottom: 30px;">Дизайнер:</div>
                    <div class="border-bottom-2" style="width: 48%; font-size: 18px; font-weight: 700; margin-bottom: 30px;">Менеджер:</div>
                </div>
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