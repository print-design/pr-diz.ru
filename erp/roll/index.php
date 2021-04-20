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

// Получение общей массы рулонов
$row = (new Fetcher("select sum(r.net_weight) total_weight from roll r left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id where rsh.status_id is null or rsh.status_id <> $utilized_status_id"))->Fetch();
$total_weight = $row['total_weight'];

// Получение всех статусов
$fetcher = (new Fetcher("select id, name, colour from roll_status"));
$statuses = array();

while ($row = $fetcher->Fetch()) {
    $status = array();
    $status['name'] = $row['name'];
    $status['colour'] = $row['colour'];
    $statuses[$row['id']] = $status;
}
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
                        <?php
                        $get_count = 0;
                        foreach ($_GET as $get_key=>$get_value) {
                            if(!empty($get_value) && $get_key != PAGE) {
                                $get_count++;
                            }
                        }
                        if($get_count > 0):
                        ?>
                        <div id="filter_params_counter_round" style="position: absolute; top: -7px; right: 0;">
                            <img src="../images/icons/filter_params_counter.svg" />
                            <div id="filter_params_counter" style="position: absolute; top: 1px; left: 8px; color: white;"><?=$get_count ?></div>
                        </div>
                        <?php endif; ?>
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
                    
                    $film_brand_name = filter_input(INPUT_GET, 'film_brand_name');
                    if(!empty($film_brand_name)) {
                        $film_brand_name = addslashes($film_brand_name);
                        $where .= " and fb.name = '$film_brand_name'";
                    }
                    
                    $thickness = filter_input(INPUT_GET, 'thickness');
                    if(!empty($thickness)) {
                        $where .= " and r.thickness = ".$thickness;
                    }
                    
                    $width_from = filter_input(INPUT_GET, 'width_from');
                    if(!empty($width_from)) {
                        $where .= " and r.width >= $width_from";
                    }
                    
                    $width_to = filter_input(INPUT_GET, 'width_to');
                    if(!empty($width_to)) {
                        $where .= " and r.width <= $width_to";
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
                    if(!empty($statuses[$row['status_id']]['name'])) {
                        $status = $statuses[$row['status_id']]['name'];
                    }

                    $colour_style = '';
                    if(!empty($statuses[$row['status_id']]['colour'])) {
                        $colour = $statuses[$row['status_id']]['colour'];
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
                                <div class="command"><a href="<?=APPLICATION ?>/roll/roll.php<?= BuildQuery('id', $row['id']) ?>">Просмотреть детали</a></div>
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
        
        <?php
        $film_brand_name = addslashes(filter_input(INPUT_GET, 'film_brand_name'));
        $thicknesses = array();
        $slider_value = 0;
        $slider_index = 0;
        
        if(!empty($film_brand_name)) {
            $grabber = (new Grabber("select distinct fbv.thickness from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$film_brand_name' order by thickness"))->result;
            
            foreach ($grabber as $row) {
                $slider_index++;
                array_push($thicknesses, $row['thickness']);
                
                if(filter_input(INPUT_GET, 'thickness') == $row['thickness']) {
                    $slider_value = $slider_index;
                }
            }
        }
        
        $json_thicknesses = json_encode($thicknesses);
        ?>
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
                                $film_brands = (new Grabber("select distinct name from film_brand order by name"))->result;
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
                            <div id="width_slider_values" style="height: 30px; position: relative; font-size: 14px; line-height: 18px;" class="d-flex justify-content-between mb-auto">
                                <div class='p-1'>все</div>
                                <?php
                                foreach ($thicknesses as $thickness) {
                                    echo "<div class='p-1'>$thickness</div>";
                                }
                                ?>
                            </div>
                            <div id="slider"></div>
                        </div>
                        <input type="hidden" id="thickness" name="thickness" value="<?= filter_input(INPUT_GET, 'thickness') ?>" />
                        <h2 style="font-size: 24px; line-height: 32px; font-weight: 600; margin-top: 43px; margin-bottom: 18px;">Ширина</h2>
                        <table style="margin-bottom: 30px;">
                            <tr>
                                <td>
                                    <div style="display: inline; width: 120px;">
                                        <div style="width: 100%; text-align: center; font-size: 14px; line-height: 18px; padding-bottom: 5px;">От</div>
                                        <input type="text" id="width_from" name="width_from" class="form-control int-only" style="width: 100px;" value="<?= filter_input(INPUT_GET, 'width_from') ?>" />
                                    </div>
                                </td>
                                <td style="font-weight: bold; padding-top: 20px; padding-left: 5px; padding-right: 5px;">-</td>
                                <td>
                                    <div style="display: inline; width: 120px;">
                                        <div style="width: 100%; text-align: center; font-size: 14px; line-height: 18px; padding-bottom: 5px;">До</div>
                                        <input type="text" id="width_to" name="width_to" class="form-control int-only" style="width: 100px;" value="<?= filter_input(INPUT_GET, 'width_to') ?>" />
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <a href="<?=APPLICATION ?>/roll/" type="button" class="btn" name="filter_clear" style="margin-top: 20px; margin-bottom: 35px; padding: 10px; border-radius: 8px; background-color: #E4E1ED;"><img src="../images/icons/white-times.svg" />&nbsp;&nbsp;Очистить</a>
                        <button type="button" class="btn" data-dismiss="modal" style="margin-top: 20px; margin-bottom: 35px; padding: 10px; border-radius: 8px; background-color: #EEEEEE;">Отменить</button>
                        <button type="submit" class="btn" style="margin-top: 20px; margin-bottom: 35px; padding: 10px; border-radius: 8px; background-color: #CECACA;">Применить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script>
            var thicknesses = JSON.parse('<?=$json_thicknesses ?>');
            
            $("#slider").slider({
                range: false,
                min: 0,
                max: <?= count($thicknesses) ?>,
                step: 1,
                value: <?=$slider_value ?>,
                slide: function(event, ui) {
                    if(ui.value == '') {
                        $("#thickness").val('');
                    }
                    else {
                        $("#thickness").val(thicknesses[ui.value - 1]);
                    }
                }
            });
            
            $('#film_brand_name').change(function(){
                if($(this).val() == '') {
                    $('#width_slider_values').html("<div class='p-1'>все</div>");
                    $("#slider").slider({
                        range: false,
                        min: 0,
                        max: 0,
                        step: 1
                    });
                    $("#thickness").val('');
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?film_brand_name="+$(this).val() })
                            .done(function(data){
                                var thicknesses = JSON.parse(data);
                        
                                var slider_labels = "<div class='p-1'>все</div>";
                                thicknesses.forEach(thickness => slider_labels = slider_labels + "<div class='p-1'>" + thickness + "</div>");
                                $('#width_slider_values').html(slider_labels);
                                
                                $("#slider").slider({
                                    range: false,
                                    min: 0,
                                    max: thicknesses.length,
                                    step: 1,
                                    value: 0,
                                    slide: function(event, ui) {
                                        if(ui.value == '') {
                                            $("#thickness").val('');
                                        }
                                        else {
                                            $("#thickness").val(thicknesses[ui.value - 1]);
                                        }
                                    }
                                });
                                
                                $("#thickness").val('');
                            })
                            .fail(function(){
                                alert("Ошибка при получении толщины по названию.");
                            });
                }
            });
            
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