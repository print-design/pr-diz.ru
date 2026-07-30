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
                    line-height: 22px;
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
        </style>
    </head>
    <body>
        <?php
        include '../include/header_buh.php';
        ?>
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
                    <h2>О заказе</h2>
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
                    <h2>Сумма к оплате</h2>
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
                    <h2>Наименования</h2>
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
                </div>
            </div>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>