<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';
include '../calculation/calculation_result.php';
include '../calculation/calculation_rolls.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ACCOUNTANT]))) {
    include '../include/_unauthorized.php';
}

// Если не указан id, направляем к списку заказов
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if($id === null) {
    header('Location: '.APPLICATION.'/buh/');
}

// Валидация формы
$form_valid = true;
$error_message = '';

$paid_at_valid = '';
$payment_num_valid = '';
$amount_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'payment_submit')) {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    
    $paid_at = filter_input(INPUT_POST, 'paid_at');
    if(empty($paid_at)) {
        $paid_at_valid = ISINVALID;
        $form_valid = false;
    }
    
    $payment_num = filter_input(INPUT_POST, 'payment_num');
    if(empty($payment_num)) {
        $payment_num_valid = ISINVALID;
        $form_valid = false;
    }
    
    $amount = filter_input(INPUT_POST, 'amount') ?? '';
    // Убираем пробелы/неразрывные пробелы-разделители разрядов и приводим запятую к точке,
    // так как на клиенте сумма может отображаться сгруппированной по разрядам (например, "12 500,50")
    $amount = str_replace([' ', "\xC2\xA0", ','], ['', '', '.'], $amount);
    if(empty($amount) || !is_numeric($amount)) {
        $amount_valid = ISINVALID;
        $form_valid = false;
    }
    
    $currency = filter_input(INPUT_POST, 'currency');
    
    $created_by = filter_input(INPUT_POST, 'created_by');
    
    if($form_valid) {
        $sql = "insert into payment (order_id, paid_at, payment_num, amount, currency, created_by) values (?, ?, ?, ?, ?, ?)";
        $executer = new Executer($sql, [$order_id, $paid_at, $payment_num, $amount, $currency, $created_by]);
        $error_message = $executer->error;
    }
}

// Получение объекта
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);
$calculation_rolls = CalculationRolls::Create($id);

// Вес брутто и количество паллетов, введённые упаковщицей при отгрузке
$gross_weight = null;
$pallet_count = null;

