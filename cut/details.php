<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(CUTTER_USERS)) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id, направляем к списку заданий
$id = filter_input(INPUT_GET, 'id');
if($id === null) {
    header('Location: '.APPLICATION.'/cut/');
}

// Печать: лицевая, оборотная
const SIDE_FRONT = 1;
const SIDE_BACK = 2;

// Получение объекта
$name = '';
$quantity = '';
$unit = '';
$work_type_id = '';
$machine_id = '';

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

$length = '';
$streams_number = '';
$ink_number = '';
$customer_id = '';
$customer = '';
$width_1 = "";
$length_pure_1 = '';
$techmap_date = '';
$side = '';
$last_name = '';
$first_name = '';
$num_for_customer = '';

$sql = "select c.name, c.quantity, c.unit, c.work_type_id, c.machine_id, "
        . "c.customer_id, cus.name customer, "
        . "c.film_variation_id, f.name film_name, fv.thickness thickness, fv.weight weight, c.price, c.currency, c.individual_film_name, c.individual_thickness, c.individual_density, c.customers_material, c.ski, c.width_ski, "
        . "c.lamination1_film_variation_id, lam1f.name lamination1_film_name, lam1fv.thickness lamination1_thickness, lam1fv.weight lamination1_weight, c.lamination1_price, c.lamination1_currency, c.lamination1_individual_film_name, c.lamination1_individual_thickness, c.lamination1_individual_density, c.lamination1_customers_material, c.lamination1_ski, c.lamination1_width_ski, "
        . "c.lamination2_film_variation_id, lam2f.name lamination2_film_name, lam2fv.thickness lamination2_thickness, lam2fv.weight lamination2_weight, c.lamination2_price, c.lamination2_currency, c.lamination2_individual_film_name, c.lamination2_individual_thickness, c.lamination2_individual_density, c.lamination2_customers_material, c.lamination2_ski, c.lamination2_width_ski, "
        . "c.length, c.streams_number, c.ink_number, "
        . "cr.width_1, cr.length_pure_1, "
        . "tm.date techmap_date, tm.side, "
        . "u.last_name, u.first_name, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
        . "from calculation c "
        . "left join film_variation fv on c.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_variation lam1fv on c.lamination1_film_variation_id = lam1fv.id "
        . "left join film lam1f on lam1fv.film_id = lam1f.id "
        . "left join film_variation lam2fv on c.lamination2_film_variation_id = lam2fv.id "
        . "left join film lam2f on lam2fv.film_id = lam2f.id "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join techmap tm on tm.calculation_id = c.id "
        . "inner join user u on c.manager_id = u.id "
        . "where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $name = $row['name'];
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
    
    $length = $row['length'];
    $streams_number = $row['streams_number'];
    $ink_number = $row['ink_number'];
    $customer_id = $row['customer_id'];
    $customer = $row['customer'];$film_name = $row['film_name'];
    $width_1 = $row["width_1"];
    $length_pure_1 = $row['length_pure_1'];
    $techmap_date = $row['techmap_date'];
    $side = $row['side'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $num_for_customer = $row['num_for_customer'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
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
        </style>
    </head>
    <body>
        <?php
        include '../include/header_cut.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?= APPLICATION.'/cut/' ?>">К списку резок</a>
            <h1><?= $name ?></h1>
            <div class="name"><?=$customer ?></div>
            <div class="subtitle">№<?=$customer_id.'-'.$num_for_customer ?></div>
            <table>
                <tr>
                    <th>Объём заказа</th>
                    <td><?= DisplayNumber(intval($quantity), 0) ?> <?=$unit == 'kg' ? 'кг' : 'шт' ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($length_pure_1), 0) ?> м</td>
                </tr>
                <tr>
                    <th>Менеджер</th>
                    <td><?=$last_name.' '.$first_name ?></td>
                </tr>
                <tr>
                    <th>Тип работы</th>
                    <td><?=WORK_TYPE_NAMES[$work_type_id] ?></td>
                </tr>
                <tr>
                    <th>Карта составлена</th>
                    <td><?= DateTime::createFromFormat('Y-m-d H:i:s', $techmap_date)->format('d.m.Y H:i') ?></td>
                </tr>
            </table>
            <div class="subtitle">Хар-ки</div>
            <div class="subtitle">ИНФОРМАЦИЯ ПО ПЕЧАТИ</div>
            <table>
                <tr>
                    <td><?=PRINTER_NAMES[$machine_id] ?> Марка мат-ла</td>
                    <td><?= empty($film_name) ? $individual_film_name : $film_name ?></td>
                </tr>
                <tr>
                    <td>Толщина</td>
                    <td><?= empty($film_name) ? DisplayNumber(floatval($individual_thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($individual_density), 2), "0").' г/м<sup>2</sup>' : DisplayNumber(floatval($thickness), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($weight), 2), "0").' г/м<sup>2</sup>' ?></td>
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
            <div class="subtitle">ИНФОРМАЦИЯ ПО ЛАМИНАЦИИ 2</div>
            <div class="subtitle">ИНФОРМАЦИЯ ДЛЯ РЕЗЧИКА</div>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_cut.php';
        ?>
    </body>
</html>