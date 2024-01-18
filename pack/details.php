<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id, направляем к списку заданий
$id = filter_input(INPUT_GET, 'id');
if($id === null) {
    header('Location: '.APPLICATION.'/pack/');
}

// Расчёт
$calculation = CalculationBase::Create($id);

if(null !== filter_input(INPUT_POST, 'confirm_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $status_id = filter_input(INPUT_POST, 'status_id');
    
    $sql = "update calculation set status_id = $status_id where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        header("Location: details.php?id=$id&waiting=1");
    }
}

// Получение объекта
$date = '';
$name = '';
$unit = '';
$work_type_id = '';
$status_id = '';
$cut_remove_cause = '';

$customer_id = '';
$customer = '';
$length_pure_1 = '';
$last_name = '';
$first_name = '';

$techmap_date = '';
$length_cut = '';
$num_for_customer = '';

$sql = "select c.id, c.date, c.name, c.unit, c.work_type_id, c.status_id, c.cut_remove_cause, c.customer_id, "
        . "cus.name customer, cr.length_pure_1, u.last_name, u.first_name, tm.date techmap_date, "
        . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cut, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
        . "from calculation c "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "inner join techmap tm on tm.calculation_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join user u on c.manager_id = u.id "
        . "where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $name = $row['name'];
    $unit = $row['unit'];
    $work_type_id = $row['work_type_id'];
    $status_id = $row['status_id'];
    $cut_remove_cause = $row['cut_remove_cause'];
    
    $customer_id = $row['customer_id'];
    $customer = $row['customer'];
    $length_pure_1 = $row['length_pure_1'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    
    $techmap_date = $row['techmap_date'];
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
        include '../include/header_pack.php';
        ?>
        </div>
        <div class="container-fluid no_print">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-4">
                    <a class="btn btn-light backlink" href="<?=APPLICATION ?>/pack/<?php if(!empty($status_id) && $status_id != ORDER_STATUS_PACK_READY) echo "?status_id=$status_id"; ?>">К списку</a>
                    <h1><?=$row['name'] ?></h1>
                    <div class="name"><?=$row['customer'] ?></div>
                    <div class="subtitle">№<?=$customer_id.'-'.$num_for_customer ?> от  <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></div>
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
                    <table>
                        <tr>
                            <td>Объём заказа</td>
                            <td><?= DisplayNumber(intval($calculation->quantity), 0) ?> <?=$unit == 'kg' ? 'кг' : 'шт' ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($length_pure_1), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Менеджер</td>
                            <td><?=$last_name.' '.$first_name ?></td>
                        </tr>
                        <tr>
                            <td>Тип работы</td>
                            <td><?=WORK_TYPE_NAMES[$work_type_id] ?></td>
                        </tr>
                        <tr>
                            <td>Карта составлена</td>
                            <td><?= DateTime::createFromFormat('Y-m-d H:i:s', $techmap_date)->format('d.m.Y H:i') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-8">
                    <?php
                    $machine_id = null;
                    $sql = "select machine_id from plan_edition where calculation_id = $id and work_id = ".WORK_CUTTING;
                    $fetcher = new Fetcher($sql);
                    if($row = $fetcher->Fetch()) {
                        $machine_id = $row[0];
                    }
                    include '../cut/_table.php';
                    ?>
                    <div class="d-flex justify-content-xl-start mt-4">
                        <?php if($status_id == ORDER_STATUS_PACK_READY): ?>
                        <div>
                            <form method="post">
                                <input type="hidden" name="id" value="<?=$id ?>" />
                                <input type="hidden" name="status_id" value="<?=ORDER_STATUS_SHIP_READY ?>" />
                                <button type="submit" name="confirm_submit" class="btn btn-dark pl-4 pr-4 mr-4"><i class="fas fa-check mr-2"></i>Подтвердить</button>
                            </form>
                        </div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                        <?php elseif($status_id == ORDER_STATUS_SHIP_READY && null == filter_input(INPUT_GET, 'waiting')): ?>
                        <div>
                            <form method="post">
                                <input type="hidden" name="id" value="<?=$id ?>" />
                                <input type="hidden" name="status_id" value="<?=ORDER_STATUS_SHIPPED ?>" />
                                <button type="submit" name="confirm_submit" class="btn btn-dark pl-4 pr-4 mr-4"><i class="fas fa-check mr-2"></i>Отгружено</button>
                            </form>
                        </div>
                        <?php endif; ?>
                        <div><button type="button" class="btn btn-light pl-4 pr-4"><i class="fas fa-download mr-2"></i>Выгрузка</button></div>
                    </div>
                </div>
            </div>
        </div>
        <?php if(null !== filter_input(INPUT_GET, 'take_stream_id')): ?>
        <div class="print_only">
            <div class="pagebreak"><?php include '../cut/_print.php'; ?></div>
            <div><?php include '../cut/_print.php'; ?></div>
        </div>
        <?php endif; ?>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            function ShowTakeTable(id) {
                $('a.show_table[data-id=' + id + ']').addClass('d-none');
                $('a.hide_table[data-id=' + id + ']').removeClass('d-none');
                $('table.take_table[data-id=' + id + ']').removeClass('d-none');
            }
            
            function HideTakeTable(id) {
                $('a.hide_table[data-id=' + id + ']').addClass('d-none');
                $('a.show_table[data-id=' + id + ']').removeClass('d-none');
                $('table.take_table[data-id=' + id + ']').addClass('d-none');
            }
            
            $('#edit_take_stream').on('shown.bs.modal', function() {
                $('input#take_stream_weight').focus();
            });
            
            $('#edit_take_stream').on('hidden.bs.modal', function() {
                $('input#take_stream_weight').val('');
            });
                
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