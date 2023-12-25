<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';

// Авторизация
if(!IsInRole(CUTTER_USERS) && !IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id, направляем к списку заданий
$id = filter_input(INPUT_GET, 'id');
if($id === null) {
    header('Location: '.APPLICATION.'/cut/');
}

// Расчёт
$calculation = Calculation::Create($id);

// Обработка формы распечатки ручья
$invalid_stream = 0;

if(null !== filter_input(INPUT_POST, 'stream_print_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    $stream_id = filter_input(INPUT_POST, 'stream_id');
    $stream_width = filter_input(INPUT_POST, 'stream_width');
    $spool = filter_input(INPUT_POST, 'spool');
    
    $density1 = floatval(filter_input(INPUT_POST, 'density1'));
    $density2 = floatval(filter_input(INPUT_POST, 'density2'));
    $density3 = floatval(filter_input(INPUT_POST, 'density3'));
    
    $weight = floatval(filter_input(INPUT_POST, 'weight'));
    $length = floatval(filter_input(INPUT_POST, 'length'));
    $radius = floatval(filter_input(INPUT_POST, 'radius'));
    
    $is_valid = false;
    $validation1 = false;
    $validation2 = false;
    
    // Валидация данных
    // Валидация 1 между инпутами «Масса» и «Метраж» 
    // 0,9* (метраж*ширину ручья/1000*(удельный вес пленка 1 + удельный вес пленка 2 + удельный вес пленка 3)/1000)
    // <Масса катушки < 1,1* (метраж*ширину ручья/1000*(удельный вес пленка 1 + удельный вес пленка 2 + удельный вес пленка 3)/1000)
    if(0.9 * ($length * $stream_width / 1000 * ($density1 + $density2 + $density3) / 1000) < $weight && $weight < 1.1 * ($length * $stream_width / 1000 * ($density1 + $density2 + $density3) / 1000)) {
        $validation1 = true;
    }
    
    // Валидация 2 между инпутами «Метраж» и «Радиус»
    // Если 76 шпуля
    // 0,85* (0,15*R*R+11,3961*R-176,4427) * (20 /(толщина пленка 1 + толщина пленка 2 + толщина пленка 3))
    // <Метраж катушки<1,15* (0,15*R*R+11,3961*R-176,4427) * (20 / (толщина пленка 1 + толщина пленка 2 + толщина пленка 3))
    // Если 152 шпуля
    // 1,15* (0,1524*R*R+23,1245*R-228,5017) * (20 / (толщина пленка 1 + толщина пленка 2 + толщина пленка 3))
    // <Метраж<1,15* (0,1524*R*R+23,1245*R-228,5017) * (20 / толщина пленка 1 + толщина пленка 2 + толщина пленка 3)
    if($spool == 76) {
        if(0.85 * (0.15 * $radius * $radius + 11.3961 * $radius - 176.4427) * (20 / ($density1 + $density2 + $density3)) < $length && $length < 1.15 * (0.15 * $radius * $radius + 11.3961 * $radius - 176.4427) * (20 / ($density1 + $density2 + $density3))) {
            $validation2 = true;
        }
    }
    elseif($spool == 152) {
        if(1.15 * (0.1524 * $radius * $radius + 23.1245 * $radius - 228.5017) * (20 / ($density1 + $density2 + $density3)) < $length && $length < 1.15 * (0.1524 * $radius * $radius + 23.1245 * $radius - 228.5017) * (20 / ($density1 + $density2 + $density3))) {
            $validation2 = true;
        }
    }
    else {
        if($weight > 0 && $length > 0 && $radius > 0) {
            $validation2 = true;
        }
    }

    if($validation1 && $validation2) {
        $is_valid = true;
    }
    
    if(!$is_valid) {
        $invalid_stream = $stream_id;
    }
    else {
        $sql = "update calculation_stream set weight = $weight, length = $length, radius = $radius, printed = now() where id = $stream_id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header("Location:".APPLICATION."/cut/take.php?id=$id&machine_id=$machine_id&stream_id=$stream_id");
        }
    }
}

// Получение объекта
$date = '';
$name = '';
$unit = '';
$status_id = '';
$customer_id = '';
$customer = '';

$techmap_date = '';
$side = '';
$winding = '';
$winding_unit = '';
$spool = '';
$labels = '';
$package = '';

$num_for_customer = '';
$take_id = '';
$take_number = '';

$sql = "select c.date, c.name, c.unit, c.status_id, c.customer_id, cus.name customer, "
        . "tm.date techmap_date, tm.side, tm.winding, tm.winding_unit, tm.spool, tm.labels, tm.package, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, "
        . "(select max(id) from calculation_take where calculation_id = $id) take_id, "
        . "(select count(id) from calculation_take where calculation_id = $id) take_number "
        . "from calculation c "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join techmap tm on tm.calculation_id = c.id "
        . "where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $name = $row['name'];
    $unit = $row['unit'];
    $status_id = $row['status_id'];
    $customer_id = $row['customer_id'];
    $customer = $row['customer'];
    
    $techmap_date = $row['techmap_date'];
    $side = $row['side'];
    $winding = $row['winding'];
    $winding_unit = $row['winding_unit'];
    $spool = $row['spool'];
    $labels = $row['labels'];
    $package = $row['package'];
    
    $num_for_customer = $row['num_for_customer'];
    $take_id = $row['take_id'];
    $take_number = $row['take_number'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            @media print {
                body {
                    padding: 0;
                    margin: 0;
                    font-size: 7px;
                }
                
                .no_print {
                    display:none;
                }
                
                .pagebreak { 
                    page-break-after: always;
                }
            }
            
            @media screen {
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
                    line-height: 22px;
                }
            
                tr td:nth-child(2) {
                    text-align: right;
                    padding-left: 10px;
                    font-weight: bold;
                }
            
                .cutter_info {
                    border-radius: 15px;
                    box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                    padding: 20px;
                    padding-top: 5px;
                }
            
                #status {
                    width: 100%;
                    padding: 12px;
                    margin-top: 40p;
                    margin-bottom: 40px;
                    border-radius: 10px;
                    font-weight: bold;
                    text-align: center; 
                }
            
                .calculation_stream {
                    border-radius: 15px;
                    box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                    padding: 20px;
                    margin-bottom: 10px;
                }
            
                #calculation_streams_bottom {
                    padding-top: 10px;
                }
            
                .target {
                    border-top: solid 3px gray;
                }
            
                .print_only {
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        <div class="no_print">
        <?php
        include '../include/header_cut.php';
        ?>
        </div>
        <div class="container-fluid no_print">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-8">
                    <div class="row">
                        <div class="col-6">
                            <h1><?=$name ?></h1>
                            <div class="name"><?=$customer ?></div>
                            <div class="subtitle">№<?=$customer_id.'-'.$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></div>
                            <div style="background-color: lightgray; padding-left: 10px; padding-right: 15px; padding-top: 2px; border-radius: 10px; display: inline-block;">
                                <i class="fas fa-circle" style="font-size: x-small; vertical-align: bottom; padding-bottom: 7px; color: <?=ORDER_STATUS_COLORS[$status_id] ?>;">&nbsp;&nbsp;</i><?=ORDER_STATUS_NAMES[$status_id]." 0 м из ".DisplayNumber(floatval($calculation->length_pure_1), 0) ?>
                            </div>
                        </div>
                    </div>
                    <div class="name">Съём <?=$take_number ?></div>
                    <div id="calculation_streams">
                        <?php include './_calculation_streams.php'; ?>
                    </div>
                    <div class="d-flex justify-content-xl-start" id="calculation_streams_bottom" data-id="0" ondragover="DragOverBottom(event);" ondrop="DropBottom(event);">
                        <div><button type="button" class="btn btn-dark pl-4 pr-4 mr-4"><i class="fas fa-check mr-2"></i>Съём закончен</button></div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4"><img src="../images/icons/error_circle.svg" class="mr-2" />Возникла проблема</button></div>
                    </div>
                </div>
                <div class="col-4">
                    <?php include './_cut_right.php'; ?>
                </div>
            </div>
        </div>
        <?php if(null !== filter_input(INPUT_GET, 'stream_id')): ?>
        <div class="print_only">
            <?php
            $stream_id = filter_input(INPUT_GET, 'stream_id');
            $stream_name = '';
            $stream_weight = '';
            $stream_length = '';
            $stream_printed = '';
            $dt_printed = '';
            
            $film1 = '';
            $film2 = '';
            $film3 = '';
            
            $density1 = 0;
            $density2 = 0;
            $density3 = 0;
    
            $sql = "select cs.name, cs.weight, cs.length, cs.printed, "
                    . "c.individual_film_name, f1.name film1, c.lamination1_individual_film_name, f2.name film2, c.lamination2_individual_film_name, f3.name film3, "
                    . "c.individual_density, fv1.weight density1, c.lamination1_individual_density, fv2.weight density2, c.lamination2_individual_density, fv3.weight density3 "
                    . "from calculation_stream cs "
                    . "inner join calculation c on cs.calculation_id = c.id "
                    . "left join film_variation fv1 on c.film_variation_id = fv1.id "
                    . "left join film_variation fv2 on c.lamination1_film_variation_id = fv2.id "
                    . "left join film_variation fv3 on c.lamination2_film_variation_id = fv3.id "
                    . "left join film f1 on fv1.film_id = f1.id "
                    . "left join film f2 on fv2.film_id = f2.id "
                    . "left join film f3 on fv3.film_id = f3.id "
                    . "where cs.id = $stream_id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $stream_name = $row['name'];
                $stream_weight = $row['weight'];
                $stream_length = $row['length'];
                $stream_printed = $row['printed'];
                $dt_printed = DateTime::createFromFormat('Y-m-d H:i:s', $stream_printed);
                
                $film1 = $row['individual_film_name'];
                if(empty($film1)) {
                    $film1 = $row['film1'];
                }
                
                $film2 = $row['lamination1_individual_film_name'];
                if(empty($film2)) {
                    $film2 = $row['film2'];
                }
                
                $film3 = $row['lamination2_individual_film_name'];
                if(empty($film3)) {
                    $film3 = $row['film3'];
                }
                
                $density1 = $row['individual_density'];
                if(empty($density1)) {
                    $density1 = $row['density1'];
                }
                if(empty($density1)) {
                    $density1 = 0;
                }
                    
                $density2 = $row['lamination1_individual_density'];
                if(empty($density2)) {
                    $density2 = $row['density2'];
                }
                if(empty($density2)) {
                    $density2 = 0;
                }
                    
                $density3 = $row['lamination2_individual_density'];
                if(empty($density3)) {
                    $density3 = $row['density3'];
                }
                if(empty($density3)) {
                    $density3 = 0;
                }
            }
            
            $stream_date = $dt_printed->format('d-m-Y');
            $stream_hour = $dt_printed->format('G');
            $stream_shift = 'day';
            if($stream_hour < 8 || $stream_hour > 19) {
                $stream_shift = 'night';
            }
            
            $sql = "select pe.last_name, pe.first_name "
                    . "from plan_workshift1 pw inner join plan_employee pe on pw.employee1_id = pe.id "
                    . "where date_format(pw.date, '%d-%m-%Y') = '$stream_date' and pw.shift = '$stream_shift' and pw.work_id = ".WORK_CUTTING." and pw.machine_id = ". filter_input(INPUT_GET, 'machine_id');
            $stream_cutter = '';

            $fetcher = new Fetcher($sql);

            while($row = $fetcher->Fetch()) {
                $stream_cutter .= $row['last_name'].(mb_strlen($row['first_name']) == 0 ? '' : ' '.mb_substr($row['first_name'], 0, 1).'.');
            }

            if(empty($stream_cutter)) {
                $stream_cutter = "ВЫХОДНОЙ ДЕНЬ";
            }
            ?>
            <div class="pagebreak"><?php include './_print.php'; ?></div>
            <div><?php include './_print.php'; ?></div>
        </div>
        <?php endif; ?>
        <?php
        include '../include/footer.php';
        include '../include/footer_cut.php';
        ?>
        <script>
            function DragStart(ev) {
                ev.dataTransfer.setData('source_id', $(ev.target).attr('data-id'));
            }
            
            function DragEnd() {
                $('.calculation_stream.target').removeClass('target');
                $('#calculation_streams_bottom').removeClass('target');
            }
            
            function DragOver(ev) {
                ev.preventDefault();
                if($(ev.target).parents('.calculation_stream').length > 0) { 
                    calculation_stream = $(ev.target).parents('.calculation_stream')[0];
                    $('.calculation_stream.target').removeClass('target');
                    $(calculation_stream).addClass('target');
                }
            }
            
            function DragOverBottom(ev) {
                ev.preventDefault();
                $('.calculation_stream.target').removeClass('target');
                $('#calculation_streams_bottom').addClass('target');
            }
            
            function Drop(ev) {
                ev.preventDefault();
                source_id = ev.dataTransfer.getData('source_id');
                target_id = $(ev.target).closest('.calculation_stream').attr('data-id');
                
                if(!isNaN(source_id) && !isNaN(target_id) && source_id != target_id) {
                    $.ajax({ dataType: 'JSON', url: "_drag_streams.php?source_id=" + source_id + "&target_id=" + target_id })
                            .done(function(data) {
                                if(data.error == '') {
                                    $('#calculation_streams').load('_calculation_streams.php?calculation_id=<?=$id ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>');
                                }
                                else {
                                    alert(data.error);
                                }
                            })
                            .fail(function() {
                                alert('Ошибка при вызове исполняющей программы');
                            });
                }
            }
            
            function DropBottom(ev) {
                ev.preventDefault();
                source_id = ev.dataTransfer.getData('source_id');
                
                if(!isNaN(source_id)) {
                    $.ajax({ dataType: 'JSON', url: "_drag_to_bottom.php?source_id=" + source_id })
                            .done(function(data) {
                                if(data.error == '') {
                                    $('#calculation_streams').load('_calculation_streams.php?calculation_id=<?=$id ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>');
                                    $('#calculation_streams_bottom').removeClass('target');
                                }
                                else {
                                    alert(data.error);
                                    $('#calculation_streams_bottom').removeClass('target');
                                }
                            })
                            .fail(function() {
                                alert('Ошибка при вызове исполняющей программы');
                                $('#calculation_streams_bottom').removeClass('target');
                            });
                    
                }
            }
            
            <?php if(null !== filter_input(INPUT_GET, 'stream_id')): ?>
            var css = '@page { size: portrait; margin: 2mm; }',
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
            <?php endif; ?>
        </script>
    </body>
</html>