$sql = "select gross_weight, pallet_count from calculation where id = ?";
$fetcher = new Fetcher($sql, [$id]);
if($row = $fetcher->Fetch()) {
    $gross_weight = $row['gross_weight'];
    $pallet_count = $row['pallet_count'];
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

// Всего порезано и упаковано
$packed_fact = '';

if($calculation->unit == KG) {
    $packed_fact = DisplayNumber($calculation->weight_cut, 2);
}
else {
    $packed_fact = DisplayNumber(floor($calculation->length_cut * $calculation->number_in_meter), 0);
}

// Стоимость заказа
$shipping_order_cost = 0;

if($calculation->unit == KG) {
    $shipping_order_cost = round($calculation->weight_cut, 2) * round($calculation_result->shipping_cost_per_unit, 3);
}
else {
    $shipping_order_cost = floor($calculation->length_cut * $calculation->number_in_meter) * round($calculation_result->shipping_cost_per_unit, 3);
}

// Общая стоимость
$shipping_cost = $shipping_order_cost + $calculation_result->shipping_cliche_cost + $calculation_result->shipping_knife_cost;

// Общая оплата
$payment_total = 0;

$sql = "select ifnull(sum(case "
        . "when p.currency = ? then p.amount * ifnull((select usd from currency where date < p.paid_at order by date desc limit 1), 1) "
        . "when p.currency = ? then p.amount * ifnull((select euro from currency where date < p.paid_at order by date desc limit 1), 1) "
        . "else p.amount "
        . "end), 0) as amount_rub from payment p where p.order_id = ?";
$fetcher = new Fetcher($sql, [CURRENCY_USD, CURRENCY_EURO, $id]);
if($row = $fetcher->Fetch()) {
    $payment_total = $row[0];
}

// Ошибки при расчётах (если есть)
if(null !== filter_input(INPUT_GET, 'error_message')) {
    $error_message = filter_input(INPUT_GET, 'error_message');
}

// ЗАПОЛНЯЕМ ДУБЛИРУЮЩЕЕСЯ ПОЛЕ
if(empty($error_message)) {
    $sql = "update calculation set duplicate_shipping_cost = ?, duplicate_payment_total = ? where id = ?";
    $executer = new Executer($sql, [$shipping_cost, $payment_total, $id]);
    $error_message = $executer->error;
}

// В производстве
$production = in_array($calculation->status_id, [ORDER_STATUS_CUT_PRILADKA, ORDER_STATUS_CUTTING, ORDER_STATUS_CUT_REMOVED]);

// Оплачено
$paid = !empty($payment_total) && !empty($shipping_cost) && $payment_total >= $shipping_cost;
?>
<!DOCTYPE html>
<html lang="ru">
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
                    line-height: 30px;
                }
                
                tr th:nth-child(2) {
                    text-align: right;
                    padding-right: 0;
                    padding-left: 10px;
                }
                
                tr th:nth-child(3) {
                    text-align: right;
                    padding-left: 10px;
                    padding-right: 10px;
                }
            
                td {
                    line-height: 30px;
                }
            
                tr td:nth-child(2) {
                    text-align: right;
                    padding-left: 10px;
                    font-weight: bold;
                }
                
                tr td:nth-child(3) {
                    text-align: right;
                    padding-left: 10px;
                    padding-right: 10px;
                    font-weight: bold;
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
                
                .original_maket {
                    border-radius: 15px;
                    box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                    padding: 15px;
                    margin: 5px 5px 8px 5px;
                }
                
                #right_part {
                    border-radius: 15px;
                    box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                    padding: 25px;
                    margin-top: 25px;
                }
        </style>
    </head>
    <body>
        <?php
        include 'header.php';
        
        include '../include/big_image.php';
        ?>
        <form id="delete_image_form" method="post">
            <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
            <input type="hidden" id="object" name="object" />
            <input type="hidden" id="id" name="id" />
            <input type="hidden" id="image" name="image" />
            <input type="hidden" name="delete_image_submit" value="1" />
            <input type="hidden" name="scroll" />
        </form>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-5">
                    <?php
                    $backlink_url = "";
                    if($production) {
                        $backlink_url = BuildQueryAddRemoveArray('production', 1, ['status', 'paid', 'id']);
                    }
                    elseif($paid) {
                        $backlink_url = BuildQueryAddRemoveArray('paid', 1, ["status", "production", "id"]);
                    }
                    elseif(!empty ($calculation->status_id) && in_array($calculation->status_id, [ORDER_STATUS_PACK_READY, ORDER_STATUS_SHIP_READY, ORDER_STATUS_SHIPPED])) {
                        $backlink_url = BuildQueryAddRemoveArray('status', $calculation->status_id, ['production', 'paid', 'id']);
                    }
                    else {
                        $backlink_url = BuildQueryRemoveArray(["status", "production", "paid", "id"]);
                    }
                    ?>
                    <a class="btn btn-light backlink" href="<?= APPLICATION ?>/buh/<?= $backlink_url ?>" title="К списку">К списку</a>
                    <h1><?=$calculation->name ?></h1>
                    <div class="name"><?=$calculation->customer ?></div>
                    <div class="subtitle">№<?=$calculation->customer_id.'-'.$calculation->num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></div>
                    <?php include '../include/order_status_details.php'; ?>
                    <div class="subtitle">О заказе</div>
                    <table>
                        <tr>
                            <td>Объём заказа</td>
                            <td><?= DisplayNumber(intval($calculation->quantity), 0) ?> <?= UNIT_NAMES[$calculation->unit] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? $calculation->length_pure : $calculation->length_pure_1), 0) ?> м&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&asymp;<?= DisplayNumber(floatval($calculation_rolls->volume), 2) ?> м<sup>3</sup> <small class="text-muted">(<?= DisplayNumber(floatval($calculation_rolls->volume_min), 2) ?>&ndash;<?= DisplayNumber(floatval($calculation_rolls->volume_max), 2) ?> м<sup>3</sup>)</small></td>
                        </tr>
                        <?php if($gross_weight !== null && $pallet_count !== null): ?>
                        <tr>
                            <td>Вес брутто</td>
                            <td><?= DisplayNumber(floatval($gross_weight), 0) ?> кг, <?= DisplayNumber(intval($pallet_count), 0) ?> <?= PluralForm($pallet_count, 'паллет', 'паллета', 'паллетов') ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Менеджер</td>
                            <td><?=$calculation->last_name.' '.$calculation->first_name ?></td>
                        </tr>
                        <tr>
                            <td>Тип работы</td>
                            <td><?= WORK_TYPE_NAMES[$calculation->work_type_id] ?></td>
                        </tr>
                        <tr>
                            <td>Материал</td>
                            <td><?= $calculation->film_1.(empty($calculation->film_2) ? '' : ' + '.$calculation->film_2).(empty($calculation->film_3) ? '' : ' + '.$calculation->film_3) ?></td>
                        </tr>
                        <tr>
                            <td>Кол-во ламинаций</td>
                            <td><?=$calculation->laminations_number ?></td>
                        </tr>
                        <?php if($calculation->status_id == ORDER_STATUS_SHIPPED): ?>
                        <tr>
                            <td>Дата отгрузки</td>
                            <td><?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->status_date)->format('d.m.Y') ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    <div class="subtitle">Сумма к оплате</div>
                    <table>
                        <tr>
                            <td>Продукция</td>
                            <td><?= DisplayNumber($shipping_order_cost, 2) ?> ₽</td>
                        </tr>
                        <tr>
                            <td>Печатные формы</td>
                            <td><?= DisplayNumber(floatval($calculation_result->shipping_cliche_cost ?? 0), 2) ?> ₽</td>
                        </tr>
                        <tr>
                            <td>Нож</td>
                            <td><?= DisplayNumber(floatval($calculation_result->shipping_knife_cost ?? 0), 2) ?> ₽</td>
                        </tr>
                        <tr>
                            <td>Отгрузочная стоимость за 1 <?= UNIT_NAMES[$calculation->unit] ?></td>
                            <td><?= DisplayNumber(floatval($calculation_result->shipping_cost_per_unit ?? 0), 2) ?> ₽</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Итого к оплате</td>
                            <td><?= DisplayNumber($shipping_cost, 2)  ?> ₽</td>
                        </tr>
                    </table>
                    <div class="subtitle">Наименования</div>
                    <table>
                        <tr>
                            <th>Наименование</th>
                            <th>Обрезная ширина</th>
                            <th>Кол-во</th>
                        </tr>
                        <?php 
                        $sql = "select cs.name, cs.width, "
                                . "ifnull((select sum(length) from calculation_take_stream where calculation_stream_id = cs.id), 0) "
                                . "+ "
                                . "ifnull((select sum(length) from calculation_not_take_stream where calculation_stream_id = cs.id), 0) length "
                                . "from calculation_stream cs where cs.calculation_id = ? "
                                . "order by cs.position";
                        $fetcher = new Fetcher($sql, [$id]);
                        while($row = $fetcher->Fetch()):
                        ?>
                        <tr>
                            <td><?= $row['name'] ?></td>
                            <td><?= DisplayNumber(floatval($row['width']), 0) ?> мм</td>
                            <td><?= DisplayNumber(floatval($row['length']), 0)  ?> шт.</td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                    <h2>Оригинал-макеты</h2>
                    <?php
                    $sql = "select cs.id, cs.name, cs.image1, cs.image2 from calculation_stream cs where cs.calculation_id = ? order by cs.position";
                    $fetcher = new Fetcher($sql, [$id]);
                    $i = 0;
                    while($row = $fetcher->Fetch()):
                    ?>
                    <div class="original_maket">
                        <div class="subtitle">Ручей <?=(++$i) ?></div>
                        <p><?=$row['name'] ?></p>
                        <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
                        <div class="d-flex justify-content-start">
                            <?php
                            $image1_wrapper_class = 'd-block';
                            if(empty($row['image1'])) {
                                $image1_wrapper_class = 'd-none';
                            }
                            $image2_wrapper_class = 'd-block';
                            if(empty($row['image2'])) {
                                $image2_wrapper_class = 'd-none';
                            }
                            ?>
                            <div id="mini_image_wrapper_stream_<?=$row['id'] ?>" class="mr-4 <?=$image1_wrapper_class ?>">
                                <a id="mini_image1_link_stream_<?=$row['id'] ?>" 
                                   href="javascript: void(0);" 
                                   data-toggle="modal" 
                                   data-target="#big_image" 
                                   onclick="javascript: ShowImage('stream', <?=$row['id'] ?>, 1);">
                                    <img id="mini_image1_stream_<?=$row['id'] ?>" src="../content/stream/mini/<?=$row['image1'].'?'.time() ?>" class="img-fluid" />
                                </a>
                                <div class="mb-2">С подписью</div>
                            </div>
                            <div id="mini_image2_wrapper_stream_<?=$row['id'] ?>" class="<?=$image2_wrapper_class ?>">
                                <a id="mini_image2_link_stream_<?=$row['id'] ?>" 
                                   href="javascript: void(0);" 
                                   data-toggle="modal" data-target="#big_image" 
                                   onclick="javascript: ShowImage('stream', <?=$row['id'] ?>, 2);">
                                    <img id="mini_image2_stream_<?=$row['id'] ?>" src="../content/stream/mini/<?=$row['image2'].'?'. time() ?>" class="img-fluid" />
                                </a>
                                <div class="mb-2">Без подписи</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="col-7">
                    <div id="right_part">
                        <h2>Оплата заказа</h2>
                        <div class="subtitle">Поступления</div>
                        <div class="row">
                            <div class="col-4">
                                Сумма заказа
                                <div style="font-size: large; font-weight: bold;"><?= DisplayNumber(floatval($shipping_cost), 2) ?>  ₽</div>
                            </div>
                            <div class="col-4">
                                Поступило
                                <div style="font-size: large; font-weight: bold; color: #55bc04;"><?= DisplayNumber(floatval($payment_total), 2) ?> ₽</div>
                            </div>
                            <div class="col-4">
                                Остаток долга
                                <div style="font-size: large; font-weight: bold; color: #ff2842;"><?= DisplayNumber(floatval($shipping_cost - $payment_total), 2) ?> ₽</div>
                            </div>
                        </div>
                        <div class="mt-4 mb-4" style="border: solid 1px #e3e3e3; border-radius: 10px;">
                            <table>
                                <tr>
                                    <th class="pl-2">Дата и время</th>
                                    <th class="text-left">№ платежа</th>
                                    <th class="text-right">Сумма</th>
                                </tr>
                                <?php
                                $sql = "select p.paid_at, p.payment_num, p.amount, p.currency, "
                                        . "case "
                                        . "when p.currency = ? then p.amount * ifnull((select usd from currency where date < p.paid_at order by date desc limit 1), 1) "
                                        . "when p.currency = ? then p.amount * ifnull((select euro from currency where date < p.paid_at order by date desc limit 1), 1) "
                                        . "else p.amount "
                                        . "end as amount_rub "
                                        . "from payment p where p.order_id = ? order by p.id desc";
                                $fetcher = new Fetcher($sql, [CURRENCY_USD, CURRENCY_EURO, $id]);
                                while($row = $fetcher->Fetch()):
                                ?>
                                <tr>
                                    <td class="pl-2"><?=DateTime::createFromFormat('Y-m-d H:i:s', $row['paid_at'])->format('d.m.Y H:i') ?></td>
                                    <td class="text-left font-weight-normal"><?=$row['payment_num'] ?></td>
                                    <td class="pr-2 font-weight-normal">
                                        <?=DisplayNumber(floatval($row['amount']), 2) ?> <?= CURRENCY_SIGNES[$row['currency']] ?>
                                        <?php 
                                        if($row['currency'] != CURRENCY_RUB) {
                                            echo " (". DisplayNumber(floatval($row['amount_rub']), 2).' '.CURRENCY_SIGNES[CURRENCY_RUB].')';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                endwhile;
                                ?>
                                <tr style="border-bottom: 0;">
                                    <td class="pl-2 font-weight-bold" style="line-height: 40px;">Итого поступило</td>
                                    <td class="text-left" style="line-height: 40px;"></td>
                                    <td style="line-height: 40px;"><?=DisplayNumber(floatval($payment_total), 2) ?> ₽</td>
                                </tr>
                            </table>
                        </div>
                        <div class="subtitle">Добавить поступление</div>
                        <form method="post" class="mb-3">
                            <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
                            <input type="hidden" name="order_id" value="<?=$id ?>" />
                            <input type="hidden" name="created_by" value="<?= GetUserId() ?>" />
                            <div class="row mb-3">
                                <div class="col-3">
                                    <label for="time">Дата и время</label>
                                    <input type="datetime-local" name="paid_at" class="form-control" required="required" />
                                    <div class="invalid-feedback">Дата и время обязательно</div>
                                </div>
                                <div class="col-3">
                                    <label for="payment">№ платежа</label>
                                    <input type="text" name="payment_num" placeholder="№" class="form-control" required="required" />
                                    <div class="invalid-feedback">№ платежа обязательно</div>
                                </div>
                                <div class="col-3">
                                    <label for="sum">Сумма</label>
                                    <div class="input-group">
                                        <input type="text" name="amount" placeholder="0,00" class="form-control float-only float-format" required="required" autocomplete="off" />
                                        <div class="input-group-append">
                                            <select name="currency" required="required">
                                                <?php foreach(CURRENCIES as $currency): ?>
                                                <option value="<?=$currency ?>"><?= CURRENCY_SIGNES[$currency] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Сумма обязательно</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-dark" name="payment_submit"><i class="fas fa-plus mr-2"></i>Добавить поступление</button>
                        </form>
                        <h2>Отгрузка</h2>
                        <table>
                            <tr>
                                <td>Фактически упаковано</td>
                                <td><?= $packed_fact.' из '.DisplayNumber(floatval($calculation->quantity ?? 0), 0).' '. UNIT_NAMES[$calculation->unit] ?></td>
                            </tr>
                            <tr>
                                <td>Дата отгрузки</td>
                                <td>
                                    <?php
                                    $sql = "select date from calculation_status_history where calculation_id = ? and status_id = ? order by id desc";
                                    $fetcher = new Fetcher($sql, [$id, ORDER_STATUS_SHIPPED]);
                                    if($row = $fetcher->Fetch()) {
                                        echo DateTime::createFromFormat('Y-m-d H:i:s', $row[0])->format('d.m.Y H:i');
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Отгрузочные документы</td>
                                <td>Не выписаны</td>
                            </tr>
                        </table>
                        <button type="button" class="btn btn-light mt-4"><img src="../images/icons/print.svg" class="mr-2" />Печать</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>