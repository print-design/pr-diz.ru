<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Смена статуса
if(null !== filter_input(INPUT_POST, 'change_status_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $status_id = filter_input(INPUT_POST, 'status_id');
    $extracharge = filter_input(INPUT_POST, 'extracharge');
    if(empty($extracharge)) {
        $sql = "update calculation set status_id=$status_id where id=$id";
    }
    else {
        $sql = "update calculation set status_id=$status_id, extracharge=$extracharge where id=$id";
    }
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        header('Location: '.APPLICATION.'/calculation/calculation.php'. BuildQuery('id', $id));
    }
}

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select c.date, c.customer_id, c.name name, c.work_type_id, c.quantity, c.unit, c.brand_name, c.thickness, c.lamination1_brand_name, c.lamination1_thickness, c.lamination2_brand_name, c.lamination2_thickness, "
        . "c.width, c.length, c.stream_width, c.streams_count, c.raport, c.paints_count, "
        . "c.paint_1, c.paint_2, c.paint_3, paint_4, paint_5, paint_6, paint_7, paint_8, "
        . "c.color_1, c.color_2, c.color_3, color_4, color_5, color_6, color_7, color_8, "
        . "c.cmyk_1, c.cmyk_2, c.cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
        . "c.percent_1, c.percent_2, c.percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
        . "c.form_1, c.form_2, c.form_3, form_4, form_5, form_6, form_7, form_8, "
        . "c.status_id, c.extracharge, "
        . "cs.name status, cs.colour, cs.colour2, cs.image, "
        . "cu.name customer, cu.phone customer_phone, cu.extension customer_extension, cu.email customer_email, cu.person customer_person, "
        . "wt.name work_type, "
        . "mt.name machine_type,"
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.brand_name and fbw.thickness = c.thickness limit 1) weight, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination1_brand_name and fbw.thickness = c.lamination1_thickness limit 1) lamination1_weight, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination2_brand_name and fbw.thickness = c.lamination2_thickness limit 1) lamination2_weight "
        . "from calculation c "
        . "left join calculation_status cs on c.status_id = cs.id "
        . "left join customer cu on c.customer_id = cu.id "
        . "left join work_type wt on c.work_type_id = wt.id "
        . "left join machine_type mt on c.machine_type_id = mt.id "
        . "where c.id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];
$customer_id = $row['customer_id'];
$name = $row['name'];
$work_type_id = $row['work_type_id'];
$quantity = $row['quantity'];
$unit = $row['unit'];
$brand_name = $row['brand_name'];
$thickness = $row['thickness'];
$weight = $row['weight'];
$lamination1_brand_name = $row['lamination1_brand_name'];
$lamination1_thickness = $row['lamination1_thickness'];
$lamination1_weight = $row['lamination1_weight'];
$lamination2_brand_name = $row['lamination2_brand_name'];
$lamination2_thickness = $row['lamination2_thickness'];
$lamination2_weight = $row['lamination2_weight'];
$width = $row['width'];
$length = $row['length'];
$stream_width = $row['stream_width'];
$streams_count = $row['streams_count'];
$raport = $row['raport'];
$paints_count = $row['paints_count'];

for($i=1; $i<=$paints_count; $i++) {
    $paint_var = "paint_$i";
    $$paint_var = $row[$paint_var];
    
    $color_var = "color_$i";
    $$color_var = $row[$color_var];
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = $row[$cmyk_var];
    
    $percent_var = "percent_$i";
    $$percent_var = $row[$percent_var];
    
    $form_var = "form_$i";
    $$form_var = $row[$form_var];
}

$status_id = $row['status_id'];
$extracharge = $row['extracharge'];

$status = $row['status'];
$colour = $row['colour'];
$colour2 = $row['colour2'];
$image = $row['image'];

$customer = $row['customer'];
$customer_phone = $row['customer_phone'];
$customer_extension = $row['customer_extension'];
$customer_email = $row['customer_email'];
$customer_person = $row['customer_person'];

$work_type = $row['work_type'];

