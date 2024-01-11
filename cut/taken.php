<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';

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

// Расчёт
$calculation = CalculationBase::Create($id);

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
$date = '';
$name = '';
$status_id = '';
$cut_remove_cause = '';
$customer_id = '';
$customer = '';

$techmap_date = '';
$side = '';
$winding = '';
$winding_unit = '';
$spool = '';
$labels = '';
$package = '';

$length_cut = '';
$num_for_customer = '';

$sql = "select c.date, c.name, c.status_id, c.cut_remove_cause, c.customer_id, cus.name customer, "
        . "tm.date techmap_date, tm.side, tm.winding, tm.winding_unit, tm.spool, tm.labels, tm.package, "
        . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cut, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
        . "from calculation c "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join techmap tm on tm.calculation_id = c.id "
        . "where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $name = $row['name'];
    $status_id = $row['status_id'];
    $cut_remove_cause = $row['cut_remove_cause'];
    $customer_id = $row['customer_id'];
    $customer = $row['customer'];

    $techmap_date = $row['techmap_date'];
    $side = $row['side'];
    $winding = $row['winding'];
    $winding_unit = $row['winding_unit'];
    $spool = $row['spool'];
    $labels = $row['labels'];
    $package = $row['package'];

    $length_cut = $row['length_cut'];
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
        <?php
        include '../include/header_cut.php';
        
        include './_cut_remove.php';
        ?>
        <div class="container-fluid">
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
                            <div style="background-color: lightgray; padding-left: 10px; padding-right: 15px; padding-top: 2px; border-radius: 10px; margin-top: 15px; margin-bottom: 15px; display: inline-block;">
                                <i class="fas fa-circle" style="font-size: x-small; vertical-align: bottom; padding-bottom: 7px; color: <?=ORDER_STATUS_COLORS[$status_id] ?>;">&nbsp;&nbsp;</i><?=ORDER_STATUS_NAMES[$status_id] ?>
                                <?php
                                if(in_array($status_id, ORDER_STATUSES_WITH_METERS)) {
                                    echo ' '.DisplayNumber(floatval($length_cut), 0)." м из ".DisplayNumber(floatval($calculation->length_pure_1), 0);
                                }
                                
                                if($status_id == ORDER_STATUS_CUT_REMOVED) {
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
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                        <div>
                            <form method="post">
                                <input type="hidden" name="id" value="<?=$id ?>" />
                                <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                                <button type="submit" name="finished_submit" class="btn btn-light pl-4 pr-4 mr-4"><i class="fas fa-check mr-2"></i>Тираж выполнен</button>
                            </form>
                        </div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4" data-toggle="modal" data-target="#cut_remove"><img src="../images/icons/error_circle.svg" class="mr-2" />Возникла проблема</button></div>
                    </div>
                </div>
                <div class="col-4">
                    <?php include './_cut_right.php'; ?>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_cut.php';
        ?>
        <script>
            $('#cut_remove').on('shown.bs.modal', function() {
                $('input:text:visible:first').focus();
            });
            
            $('#cut_remove').on('hidden.bs.modal', function() {
                $('input#cut_remove_cause').val('');
            });
        </script>
    </body>
</html>