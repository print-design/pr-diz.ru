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

$sql = "select c.date, c.customer_id, c.name calculation, c.quantity, c.unit, "
        . "c.film_variation_id, f.name film_name, fv.thickness thickness, fv.weight weight, c.price, c.currency, c.individual_film_name, c.individual_thickness, c.individual_density, c.customers_material, c.ski, c.width_ski, "
        . "c.lamination1_film_variation_id, lam1f.name lamination1_film_name, lam1fv.thickness lamination1_thickness, lam1fv.weight lamination1_weight, c.lamination1_price, c.lamination1_currency, c.lamination1_individual_film_name, c.lamination1_individual_thickness, c.lamination1_individual_density, c.lamination1_customers_material, c.lamination1_ski, c.lamination1_width_ski, "
        . "c.lamination2_film_variation_id, lam2f.name lamination2_film_name, lam2fv.thickness lamination2_thickness, lam2fv.weight lamination2_weight, c.lamination2_price, c.lamination2_currency, c.lamination2_individual_film_name, c.lamination2_individual_thickness, c.lamination2_individual_density, c.lamination2_customers_material, c.lamination2_ski, c.lamination2_width_ski, "
        . "c.streams_number, c.stream_width, c.raport, c.lamination_roller_width, c.ink_number, "
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
$ink_number = $row['ink_number'];

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
            <table>
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
                <div class="col-4">
                    <h2>Информация для печатника</h2>
                </div>
                <div class="col-4">
                    <h2>Информация для ламинации</h2>
                    <div class="subheader">Кол-во ламинаций: <?=$lamination ?></div>
                </div>
                <div class="col-4">
                    <h2>Информация для резчика</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <h3>Печать</h3>
                    <table>
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
                            <td>нет</td>
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
                <div class="col-4">
                    <h3>Ламинация 1</h3>
                    <table>
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
                <div class="col-4">
                    <h3>Информация для резчика</h3>
                    <table>
                        <tr>
                            <td>Отгрузка в</td>
                            <td><?=$unit == 'kg' ? 'кг' : 'шт' ?></td>
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
                <div class="col-4">
                    <h3>Красочность: <?=$ink_number ?> цв.</h3>
                </div>
                <div class="col-4">
                    <h3>Ламинация 2</h3>
                    <table>
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
            <form method="post">
                <div class="row">
                    <div class="col-6">
                        <h2>Информация для резчика</h2>
                        <div class="form-group">
                            <label for="side">Печать</label>
                            <select id="side" name="side" class="form-control" required="required">
                                <option value="" hidden="hidden">...</option>
                            </select>
                            <div class="invalid-feedback">Сторона обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="winding">Намотка до</label>
                            <select id="winding" name="winding" class="form-control" required="required">
                                <option value="" hidden="hidden">...</option>
                            </select>
                            <div class="invalid-feedback">Намотка обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="spool">Шпуля</label>
                            <select id="spool" name="spool" class="form-control" required="required">
                                <option value="" hidden="hidden">...</option>
                            </select>
                            <div class="invalid-feedback">Шпуля обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="labels">Бирки</label>
                            <select id="labels" name="labels" class="form-control" required="required">
                                <option value="" hidden="hidden">...</option>
                            </select>
                            <div class="invalid-feedback">Бирки обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="package">Упаковка</label>
                            <select id="package" name="package" class="form-control" required="required">
                                <option value="" hidden="">...</option>
                            </select>
                            <div class="invalid-feedback">Упаковка обязательно</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <h3>Выберите фотометку</h3>
                        <div class="form-group"></div>
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