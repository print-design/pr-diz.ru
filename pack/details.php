<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';
include '../calculation/calculation_result.php';
include '../calculation/calculation_rolls.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT]))) {
    include '../include/_unauthorized.php';
}

// Если не указан id, направляем к списку заданий
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if($id === null) {
    header('Location: '.APPLICATION.'/pack/');
}

// Смена статуса
if(null !== filter_input(INPUT_POST, 'confirm_submit')) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $status_id = filter_input(INPUT_POST, 'status_id', FILTER_VALIDATE_INT);
    $form_valid = true;
    
    // Перед отгрузкой проверяем, что вес брутто, количество паллетов и габариты паллета
    // уже сохранены отдельной формой "Сохранить" (либо напрямую, либо через связанный заказ) --
    // саму смену статуса это больше не затрагивает
    if($status_id == ORDER_STATUS_SHIPPED) {
        $sql = "select gross_weight, pallet_count, pallet_length, pallet_width, pallet_height, pallet_shared_with_id from calculation where id = ?";
        $fetcher = new Fetcher($sql, [$id]);
        $pallet_data_row = $fetcher->Fetch();
        
        if($pallet_data_row && !empty($pallet_data_row['pallet_shared_with_id'])) {
            // Данные хранятся у связанного заказа -- проверяем их там
            $sql = "select gross_weight, pallet_count, pallet_length, pallet_width, pallet_height from calculation where id = ?";
            $fetcher = new Fetcher($sql, [$pallet_data_row['pallet_shared_with_id']]);
            $pallet_data_row = $fetcher->Fetch();
        }
        
        if(!$pallet_data_row || $pallet_data_row['gross_weight'] === null || $pallet_data_row['pallet_count'] === null
                || $pallet_data_row['pallet_length'] === null || $pallet_data_row['pallet_width'] === null || $pallet_data_row['pallet_height'] === null) {
            $form_valid = false;
            $error_message = "Перед отгрузкой заполните и сохраните вес брутто, количество паллетов и габариты паллета, либо выберите заказ, с которым вместе он будет отгружаться, и нажмите 'Сохранить'";
        }
    }
    
    if($form_valid && empty($error_message)) {
        $error_message = SetCalculationStatus($id, $status_id, '');
    }
    
    if($form_valid && empty($error_message)) {
        header("Location: details.php?id=$id&waiting=1");
    }
}

// Сохранение веса брутто, количества паллетов и габаритов паллета -- отдельно от смены статуса,
// чтобы кладовщица могла в любой момент исправить неверно введённые данные до отгрузки
$gross_weight_valid = '';
$pallet_count_valid = '';
$pallet_length_valid = '';
$pallet_width_valid = '';
$pallet_height_valid = '';

if(null !== filter_input(INPUT_POST, 'save_pallet_data_submit')) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $form_valid = true;
    
    // Позиция прокрутки страницы -- редирект ниже теряет обычные POST-данные,
    // поэтому передаём её явно через строку запроса, чтобы footer.php смог прокрутить обратно
    $scroll = filter_input(INPUT_POST, 'scroll');
    $scroll_param = !empty($scroll) ? '&scroll='.intval($scroll) : '';
    
    $gross_weight = filter_input(INPUT_POST, 'gross_weight') ?? '';
    $gross_weight = str_replace([' ', "\xC2\xA0", ','], ['', '', '.'], $gross_weight);
    $gross_weight_ok = !empty($gross_weight) && is_numeric($gross_weight);
    
    $pallet_count = filter_input(INPUT_POST, 'pallet_count', FILTER_VALIDATE_INT);
    $pallet_count_ok = !empty($pallet_count);
    
    $pallet_length = filter_input(INPUT_POST, 'pallet_length') ?? '';
    $pallet_length = str_replace([' ', "\xC2\xA0", ','], ['', '', '.'], $pallet_length);
    $pallet_length_ok = !empty($pallet_length) && is_numeric($pallet_length);
    
    $pallet_width = filter_input(INPUT_POST, 'pallet_width') ?? '';
    $pallet_width = str_replace([' ', "\xC2\xA0", ','], ['', '', '.'], $pallet_width);
    $pallet_width_ok = !empty($pallet_width) && is_numeric($pallet_width);
    
    $pallet_height = filter_input(INPUT_POST, 'pallet_height') ?? '';
    $pallet_height = str_replace([' ', "\xC2\xA0", ','], ['', '', '.'], $pallet_height);
    $pallet_height_ok = !empty($pallet_height) && is_numeric($pallet_height);
    
    $all_five_filled = $gross_weight_ok && $pallet_count_ok && $pallet_length_ok && $pallet_width_ok && $pallet_height_ok;
    
    $pallet_shared_with_id = filter_input(INPUT_POST, 'pallet_shared_with_id', FILTER_VALIDATE_INT);
    
    if($all_five_filled) {
        // Если все пять полей заполнены -- сохраняем собственные значения, независимо от того,
        // что выбрано в раскрывающемся списке, и обнуляем ссылку на другой заказ
        $sql = "update calculation set gross_weight = ?, pallet_count = ?, pallet_length = ?, pallet_width = ?, pallet_height = ?, pallet_shared_with_id = NULL where id = ?";
        $executer = new Executer($sql, [$gross_weight, $pallet_count, $pallet_length, $pallet_width, $pallet_height, $id]);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header("Location: details.php?id=$id$scroll_param");
        }
    }
    elseif(!empty($pallet_shared_with_id)) {
        // Выбран другой заказ -- собственные значения не нужны, обнуляем их
        $sql = "update calculation set gross_weight = NULL, pallet_count = NULL, pallet_length = NULL, pallet_width = NULL, pallet_height = NULL, pallet_shared_with_id = ? where id = ?";
        $executer = new Executer($sql, [$pallet_shared_with_id, $id]);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header("Location: details.php?id=$id$scroll_param");
        }
    }
    else {
        // Ни все поля не заполнены, ни заказ не выбран -- показываем, каких полей не хватает
        if(!$gross_weight_ok) $gross_weight_valid = ISINVALID;
        if(!$pallet_count_ok) $pallet_count_valid = ISINVALID;
        if(!$pallet_length_ok) $pallet_length_valid = ISINVALID;
        if(!$pallet_width_ok) $pallet_width_valid = ISINVALID;
        if(!$pallet_height_ok) $pallet_height_valid = ISINVALID;
        $error_message = "Заполните вес брутто, количество паллетов и габариты паллета, либо выберите заказ, с которым отгружается этот паллет";
    }
}