$machine_type = $row['machine_type']
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
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="backlink">
                <a href="<?=APPLICATION ?>/calculation/">Назад</a>
            </div>
            <div class="row">
                <!-- Левая половина -->
                <div class="col-6" id="left_side">
                    <h1 style="font-size: 32px; font-weight: 600;"><?= htmlentities($name) ?></h1>
                    <h2 style="font-size: 26px;">№<?=$id ?> от <?= DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y') ?></h2>
                    <div style="width: 100%; padding: 12px; margin-top: 40px; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; background-color: <?=$colour2 ?>; border: solid 2px <?=$colour ?>; color: <?=$colour ?>">
                        <?=$image ?>&nbsp;&nbsp;&nbsp;<?=$status ?>
                    </div>
                    <div class="row">
                        <div class="row-6 w-50">
                            <table class="table table-striped">
                                <tr>
                                    <th class="font-weight-bold">Заказчик</th>
                                    <td>
                                        <p><?=$customer ?></p>
                                        <p><?=$customer_phone ?><?= empty($customer_extension) ? '' : ", доб. $customer_extension" ?></p>
                                        <p><?=$customer_email ?></p>
                                        <p><?=$customer_person ?></p>
                                    </td>
                                </tr>
                                <tr><th class="font-weight-bold">Тип работы</th><td><?=$work_type ?></td></tr>
                                    <?php
                                    if(!empty($quantity) && !empty($unit)):
                                    ?>
                                <tr><th class="font-weight-bold">Объем заказа</th><td><?=$quantity ?> <?=$unit ?></td></tr>
                                    <?php
                                    endif;
                                    if(!empty($machine_type)):
                                    ?>
                                <tr><th class="font-weight-bold">Печатная машина</th><td><?=$machine_type ?></td></tr>
                                    <?php
                                    endif;
                                    if(!empty($width)):
                                    ?>
                                <tr><th class="font-weight-bold">Обрезная ширина</th><td><?= number_format($width, 0, ",", " ") ?></td></tr>
                                    <?php
                                    endif;
                                    if(!empty($length)):
                                    ?>
                                <tr><th class="font-weight-bold">Длина от метки до метки</th><td><?= number_format($length, 2, ",", " ") ?></td></tr>
                                    <?php
                                    endif;
                                    if(!empty($streams_count)):
                                    ?>
                                <tr><th class="font-weight-bold">Количество ручьев</th><td><?= number_format($streams_count, 0, ",", " ") ?></td></tr>
                                    <?php
                                    endif;
                                    if(!empty($stream_width)):
                                    ?>
                                <tr><th class="font-weight-bold">Ширина ручья</th><td><?= number_format($stream_width, 2, ",", " ") ?></td></tr>
                                    <?php
                                    endif;
                                    if(!empty($raport)):
                                    ?>
                                <tr><th class="font-weight-bold">Рапорт</th><td><?= number_format($raport, 3, ",", " ") ?></td></tr>
                                    <?php
                                    endif;
                                    ?>
                            </table>
                        </div>
                        <div class="col-6 w-50">
                            <table class="table table-striped">
                                <?php
                                
                                if(!empty($brand_name) && !empty($thickness)):
                                    ?>
                                <tr>
                                    <th class="font-weight-bold">Пленка</th>
                                    <td>
                                        <p><?=$brand_name ?></p>
                                        <p><?= number_format($thickness, 0, ",", " ") ?> мкм&nbsp;&ndash;&nbsp;<?=$weight ?> г/м<sup>2</sup></p>
                                    </td>
                                </tr>
                                    <?php
                                    endif;
                                    if(!empty($lamination1_brand_name) && !empty($lamination1_thickness)):
                                    ?>
                                <tr>
                                    <th class="font-weight-bold">Ламинация 1</th>
                                    <td>
                                        <p><?=$lamination1_brand_name ?></p>
                                        <p><?= number_format($lamination1_thickness, 0, ",", " ") ?> мкм&nbsp;&ndash;&nbsp;<?=$lamination1_weight ?> г/м<sup>2</sup></p>
                                    </td>
                                </tr>
                                    <?php
                                    endif;
                                    if(!empty($lamination2_brand_name) && !empty($lamination2_thickness)):
                                    ?>
                                <tr>
                                    <th class="font-weight-bold">Ламинация 2</th>
                                    <td>
                                        <p><?=$lamination2_brand_name ?></p>
                                        <p><?= number_format($lamination2_thickness, 0, ",", " ") ?> мкм&nbsp;&ndash;&nbsp;<?=$lamination2_weight ?> г/м<sup>2</sup></p>
                                    </td>
                                </tr>
                                    <?php
                                    endif;
                                if(!empty($paints_count)):
                                ?>
                                <tr><th class="font-weight-bold">Количество красок</th><td><?=$paints_count ?></td></tr>
                                <?php
                                endif;
                                ?>
                            </table>
                            <table class="table table-striped">
                                <?php
                                for($i=1; $i<=$paints_count; $i++):
                                $paint_var = "paint_$i";
                                $color_var = "color_$i";
                                $cmyk_var = "cmyk_$i";
                                $percent_var = "percent_$i";
                                $form_var = "form_$i";
                                ?>
                                <tr>
                                    <th class="font-weight-bold">
                                        <?php
                                        echo $i.". ";
                                        
                                        switch ($$paint_var) {
                                        case 'cmyk':
                                            echo "CMYK";
                                            break;
                                        case 'panton':
                                            echo 'Пантон';
                                            break;
                                        case 'lacquer':
                                            echo 'Лак';
                                            break;
                                        case  'white':
                                            echo 'Белый';
                                            break;
                                        }
                                        ?>
                                    </th>
                                    <td>
                                        <?php if($$paint_var == "cmyk") echo $$cmyk_var ?>
                                        <?php if($$paint_var == "panton") echo $$color_var ?>
                                    </td>
                                    <td><?=$$percent_var ?>%</td>
                                    <td><?=$$form_var ?></td>
                                </tr>
                                <?php
                                endfor;
                                ?>
                            </table>
                        </div>
                    </div>
                    <?php if($status_id == 3): ?>
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <input type="hidden" id="change_status_submit" name="change_status_submit" />
                        <button type="submit" id="status_id" name="status_id" value="5" class="btn btn-outline-dark mt-5 mr-2 pl-5 pr-5">Отклонить</button>
                        <button type="submit" id="status_id" name="status_id" value="4" class="btn btn-dark mt-5 mr-2 pl-5 pr-5">Одобрить</button>
                    </form>
                    <?php endif; ?>
                </div>
                <!-- Правая половина -->
                <div class="col-6 col-lg-3">
                    <!-- Расчёт -->
                    <?php
                    include './right_panel.php';
                    ?>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // Автовыделение при щелчке для поля "наценка"
            $('#extracharge').click(function() {
                $(this).prop("selectionStart", 0);
                $(this).prop("selectionEnd", $(this).val().length);
            });
            
            // Показ расходов
            function ShowCosts() {
                $("#costs").removeClass("d-none");
                $("#show_costs").addClass("d-none");
            }
            
            // Скрытие расходов
            function HideCosts() {
                $("#costs").addClass("d-none");
                $("#show_costs").removeClass("d-none");
            }
        </script>
    </body>
</html>