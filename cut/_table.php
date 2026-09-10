<?php
if(!isset($calculation_rolls)) {
    $calculation_rolls = CalculationRolls::Create($id);
}
$calculation_rolls->LoadDetails($machine_id ?? null);

include '../include/big_image.php';
?>
<div id="edit_take_stream" class="modal fade show">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?=APPLICATION ?>/cut/_edit_take_stream.php" onsubmit="javascript: return CutValidate();">
                <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
                <input type="hidden" name="id" id="take_stream_id" />
                <input type="hidden" name="scroll" />
                <input type="hidden" name="php_self" value="<?=$_SERVER['PHP_SELF'] ?>" />
                <input type="hidden" name="take_stream_old_weight" id="take_stream_old_weight" />
                <input type="hidden" name="take_stream_old_length" id="take_stream_old_length" />
                <?php foreach ($_GET as $get_key => $get_value): ?>
                <input type="hidden" name="get_<?=$get_key ?>" value="<?=$get_value ?>" />
                <?php endforeach; ?>
                <div class="modal-header">
                    <p class="font-weight-bold" style="font-size: x-large;" id="take_stream_name"></p>
                    <button type="button" class="close edit_take_stream_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                </div>
                <div class="modal-body">
                    <div id="edit_take_stream_alert" class="alert alert-danger d-none">
                        Метраж не соответствует радиусу
                    </div>
                    <div class="row">
                        <div class="form-group col-4">
                            <label for="weight">Масса катушки</label>
                            <div class="input-group">
                                <input type="text" name="weight" class="form-control float-only" id="take_stream_weight" required="required" autocomplete="off" onkeyup="javascript: $('#edit_take_stream_alert').addClass('d-none'); CutCalculate($(this));" onchange="javascript: $('#edit_take_stream_alert').addClass('d-none'); CutCalculate($(this));" />
                                <div class="input-group-append">
                                    <span class="input-group-text">кг</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="length" />
                        <div class="form-group col-4">
                            <label for="length">Метраж</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="take_stream_length" disabled="disabled" />
                                <div class="input-group-append">
                                    <span class="input-group-text">м</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-4">
                            <label for="radius">Радиус от вала</label>
                            <div class="input-group">
                                <input type="text" name="radius" class="form-control float-only" id="take_stream_radius" required="required" autocomplete="off" onkeyup="javascript: $('#edit_take_stream_alert').addClass('d-none');" onchange="javascript: $('#edit_take_stream_alert').addClass('d-none');" />
                                <div class="input-group-append">
                                    <span class="input-group-text">мм</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-start;">
                    <button type="submit" class="btn btn-dark w-50" id="edit_take_stream_submit" name="edit_take_stream_submit"><img src="../images/icons/print_light.svg" class="mr-2" />Распечатать бирку</button>
                    <button type="button" class="btn btn-light w-50" id="edit_take_stream_dismiss" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="add_not_take_stream" class="modal fade show">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?=APPLICATION ?>/cut/_add_not_take_stream.php" onsubmit="javascript: return ANTCutValidate();">
                <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
                <input type="hidden" name="id" value="<?=$id ?>" />
                <input type="hidden" name="php_self" value="<?=$_SERVER['PHP_SELF'] ?>" />
                <input type="hidden" name="add_not_take_stream_sum_weight" id="add_not_take_stream_sum_weight" />
                <input type="hidden" name="add_not_take_stream_sum_length" id="add_not_take_stream_sum_length" />
                <?php foreach($_GET as $get_key => $get_value): ?>
                <input type="hidden" name="get_<?=$get_key ?>" value="<?=$get_value ?>" />
                <?php endforeach; ?>
                <div class="modal-header">
                    <p class="font-weight-bold" style="font-size: x-large;">Добавление рулона не из съёма</p>
                    <button type="button" class="close edit_take_stream_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                </div>
                <div class="modal-body">
                    <div id="add_not_take_stream_alert" class="alert alert-danger d-none">
                        Метраж не соответствует радиусу
                    </div>
                    <div class="form-group">
                        <label for="calculation_stream_id">Наименование</label>
                        <select name="calculation_stream_id" id="calculation_stream_id" class="form-control" required="required" onchange="javascript: ANTStreamSelect($(this));">
                            <option value="" hidden="hidden">...</option>
                            <?php foreach($calculation_rolls->streams as $stream): ?>
                            <option value="<?=$stream['id'] ?>"><?=$stream['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="form-group col-4">
                            <label for="weight">Масса катушки</label>
                            <div class="input-group">
                                <input type="text" name="weight" id="add_not_take_stream_weight" class="form-control float-only" required="required" autocomplete="off" onkeyup="javascript: $('#add_not_take_stream_alert').addClass('d-none'); ANTCutCalculate($(this));" onchange="javascript: $('#add_not_take_stream_alert').addClass('d-none'); ANTCutCalculate($(this));" />
                                <div class="input-group-append">
                                    <span class="input-group-text">кг</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="length" />
                        <div class="form-group col-4">
                            <label for="length">Метраж</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="add_not_take_stream_length" disabled="disabled" />
                                <div class="input-group-append">
                                    <span class="input-group-text">м</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-4">
                            <label for="radius">Радиус от вала</label>
                            <div class="input-group">
                                <input type="text" name="radius" class="form-control float-only" id="add_not_take_stream_radius" required="required" autocomplete="off" onkeyup="javascript: $('#add_not_take_stream_alert').addClass('d-none');" onchange="javascript: $('#add_not_take_stream_alert').addClass('d-none');" />
                                <div class="input-group-append">
                                    <span class="input-group-text">мм</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-start;">
                    <button type="submit" class="btn btn-dark" id="add_not_take_stream_submit" name="add_not_take_stream_submit"><img src="../images/icons/print_light.svg" class="mr-2" />Распечатать бирку</button>
                    <button type="button" class="btn btn-light" id="add_not_take_stream_dismiss" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="edit_not_take_stream" class="modal fade show">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?=APPLICATION ?>/cut/_edit_not_take_stream.php" onsubmit="javascript: return NTCutValidate();">
                <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
                <input type="hidden" name="id" id="not_take_stream_id" />
                <input type="hidden" name="php_self" value="<?=$_SERVER['PHP_SELF'] ?>" />
                <input type="hidden" name="not_take_stream_old_weight" id="not_take_stream_old_weight" />
                <input type="hidden" name="not_take_stream_old_length" id="not_take_stream_old_length" />
                <?php foreach ($_GET as $get_key => $get_value): ?>
                <input type="hidden" name="get_<?=$get_key ?>" value="<?=$get_value ?>" />
                <?php endforeach; ?>
                <div class="modal-header">
                    <p class="font-weight-bold" style="font-size: x-large;" id="not_take_stream_name"></p>
                    <button type="button" class="close edit_not_take_stream_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                </div>
                <div class="modal-body">
                    <div id="edit_not_take_stream_alert" class="alert alert-danger d-none">
                        Метраж не соответствует радиусу
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="form-group col-4">
                                <label for="weight">Масса катушки</label>
                                <div class="input-group">
                                    <input type="text" name="weight" class="form-control float-only" id="not_take_stream_weight" required="required" autocomplete="off" onkeyup="javascript: $('#edit_not_take_stream_alert').addClass('d-none'); NTCutCalculate($(this));" onchange="javascript: $('#edit_not_take_stream_alert').addClass('d-none'); NTCutCalculate($(this));" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">кг</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="length" />
                            <div class="form-group col-4">
                                <label for="length">Метраж</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="not_take_stream_length" disabled="disabled" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">м</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-4">
                                <label for="radius">Радиус от вала</label>
                                <div class="input-group">
                                    <input type="text" name="radius" class="form-control float-only" id="not_take_stream_radius" required="required" autocomplete="off" onkeyup="javascript: $('#edit_not_take_stream_alert').addClass('d-none');" onchange="javascript: $('#edit_not_take_stream_alert').addClass('d-none');" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">мм</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-start;">
                    <button type="submit" class="btn btn-dark" id="edit_not_take_stream_submit" name="edit_not_take_stream_submit"><img src="../images/icons/print_light.svg" class="mr-2" />Распечатать бирку</button>
                    <button type="button" class="btn btn-light" id="edit_not_take_stream_dismiss" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="calculation_stream">
    <div class="name" style="font-size: 33px;"><?= key_exists($machine_id, CUTTER_NAMES) ? CUTTER_NAMES[$machine_id] : "" ?></div>
    <div class="name">Результаты резки</div>
    <div class="subtitle">Всего: катушек <?= DisplayNumber(intval($calculation_rolls->totals['bobbins']), 0) ?> шт., <?= rtrim(rtrim(DisplayNumber(floatval($calculation_rolls->totals['weight']), 2), '0'), ',') ?> кг, <?= rtrim(rtrim(DisplayNumber(floatval($calculation_rolls->totals['length']), 2), '0'), ',') ?> м<?= $calculation->work_type_id == WORK_TYPE_NOPRINT ? "." : ", этикеток ".DisplayNumber(floor($calculation_rolls->totals['length'] * $calculation->number_in_meter), 0)." шт." ?></div>
    <table class="table">
        <tr>
            <th style="border-top-width: 0; font-weight: bold;">Наименование</th>
            <th style="border-top-width: 0; font-weight: bold;">Ширина ручья</th>
            <th style="border-top-width: 0; font-weight: bold;">Катушек</th>
            <th style="border-top-width: 0; font-weight: bold;">Масса</th>
            <th style="border-top-width: 0; font-weight: bold;">Метраж</th>
            <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
            <th style="border-top-width: 0; font-weight: bold;">Этикеток</th>
            <?php endif; ?>
            <th style="border-top-width: 0;"></th>
        </tr>
        <?php foreach($calculation_rolls->streams as $stream): ?>
        <tr>
            <td style="text-align: left;"><?=$stream['name'] ?></td>
            <td style="text-align: left;"><?=$stream['width'] ?> мм</td>
            <td style="text-align: left;"><?=$stream['bobbins'] ?></td>
            <td style="text-align: left;"><input type="hidden" id="sum_weight_stream_<?=$stream['id'] ?>" value="<?=$stream['weight'] ?>" /><?= rtrim(rtrim(DisplayNumber(floatval($stream['weight'] ?? 0), 2), '0'), ',') ?> кг</td>
            <td style="text-align: left;"><input type="hidden" id="sum_length_stream_<?=$stream['id'] ?>" value="<?=$stream['length'] ?>" /><?= rtrim(rtrim(DisplayNumber(floatval($stream['length'] ?? 0), 2), '0'), ',') ?> м</td>
            <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
            <td style="text-align: left;"><?= DisplayNumber(floor($stream['length'] * $calculation->number_in_meter), 0) ?> шт.</td>
            <?php endif; ?>
            <td style="text-align: right;">
                <?php if(!empty($stream['image1']) || !empty($stream['image2'])): ?>
                <a href="javascript: void(0);" class="ui_tooltip left" data-placement="left" title="Посмотреть макеты" data-toggle="modal" data-target="#big_image" onclick="javascript: ShowImageStream(<?=$stream['id'] ?>);"><img src="../images/icons/attach.svg" /></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php if(!IsInRole(ROLE_NAMES[ROLE_ACCOUNTANT])): ?>
    <div class="name">Готовые съёмы</div>
    <div class="subtitle">Общий метраж съёмов: <?= rtrim(rtrim(DisplayNumber(floatval($calculation_rolls->totals['length']), 2), '0'), ',') ?> м</div>
    <?php
    $take_ordinal = 0;
    $editable = true;
    
    if(mb_substr_count($_SERVER['PHP_SELF'], 'cut.php') == 1) {
        $editable = false;
    }
    
    foreach($calculation_rolls->takes as $take):
        $take_date = DateTime::createFromFormat('Y-m-d H:i:s', $take['timestamp']);
        
        $hide_table_class = " d-none";
        $show_table_class = "";
        if(filter_input(INPUT_GET, 'take_id', FILTER_VALIDATE_INT) == $take['id']) {
            $hide_table_class = "";
            $show_table_class = " d-none";
        }
    ?>
    <div style="padding-left: 10px; padding-right: 10px; border: solid 1px #e3e3e3; border-radius: 15px; margin-top: 15px; margin-bottom: 5px;">
        <div style="padding-top: 15px; padding-bottom: 15px;">
            <a href="javascript: void(0);" class="show_table<?=$show_table_class ?>" data-id="<?=$take['id'] ?>" onclick="javascript: ShowTakeTable(<?=$take['id'] ?>);"><i class="fa fa-chevron-down" style="color: #EC3A7A; margin-left: 15px; margin-right: 15px;"></i></a>
            <a href="javascript: void(0);" class="hide_table<?=$hide_table_class ?>" data-id="<?=$take['id'] ?>" onclick="javascript: HideTakeTable(<?=$take['id'] ?>);"><i class="fa fa-chevron-up" style="color: #EC3A7A; margin-left: 15px; margin-right: 15px;"></i></a>
            <strong>Съём <?=(++$take_ordinal).'. '.$take_date->format('j').' '.mb_substr($months_genitive[$take_date->format('n')], 0, 3).' '.$take_date->format('Y') ?>, <?=$take['worker'] ?>,</strong> <?= rtrim(rtrim(DisplayNumber(floatval($take['weight']), 2), '0'), ',') ?> кг, <?= rtrim(rtrim(DisplayNumber(floatval($take['length']), 2), '0'), ',') ?> м<?=$calculation->work_type_id == WORK_TYPE_NOPRINT ? "." : ", ".DisplayNumber(floor($take['length'] * $calculation->number_in_meter), 0)." шт." ?>
        </div>
        <table class="table take_table<?=$hide_table_class ?>" data-id="<?=$take['id'] ?>" style="border-bottom: 0;">
            <tr>
                <th style="font-weight: bold;">ID</th>
                <th style="font-weight: bold;">Наименование</th>
                <th style="font-weight: bold;">Ширина ручья</th>
                <th style="font-weight: bold;">Резчик</th>
                <th style="font-weight: bold;">Дата</th>
                <th style="font-weight: bold;">Время</th>
                <th style="font-weight: bold;">Масса</th>
                <th style="font-weight: bold;">Метраж</th>
                <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
                <th style="font-weight: bold;">Этикеток</th>
                <?php endif; ?>
                <?php if($editable && $calculation->status_id != ORDER_STATUS_SHIPPED): ?>
                <th style="font-weight: bold;"></th>
                <?php endif; ?>
            </tr>
            <?php foreach($take['rolls'] as $roll): ?>
            <?php $printed = DateTime::createFromFormat('Y-m-d H:i:s', $roll['printed']); ?>
            <tr style="border-bottom: 0;">
                <td style="text-align: left;"><?=$roll['id'] ?></td>
                <td style="text-align: left;"><?=$roll['name'] ?></td>
                <td style="text-align: left;"><?=$roll['width'] ?> мм</td>
                <td style="text-align: left;"><?=$roll['last_name'].' '.(empty($roll['first_name']) ? '' : mb_substr($roll['first_name'], 0, 1).'.') ?></td>
                <td style="text-align: left;"><?=$printed->format('j').' '.mb_substr($months_genitive[$printed->format('n')], 0, 3).' '.$printed->format('Y') ?></td>
                <td style="text-align: left;"><?=$printed->format('H:i') ?></td>
                <td style="text-align: left;"><?= rtrim(rtrim(DisplayNumber(floatval($roll['weight'] ?? 0), 2), '0'), ',') ?> кг</td>
                <td style="text-align: left;"><?= rtrim(rtrim(DisplayNumber(floatval($roll['length'] ?? 0), 2), '0'), ',') ?> м</td>
                <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
                <td style="text-align: left;"><?= DisplayNumber(floor($roll['length'] * $calculation->number_in_meter), 0) ?> шт.</td>
                <?php endif; ?>
                <?php if($editable && $calculation->status_id != ORDER_STATUS_SHIPPED): ?>
                <td style="text-align: left;"><a href="javascript: void(0);" title="Редактировать" data-toggle="modal" data-target="#edit_take_stream" onclick="javascript: $('#take_stream_id').val('<?=$roll['id'] ?>'); $('#take_stream_name').html('<?= htmlentities($roll['name'] ?? '') ?>'); $('#take_stream_old_weight').val('<?=$roll['weight'] ?>'); $('#take_stream_old_length').val('<?=$roll['length'] ?>');"><img src="../images/icons/edit1.svg" /></a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endforeach; ?>
    <a name="not_take"></a>
    <?php if(count($calculation_rolls->externalRolls) > 0):
    
    $hide_table_class = " d-none";
    $show_table_class = "";
    if(!empty(filter_input(INPUT_GET, 'not_take_stream_id', FILTER_VALIDATE_INT)) || !empty(filter_input(INPUT_GET, 'invalid_not_take'))) {
        $hide_table_class = "";
        $show_table_class = " d-none";
    }
    ?>
    <div style="padding-left: 10px; padding-right: 10px; border: solid 1px #e3e3e3; border-radius: 15px; margin-top: 15px; margin-bottom: 5px;">
        <div style="padding-top: 15px; padding-bottom: 15px;">
            <a href="javascript: void(0);" class="show_not_take_table<?=$show_table_class ?>" onclick="javascript: ShowNotTakeTable();"><i class="fa fa-chevron-down" style="color: #EC3A7A; margin-left: 15px; margin-right: 15px;"></i></a>
            <a href="javascript: void(0);" class="hide_not_take_table<?=$hide_table_class ?>" onclick="javascript: HideNotTakeTable();"><i class="fa fa-chevron-up" style="color: #EC3A7A; margin-left: 15px; margin-right: 15px;"></i></a>
            <strong>Рулоны не из съёма</strong> <?= rtrim(rtrim(DisplayNumber(floatval($calculation_rolls->externalTotals['weight']), 2), '0'), ',') ?> кг, <?= rtrim(rtrim(DisplayNumber(floatval($calculation_rolls->externalTotals['length']), 2), '0'), ',') ?> м<?=$calculation->work_type_id == WORK_TYPE_NOPRINT ? "." : ", ".DisplayNumber(floor($calculation_rolls->externalTotals['length'] * $calculation->number_in_meter), 0)." шт." ?>
        </div>
        <table class="table not_take_table<?=$hide_table_class ?>" style="border-bottom: 0;">
            <tr>
                <td style="font-weight: bold;">ID</td>
                <th style="font-weight: bold;">Наименование</th>
                <th style="font-weight: bold;">Резчик</th>
                <th style="font-weight: bold;">Дата</th>
                <th style="font-weight: bold;">Время</th>
                <th style="font-weight: bold;">Масса</th>
                <th style="font-weight: bold;">Метраж</th>
                <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
                <th style="font-weight: bold;">Этикеток</th>
                <?php endif; ?>
                <?php if($editable && $calculation->status_id != ORDER_STATUS_SHIPPED): ?>
                <th style="font-weight: bold;"></th>
                <?php endif; ?>
            </tr>
            <?php foreach($calculation_rolls->externalRolls as $roll): ?>
            <?php $printed = DateTime::createFromFormat('Y-m-d H:i:s', $roll['printed']); ?>
            <tr style="border-bottom: 0;">
                <td style="text-align: left;"><?=$roll['id'] ?></td>
                <td style="text-align: left;"><?=$roll['name'] ?></td>
                <td style="text-align: left;"><?=$roll['last_name'].' '.(empty($roll['first_name']) ? '' : mb_substr($roll['first_name'], 0, 1).'.') ?></td>
                <td style="text-align: left;"><?=$printed->format('j').' '. mb_substr($months_genitive[$printed->format('n')], 0, 3).' '.$printed->format('Y') ?></td>
                <td style="text-align: left;"><?=$printed->format('H:i') ?></td>
                <td style="text-align: left;"><?= rtrim(rtrim(DisplayNumber(floatval($roll['weight'] ?? 0), 2), '0'), ',') ?> кг</td>
                <td style="text-align: left;"><?= rtrim(rtrim(DisplayNumber(floatval($roll['length'] ?? 0), 2), '0'), ',') ?> м</td>
                <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
                <td style="text-align: left;"><?= DisplayNumber(floor($roll['length'] * $calculation->number_in_meter), 0) ?> шт.</td>
                <?php endif; ?>
                <?php if($editable && $calculation->status_id != ORDER_STATUS_SHIPPED): ?>
                <td style="text-align: left;" data-toggle="modal" data-target="#edit_not_take_stream" onclick="javascript: $('#not_take_stream_id').val('<?=$roll['id'] ?>'); $('#not_take_stream_name').html('<?=$roll['name'] ?>'); $('#not_take_stream_old_weight').val('<?=$roll['weight'] ?>'); $('#not_take_stream_old_length').val('<?=$roll['length'] ?>');"><a href="javascript: void(0);" title="Редактировать"><img src="../images/icons/edit1.svg" /></a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php
    endif; // if(count($calculation_rolls->externalRolls) > 0):
    endif; // if(!IsInRole(ROLE_NAMES[ROLE_ACCOUNTANT])):
    ?>
</div>
