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
            
            .calculation_stream {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .target {
                border-top: solid 3px gray;
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
                    <div id="calculation_streams">
                        <?php include './_calculation_streams.php'; ?>
                    </div>
                    <div class="d-flex justify-content-xl-start">
                        <div><button type="button" class="btn btn-dark pl-4 pr-4 mr-4"><i class="fas fa-check mr-2"></i>Съём закончен</button></div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4"><img src="../images/icons/error_circle.svg" class="mr-2" />Возникла проблема</button></div>
                    </div>
                    <div class="calculation_stream mt-5">
                        <div class="name">Готовые съёмы</div>
                        <div class="subtitle">Общий метраж съёмов: 0 м</div>
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
            function DragStart(ev) {
                ev.dataTransfer.setData('source_id', $(ev.target).attr('data-id'));
            }
            
            function DragEnd() {
                $('.calculation_stream.target').removeClass('target');
            }
            
            function DragOver(ev) {
                ev.preventDefault();
                if($(ev.target).parents('.calculation_stream').length > 0) { 
                    calculation_stream = $(ev.target).parents('.calculation_stream')[0];
                    $('.calculation_stream.target').removeClass('target');
                    $(calculation_stream).addClass('target');
                }
            }
            
            function Drop(ev) {
                ev.preventDefault();
                source_id = ev.dataTransfer.getData('source_id');
                target_id = $(ev.target).closest('.calculation_stream').attr('data-id');
                
                if(!isNaN(source_id) && !isNaN(target_id) && source_id != target_id) {
                    $.ajax({ dataType: 'JSON', url: "_drag_streams.php?source_id=" + source_id + "&target_id=" + target_id })
                            .done(function(data) {
                                if(data.error == '') {
                                    $('#calculation_streams').load('_calculation_streams.php?calculation_id=<?=$id ?>');
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
        </script>
    </body>
</html>