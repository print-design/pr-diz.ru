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

// Готовность к упаковке
if(null !== filter_input(INPUT_POST, 'pack_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    // При установке статуса "Готово к упаковке" нет перехода в другой раздел
    $sql = "update calculation set status_id = ".ORDER_STATUS_PACK_READY." where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        header("Location: ".APPLICATION."/cut/?machine_id=$machine_id");
    }
}

// Получение объекта
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);

$comment = '';

$sql = "select e.comment, pc.comment as continuation_comment "
        . "from plan_edition e "
        . "left join plan_continuation pc on pc.plan_edition_id = e.id "
        . "where e.work_id = ".WORK_CUTTING." and e.calculation_id = $id";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $comment = trim($row['comment'].' '.$row['continuation_comment'], ' ');
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
            
                #status {
                    width: 100%;
                    padding: 12px;
                    margin-top: 40p;
                    margin-bottom: 20px;
                    border-radius: 10px;
                    font-weight: bold;
                    text-align: center; 
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
        include '../include/header_cut.php';
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
                    <a class="btn btn-light backlink" href="take.php?id=<?=$id ?>&machine_id=<?=$machine_id ?>">Вернуться к резке</a>
                    <div class="row">
                        <div class="col-6">
                            <h1><?=$calculation->name ?></h1>
                            <div class="name"><?=$calculation->customer ?></div>
                            <div class="subtitle">№<?=$calculation->customer_id.'-'.$calculation->num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6"><?php include '../include/order_status_details.php'; ?></div>
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
                            endif;
                        
                            if(!empty($comment)) {
                                echo "<p>Комментарий: <strong>$comment</strong></p>";
                            }
                            ?>
                        </div>
                    </div>
                    <?php include './_table.php'; ?>
                    <div class="d-flex justify-content-start mb-4 mt-4">
                        <div>
                            <form method="post">
                                <input type="hidden" name="id" value="<?=$id ?>" />
                                <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                                <button type="submit" name="pack_submit" class="btn btn-dark pl-4 pr-4 mr-4">Тираж выполнен</button>
                            </form>
                        </div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4" data-toggle="modal" data-target="#add_not_take_stream"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                    </div>
                </div>
                <div class="col-4">
                    <?php include './_cut_right.php'; ?>
                </div>
            </div>
        </div>
        <?php if(null !== filter_input(INPUT_GET, 'take_stream_id') || null != filter_input(INPUT_GET, 'not_take_stream_id')): ?>
        <div class="print_only">
            <?php if(false): ?>
            <div class="pagebreak"><?php include './_print.php'; ?></div>
            <div><?php include './_print.php'; ?></div>
            <?php endif; ?>
            <div style="position: absolute; top: 0; left: 0;"><?php include './_print.php'; ?></div>
            <div style="position: absolute; top: 400px; left: 0;"><?php include './_print.php'; ?></div>
        </div>
        <?php endif; ?>
        <?php
        include '../include/footer.php';
        include '../include/footer_cut.php';
        ?>
        <script>
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