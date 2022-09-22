<?php
include '../include/topscripts.php';
include '../calculation/status_ids.php';
include '../calculation/calculation.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан calculation_id, направляем к списку технических карт
if(null === filter_input(INPUT_GET, 'calculation_id')) {
    header('Location: '.APPLICATION.'/techmap/');
}

// Значение марки плёнки "другая"
const INDIVIDUAL = -1;

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

// Создание технологической карты
if(null !== filter_input(INPUT_POST, 'techmap_submit')) {
    if(empty(filter_input(INPUT_GET, 'calculation_id'))) {
        $error_message == "Не указан ID расчёта";
        $form_valid = false;
    }
    
    if($form_valid) {
        $calculation_id = filter_input(INPUT_GET, 'calculation_id');
        
        $sql = "insert into techmap (calculation_id) values($calculation_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $id = $executer->insert_id;
        
        if(empty($error_message)) {
            $sql = "update calculation set status_id = ".TECHMAP." where id = $calculation_id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message) && !empty($id)) {
            header("Location: details.php?id=$id");
        }
    }
}

// Получение объекта
$calculation_id = filter_input(INPUT_GET, 'calculation_id');

$sql = "select c.date, c.customer_id, c.name calculation, c.quantity, c.unit, c.work_type_id, c.machine_id, "
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
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
        . "from calculation c "
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
        . "where c.id = $calculation_id";
$fetcher = new Fetcher($sql);
$row = $fetcher->Fetch();

