<?php
include '../include/topscripts.php';
include './status_ids.php';
include './calculation.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Типы работы
const WORK_TYPE_NOPRINT = 1;
const WORK_TYPE_PRINT = 2;
const WORK_TYPE_SELF_ADHESIVE = 3;

// Лыжи
const NO_SKI = 1;
const STANDARD_SKI = 2;
const NONSTANDARD_SKI = 3;

// Формы
const OLD = "old";
const FLINT = "flint";
const KODAK = "kodak";
const TVER = "tver";

// Атрибут "поле неактивно"
const DISABLED_ATTR = "";

// Редактирование включения форм в стоимость
if(null !== filter_input(INPUT_POST, 'cliche_in_price_submit')) {
    $cliche_in_price = 0; if(filter_input(INPUT_POST, 'cliche_in_price') == 'on') $cliche_in_price = 1;
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "update calculation set cliche_in_price = $cliche_in_price where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "delete from calculation_result where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Смена статуса
if(null !== filter_input(INPUT_POST, 'change-status-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $status_id = filter_input(INPUT_POST, 'status_id');
    
    $sql = "update calculation set status_id = $status_id where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    header("Location: ".BuildQueryRemove("status"));
}

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select rc.date, rc.customer_id, rc.name, rc.unit, rc.quantity, rc.work_type_id, wt.name work_type, "
        . "rc.film_variation_id, f.name film_name, fv.thickness thickness, fv.weight weight, rc.price, rc.currency, rc.individual_film_name, rc.individual_thickness, rc.individual_density, rc.customers_material, rc.ski, rc.width_ski, "
        . "rc.lamination1_film_variation_id, lam1f.name lamination1_film_name, lam1fv.thickness lamination1_thickness, lam1fv.weight lamination1_weight, rc.lamination1_price, rc.lamination1_currency, rc.lamination1_individual_film_name, rc.lamination1_individual_thickness, rc.lamination1_individual_density, rc.lamination1_customers_material, rc.lamination1_ski, rc.lamination1_width_ski, "
        . "rc.lamination2_film_variation_id, lam2f.name lamination2_film_name, lam2fv.thickness lamination2_thickness, lam2fv.weight lamination2_weight, rc.lamination2_price, rc.lamination2_currency, rc.lamination2_individual_film_name, rc.lamination2_individual_thickness, rc.lamination2_individual_density, rc.lamination2_customers_material, rc.lamination2_ski, rc.lamination2_width_ski, "
        . "rc.streams_number, m.name machine, m.colorfulness colorfulness, rc.length, rc.stream_width, rc.raport, rc.number_in_raport, rc.lamination_roller_width, rc.ink_number, u.first_name, u.last_name, rc.status_id, "
        . "rc.ink_1, rc.ink_2, rc.ink_3, rc.ink_4, rc.ink_5, rc.ink_6, rc.ink_7, rc.ink_8, "
        . "rc.color_1, rc.color_2, rc.color_3, rc.color_4, rc.color_5, rc.color_6, rc.color_7, rc.color_8, "
        . "rc.cmyk_1, rc.cmyk_2, rc.cmyk_3, rc.cmyk_4, rc.cmyk_5, rc.cmyk_6, rc.cmyk_7, rc.cmyk_8, "
        . "rc.percent_1, rc.percent_2, rc.percent_3, rc.percent_4, rc.percent_5, rc.percent_6, rc.percent_7, rc.percent_8, rc.cliche_1, "
        . "rc.cliche_2, rc.cliche_3, rc.cliche_4, rc.cliche_5, rc.cliche_6, rc.cliche_7, rc.cliche_8, "
        . "rc.cliche_in_price, rc.extracharge, rc.extracharge_cliche, "
        . "cus.name customer, cus.phone customer_phone, cus.extension customer_extension, cus.email customer_email, cus.person customer_person, "
        . "(select count(id) from calculation where customer_id = rc.customer_id and id <= rc.id) num_for_customer "
        . "from calculation rc "
        . "left join film_variation fv on rc.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_variation lam1fv on rc.lamination1_film_variation_id = lam1fv.id "
        . "left join film lam1f on lam1fv.film_id = lam1f.id "
        . "left join film_variation lam2fv on rc.lamination2_film_variation_id = lam2fv.id "
        . "left join film lam2f on lam2fv.film_id = lam2f.id "
        . "left join machine m on rc.machine_id = m.id "
        . "left join user u on rc.manager_id = u.id "
        . "left join work_type wt on rc.work_type_id = wt.id "
        . "left join customer cus on rc.customer_id = cus.id "
        . "where rc.id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];
$customer_id = $row['customer_id'];
$name = $row['name'];
$unit = $row['unit'];
$quantity = $row['quantity'];
$work_type_id = $row['work_type_id'];
$work_type = $row['work_type'];

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
$machine = $row['machine'];
$colorfulness = $row['colorfulness'];
$length = $row['length'];
$stream_width = $row['stream_width'];
$raport = $row['raport']; //rtrim(rtrim(number_format($row['raport'], 3, ",", " "), "0"), ",");
$number_in_raport = $row['number_in_raport'];
$lamination_roller_width = $row['lamination_roller_width'];
$ink_number = $row['ink_number'];
$first_name = $row['first_name'];
$last_name = $row['last_name'];
$status_id = $row['status_id'];

$new_forms_number = 0;

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
    
    if(!empty($$cliche_var) && $$cliche_var != OLD) {
        $new_forms_number++;
    }
}

$cliche_in_price = $row['cliche_in_price'];
$extracharge = $row['extracharge'];
$extracharge_cliche = $row['extracharge_cliche'];

$customer = $row['customer'];
$customer_phone = $row['customer_phone'];
$customer_extension = $row['customer_extension'];
$customer_email = $row['customer_email'];
$customer_person = $row['customer_person'];

$num_for_customer = $row['num_for_customer'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table.calculation-table tr th, table.calculation-table tr td {
                padding-top: 5px;
                padding-right: 5px;
                padding-bottom: 5px;
                padding-left: 5px;
                vertical-align: top;
            }
            
            table.calculation-table tr td {
                white-space: nowrap;
            }
            
            #left_side {
                width: 45%;
            }
            
            #calculation {
                width: 50%;
            }
            
            .btn-outline-dark.draft {
                color: gray;
                background-color: white;
                border-color: gray;
                border-radius: 8px;
            }
            
            .btn-outline-dark.draft:hover, .btn-outline-dark.draft:active {
                color: white;
                background-color: gray;
                border-color: gray;
            }
        </style>
    </head>
    <body>
        <?php
        include './right_panel.php';
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/calculation/<?= $status_id == CALCULATION ? BuildQueryRemove("id") : BuildQueryAddRemove('status', $status_id, 'id') ?>">Назад</a>
            <!-- Левая половина -->
            <div id="left_side">
                <h1 style="font-size: 32px; font-weight: 600;"><?= htmlentities($name) ?></h1>
                <h2 style="font-size: 26px;">№<?=$customer_id."-".$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></h2>
                <?php
                if($status_id == DRAFT):
                ?>
                <div style="width: 100%; padding: 12px; margin-top: 40p; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; border: solid 2px gray; color: gray;">
                    <i class="fas fa-scroll"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Черновик
                </div>
                <?php
                elseif($status_id == CALCULATION):
                ?>
                <div style="width: 100%; padding: 12px; margin-top: 40p; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; border: solid 2px blue; color: blue;">
                    <i class="fas fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Сделан расчёт
                </div>
                <?php
                elseif($status_id == TECHMAP):
                ?>
                <div style="width: 100%; padding: 12px; margin-top: 40p; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; border: solid 2px green; color: green;">
                    <i class="fas fa-file"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Составлена технологическая карта
                </div>
                <?php
                endif;
                
                include './left_panel.php';
                ?>
                <a href="create.php<?= BuildQuery("mode", "recalc") ?>" class="btn btn-dark mt-5 mr-2" style="width: 200px;">Пересчитать</a>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // Показ расходов
            function ShowCosts() {
                $("#costs").removeClass("d-none");
                $("#show_costs").addClass("d-none");
                AdjustFixedBlock($('#calculation'));
            }
            
            // Скрытие расходов
            function HideCosts() {
                $("#costs").addClass("d-none");
                $("#show_costs").removeClass("d-none");
                AdjustFixedBlock($('#calculation'));
            }
            
            // Ограничение значений наценки
            $('#extracharge').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('#extracharge_cliche').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('#extracharge').change(function(){
                ChangeLimitIntValue($(this), 999);
            });
            
            $('#extracharge_cliche').change(function(){
                ChangeLimitIntValue($(this), 999);
            });
            
            $('#extracharge').keyup(function(){
                $('#extracharge-submit').removeClass('d-none');
            });
            
            $('#extracharge_cliche').keyup(function(){
                $('#extracharge-cliche-submit').removeClass('d-none');
            });
            
            // Отображение полностью блока с фиксированной позицией, не умещающегося полностью в окне
            AdjustFixedBlock($('#calculation'));
            
            $(window).on("scroll", function(){
                AdjustFixedBlock($('#calculation'));
            });
        </script>
    </body>
</html>