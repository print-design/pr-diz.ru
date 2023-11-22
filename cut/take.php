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

// Получение объекта
$date = '';
$name = '';
$status_id = '';
$customer_id = '';
$customer = '';
$num_for_customer = '';

$sql = "select c.date, c.name, c.status_id, c.customer_id, cus.name customer, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
        . "from calculation c "
        . "inner join customer cus on c.customer_id = cus.id "
        . "where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $name = $row['name'];
    $status_id = $row['status_id'];
    $customer_id = $row['customer_id'];
    $customer = $row['customer'];
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
                <div class="col-8">
                    <div class="row">
                        <div class="col-6">
                            <h1><?=$name ?></h1>
                            <div class="name"><?=$customer ?></div>
                            <div class="subtitle mb-4">№<?=$customer_id.'-'.$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></div>
                            <div id="status" style="border: solid 2px <?=ORDER_STATUS_COLORS[$status_id] ?>; color: <?=ORDER_STATUS_COLORS[$status_id] ?>;">
                                <i class="<?=ORDER_STATUS_ICONS[$status_id] ?>"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=ORDER_STATUS_NAMES[$status_id] ?>
                            </div>
                        </div>
                    </div>
                    <div class="name">Съём 1</div>
                    <?php
                    $sql = "select id, name from calculation_stream where calculation_id = $id order by position";
                    $fetcher = new Fetcher($sql);
                    while($row = $fetcher->Fetch()):
                    ?>
                    <div class="calculation_stream">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex justify-content-sm-start">
                                <div class="mr-3" draggable="true">
                                    <img src="../images/icons/double-vertical-dots.svg" />
                                </div>
                                <div class="font-weight-bold"><?=$row['name'] ?></div>
                            </div>
                            <div><span style="font-size: x-small; vertical-align: middle;">&#9679;</span>&nbsp;&nbsp;&nbsp;Распечатано</div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="weight">Масса катушки</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control int-only" name="weight" value="22" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">кг</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="length">Метраж</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control int-only" name="length" value="80" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">м</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="diameter">Диаметр от вала</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control int-only" name="diameter" value="120" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">мм</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="print_label">&nbsp;</label>
                                    <button type="button" class="btn btn-light w-100" name="print_label"><img src="../images/icons/print.svg" class="mr-2" />Распечатать бирку</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="col-4">
                    <?php include './_cut_right.php'; ?>
                </div>
            </div>
        </div>
    </body>
</html>