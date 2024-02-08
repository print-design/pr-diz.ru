<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';
include '../calculation/calculation_result.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id, направляем к списку заданий
$id = filter_input(INPUT_GET, 'id');
if($id === null) {
    header('Location: '.APPLICATION.'/pack/');
}

// Смена статуса
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
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);

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
                
                table.fotometka {
                    border-collapse: separate;
                }
                
                table.fotometka tr td, table.fotometka tr td:nth-child(2) {
                    text-align: left;
                    vertical-align: top;
                    border-bottom: 0;
                    padding: 0;
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
                <div class="col-8">
                    <div class="row">
                        <div class="col-6">
                            <a class="btn btn-light backlink" href="<?=APPLICATION ?>/pack/<?php if(!empty($calculation->status_id) && $calculation->status_id != ORDER_STATUS_PACK_READY) echo "?status_id=".$calculation->status_id; ?>">К списку</a>
                            <h1><?=$calculation->name ?></h1>
                            <div class="name"><?=$calculation->customer ?></div>
                            <div class="subtitle">№<?=$calculation->customer_id.'-'.$calculation->num_for_customer ?> от  <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <?php include '../include/order_status_details.php'; ?>
                            <table>
                                <tr>
                                    <td>Объём заказа</td>
                                    <td><?= DisplayNumber(intval($calculation->quantity), 0) ?> <?=$calculation->unit == 'kg' ? 'кг' : 'шт' ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? $calculation->length_pure : $calculation->length_pure_1), 0) ?> м</td>
                                </tr>
                                <tr>
                                    <td>Менеджер</td>
                                    <td><?=$calculation->last_name.' '.$calculation->first_name ?></td>
                                </tr>
                                <tr>
                                    <td>Тип работы</td>
                                    <td><?=WORK_TYPE_NAMES[$calculation->work_type_id ] ?></td>
                                </tr>
                                <tr>
                                    <td>Карта составлена</td>
                                    <td><?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation_result->techmap_date)->format('d.m.Y H:i') ?></td>
                                </tr>
                            </table>
                        </div>
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
                            ?>
                            <table class="fotometka">
                                <tr>
                                    <td class="fotometka<?= $calculation_result->roll_type == 1 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_1_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 1): ?><br /><img src="../images/icons/check.svg" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 2 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_2_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 2): ?><br /><img src="../images/icons/check.svg" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 3 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_3_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 3): ?><br /><img src="../images/icons/check.svg" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 4 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_4_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 4): ?><br /><img src="../images/icons/check.svg" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 5 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_5_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 5): ?><br /><img src="../images/icons/check.svg" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 6 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_6_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 6): ?><br /><img src="../images/icons/check.svg" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 7 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_7_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 7): ?><br /><img src="../images/icons/check.svg" /><?php endif; ?>
                                    </td>
                                    <td class="fotometka<?= $calculation_result->roll_type == 8 ? " fotochecked" : "" ?>">
                                        <img src="../images/<?=$roll_folder ?>/roll_type_8_black.svg<?='?'. time() ?>" />
                                        <?php if($calculation_result->roll_type == 8): ?><br /><img src="../images/icons/check.svg" /><?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
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
                        <?php if($calculation->status_id == ORDER_STATUS_PACK_READY): ?>
                        <div>
                            <form method="post">
                                <input type="hidden" name="id" value="<?=$id ?>" />
                                <input type="hidden" name="status_id" value="<?=ORDER_STATUS_SHIP_READY ?>" />
                                <button type="submit" name="confirm_submit" class="btn btn-dark pl-4 pr-4 mr-4"><i class="fas fa-check mr-2"></i>Подтвердить</button>
                            </form>
                        </div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4" data-toggle="modal" data-target="#add_not_take_stream"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                        <?php elseif($calculation->status_id == ORDER_STATUS_SHIP_READY && null == filter_input(INPUT_GET, 'waiting')): ?>
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
                <div class="col-4">
                    <?php include '../cut/_cut_right.php'; ?>
                </div>
            </div>
        </div>
        <?php if(null !== filter_input(INPUT_GET, 'take_stream_id') || null != filter_input(INPUT_GET, 'not_take_stream_id')): ?>
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
            
            function ShowNotTakeTable() {
                $('a.show_not_take_table').addClass('d-none');
                $('a.hide_not_take_table').removeClass('d-none');
                $('table.not_take_table').removeClass('d-none');
            }
            
            function HideNotTakeTable() {
                $('a.hide_not_take_table').addClass('d-none');
                $('a.show_not_take_table').removeClass('d-none');
                $('table.not_take_table').addClass('d-none');
            }
            
            $('#edit_take_stream').on('shown.bs.modal', function() {
                $('input#take_stream_weight').focus();
            });
            
            $('#edit_take_stream').on('hidden.bs.modal', function() {
                $('input#take_stream_weight').val('');
            });
            
            $('#add_not_take_stream').on('shown.bs.modal', function() {
                $('select#calculation_stream_id').focus();
            });
            
            $('#add_not_take_stream').on('hidden.bs.modal', function() {
                $('select#calculation_stream_id').val('');
                $('input#weight').val('');
            });
            
            $('#edit_not_take_stream').on('shown.bs.modal', function() {
                $('input#not_take_stream_weight').focus();
            });
            
            $('#edit_not_take_stream').on('hidden.bs.modal', function() {
                $('input#not_take_stream_weight').val('');
            });
                
            <?php if(null !== filter_input(INPUT_GET, 'take_stream_id') || null !== filter_input(INPUT_GET, 'not_take_stream_id')): ?>
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