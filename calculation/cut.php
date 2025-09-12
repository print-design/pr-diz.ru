<?php
include '../include/topscripts.php';
include './calculation.php';
include './calculation_result.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD], ROLE_NAMES[ROLE_STOREKEEPER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id, направляем к списку
if(null === filter_input(INPUT_GET, 'id')) {
    header('Location: '.APPLICATION.'/calculation/');
}

// ПОЛУЧЕНИЕ ОБЪЕКТА
$id = filter_input(INPUT_GET, 'id');
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
        </style>
    </head>
    <body>
        <?php
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-8">
                    <div class="text-nowrap nav2">
                        <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))): ?>
                        <a href="details.php?<?= http_build_query($_GET) ?>" class="mr-4">Расчёт</a>
                        <?php endif; ?>
                        <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))): ?>
                        <a href="techmap.php?<?= http_build_query($_GET) ?>" class="mr-4">Тех. карта</a>
                        <?php endif; ?>
                        <a href="cut.php?<?= http_build_query($_GET) ?>" class="mr-4 active">Результаты</a>
                    </div>
                    <hr />
                    <?php
                    if(!empty($error_message)) {
                        echo "<div class='alert alert-danger'>$error_message</div>";
                    }
                    ?>
                    <h1><?=$calculation->name ?></h1>
                    <div class="name"><?=$calculation->customer ?></div>
                    <div class="subtitle">№<?=$calculation->customer_id.'-'.$calculation->num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></div>
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
                    <?php
                    $machine_id = null;
                    $sql = "select machine_id from plan_edition where calculation_id = $id and work_id = ".WORK_CUTTING;
                    $fetcher = new Fetcher($sql);
                    if($row = $fetcher->Fetch()) {
                        $machine_id = $row[0];
                    }
                    include '../cut/_table.php';
                    ?>
                </div>
                <div class="col-4">
                    <?php include '../cut/_cut_right.php'; ?>
                </div>
            </div>
        </div>
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
            
            function ShowImage(stream_id) {
                $.ajax({ url: "../include/big_image_show.php?stream_id=" + stream_id,
                    dataType: "json",
                    success: function(response) {
                        if(response.error.length > 0) {
                            alert(response.error);
                        }
                        else {
                            $('#big_image_header').text(response.name);
                            $('#big_image_img').attr('src', '../content/stream/' + response.filename + '?' + Date.now());
                            document.forms.download_image_form.object.value = 'stream';
                            document.forms.download_image_form.id.value = stream_id;
                            document.forms.download_image_form.image.value = response.image;
                            ShowImageButtons(stream_id, response.image);
                        }
                    },
                    error: function() {
                        alert('Ошибка при открыти макета.');
                    }
                });
            }
            
            function ShowImageButtons(stream_id, image) {
                $.ajax({ url: "../include/big_image_buttons.php?stream_id=" + stream_id + "&image=" + image,
                    success: function(response) {
                        $('#big_image_buttons').html(response);
                    },
                    error: function() {
                        alert('Ошибка при создании кнопок макета.');
                    }
                });
            }
        </script>
    </body>
</html>