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

// Начало резки
if(null !== filter_input(INPUT_POST, 'ready_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    $length = filter_input(INPUT_POST, 'length');
    
    $sql = "update calculation set status_id = ".ORDER_STATUS_CUTTING.", cut_priladka = $length where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "select count(id) from calculation_take where calculation_id = $id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            if($row[0] == 0) {
                $sql = "insert into calculation_take (calculation_id) values ($id)";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
    }
    
    if(empty($error_message)) {
        header('Location: take.php?id='.$id.(empty($machine_id) ? '' : '&machine_id='.$machine_id));
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
                <div class="col-4">
                    <h1><?=$calculation->name ?></h1>
                    <div class="name"><?=$calculation->customer ?></div>
                    <div class="subtitle">№<?=$calculation->customer_id.'-'.$calculation->num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></div>
                    <div style="background-color: lightgray; padding-left: 10px; padding-right: 15px; padding-top: 2px; border-radius: 10px; margin-top: 20px; margin-bottom: 20px; display: inline-block;">
                        <i class="fas fa-circle" style="font-size: x-small; vertical-align: bottom; padding-bottom: 7px; color: <?=ORDER_STATUS_COLORS[$calculation->status_id] ?>;">&nbsp;&nbsp;</i><?=ORDER_STATUS_NAMES[$calculation->status_id] ?>
                        <?php
                        if(in_array($calculation->status_id, ORDER_STATUSES_WITH_METERS)) {
                            echo ' '.DisplayNumber(floatval($length_cut), 0)." м из ".DisplayNumber(floatval($calculation_result->length_pure_1), 0);
                        }
                                
                        if($calculation->status_id == ORDER_STATUS_CUT_REMOVED) {
                            echo " ".$calculation->cut_remove_cause;
                        }
                        ?>
                    </div>
                    <div class="name">Приладка</div>
                    <form method="post">
                        <input type="hidden" name="id" value="<?=$id ?>" />
                        <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                        <div class="input-group">
                            <input type="text" class="form-control int-only" name="length" placeholder="Метраж приладки" required="required" autocomplete="off" />
                            <div class="input-group-append">
                                <span class="input-group-text">м</span>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-6">
                                <button type="submit" class="btn btn-dark w-100" name="ready_submit"><i class="fas fa-check"></i>&nbsp;&nbsp;&nbsp;Приладка выполнена</button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-light w-100" data-toggle="modal" data-target="#cut_remove"><img src="../images/icons/error_circle.svg" />&nbsp;&nbsp;&nbsp;Возникла проблема</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-4"></div>
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