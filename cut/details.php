<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';
include '../calculation/calculation_result.php';

// Авторизация
if(!IsInRole(CUTTER_USERS) && !IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id или машина, направляем к списку заданий
$id = filter_input(INPUT_GET, 'id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
if(empty($id) || empty($machine_id)) {
    header('Location: '.APPLICATION.'/cut/');
}

// Начало резки
if(null !== filter_input(INPUT_POST, 'start_cut_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    $sql = "update calculation set status_id = ".ORDER_STATUS_CUT_PRILADKA." where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        header('Location: priladka.php?id='.$id.(empty($machine_id) ? '' : '&machine_id='.$machine_id));
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
                font-weight: bold;
                white-space: nowrap;
                padding-right: 30px;
                vertical-align: top;
            }
            
            td {
                line-height: 22px;
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
            
            .cutter_info {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 20px;
                padding-top: 5px;
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
                    <a class="btn btn-light backlink" href="<?= APPLICATION.'/cut/?machine_id='.$machine_id ?>">К списку резок</a>
                    <div class="row">
                        <div class="col-6">
                            <h1><?= $calculation->name ?></h1>
                            <div class="name"><?=$calculation->customer ?></div>
                            <div class="subtitle">№<?=$calculation->customer_id.'-'.$calculation->num_for_customer ?> от  <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <table>
                                <tr>
                                    <td>Объём заказа</td>
                                    <td><?= DisplayNumber(floatval($calculation->quantity), 0) ?> <?=$calculation->unit == 'kg' ? 'кг' : 'шт' ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($calculation_result->length_pure_1), 0) ?> м</td>
                                </tr>
                                <tr>
                                    <td>Менеджер</td>
                                    <td><?=$calculation->last_name.' '.$calculation->first_name ?></td>
                                </tr>
                                <tr>
                                    <td>Тип работы</td>
                                    <td><?=WORK_TYPE_NAMES[$calculation->work_type_id] ?></td>
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
                        </div>
                    </div>
                    <div style="position: absolute; left: 0px; bottom: 0px; margin: 15px;">
                        <form method="post">
                            <input type="hidden" name="id" value="<?=$id ?>" />
                            <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                            <button type="submit" name="start_cut_submit" class="btn btn-dark" style="width: 175px;">Начать работу</button>
                        </form>
                    </div>
                </div>
                <div class="col-4" style="padding-left: 20px;">
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