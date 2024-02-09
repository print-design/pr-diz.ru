<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';
include '../calculation/calculation_result.php';

// Авторизация
if(!IsInRole(CUTTER_USERS) && !IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id или не указана машина, направляем к списку заданий
$id = filter_input(INPUT_GET, 'id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
if(empty($id) || empty($machine_id)) {
    header('Location: '.APPLICATION.'/cut/');
}

// Обработка формы распечатки ручья
$error_message = '';
$invalid_stream = 0;

if(null !== filter_input(INPUT_POST, 'stream_print_submit')) {
    $take_id = filter_input(INPUT_POST, 'take_id');
    $calculation_id = filter_input(INPUT_POST, 'calculation_id');
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
    
    $is_valid = true;
    $validation1 = true;
    $validation2 = true;
    
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
        if(0.85 * (0.1524 * $radius * $radius + 23.1245 * $radius - 228.5017) * (20 / ($density1 + $density2 + $density3)) < $length && $length < 1.15 * (0.1524 * $radius * $radius + 23.1245 * $radius - 228.5017) * (20 / ($density1 + $density2 + $density3))) {
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
        $take_stream_id = 0;
        $sql = "select id from calculation_take_stream where calculation_take_id = $take_id and calculation_stream_id = $stream_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $take_stream_id = $row['id'];
        }
        
        if(empty($take_stream_id)) {
            $sql = "insert into calculation_take_stream (calculation_take_id, calculation_stream_id, weight, length, printed) values($take_id, $stream_id, $weight, $length, now())";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        else {
            $sql = "update calculation_take_stream set weight = $weight, length = $length, printed = now() where id = $take_stream_id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message)) {
            $sql = "update calculation set status_id = ".ORDER_STATUS_CUTTING." where id = $id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message)) {
            header("Location: take.php?id=$calculation_id&machine_id=$machine_id&stream_id=$stream_id");
        }
    }
}

// Завершение резки
if(null !== filter_input(INPUT_POST, 'finished_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    $sql = "update calculation set status_id = ".ORDER_STATUS_CUT_FINISHED." where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        header("Location: finished.php?id=$id&machine_id=$machine_id#");
    }
}

// Снятие с резки
if(null !== filter_input(INPUT_POST, 'cut_remove_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    $cut_remove_cause = addslashes(filter_input(INPUT_POST, 'cut_remove_cause'));
    
    $sql = "update calculation set cut_remove_cause = '$cut_remove_cause' where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation set status_id = ".ORDER_STATUS_CUT_REMOVED." where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        header('Location: '.APPLICATION.'/cut/?machine_id='.$machine_id);
    }
}

// Получение объекта
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);

$take_id = null;
$take_number = null;
$printed_streams_count = null;
$comment = '';

$sql = "select (select max(id) from calculation_take where calculation_id = c.id) take_id, "
        . "(select count(id) from calculation_take where calculation_id = c.id) take_number, "
        . "(select count(id) from calculation_take_stream where calculation_take_id = (select max(id) from calculation_take where calculation_id = c.id)) printed_streams_count, "
        . "(select comment from plan_edition where work_id = ".WORK_CUTTING." and calculation_id = c.id limit 1) comment, "
        . "(select comment from plan_continuation where plan_edition_id in (select id from plan_edition where work_id = ".WORK_CUTTING." and calculation_id = c.id) limit 1) continuation_comment "
        . "from calculation c where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $take_id = $row['take_id'];
    $take_number = $row['take_number'];
    $printed_streams_count = $row['printed_streams_count'];
    $comment = trim($row['comment'].' '.$row['continuation_comment'], ' ');
}

// Если у данной работы ещё не было сделано ни одного съёма, перенаправляем на страницу начала работы
if(empty($take_id)) {
    header('Location: details.php?id='.$id.(empty($machine_id) ? '' : '&machine_id='.$machine_id));
}

