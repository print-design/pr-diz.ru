<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';

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
            
                td {
                    line-height: 22px;
                }
            
                tr td:nth-child(2) {
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
                    </table>
                </div>
            </div>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>