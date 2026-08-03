<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';
include '../calculation/calculation_result.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ACCOUNTANT]))) {
    include '../include/_unauthorized.php';
}

// Если не указан id, направляем к списку заказов
$id = filter_input(INPUT_GET, 'id');
if($id === null) {
    header('Location: '.APPLICATION.'/buh/');
}

// Получение объекта
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);

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
                    line-height: 30px;
                }
                
                tr th:nth-child(2) {
                    text-align: right;
                    padding-right: 0;
                }
                
                tr th:nth-child(3) {
                    text-align: right;
                    padding-right: 0;
                    padding-left: 10px;
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
        include '../include/header_buh.php';
        
        include '../include/big_image.php';
        ?>
        <form id="delete_image_form" method="post">
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
                    <a class="btn btn-light backlink" href="<?= APPLICATION ?>/buh/<?= BuildQueryRemove('id') ?>" title="К списку">К списку</a>
                    <h1><?=$calculation->name ?></h1>
                    <div class="name"><?=$calculation->customer ?></div>
                    <div class="subtitle">№<?=$calculation->customer_id.'-'.$calculation->num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></div>
                    <?php include '../include/order_status_details.php'; ?>
                    <div class="subtitle">О заказе</div>
                    <table>
                        <tr>
                            <td>Объём заказа</td>
                            <td><?= DisplayNumber(intval($calculation->quantity), 0) ?> <?= UNIT_NAMES[$calculation->unit] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? $calculation->length_pure : $calculation->length_pure_1), 0) ?> м</td>
                        </tr>
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
                            <td><?= DisplayNumber(floatval($calculation_result->cost ?? 0), 0) ?> ₽</td>
                        </tr>
                        <tr>
                            <td>Клише</td>
                            <td><?= DisplayNumber(floatval($calculation_result->cliche_cost ?? 0), 0) ?> ₽</td>
                        </tr>
                        <tr>
                            <td>Нож</td>
                            <td><?= DisplayNumber(floatval($calculation_result->knife_cost ?? 0), 0) ?> ₽</td>
                        </tr>
                        <tr>
                            <td>Отгрузочная стоимость за 1 кг</td>
                            <td><?= DisplayNumber(floatval($calculation_result->shipping_cost_per_unit ?? 0), 0) ?> ₽</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Итого к оплате</td>
                            <td><?= DisplayNumber(floatval(($calculation_result->cost ?? 0) + ($calculation_result->cliche_cost ?? 0) + ($calculation_result->knife_cost ?? 0) + ($calculation_result->shipping_cost_per_unit ?? 0)), 0)  ?> ₽</td>
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
                                . "from calculation_stream cs where cs.calculation_id = $id "
                                . "order by cs.position";
                        $fetcher = new Fetcher($sql);
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
                    $sql = "select cs.id, cs.name, cs.image1, cs.image2 from calculation_stream cs where cs.calculation_id = $id order by cs.position";
                    $fetcher = new Fetcher($sql);
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
                                   onclick="javascript: ShowImage('stream', <?=$row['id'] ?>, 1, false);">
                                    <img id="mini_image1_stream_<?=$row['id'] ?>" src="../content/stream/mini/<?=$row['image1'].'?'.time() ?>" class="img-fluid" />
                                </a>
                                <div class="mb-2">С подписью</div>
                            </div>
                            <div id="mini_image2_wrapper_stream_<?=$row['id'] ?>" class="<?=$image2_wrapper_class ?>">
                                <a id="mini_image2_link_stream_<?=$row['id'] ?>" 
                                   href="javascript: void(0);" 
                                   data-toggle="modal" data-target="#big_image" 
                                   onclick="javascript: ShowImage('stream', <?=$row['id'] ?>, 2, false);">
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
                        <?php
                        $buh_order_sum = 0;
                        
                        if($calculation->unit == KG) {
                            $buh_order_sum = round($calculation->weight_cut, 2) * round($calculation_result->shipping_cost_per_unit, 3) + $calculation_result->shipping_cliche_cost + $calculation_result->shipping_knife_cost;
                        }
                        else {
                            $buh_order_sum = floor($calculation->length_cut * $calculation->number_in_meter) * round($calculation_result->shipping_cost_per_unit, 3) + $calculation_result->shipping_cliche_cost + $calculation_result->shipping_knife_cost;
                        }
                        ?>
                        <h2>Оплата заказа</h2>
                        <div class="subtitle">Поступления</div>
                        <div class="row">
                            <div class="col-4">
                                Сумма заказа
                                <div style="font-size: large; font-weight: bold;"><?= DisplayNumber(floatval($buh_order_sum), 2) ?>  ₽</div>
                            </div>
                            <div class="col-4">
                                Поступило
                                <div style="font-size: large; font-weight: bold; color: #55bc04;"><?= DisplayNumber(floatval('140000'), 0) ?> ₽</div>
                            </div>
                            <div class="col-4">
                                Остаток долга
                                <div style="font-size: large; font-weight: bold; color: #ff2842;"><?= DisplayNumber(floatval('140000'), 0) ?> ₽</div>
                            </div>
                        </div>
                        <div class="mt-4 mb-4" style="border: solid 1px #e3e3e3; border-radius: 10px;">
                            <table>
                                <tr>
                                    <th class="pl-2" style="line-height: 40px;">Дата и время</th>
                                    <th class="text-left" style="line-height: 40px;">№ платежа</th>
                                    <th class="pr-2" style="line-height: 40px;">Сумма</th>
                                </tr>
                                <tr>
                                    <td class="pl-2">12.05.2026, 14:32</td>
                                    <td class="text-left">1043</td>
                                    <td class="pr-2">100 000,00 ₽</td>
                                </tr>
                                <tr>
                                    <td class="pl-2">18.05.2026, 10:05</td>
                                    <td class="text-left">1097</td>
                                    <td class="pr-2">40 000,00 ₽</td>
                                </tr>
                                <tr style="border-bottom: 0;">
                                    <td class="pl-2" style="line-height: 40px;">Итого поступило</td>
                                    <td class="text-left" style="line-height: 40px;"></td>
                                    <td class="pr-2" style="line-height: 40px;">140 000,00 ₽</td>
                                </tr>
                            </table>
                        </div>
                        <div class="subtitle">Добавить поступление</div>
                        <form method="post" class="mb-3">
                            <div class="row mb-3">
                                <div class="col-3">
                                    <label for="time">Дата и время</label>
                                    <input type="datetime-local" name="time" class="form-control" />
                                </div>
                                <div class="col-3">
                                    <label for="payment">№ платежа</label>
                                    <input type="text" name="payment" placeholder="№" class="form-control" />
                                </div>
                                <div class="col-3">
                                    <label for="sum">Сумма</label>
                                    <div class="input-group">
                                        <input type="text" name="sum" placeholder="0,00" class="form-control float-only" />
                                        <div class="input-group-append"><span class="input-group-text">₽</span></div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-dark"><i class="fas fa-plus mr-2"></i>Добавить поступление</button>
                        </form>
                        <h2>Отгрузка</h2>
                        <table>
                            <tr>
                                <td>Фактически упаковано</td>
                                <td>510,2 кг из 500</td>
                            </tr>
                            <tr>
                                <td>Дата отгрузки</td>
                                <td>26.05.2026</td>
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
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>