// Получение объекта
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);
$calculation_rolls = CalculationRolls::Create($id);

// Вес брутто, количество паллетов и габариты паллета, введённые упаковщицей при отгрузке
// (собственные значения этого заказа -- используются для предзаполнения формы)
$gross_weight = null;
$pallet_count = null;
$pallet_length = null;
$pallet_width = null;
$pallet_height = null;
$pallet_shared_with_id = null;

$sql = "select gross_weight, pallet_count, pallet_length, pallet_width, pallet_height, pallet_shared_with_id from calculation where id = ?";
$fetcher = new Fetcher($sql, [$id]);
if($row = $fetcher->Fetch()) {
    $gross_weight = $row['gross_weight'];
    $pallet_count = $row['pallet_count'];
    $pallet_length = $row['pallet_length'];
    $pallet_width = $row['pallet_width'];
    $pallet_height = $row['pallet_height'];
    $pallet_shared_with_id = $row['pallet_shared_with_id'];
}

// Значения для отображения в сводной строке таблицы -- если заказ отгружается вместе с другим,
// берём данные того, другого, заказа
$display_gross_weight = $gross_weight;
$display_pallet_count = $pallet_count;
$display_pallet_length = $pallet_length;
$display_pallet_width = $pallet_width;
$display_pallet_height = $pallet_height;

if(!empty($pallet_shared_with_id)) {
    $sql = "select gross_weight, pallet_count, pallet_length, pallet_width, pallet_height from calculation where id = ?";
    $fetcher = new Fetcher($sql, [$pallet_shared_with_id]);
    if($row = $fetcher->Fetch()) {
        $display_gross_weight = $row['gross_weight'];
        $display_pallet_count = $row['pallet_count'];
        $display_pallet_length = $row['pallet_length'];
        $display_pallet_width = $row['pallet_width'];
        $display_pallet_height = $row['pallet_height'];
    }
}

$comment = '';

$sql = "select e.comment, pc.comment as continuation_comment "
        . "from plan_edition e "
        . "left join plan_continuation pc on pc.plan_edition_id = e.id "
        . "where e.work_id = ? and e.calculation_id = ?";
$fetcher = new Fetcher($sql, [WORK_CUTTING, $id]);

if($row = $fetcher->Fetch()) {
    $comment = trim($row['comment'].' '.$row['continuation_comment'], ' ');
}

// Количество новых форм
$new_forms_number = 0;

if($calculation->work_type_id == WORK_TYPE_PRINT) {
    for($i = 1; $i <= $calculation->ink_number; $i++) {
        $cliche_var = "cliche_$i";
        $$cliche_var = $calculation->$cliche_var;
        
        if(!empty($$cliche_var) && $$cliche_var != CLICHE_OLD) {
            $new_forms_number++;
        }
    }
    
    for($i = 1; $i <= $calculation->ink_run2_number; $i++) {
        $cliche_run2_var = "cliche_run2_$i";
        $$cliche_run2_var = $calculation->$cliche_run2_var;
        
        if(!empty($$cliche_run2_var) && $$cliche_run2_var != CLICHE_OLD) {
            $new_forms_number++;
        }
    }
}

