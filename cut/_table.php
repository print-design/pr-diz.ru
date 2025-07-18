<div id="edit_take_stream" class="modal fade show">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?=APPLICATION ?>/cut/_edit_take_stream.php" onsubmit="javascript: return CutValidate();">
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
                            <?php
                            $sql = "select id, name from calculation_stream where calculation_id = $id";
                            $fetcher = new Fetcher($sql);
                            while($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?=$row['id'] ?>"><?=$row['name'] ?></option>
                            <?php endwhile; ?>
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
    <div class="name" style="font-size: 33px;"><?=CUTTER_NAMES[$machine_id] ?></div>
    <div class="name">Результаты резки</div>
    <?php
    $bobbins = 0;
    $weight = 0;
    $length = 0;
    
    // Количество катушек, суммарный вес и суммарная длина роликов из съёмов.
    $sql = "select count(id) bobbins, sum(weight) weight, sum(length) length from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id) and weight > 0 and length > 0";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $bobbins = $row['bobbins'];
        $weight = $row['weight'];
        $length = $row['length'];
    }
    
    // Количество катушек, суммарный вес и суммарная длина роликов не из съёмов.
    $sql = "select count(id) bobbins, sum(weight) weight, sum(length) length from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = $id) and weight > 0 and length > 0";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $bobbins += $row['bobbins'];
        $weight += $row['weight'];
        $length += $row['length'];
    }
    ?>
    <div class="subtitle">Всего: катушек <?= DisplayNumber(intval($bobbins), 0) ?> шт., <?= rtrim(rtrim(DisplayNumber(floatval($weight), 2), '0'), ',') ?> кг, <?= rtrim(rtrim(DisplayNumber(floatval($length), 2), '0'), ',') ?> м<?= $calculation->work_type_id == WORK_TYPE_NOPRINT ? "." : ", этикеток ".DisplayNumber(floor($length * $calculation->number_in_meter), 0)." шт." ?></div>
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
        </tr>
        <?php
        $sql = "select cs.id, cs.name, cs.width, "
                . "ifnull((select count(id) from calculation_take_stream where calculation_stream_id = cs.id and weight > 0 and length > 0), 0) "
                . "+ "
                . "ifnull((select count(id) from calculation_not_take_stream where calculation_stream_id = cs.id and weight > 0 and length > 0), 0) "
                . "bobbins, "
                . "ifnull((select sum(weight) from calculation_take_stream where calculation_stream_id = cs.id), 0) "
                . "+ "
                . "ifnull((select sum(weight) from calculation_not_take_stream where calculation_stream_id = cs.id), 0) "
                . "weight, "
                . "ifnull((select sum(length) from calculation_take_stream where calculation_stream_id = cs.id), 0) "
                . "+ "
                . "ifnull((select sum(length) from calculation_not_take_stream where calculation_stream_id = cs.id), 0) "
                . "length "
                . "from calculation_stream cs "
                . "where cs.calculation_id = $id "
                . "order by cs.position";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()):
        ?>
        <tr>
            <td style="text-align: left;"><?=$row['name'] ?></td>
            <td style="text-align: left;"><?=$row['width'] ?> мм</td>
            <td style="text-align: left;"><?=$row['bobbins'] ?></td>
            <td style="text-align: left;"><input type="hidden" id="sum_weight_stream_<?=$row['id'] ?>" value="<?=$row['weight'] ?>" /><?= rtrim(rtrim(DisplayNumber(floatval($row['weight'] ?? 0), 2), '0'), ',') ?> кг</td>
            <td style="text-align: left;"><input type="hidden" id="sum_length_stream_<?=$row['id'] ?>" value="<?=$row['length'] ?>" /><?= rtrim(rtrim(DisplayNumber(floatval($row['length'] ?? 0), 2), '0'), ',') ?> м</td>
            <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
            <td style="text-align: left;"><?= DisplayNumber(floor($row['length'] * $calculation->number_in_meter), 0) ?> шт.</td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
    <div class="name">Готовые съёмы</div>
    <div class="subtitle">Общий метраж съёмов: <?= rtrim(rtrim(DisplayNumber(floatval($length), 2), '0'), ',') ?> м</div>
    <?php
    // Смены и резчики
    $workers = array();
    
    $sql = "select date_format(pw.date, '%d-%m-%Y') date, pw.shift, pe.last_name, pe.first_name "
            . "from plan_workshift1 pw inner join plan_employee pe on pw.employee1_id = pe.id "
            . "where (pw.date in (select cast(timestamp as date) from calculation_take where calculation_id = $id) "
            . "or pw.date = (select cast(min(timestamp) - interval 1 day as date) from calculation_take where calculation_id = $id) "
            . "or pw.date in (select cast(printed as date) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id)) "
            . "or pw.date = (select cast(min(printed) - interval 1 day as date) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id)) "
            . "or pw.date in (select cast(printed as date) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = $id)) "
            . "or pw.date = (select cast(min(printed) - interval 1 day as date) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = $id))) "
            . "and pw.work_id = ".WORK_CUTTING." and pw.machine_id = $machine_id "
            . "order by date, shift";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()) {
        if(empty($row['last_name']) && empty($row['first_name'])) {
            $workers[$row['date'].$row['shift']] = "ВЫХОДНОЙ ДЕНЬ";
        }
        else {
            $workers[$row['date'].$row['shift']] = $row['last_name'].' '. mb_substr($row['first_name'], 0, 1).'.';
        }
    }
    
    // Съёмы
    $sql = "select ct.id, ct.timestamp, sum(cts.weight) weight, sum(cts.length) length "
                . "from calculation_take_stream cts "
                . "left join calculation_take ct on cts.calculation_take_id = ct.id "
                . "where ct.calculation_id = $id "
                . "group by cts.calculation_take_id "
                . "order by ct.timestamp";
    $grabber = new Grabber($sql);
    $takes = $grabber->result;
    $take_ordinal = 0;
    
    foreach($takes as $take):
        // Дневная смена: 8:00 текущего дня - 19:59 текущего дня
        // Ночная смена: 20:00 текущего дна - 23:59 текущего дня, 0:00 предыдущего дня - 7:59 предыдущего дня
        // (например, когда наступает 0:00 7 марта, то это считается ночной сменой 6 марта)
    $take_date = DateTime::createFromFormat('Y-m-d H:i:s', $take['timestamp']);
    $take_hour = $take_date->format('G');
    $take_shift = 'day';
    $working_take_date = clone $take_date; // Дата с точки зрения рабочего графика (напр. ночь 7 марта считается ночной сменой 6 марта)
    
    if($take_hour > 19 && $take_hour < 24) {
        $take_shift = 'night';
    }
    elseif ($take_hour >= 0 && $take_hour < 8) {
        $take_shift = 'night';
        $working_take_date->modify("-1 day");
    }
    
    $worker = "ВЫХОДНОЙ ДЕНЬ";
    
    if(array_key_exists($working_take_date->format('d-m-Y').$take_shift, $workers)) {
        $worker = $workers[$working_take_date->format('d-m-Y').$take_shift];
    }
    
    $hide_table_class = " d-none";
    $show_table_class = "";
    if(filter_input(INPUT_GET, 'take_id') == $take['id']) {
        $hide_table_class = "";
        $show_table_class = " d-none";
    }
    
    $editable = true;
    
    if(mb_substr_count($_SERVER['PHP_SELF'], 'cut.php') == 1) {
        $editable = false;
    }
    ?>
    <div style="padding-left: 10px; padding-right: 10px; border: solid 1px #e3e3e3; border-radius: 15px; margin-top: 15px; margin-bottom: 5px;">
        <div style="padding-top: 15px; padding-bottom: 15px;">
            <a href="javascript: void(0);" class="show_table<?=$show_table_class ?>" data-id="<?=$take['id'] ?>" onclick="javascript: ShowTakeTable(<?=$take['id'] ?>);"><i class="fa fa-chevron-down" style="color: #EC3A7A; margin-left: 15px; margin-right: 15px;"></i></a>
            <a href="javascript: void(0);" class="hide_table<?=$hide_table_class ?>" data-id="<?=$take['id'] ?>" onclick="javascript: HideTakeTable(<?=$take['id'] ?>);"><i class="fa fa-chevron-up" style="color: #EC3A7A; margin-left: 15px; margin-right: 15px;"></i></a>
            <strong>Съём <?=(++$take_ordinal).'. '.$take_date->format('j').' '.mb_substr($months_genitive[$take_date->format('n')], 0, 3).' '.$take_date->format('Y') ?>, <?=$worker ?>,</strong> <?= rtrim(rtrim(DisplayNumber(floatval($take['weight']), 2), '0'), ',') ?> кг, <?= rtrim(rtrim(DisplayNumber(floatval($take['length']), 2), '0'), ',') ?> м<?=$calculation->work_type_id == WORK_TYPE_NOPRINT ? "." : ", ".DisplayNumber(floor($take['length'] * $calculation->number_in_meter), 0)." шт." ?>
        </div>
        <table class="table take_table<?=$hide_table_class ?>" data-id="<?=$take['id'] ?>" style="border-bottom: 0;">
            <tr>
                <th style="font-weight: bold;">ID</th>
                <th style="font-weight: bold;">Наименование</th>
                <th style="font-weight: bold;">Ширина ручья</th>
                <th style="font-weight: bold;">Резчик</th>
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
            <?php
            $sql = "select cts.id, cs.name, cs.width, cts.printed, cts.weight, cts.length "
                    . "from calculation_take_stream cts "
                    . "inner join calculation_stream cs on cts.calculation_stream_id = cs.id "
                    . "where cts.calculation_take_id = ".$take['id']
                    . " order by cs.position";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
                $printed = DateTime::createFromFormat('Y-m-d H:i:s', $row['printed']);
            // Дневная смена: 8:00 текущего дня - 19:59 текущего дня
            // Ночная смена: 20:00 текущего дна - 23:59 текущего дня, 0:00 предыдущего дня - 7:59 предыдущего дня
            // (например, когда наступает 0:00 7 марта, то это считается ночной сменой 6 марта)
            $stream_hour = $printed->format('G');
            $stream_shift = 'day';
            $working_printed = clone $printed; // Дата с точки зрения рабочего графика (напр. ночь 7 марта считается ночной сменой 6 марта)
            
            if($stream_hour > 19 && $stream_hour < 24) {
                $stream_shift = 'night';
            }
            elseif($stream_hour >= 0 && $stream_hour < 8) {
                $stream_shift = 'night';
                $working_printed->modify("-1 day");
            }
            
            $stream_worker = "ВЫХОДНОЙ ДЕНЬ";
            
            if(array_key_exists($working_printed->format('d-m-Y').$stream_shift, $workers)) {
                $stream_worker = $workers[$working_printed->format('d-m-Y').$stream_shift];
            }
            ?>
            <tr style="border-bottom: 0;">
                <td style="text-align: left;"><?=$row['id'] ?></td>
                <td style="text-align: left;"><?=$row['name'] ?></td>
                <td style="text-align: left;"><?=$row['width'] ?> мм</td>
                <td style="text-align: left;"><?=$stream_worker ?></td>
                <td style="text-align: left;"><?=$printed->format('H:i') ?></td>
                <td style="text-align: left;"><?= rtrim(rtrim(DisplayNumber(floatval($row['weight'] ?? 0), 2), '0'), ',') ?> кг</td>
                <td style="text-align: left;"><?= rtrim(rtrim(DisplayNumber(floatval($row['length'] ?? 0), 2), '0'), ',') ?> м</td>
                <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
                <td style="text-align: left;"><?= DisplayNumber(floor($row['length'] * $calculation->number_in_meter), 0) ?> шт.</td>
                <?php endif; ?>
                <?php if($editable && $calculation->status_id != ORDER_STATUS_SHIPPED): ?>
                <td style="text-align: left;"><a href="javascript: void(0);" title="Редактировать" data-toggle="modal" data-target="#edit_take_stream" onclick="javascript: $('#take_stream_id').val('<?=$row['id'] ?>'); $('#take_stream_name').html('<?= htmlentities($row['name']) ?>'); $('#take_stream_old_weight').val('<?=$row['weight'] ?>'); $('#take_stream_old_length').val('<?=$row['length'] ?>');"><img src="../images/icons/edit1.svg" /></a></td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php endforeach; ?>
    <a name="not_take"></a>
    <?php
    $sql = "select cnts.id, cs.name, cnts.printed, cnts.weight, cnts.length "
            . "from calculation_not_take_stream cnts "
            . "inner join calculation_stream cs on cnts.calculation_stream_id = cs.id "
            . "where cs.calculation_id = $id";
    $grabber = new Grabber($sql);
    $streams = $grabber->result;
    
    $total_weight = 0;
    $total_length = 0;
    
    foreach($streams as $stream) {
        $total_weight += $stream['weight'];
        $total_length += $stream['length'];
    }
    
    if(count($streams) > 0):
    
    $hide_table_class = " d-none";
    $show_table_class = "";
    if(!empty(filter_input(INPUT_GET, 'not_take_stream_id')) || !empty(filter_input(INPUT_GET, 'invalid_not_take'))) {
        $hide_table_class = "";
        $show_table_class = " d-none";
    }
    ?>
    <div style="padding-left: 10px; padding-right: 10px; border: solid 1px #e3e3e3; border-radius: 15px; margin-top: 15px; margin-bottom: 5px;">
        <div style="padding-top: 15px; padding-bottom: 15px;">
            <a href="javascript: void(0);" class="show_not_take_table<?=$show_table_class ?>" onclick="javascript: ShowNotTakeTable();"><i class="fa fa-chevron-down" style="color: #EC3A7A; margin-left: 15px; margin-right: 15px;"></i></a>
            <a href="javascript: void(0);" class="hide_not_take_table<?=$hide_table_class ?>" onclick="javascript: HideNotTakeTable();"><i class="fa fa-chevron-up" style="color: #EC3A7A; margin-left: 15px; margin-right: 15px;"></i></a>
            <strong>Рулоны не из съёма</strong> <?= rtrim(rtrim(DisplayNumber(floatval($total_weight), 2), '0'), ',') ?> кг, <?= rtrim(rtrim(DisplayNumber(floatval($total_length), 2), '0'), ',') ?> м<?=$calculation->work_type_id == WORK_TYPE_NOPRINT ? "." : ", ".DisplayNumber(floor($total_length * $calculation->number_in_meter), 0)." шт." ?>
        </div>
        <table class="table not_take_table<?=$hide_table_class ?>" style="border-bottom: 0;">
            <tr>
                <td style="font-weight: bold;">ID</td>
                <th style="font-weight: bold;">Наименование</th>
                <th style="font-weight: bold;">Резчик</th>
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
            <?php
            foreach($streams as $stream):
                $printed = DateTime::createFromFormat('Y-m-d H:i:s', $stream['printed']);
            // Дневная смена: 8:00 текущего дня - 19:59 текущего дня
            // Ночная смена: 20:00 текущего дна - 23:59 текущего дня, 0:00 предыдущего дня - 7:59 предыдущего дня
            // (например, когда наступает 0:00 7 марта, то это считается ночной сменой 6 марта)
            $stream_hour = $printed->format('G');
            $stream_shift = 'day';
            $working_printed = clone $printed; // Дата с точки зрения рабочего графика (напр. ночь 7 марта считается ночной сменой 6 марта)
            
            if($stream_hour > 19 && $stream_hour < 24) {
                $stream_shift = 'night';
            }
            elseif($stream_hour >= 0 && $stream_hour < 8) {
                $stream_shift = 'night';
                $working_printed->modify("-1 day");
            }
            
            $stream_worker = "ВЫХОДНОЙ ДЕНЬ";
            
            if(array_key_exists($working_printed->format('d-m-Y').$stream_shift, $workers)) {
                $stream_worker = $workers[$working_printed->format('d-m-Y').$stream_shift];
            }
            ?>
            <tr style="border-bottom: 0;">
                <td style="text-align: left;"><?=$stream['id'] ?></td>
                <td style="text-align: left;"><?=$stream['name'] ?></td>
                <td style="text-align: left;"><?=$stream_worker ?></td>
                <td style="text-align: left;"><?=$printed->format('H:i') ?></td>
                <td style="text-align: left;"><?= rtrim(rtrim(DisplayNumber(floatval($stream['weight'] ?? 0), 2), '0'), ',') ?> кг</td>
                <td style="text-align: left;"><?= rtrim(rtrim(DisplayNumber(floatval($stream['length'] ?? 0), 2), '0'), ',') ?> м</td>
                <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
                <td style="text-align: left;"><?= DisplayNumber(floor($stream['length'] * $calculation->number_in_meter), 0) ?> шт.</td>
                <?php endif; ?>
                <?php if($editable && $calculation->status_id != ORDER_STATUS_SHIPPED): ?>
                <td style="text-align: left;" data-toggle="modal" data-target="#edit_not_take_stream" onclick="javascript: $('#not_take_stream_id').val('<?=$stream['id'] ?>'); $('#not_take_stream_name').html('<?=$stream['name'] ?>'); $('#not_take_stream_old_weight').val('<?=$stream['weight'] ?>'); $('#not_take_stream_old_length').val('<?=$stream['length'] ?>');"><a href="javascript: void(0);" title="Редактировать"><img src="../images/icons/edit1.svg" /></a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php
    endif;
    ?>
</div>