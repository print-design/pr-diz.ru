<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'delete-roll-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from roll_status_history where roll_id = $id"))->error;
    
    if(empty($error_message)) {
        $error_message = (new Executer("delete from roll where id = $id"))->error;
    }
}

// СТАТУС "СРАБОТАННЫЙ" ДЛЯ РУЛОНА
$utilized_status_id = 2;

// Получение общей массы паллетов
$row = (new Fetcher("select sum(r.net_weight) total_weight from roll r left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id where rsh.status_id is null or rsh.status_id <> $utilized_status_id"))->Fetch();
$total_weight = $row['total_weight'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/jquery-ui.css" rel="stylesheet"/>
    </head>
    <body>
        <?php
        include '../include/header.php';
        include '../include/pager_top.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <table>
                        <tr>
                            <td><h1 style="font-size: 32px; line-height: 48px; font-weight: 600;">Рулоны</h1></td>
                            <td style="padding-left: 20px; padding-right: 20px; font-weight: bold;">(<?= number_format($total_weight, 0, ',', ' ') ?> кг)</td>
                            <td class="d-none" style="padding-left: 35px; padding-right: 10px;">
                                <a class="btn btn-dark disabled" id="btn-cut-request" style="padding-left: 40px; padding-right: 60px; padding-bottom: 8px; padding-top: 9px;">
                                    <div style="float: left; padding-top: 8px; padding-right: 30px; font-size: 12px;"><i class="fas fa-plus"></i></div>
                                    &nbsp;Заявка на<br />раскрой
                                </a>
                            </td>
                            <td class="d-none" style="padding-left: 15px; padding-right: 30px;">
                                <a class="btn btn-dark disabled" id="btn-print-request" style="padding-left: 40px; padding-right: 60px; padding-bottom: 8px; padding-top: 9px;">
                                    <div style="float: left; padding-top: 8px; padding-right: 30px; font-size: 12px;"><i class="fas fa-plus"></i></div>
                                    &nbsp;Заявка на<br />печать
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="p-1">
                    <?php if(IsInRole(array('technologist', 'dev', 'storekeeper'))): ?>
                    <a href="new.php" class="btn btn-outline-dark" style="padding-top: 14px; padding-bottom: 14px; padding-left: 30px; width: 200px; text-align: left;"><i class="fas fa-plus" style="font-size: 10px; margin-right: 18px;"></i>Новый ролик</a>
                    <?php endif; ?>
                    <button class="btn btn-outline-dark disabled d-none" data-toggle="modal" data-target="#filterModal" data-text="Фильтр"><img src="../images/icons/filter.svg" style="margin-right: 20px;" />Фильтр</button>
                    <div style="display: inline-block; position: relative; margin-right: 55px; margin-left: 80px;">
                        <a href="javascript: void(0);"><img src="../images/icons/filter1.svg" data-toggle="modal" data-target="#filterModal" data-text="Фильтр" /></a>
                        <div id="filter_params_counter_round" style="position: absolute; top: -7px; right: 0;">
                            <img src="../images/icons/filter_params_counter.svg" />
                            <div id="filter_params_counter" style="position: absolute; top: 1px; left: 8px; color: white;">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table" id="content_table">
                <thead>
                    <tr style="border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <th class="d-none" style="padding-left: 5px; padding-right: 5px;"></th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 8%;">Дата<br />создания</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 16%;">Марка пленки</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 4%;">Толщина</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 4%;">Плотность</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 4%;">Ширина</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 4%;">Вес</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 6%;">Длина</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 10%;">Поставщик</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 6%;">ID от поставщика</th>
                        <th style="padding-left: 5px; padding-right: 5px;">ID рулона</th>
                        <th style="padding-left: 5px; padding-right: 5px;">№ ячейки</th>
                        <th style="padding-left: 5px; padding-right: 5px;" class="d-none">Менеджер</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 6%;">Статус</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 15%;">Комментарий</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 2%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $where = "(rsh.status_id is null or rsh.status_id <> $utilized_status_id)";
                    
                    $film_brand_id = filter_input(INPUT_GET, 'film_brand_id');
                    if(!empty($film_brand_id)) {
                        $where .= " and r.film_brand_id = $film_brand_id";
                    }
                    
                    $thickness_from = filter_input(INPUT_GET, 'thickness_from');
                    if(!empty($thickness_from)) {
                        $where .= " and r.thickness >= ".$thickness_from;
                    }
                    
                    $thickness_to = filter_input(INPUT_GET, 'thickness_to');
                    if(!empty($thickness_to)) {
                        $where .= " and r.thickness <= $thickness_to";
                    }
                    
                    $width_from = filter_input(INPUT_GET, 'width_from');
                    if(!empty($width_from)) {
                        $where .= " and r.width >= $width_from";
                    }
                    
                    $width_to = filter_input(INPUT_GET, 'width_to');
                    if(!empty($width_to)) {
                        $where .= " and r.width <= $width_to";
                    }
                    
                    $arrStatuses = array();
                    
                    $sql = "select distinct id, name, colour from roll_status";
                    $grabber = (new Grabber($sql));
                    $error_message = $grabber->error;
                    $statuses = $grabber->result;
                    foreach ($statuses as $status) {
                        if(!empty(filter_input(INPUT_GET, 'chk'.$status['id'])) && filter_input(INPUT_GET, 'chk'.$status['id']) == 'on') {
                            array_push($arrStatuses, $status['id']);
                        }
                    }
                    
                    $statuses1 = array();
                    foreach ($statuses as $status) {
                        $statuses1[$status['id']] = $status;
                    }
                    
                    $strStatuses = implode(", ", $arrStatuses);
                    
                    if(!empty($strStatuses)) {
                        $where .= " and rsh.status_id in ($strStatuses)";
                    }
                    
                    if(!empty($where)) {
                        $where = "where $where";
                    }
                    
                    $sql = "select count(r.id) "
                            . "from roll r "
                            . "left join film_brand fb on r.film_brand_id = fb.id "
                            . "left join supplier s on r.supplier_id = s.id "
                            . "left join user u on r.storekeeper_id = u.id "
                            . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                            . $where;
                    $fetcher = new Fetcher($sql);
                    
                    if($row = $fetcher->Fetch()) {
                        $pager_total_count = $row[0];
                    }
                    
                    $sql = "select r.id, r.date, fb.name film_brand, r.width, r.thickness, r.net_weight, r.length, "
                            . "s.name supplier, r.id_from_supplier, r.cell, u.first_name, u.last_name, "
                            . "rsh.status_id status_id, r.comment, "
                            . "(select weight from film_brand_variation where film_brand_id=fb.id and thickness=r.thickness limit 1) density "
                            . "from roll r "
                            . "left join film_brand fb on r.film_brand_id = fb.id "
                            . "left join supplier s on r.supplier_id = s.id "
                            . "left join user u on r.storekeeper_id = u.id "
                            . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                            . "$where "
                            . "order by r.id desc limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    
                    while ($row = $fetcher->Fetch()):
                        
                    $status = '';
                    if(!empty($statuses1[$row['status_id']]['name'])) {
                        $status = $statuses1[$row['status_id']]['name'];
                    }

                    $colour_style = '';
                    if(!empty($statuses1[$row['status_id']]['colour'])) {
                        $colour = $statuses1[$row['status_id']]['colour'];
                        $colour_style = " color: $colour";
                    }
                    ?>
                    <tr style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <td class="d-none" style="padding-left: 5px; padding-right: 5px;"><input type="checkbox" id="chk<?=$row['id'] ?>" name="chk<?=$row['id'] ?>" data-id="<?=$row['id'] ?>" class="form-check chkRoll" /></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= date_create_from_format("Y-m-d", $row['date'])->format("d.m.Y") ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['film_brand'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['thickness'] ?> мкм</td>
                        <td style="padding-left: 5px; padding-right: 5px;" class="text-nowrap"><?= round($row['density'], 2) ?> г/м<sup>2</sup></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['width'] ?> мм</td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['net_weight'] ?> кг</td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['length'] ?> м</td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['supplier'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['id_from_supplier'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= "Р".$row['id'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['cell'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;" class="d-none"><?= $row['last_name'].' '.$row['first_name'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px; font-size: 10px; line-height: 14px; font-weight: 600;<?=$colour_style ?>"><?= mb_strtoupper($status) ?></td>
                        <td style="padding-left: 5px; padding-right: 5px; white-space: pre-wrap;"><?= htmlentities($row['comment']) ?></td>
                        <td style="padding-left: 5px; padding-right: 5px; position: relative;">
                            <a class="black film_menu_trigger" href="javascript: void(0);"><i class="fas fa-ellipsis-h"></i></a>
                            <div class="film_menu">
                                <div class="command"><a href="<?=APPLICATION ?>/roll/roll.php?id=<?=$row['id'] ?>">Просмотреть детали</a></div>
                                <?php
                                if(IsInRole(array('technologist', 'dev'))):
                                ?>
                                <div class="command">
                                    <form method="post">
                                        <input type="hidden" id="id" name="id" value="<?=$row['id'] ?>" />
                                        <input type="hidden" id="scroll" name="scroll" />
                                        <button type="submit" class="btn btn-link confirmable" id="delete-roll-submit" name="delete-roll-submit" style="font-size: 14px;">Удалить</button>
                                    </form>
                                </div>
                                <?php
                                endif;
                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                    endwhile;
                    ?>
                </tbody>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <div class="modal fixed-left fade" id="filterModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-aside" role="document">
                <div class="modal-content" style="padding-left: 35px; padding-right: 35px; width: 521px;">
                    <button type="button" class="close" data-dismiss="modal" style="position: absolute; right: 32px; top: 55px;"><img src="../images/icons//close_modal.png" /></button>
                    <h1 style="margin-top: 53px; margin-bottom: 20px; font-size: 32px; line-height: 48px; font-weight: 600;">Фильтр</h1>
                    <form method="get">
                        <div class="form-group">
                            <select id="film_brand_name" name="film_brand_name" class="form-control" style="margin-top: 30px; margin-bottom: 30px;">
                                <option value="">МАРКА ПЛЕНКИ</option>
                                <?php
                                $film_brands = (new Grabber("select distinct fb.name from roll r inner join film_brand fb on r.film_brand_id = fb.id order by fb.name"))->result;
                                foreach ($film_brands as $film_brand) {
                                    $name = $film_brand['name'];
                                    $selected = '';
                                    if(filter_input(INPUT_GET, 'film_brand_name') == $film_brand['name']) $selected = " selected='selected'";
                                    echo "<option value='$name'$selected>$name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <h2 style="font-size: 24px; line-height: 32px; font-weight: 600;">Толщина</h2>
                        <div id="width_slider" style="width: 465px;">
                            <div id="width_slider_values" style="height: 50px; position: relative; font-size: 14px; line-height: 18px;">
                                <div style="position: absolute; bottom: 10px; left: 0;">8 мкм</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 10 / 72) + 6 ?>px;">20</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 20 / 72) + 6 ?>px;">30</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 30 / 72) + 6 ?>px;">40</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 40 / 72) + 6 ?>px;">50</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 50 / 72) + 6 ?>px;">60</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 60 / 72) + 6 ?>px;">70</div>
                                <div style="position: absolute; bottom: 10px; right: -7px;">80</div>
                                <div style="position: absolute; bottom: 10px; right: -34px;">мкм</div>
                            </div>
                            <div id="slider"></div>
                        </div>
                        <input type="hidden" id="thickness_from" name="thickness_from" />
                        <input type="hidden" id="thickness_to" name="thickness_to" />
                        <h2 style="font-size: 24px; line-height: 32px; font-weight: 600; margin-top: 43px; margin-bottom: 18px;">Ширина</h2>
                        <table style="margin-bottom: 30px;">
                            <tr>
                                <td>
                                    <div style="display: inline; width: 120px;">
                                        <div style="width: 100%; text-align: center; font-size: 14px; line-height: 18px; padding-bottom: 5px;">От</div>
                                        <input type="number" min="1" id="width_from" name="width_from" class="form-control" style="width: 100px;" value="<?= filter_input(INPUT_GET, 'width_from') ?>" />
                                    </div>
                                </td>
                                <td style="font-weight: bold; padding-top: 20px; padding-left: 5px; padding-right: 5px;">-</td>
                                <td>
                                    <div style="display: inline; width: 120px;">
                                        <div style="width: 100%; text-align: center; font-size: 14px; line-height: 18px; padding-bottom: 5px;">До</div>
                                        <input type="number" min="1" id="width_to" name="width_to" class="form-control" style="width: 100px;" value="<?= filter_input(INPUT_GET, 'width_to') ?>" />
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <button type="button" class="btn" id="filter_clear" name="filter_clear" style="margin-top: 20px; margin-bottom: 35px; padding: 10px; border-radius: 8px; background-color: #E4E1ED;"><img src="../images/icons/white-times.svg" />&nbsp;&nbsp;Очистить</button>
                        <button type="button" class="btn" id="filter_cancel" name="filter_cancel" style="margin-top: 20px; margin-bottom: 35px; padding: 10px; border-radius: 8px; background-color: #FFFFFF;">Отменить</button>
                        <button type="submit" class="btn" id="filter_submit" name="filter_submit" style="margin-top: 20px; margin-bottom: 35px; padding: 10px; border-radius: 8px; background-color: #CECACA;">Применить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script>
            var slider_start_from = <?= null === filter_input(INPUT_GET, 'thickness_from') ? "20" : filter_input(INPUT_GET, 'thickness_from') ?>;
            var slider_start_to = <?= null === filter_input(INPUT_GET, 'thickness_to') ? "50" : filter_input(INPUT_GET, 'thickness_to') ?>;
            
            $( "#slider" ).slider({
                    step: 10
            });
            
            $( "#slider-range" ).slider({
                range: true,
                min: 8,
                max: 80,
                values: [slider_start_from, slider_start_to],
                slide: function(event, ui) {
                    $("#thickness_from").val(ui.values[0]);
                    $("#thickness_to").val(ui.values[1]);
                }
            });
            
            $("#thickness_from").val(slider_start_from);
            $("#thickness_to").val(slider_start_to);
            
            $('#chkMain').change(function(){
                if($(this).is(':checked')) {
                    $('.chkPallet').prop('checked', true);
                }
                else {
                    $('.chkPallet').prop('checked', false);
                }
            });
            
            $('.chkRoll').change(function(){
                if($(this).is(':checked')) {
                    $('.chkRoll').not($(this)).prop('checked', false);
                    $('#btn-cut-request').removeClass('disabled');
                    $('#btn-cut-request').attr('href', 'cut_request.php?id=' + $(this).attr('data-id'));
                    $('#btn-print-request').removeClass('disabled');
                    $('#btn-print-request').attr('href', 'print_request.php?id=' + $(this).attr('data-id'));
                    $('tr.selected').removeClass('selected');
                    $(this).closest('tr').addClass('selected');
                }
                else {
                    $('#btn-cut-request').addClass('disabled');
                    $('#btn-cut-request').removeAttr('href');
                    $('#btn-print-request').addClass('disabled');
                    $('#btn-print-request').removeAttr('href');
                    $(this).closest('tr').removeClass('selected');
                }
            });
            
            $('.film_menu_trigger').click(function() {
                var menu = $(this).next('.film_menu');
                $('.film_menu').not(menu).hide();
                menu.slideToggle();
            });
            
            $(document).click(function(e) {
                if($(e.target).closest($('.film_menu')).length || $(e.target).closest($('.film_menu_trigger')).length) return;
                $('.film_menu').slideUp();
            });
        </script>
    </body>
</html>