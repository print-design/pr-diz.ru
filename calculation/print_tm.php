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

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select c.date, c.customer_id, c.name calculation, c.quantity, c.unit, c.work_type_id, c.machine_id, (select shortname from machine where id = c.machine_id) machine, laminator_id, "
        . "c.film_variation_id, f.name film_name, fv.thickness thickness, fv.weight weight, c.price, c.currency, c.individual_film_name, c.individual_thickness, c.individual_density, c.customers_material, c.ski, c.width_ski, "
        . "c.lamination1_film_variation_id, lam1f.name lamination1_film_name, lam1fv.thickness lamination1_thickness, lam1fv.weight lamination1_weight, c.lamination1_price, c.lamination1_currency, c.lamination1_individual_film_name, c.lamination1_individual_thickness, c.lamination1_individual_density, c.lamination1_customers_material, c.lamination1_ski, c.lamination1_width_ski, "
        . "c.lamination2_film_variation_id, lam2f.name lamination2_film_name, lam2fv.thickness lamination2_thickness, lam2fv.weight lamination2_weight, c.lamination2_price, c.lamination2_currency, c.lamination2_individual_film_name, c.lamination2_individual_thickness, c.lamination2_individual_density, c.lamination2_customers_material, c.lamination2_ski, c.lamination2_width_ski, "
        . "c.streams_number, c.stream_width, c.raport, c.lamination_roller_width, c.ink_number, "
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
                <div class="d-inline-block"><img src='<?=$filename ?>' style='height: 100px; width: 100px;' /></div>
                <div class="d-inline-block">
                    Заказ №<?=$customer_id ?>-<?=$num_for_customer ?><br />
                    от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?>
                </div>
            </div>
            <div>
                <div class="d-inline-block"><img src="../images/logo.svg" /></div>
                <div class="d-inline-block">Принт-Дизайн</div>
            </div>
        </div>
        <h1><?=$customer ?></h1>
        <div class="subtitle"><?=$calculation ?></div>
        <div class="row">
            <div class="col-6">
                <p>Карта составлена: <?= DateTime::createFromFormat('Y-m-d H:i:s', $techmap_date)->format('d.m.Y H:i') ?></p>
                <p>Объем заказа: <strong><?= CalculationBase::Display(intval($quantity), 0) ?> <?=$unit == 'kg' ? 'кг' : 'шт' ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= CalculationBase::Display(floatval($length_pure_1), 0) ?> м</p>
            </div>
            <div class="col-6">
                <p>Менеджер: <?=$first_name ?> <?=$last_name ?></p>
                <p>Тип работы: <?=$work_type ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <table class="w-100">
                    <tr>
                        <td colspan="2">Информация для печатника</td>
                    </tr>
                    <tr>
                        <td colspan="2">Печать</td>
                    </tr>
                    <tr>
                        <td>Машина</td>
                        <td class="text-right"><?= empty($machine) ? "" : ($machine == CalculationBase::COMIFLEX ? "Comiflex" : "ZBS") ?></td>
                    </tr>
                    <tr>
                        <td>Марка пленки</td>
                        <td class="text-right"><?= empty($film_name) ? $individual_film_name : $film_name ?></td>
                    </tr>
                    <tr>
                        <td>Толщина</td>
                        <td class="text-right"><?= empty($film_name) ? CalculationBase::Display(floatval($individual_thickness), 0) : CalculationBase::Display(floatval($thickness), 0) ?> мкм</td>
                    </tr>
                    <tr>
                        <td>Ширина мат-ла</td>
                        <td class="text-right"><?= CalculationBase::Display(floatval($width_1), 0) ?> мм</td>
                    </tr>
                    <tr>
                        <td>Метраж на приладку</td>
                        <td class="text-right"><?= CalculationBase::Display(floatval($data_priladka->length) * floatval($ink_number), 0) ?> м</td>
                    </tr>
                    <tr>
                        <td>Метраж на тираж</td>
                        <td class="text-right"><?= CalculationBase::Display(floatval($length_pure_1), 0) ?> м</td>
                    </tr>
                    <tr>
                        <td>Всего мат-ла</td>
                        <td class="text-right"><?= CalculationBase::Display(floatval($length_dirty_1), 0) ?> м</td>
                    </tr>
                    <tr>
                        <td>Печать</td>
                        <td class="text-right">
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
                        <td class="text-right"><?= CalculationBase::Display(floatval($raport), 3) ?></td>
                    </tr>
                    <tr>
                        <td>Растяг</td>
                        <td class="text-right">Нет</td>
                    </tr>
                    <tr>
                        <td>Ширина ручья</td>
                        <td class="text-right"><?=$stream_width ?></td>
                    </tr>
                    <tr>
                        <td>Кол-во ручьёв</td>
                        <td class="text-right"><?=$streams_number ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-4">
                <table class="w-100">
                    <tr>
                        <td colspan="2">Информация для ламинации</td>
                    </tr>
                    <tr>
                        <td>Кол-во ламинаций</td>
                        <td class="text-right"><?=$lamination ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">Ламинация 1</td>
                    </tr>
                    <tr>
                        <td>Марка пленки</td>
                        <td class="text-right"><?= empty($lamination1_film_name) ? $lamination1_individual_film_name : $lamination1_film_name ?></td>
                    </tr>
                    <tr>
                        <td>Толщина</td>
                        <td class="text-right"><?= empty($lamination1_film_name) ? CalculationBase::Display(floatval($lamination1_individual_thickness), 0) : CalculationBase::Display(floatval($lamination1_thickness), 0) ?> мкм</td>
                    </tr>
                    <tr>
                        <td>Ширина мат-ла</td>
                        <td class="text-right"><?= CalculationBase::Display(floatval($width_2), 0) ?> мм</td>
                    </tr>
                    <tr>
                        <td>Метраж на приладку</td>
                        <td class="text-right"><?= CalculationBase::Display(floatval($data_priladka_laminator->length) * $uk2, 0) ?> м</td>
                    </tr>
                    <tr>
                        <td>Метраж на тираж</td>
                        <td class="text-right"><?= CalculationBase::Display(floatval($length_pure_2), 0) ?> м</td>
                    </tr>
                    <tr>
                        <td>Всего мат-ла</td>
                        <td class="text-right"><?= CalculationBase::Display(floatval($length_dirty_2), 0) ?> м</td>
                    </tr>
                    <tr>
                        <td>Ламинационный вал</td>
                        <td class="text-right"><?= CalculationBase::Display(floatval($lamination_roller_width), 0) ?> мм</td>
                    </tr>
                    <tr>
                        <td>Анилокс</td>
                        <td class="text-right">Нет</td>
                    </tr>
                </table>
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
    </body>
</html>