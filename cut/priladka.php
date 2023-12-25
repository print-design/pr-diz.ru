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

// Получение объекта
$date = '';
$name = '';
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

$sql = "select c.date, c.name, c.status_id, c.customer_id, cus.name customer, "
        . "tm.date techmap_date, tm.side, tm.winding, tm.winding_unit, tm.spool, tm.labels, tm.package, "
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
            <div class="row">
                <div class="col-4">
                    <h1><?=$name ?></h1>
                    <div class="name"><?=$customer ?></div>
                    <div class="subtitle">№<?=$customer_id.'-'.$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></div>
                    <div style="background-color: lightgray; padding-left: 10px; padding-right: 15px; padding-top: 2px; border-radius: 10px; display: inline-block;">
                        <i class="fas fa-circle" style="font-size: x-small; vertical-align: bottom; padding-bottom: 7px; color: <?=ORDER_STATUS_COLORS[$status_id] ?>;">&nbsp;&nbsp;</i><?=ORDER_STATUS_NAMES[$status_id] ?>
                    </div>
                    <div class="name">Приладка</div>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <input type="hidden" name="machine_id" value="<?= filter_input(INPUT_GET, 'machine_id') ?>" />
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
                                <button type="button" class="btn btn-light w-100"><img src="../images/icons/error_circle.svg" />&nbsp;&nbsp;&nbsp;Возникла проблема</button>
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
    </body>
</html>