if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
    $new_forms_number += ($calculation->cliches_count_flint + $calculation->cliches_count_kodak);
}

// Ошибки при расчётах (если есть)
if(null !== filter_input(INPUT_GET, 'error_message')) {
    $error_message = filter_input(INPUT_GET, 'error_message');
}
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            @media print {
                body {
                    padding: 0;
                    margin: 0;
                    font-size: 14px;
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
        include 'header.php';
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
                    <?php
                    $backlink_url = '';
                    if(in_array($calculation->status_id, [ORDER_STATUS_PACK_READY, ORDER_STATUS_SHIP_READY, ORDER_STATUS_SHIPPED])) {
                        $backlink_url = BuildQueryAddRemove('status', $calculation->status_id, 'id');
                    }
                    else {
                        $backlink_url = BuildQueryRemove('id');
                    }
                    ?>
                    <a class="btn btn-light backlink" href="<?=APPLICATION ?>/pack/<?=$backlink_url ?>">К списку</a>
                    <h1><?=$calculation->name ?></h1>
                    <div class="name"><?=$calculation->customer ?></div>
                    <div class="subtitle">№<?=$calculation->customer_id.'-'.$calculation->num_for_customer ?> от  <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <?php include '../include/order_status_details.php'; ?>
                            <table>
                                <tr>
                                    <td>Объём заказа</td>
                                    <td><?= DisplayNumber(intval($calculation->quantity), 0) ?> <?=$calculation->unit == 'kg' ? 'кг' : 'шт' ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? $calculation->length_pure : $calculation->length_pure_1), 0) ?> м&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&asymp;<?= DisplayNumber(floatval($calculation_rolls->volume), 2) ?> м<sup>3</sup> <small class="text-muted">(<?= DisplayNumber(floatval($calculation_rolls->volume_min), 2) ?>&ndash;<?= DisplayNumber(floatval($calculation_rolls->volume_max), 2) ?> м<sup>3</sup>)</small></td>
                                </tr>
                                <?php if($display_gross_weight !== null && $display_pallet_count !== null && $display_pallet_length !== null && $display_pallet_width !== null && $display_pallet_height !== null): ?>
                                <tr>
                                    <td>Вес брутто</td>
                                    <td><?= DisplayNumber(floatval($display_gross_weight), 0) ?> кг, <?= DisplayNumber(intval($display_pallet_count), 0) ?> <?= PluralForm($display_pallet_count, 'паллет', 'паллета', 'паллетов') ?>, <?= DisplayNumber(floatval($display_pallet_length), 2) ?>&times;<?= DisplayNumber(floatval($display_pallet_width), 2) ?>&times;<?= DisplayNumber(floatval($display_pallet_height), 2) ?> м</td>
                                </tr>
                                <?php endif; ?>
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
                            <?php if(IsInRole(ROLE_NAMES[ROLE_ACCOUNTANT])): ?>
                            <div style="font-size: 20px; font-weight: bold;">Отгрузочная стоимость</div>
                            <table>
                                <tr>
                                    <td>Отгр. стоимость</td>
                                    <td><?= DisplayNumber(floatval($calculation_result->shipping_cost), 2) ?> &#8381;</td>
                                </tr>
                                <tr>
                                    <td>Отгр. стоимость за <?=(empty($calculation->unit) || $calculation->unit == KG ? "кг" : "шт") ?></td>
                                    <td><?= DisplayNumber(floatval($calculation_result->shipping_cost_per_unit), 3) ?> &#8381;</td>
                                </tr>
                                <tr>
                                    <td>Отгр. стоимость ПФ</td>
                                    <td><?= DisplayNumber(floatval($calculation_result->shipping_cliche_cost), 2) ?> &#8381;</td>
                                </tr>
                                <tr>
                                    <td>Новые ПФ</td>
                                    <td><?=$new_forms_number ?>&nbsp;шт&nbsp;<?= DisplayNumber(($calculation->stream_width * $calculation->streams_number + 20) + ($calculation->ski_1 == SKI_NO ? 0 : 20), 0) ?>&nbsp;мм&nbsp;<i class="fas fa-times" style="font-size: small;"></i>&nbsp;<?= (intval($calculation->raport) + 20) ?>&nbsp;мм</td>
                                </tr>
                                <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
                                <tr>
                                    <td>Отгр. стоимость ножа</td>
                                    <td><?= DisplayNumber(floatval($calculation_result->shipping_knife_cost), 0) ?> &#8381;</td>
                                </tr>
                                <?php endif; ?>
                            </table>
                            <?php
                            else: // if(IsInRole(ROLE_NAMES[ROLE_ACCOUNTANT])):
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
                            endif; // if($calculation_result->photolabel != CalculationResult::PHOTOLABEL_NOT_FOUND):
                        
                            if(!empty($comment)) {
                                echo "<p>Комментарий: <strong>$comment</strong></p>";
                            }
                            endif; // if(IsInRole(ROLE_NAMES[ROLE_ACCOUNTANT])):
                            ?>
                        </div>
                    </div>
                    <?php
                    $machine_id = null;
                    $sql = "select machine_id from plan_edition where calculation_id = ? and work_id = ?";
                    $fetcher = new Fetcher($sql, [$id, WORK_CUTTING]);
                    if($row = $fetcher->Fetch()) {
                        $machine_id = $row[0];
                    }
                    include '../cut/_table.php';
                    ?>
                    <div class="d-flex justify-content-xl-start mt-4">
                        <?php
                        if($calculation->status_id == ORDER_STATUS_SHIP_READY) {
                            include '../include/pallet_shipping_form.php';
                        }
                        ?>
                    </div>
                    <?php
                    if(!IsInRole(ROLE_NAMES[ROLE_ACCOUNTANT])):
                    ?>
                    <div class="d-flex justify-content-xl-start mt-4">
                        <?php if($calculation->status_id == ORDER_STATUS_PACK_READY): ?>
                        <div>
                            <form method="post" class="form-inline">
                                <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
                                <input type="hidden" name="id" value="<?=$id ?>" />
                                <input type="hidden" name="status_id" value="<?=ORDER_STATUS_SHIP_READY ?>" />
                                <button type="submit" name="confirm_submit" class="btn btn-dark pl-4 pr-4 mr-4"><i class="fas fa-check mr-2"></i>Подтвердить</button>
                            </form>
                        </div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4" data-toggle="modal" data-target="#add_not_take_stream"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                        <?php elseif($calculation->status_id == ORDER_STATUS_SHIP_READY && null == filter_input(INPUT_GET, 'waiting')): ?>
                        <div>
                            <form method="post" class="form-inline">
                                <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
                                <input type="hidden" name="id" value="<?=$id ?>" />
                                <input type="hidden" name="status_id" value="<?=ORDER_STATUS_SHIPPED ?>" />
                                <button type="submit" name="confirm_submit" class="btn btn-dark pl-4 pr-4 mr-4"><i class="fas fa-check mr-2"></i>Отгружено</button>
                            </form>
                        </div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4" data-toggle="modal" data-target="#add_not_take_stream"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                        <?php endif; ?>
                        <div><button type="button" class="btn btn-light pl-4 pr-4"><i class="fas fa-download mr-2"></i>Выгрузка</button></div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-4">
                    <?php include '../cut/_cut_right.php'; ?>
                </div>
            </div>
        </div>
        <?php if(null !== filter_input(INPUT_GET, 'take_stream_id', FILTER_VALIDATE_INT) || null != filter_input(INPUT_GET, 'not_take_stream_id', FILTER_VALIDATE_INT)): ?>
        <div class="print_only">
            <?php if(false): ?>
            <div class="pagebreak"><?php include '../cut/_print.php'; ?></div>
            <div><?php include '../cut/_print.php'; ?></div>
            <?php endif; ?>
            <div style="position: absolute; top: 0; left: 0;"><?php include '../cut/_print.php'; ?></div>
            <div style="position: absolute; top: 400px; left: 0;"><?php include '../cut/_print.php'; ?></div>
        </div>
        <?php endif; ?>
        <?php
        include '../include/footer.php';
        include '../include/pallet_shipping_form_script.php';
        include '../include/footer_cut_validate.php';
        include '../include/footer_cut.php';
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
                $('input#take_stream_length').val('');
                $('input#take_stream_radius').val('');
                $('#edit_take_stream_alert').addClass('d-none');
            });
            
            $('#add_not_take_stream').on('shown.bs.modal', function() {
                $('select#calculation_stream_id').focus();
            });
            
            $('#add_not_take_stream').on('hidden.bs.modal', function() {
                $('select#calculation_stream_id').val('');
                $('input#add_not_take_stream_weight').val('');
                $('input#add_not_take_stream_length').val('');
                $('input#add_not_take_stream_radius').val('');
                $('#add_not_take_stream_alert').addClass('d-none');
            });
            
            $('#edit_not_take_stream').on('shown.bs.modal', function() {
                $('input#not_take_stream_weight').focus();
            });
            
            $('#edit_not_take_stream').on('hidden.bs.modal', function() {
                $('input#not_take_stream_weight').val('');
                $('input#not_take_stream_length').val('');
                $('input#not_take_stream_radius').val('');
                $('#edit_not_take_stream_alert').addClass('d-none');
            });
                
            <?php if(null !== filter_input(INPUT_GET, 'take_stream_id', FILTER_VALIDATE_INT) || null !== filter_input(INPUT_GET, 'not_take_stream_id', FILTER_VALIDATE_INT)): ?>
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