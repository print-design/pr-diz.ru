<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';
include '../calculation/calculation_result.php';

// Авторизация
if(!IsInRole(CUTTER_USERS) && !IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id или машина, направляем к списку заданий
$id = filter_input(INPUT_GET, 'id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
if(empty($id) || empty($machine_id)) {
    header('Location: '.APPLICATION.'/cut/');
}

// Создание нового съёма
$error_message = '';

if(null !== filter_input(INPUT_POST, 'new_take_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    $sql = "insert into calculation_take (calculation_id) values ($id)";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        header("Location: take.php?id=$id&machine_id=$machine_id");
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
        header("Location: finished.php?id=$id&machine_id=$machine_id");
    }
}

// Снятие с резки
if(null !== filter_input(INPUT_POST, 'cut_remove_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $cut_remove_cause = addslashes(filter_input(INPUT_POST, 'cut_remove_cause'));
    
    $sql = "update calculation set cut_remove_cause = '$cut_remove_cause' where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation set status_id = ".ORDER_STATUS_CUT_REMOVED." where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Получение объекта
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);

$length_cut = null;

$sql = "select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id)";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $length_cut = $row['0'];
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
                    font-weight: bold;
                    white-space: nowrap;
                    padding-right: 30px;
                    vertical-align: top;
                }
            
                td {
                    line-height: 22px;
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
                            <div style="background-color: lightgray; padding-left: 10px; padding-right: 15px; padding-top: 2px; border-radius: 10px; margin-top: 15px; margin-bottom: 15px; display: inline-block;">
                                <i class="fas fa-circle" style="font-size: x-small; vertical-align: bottom; padding-bottom: 7px; color: <?=ORDER_STATUS_COLORS[$calculation->status_id] ?>;">&nbsp;&nbsp;</i><?=ORDER_STATUS_NAMES[$calculation->status_id] ?>
                                <?php
                                if(in_array($calculation->status_id, ORDER_STATUSES_WITH_METERS)) {
                                    echo ' '.DisplayNumber(floatval($length_cut), 0)." м из ".DisplayNumber(floatval(is_a($calculation, CalculationSelfAdhesive::class) ? $calculation->length_pure : $calculation->length_pure_1), 0);
                                }
                                
                                if($calculation->status_id == ORDER_STATUS_CUT_REMOVED) {
                                    echo " ".$cut_remove_cause;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start mb-4 mt-4">
                        <div>
                            <form method="post">
                                <input type="hidden" name="id" value="<?=$id ?>" />
                                <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                                <button type="submit" name="new_take_submit" class="btn btn-dark pl-4 pr-4 mr-4"><i class="fas fa-plus mr-2"></i>Начать новый съём</button>
                            </form>
                        </div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4" data-toggle="modal" data-target="#add_not_take_stream"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                        <div>
                            <form method="post">
                                <input type="hidden" name="id" value="<?=$id ?>" />
                                <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                                <button type="submit" name="finished_submit" class="btn btn-light pl-4 pr-4 mr-4"><i class="fas fa-check mr-2"></i>Тираж выполнен</button>
                            </form>
                        </div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4" data-toggle="modal" data-target="#cut_remove"><img src="../images/icons/error_circle.svg" class="mr-2" />Возникла проблема</button></div>
                    </div>
                    <?php include './_table.php'; ?>
                </div>
                <div class="col-4">
                    <?php include './_cut_right.php'; ?>
                </div>
            </div>
        </div>
        <?php if(null !== filter_input(INPUT_GET, 'take_stream_id')): ?>
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
            <?php if(null !== filter_input(INPUT_GET, 'take_stream_id')): ?>
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