// Ошибки при расчётах (если есть)
if(null !== filter_input(INPUT_GET, 'error_message')) {
    $error_message = filter_input(INPUT_GET, 'error_message');
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
                    font-weight: bold!important;
                    white-space: nowrap;
                    padding-right: 30px;
                    vertical-align: top;
                }
                            
                td {
                    line-height: 22px;
                }
                
                table.fotometka {
                    border-collapse: separate;
                }
                
                table.fotometka tr td, table.fotometka tr td:nth-child(2) {
                    text-align: left;
                    vertical-align: top;
                    border-bottom: 0;
                    padding: 0;
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
                    margin-bottom: 20px;
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
            
            .modal-content {
                border-radius: 20px;
            }
            
            .modal-header {
                border-bottom: 0;
                padding-bottom: 0;
            }
            
            .modal-footer {
                border-top: 0;
                padding-top: 0;
            }
        </style>
    </head>
    <body>
        <div class="no_print">
        <?php
        include '../include/header_cut.php';
        
        include './_cut_remove.php';
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
                            <h1><?=$calculation->name ?></h1>
                            <div class="name"><?=$calculation->customer ?></div>
                            <div class="subtitle">№<?=$calculation->customer_id.'-'.$calculation->num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6"><?php include '../include/order_status_details.php'; ?></div>
                        <div class="col-6">
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
                            
                            if($calculation_result->photolabel != CalculationResult::PHOTOLABEL_NOT_FOUND):
                            ?>
                            <table class="fotometka">
                                <tr>
                                    <td class="fotometka<?= $calculation_result->roll_type == 1 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_1_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 1): ?><br /><img src="../images/icons/check.svg" class="ml-2" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 2 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_2_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 2): ?><br /><img src="../images/icons/check.svg" class="ml-2" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 3 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_3_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 3): ?><br /><img src="../images/icons/check.svg" class="ml-2" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 4 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_4_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 4): ?><br /><img src="../images/icons/check.svg" class="ml-2" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 5 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_5_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 5): ?><br /><img src="../images/icons/check.svg" class="ml-2" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 6 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_6_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 6): ?><br /><img src="../images/icons/check.svg" class="ml-2" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 7 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_7_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 7): ?><br /><img src="../images/icons/check.svg" class="ml-2" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 8 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_8_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 8): ?><br /><img src="../images/icons/check.svg" class="ml-2" /><?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            <?php
                            endif;
                        
                            if(!empty($comment)) {
                                echo "<p>Комментарий: <strong>$comment</strong></p>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="name">Съём <?=$take_number ?></div>
                    <div id="calculation_streams">
                        <?php include './_calculation_streams.php'; ?>
                    </div>
                    <div class="d-flex justify-content-xl-start mb-4" id="calculation_streams_bottom" data-id="0" ondragover="DragOverBottom(event);" ondrop="DropBottom(event);">
                        <?php
                        $finish_submit_disabled_class = ' disabled';
                        
                        if($printed_streams_count >= $calculation->streams_number) {
                            $finish_submit_disabled_class = '';
                        }
                        ?>
                        <div><a href="taken.php?id=<?=$id ?>&machine_id=<?= $machine_id ?>" class="btn btn-dark pl-4 pr-4 mr-4<?=$finish_submit_disabled_class ?>"><i class="fas fa-check mr-2"></i>Съём закончен</a></div>
                        <?php
                        $add_not_take_stream_class = ' disabled';
                        
                        if($take_number > 1 || $printed_streams_count >= $calculation->streams_number) {
                            $add_not_take_stream_class = '';
                        }
                        ?>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4<?=$add_not_take_stream_class ?>" data-toggle="modal" data-target="#add_not_take_stream"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                        <?php
                        $finished_class = ' disabled';
                        
                        if($take_number > 1 || $printed_streams_count >= $calculation->streams_number) {
                            $finished_class = '';
                        }
                        ?>
                        <div>
                            <form method="post">
                                <input type="hidden" name="id" value="<?=$id ?>" />
                                <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                                <button type="submit" name="finished_submit" class="btn btn-light pl-4 pr-4 mr-4<?=$finished_class ?>"><i class="fas fa-check mr-2"></i>Тираж выполнен</button>
                            </form>
                        </div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4" data-toggle="modal" data-target="#cut_remove"><img src="../images/icons/error_circle.svg" class="mr-2" />Возникла проблема</button></div>
                    </div>
                    <?php include './_table.php'; ?>
                </div>
                <div class="col-4">
                    <?php include './_cut_right.php'; ?>
                </div>
            </div>
        </div>
        <?php if(null !== filter_input(INPUT_GET, 'stream_id') || null !== filter_input(INPUT_GET, 'take_stream_id') || null != filter_input(INPUT_GET, 'not_take_stream_id')): ?>
        <div class="print_only">
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
                                    $('#calculation_streams').load('_calculation_streams.php?take_id=<?=$take_id ?>&machine_id=<?=$machine_id ?>');
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
                                    $('#calculation_streams').load('_calculation_streams.php?take_id=<?=$take_id ?>&machine_id=<?=$machine_id ?>');
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
            
            <?php if(null !== filter_input(INPUT_GET, 'stream_id') || null !== filter_input(INPUT_GET, 'take_stream_id') || null !== filter_input(INPUT_GET, 'not_take_stream_id')): ?>
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