$calculation_date = $row['date'];
$customer_id = $row['customer_id'];
$calculation = $row['calculation'];
$quantity = $row['quantity'];
$unit = $row['unit'];
$work_type_id = $row['work_type_id'];
$machine_id = $row['machine_id'];

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
$date = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            .roll-selector input {
                margin:0;padding:0;
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
                border: solid 5px darkblue;
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/calculation/details.php?id=<?= filter_input(INPUT_GET, 'calculation_id') ?>">К расчету</a>
            <h1>Составление тех. карты</h1>
            <div class="name">Заказчик: <?=$customer ?></div>
            <div class="name">Наименование: <?=$calculation ?></div>
            <div class="subheader">№<?=$customer_id ?>-<?=$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation_date)->format('d.m.Y') ?></div>
            <h2>Остальная информация</h2>
            <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                <tr>
                    <th>Карта составлена</th>
                    <td><?= DateTime::createFromFormat('Y-d-m H:i:s', $date)->format('d.m.Y H:i') ?></td>
                </tr>
                <tr>
                    <th>Заказчик</th>
                    <td><?=$customer ?></td>
                </tr>
                <tr>
                    <th class="pb-3">Название заказа</th>
                    <td class="pb-3"><?=$calculation ?></td>
                </tr>
                <tr>
                    <th>Объем заказа</th>
                    <td><strong><?= CalculationBase::Display(intval($quantity), 0) ?> <?=$unit == 'kg' ? 'кг' : 'шт' ?></strong> <?= CalculationBase::Display(floatval($length_pure_1), 2) ?> м</td>
                </tr>
                <tr>
                    <th>Менеджер</th>
                    <td><?=$first_name ?> <?=$last_name ?></td>
                </tr>
                <tr>
                    <th>Тип работы</th>
                    <td><?=$work_type ?></td>
                </tr>
            </table>
            <div class="row">
                <div class="col-4 col-md-3">
                    <h2>Информация для печатника</h2>
                </div>
                <div class="col-4 col-md-3">
                    <h2>Информация для ламинации</h2>
                    <div class="subheader">Кол-во ламинаций: <?=$lamination ?></div>
                </div>
                <div class="col-4 col-md-3">
                    <h2>Информация для резчика</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-4 col-md-3">
                    <h3>Печать</h3>
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                        <tr>
                            <td>Марка пленки</td>
                            <td><?= empty($film_name) ? $individual_film_name : $film_name ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td><?= empty($film_name) ? CalculationBase::Display(floatval($individual_thickness), 2) : CalculationBase::Display(floatval($thickness), 2) ?> мкм</td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= CalculationBase::Display(floatval($width_1), 2) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Метраж на приладку</td>
                            <td><?= CalculationBase::Display(floatval($length_dirty_1) - floatval($length_pure_1), 2) ?> м</td>
                        </tr>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= CalculationBase::Display(floatval($length_pure_1), 2) ?> м</td>
                        </tr>
                        <tr>
                            <td>Печать</td>
                            <td>Ждём данные</td>
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
                            <td><?=$stream_width ?></td>
                        </tr>
                        <tr>
                            <td>Кол-во ручьёв</td>
                            <td><?=$streams_number ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-4 col-md-3">
                    <h3>Ламинация 1</h3>
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                        <tr>
                            <td>Марка пленки</td>
                            <td><?= empty($lamination1_film_name) ? $lamination1_individual_film_name : $lamination1_film_name ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td><?= empty($lamination1_film_name) ? CalculationBase::Display(floatval($lamination1_individual_thickness), 2) : CalculationBase::Display(floatval($lamination1_thickness), 2) ?> мкм</td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= CalculationBase::Display(floatval($width_2), 2) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Метраж на приладку</td>
                            <td><?= CalculationBase::Display(floatval($length_dirty_2) - floatval($length_pure_2), 2) ?> м</td>
                        </tr>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= CalculationBase::Display(floatval($length_pure_2), 2) ?> м</td>
                        </tr>
                        <tr>
                            <td>Ламинационный вал</td>
                            <td><?= CalculationBase::Display(floatval($lamination_roller_width), 2) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Анилокс</td>
                            <td>Нет</td>
                        </tr>
                    </table>
                </div>
                <div class="col-4 col-md-3">
                    <h3>Информация для резчика</h3>
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                        <tr>
                            <td>Отгрузка в</td>
                            <td><?=$unit == 'kg' ? 'Кг' : 'Шт' ?></td>
                        </tr>
                        <tr>
                            <td>Намотка до</td>
                            <td>Ждем данные</td>
                        </tr>
                        <tr>
                            <td>Шпуля</td>
                            <td>Ждем данные</td>
                        </tr>
                        <tr>
                            <td>Бирки</td>
                            <td>Ждем данные</td>
                        </tr>
                        <tr>
                            <td>Упаковка</td>
                            <td>Ждем данные</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-4 col-md-3">
                    <h3>Красочность: <?=$ink_number ?> цв.</h3>
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                        <?php
                        for($i = 1; $i <= $ink_number; $i++):
                        $ink_var = "ink_$i";
                        $color_var = "color_$i";
                        $cmyk_var = "cmyk_$i";
                        $percent_var = "percent_$i";
                        $cliche_var = "cliche_$i";
                        
                        $machine_coeff = $machine_id == CalculationBase::COMIFLEX ? "1.14" : "1.7";
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
                    </table>
                </div>
                <div class="col-4 col-md-3">
                    <h3>Ламинация 2</h3>
                    <table<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                        <tr>
                            <td>Марка пленки</td>
                            <td><?= empty($lamination2_film_name) ? $lamination2_individual_film_name : $lamination2_film_name ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td><?= empty($lamination2_film_name) ? CalculationBase::Display(floatval($lamination2_individual_thickness), 2) : CalculationBase::Display(floatval($lamination2_thickness), 2) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= CalculationBase::Display(floatval($width_3), 2) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Метраж на приладку</td>
                            <td><?= CalculationBase::Display(floatval($length_dirty_3) - floatval($length_pure_3), 2) ?> м</td>
                        </tr>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= CalculationBase::Display(floatval($length_pure_3), 2) ?> м</td>
                        </tr>
                    </table>
                </div>
            </div>
            <form method="post"<?=$work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE ? " class='d-none'" : "" ?>>
                <div class="row">
                    <div class="col-6 col-md-4">
                        <h2>Информация для резчика</h2>
                        <div class="form-group">
                            <label for="side">Печать</label>
                            <select id="side" name="side" class="form-control" required="required">
                                <option value="" hidden="hidden">...</option>
                                <option value="1">Лицевая</option>
                                <option value="2">Оборотная</option>
                            </select>
                            <div class="invalid-feedback">Сторона обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="winding">Намотка до</label>
                            <div class="input-group">
                                <input type="text" 
                                       id="winding" 
                                       name="winding" 
                                       class="form-control int-only" 
                                       placeholder="Намотка до" 
                                       value="" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('name', 'winding'); $(this).attr('placeholder', 'Намотка до');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('name', 'winding'); $(this).attr('placeholder', 'Намотка до');" 
                                       onfocusout="javascript: $(this).attr('name', 'winding'); $(this).attr('placeholder', 'Намотка до');" />
                                <div class="input-group-append">
                                    <select id="winding_unit" name="winding_unit" required="required">
                                        <option value="" hidden="hidden">...</option>
                                        <option value="kg">кг</option>
                                        <option value="mm">мм</option>
                                    </select>
                                </div>
                                <div class="invalid-feedback">Намотка обязательно</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="spool">Шпуля</label>
                            <select id="spool" name="spool" class="form-control" required="required">
                                <option value="" hidden="hidden">...</option>
                                <option>40</option>
                                <option>76</option>
                                <option>152</option>
                            </select>
                            <div class="invalid-feedback">Шпуля обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="labels">Бирки</label>
                            <select id="labels" name="labels" class="form-control" required="required">
                                <option value="" hidden="hidden">...</option>
                                <option value="1">Принт-Дизайн</option>
                                <option value="2">Безликие</option>
                            </select>
                            <div class="invalid-feedback">Бирки обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="package">Упаковка</label>
                            <select id="package" name="package" class="form-control" required="required">
                                <option value="" hidden="">...</option>
                                <option value="1">Паллетирование</option>
                                <option value="2">Россыпью</option>
                            </select>
                            <div class="invalid-feedback">Упаковка обязательно</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <h3>Выберите фотометку</h3>
                        <div class="form-group">
                            <label for="x"></label>
                            <input type="text" id="x" style="visibility: hidden;" />
                        </div>
                        <div class="form-group roll-selector">
                            <input type="radio" class="form-check-inline" id="roll_type_1" name="roll_type" value="1" />
                            <label for="roll_type_1"><image src="../images/rolls/1-50.gif" style="height: 50px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_2" name="roll_type" value="2" />
                            <label for="roll_type_2""><image src="../images/rolls/2-50.gif" style="height: 50px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_3" name="roll_type" value="3" />
                            <label for="roll_type_3""><image src="../images/rolls/3-50.gif" style="height: 50px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_4" name="roll_type" value="4" />
                            <label for="roll_type_4""><image src="../images/rolls/4-50.gif" style="height: 50px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_5" name="roll_type" value="5" />
                            <label for="roll_type_5""><image src="../images/rolls/5-50.gif" style="height: 50px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_6" name="roll_type" value="6" />
                            <label for="roll_type_6""><image src="../images/rolls/6-50.gif" style="height: 50px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_7" name="roll_type" value="7" />
                            <label for="roll_type_7""><image src="../images/rolls/7-50.gif" style="height: 50px; width: auto;" /></label>
                            <input type="radio" class="form-check-inline" id="roll_type_8" name="roll_type" value="8" />
                            <label for="roll_type_8""><image src="../images/rolls/8-50.gif" style="height: 50px; width: auto;" /></label>
                        </div>
                        <h3>Комментарий</h3>
                        <textarea rows="10" name="comment" class="form-control"></textarea>
                    </div>
                </div>
                <input type="hidden" name="calculation_id" value="<?= filter_input(INPUT_GET, 'calculation_id') ?>" />
                <button type="submit" name="techmap_submit" class="btn btn-outline-dark draft mt-3" style="width: 200px;">Сохранить</button>
            </form>
        </div> 
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>