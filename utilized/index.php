<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'delete-film-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $type = filter_input(INPUT_POST, 'type');
    
    $sql_history = '';
    $sql = '';
    
    switch ($type) {
        case 'pallet_roll':
            $sql_history = "delete from pallet_roll_status_history where pallet_roll_id = $id";
            $sql = "delete from pallet_roll where id = $id";
            break;
        case 'roll':
            $sql_history = "delete from roll_status_history where roll_id = $id";
            $sql = "delete from roll where id = $id";
            break;
    }
    
    if(!empty($sql)) {
        $error_message = (new Executer($sql_history))->error;
        
        if(empty($error_message)) {
            $error_message = (new Executer($sql))->error;
            
            $sql_empty_pallet_history = "delete from pallet_status_history where pallet_id not in (select distinct pallet_id from pallet_roll)";
            $error_message = (new Executer($sql_empty_pallet_history))->error;
            
            $sql_empty_pallet = "delete from pallet where id not in (select distinct pallet_id from pallet_roll)";
            $error_message = (new Executer($sql_empty_pallet))->error;
        }
    }
}

// СТАТУС "СРАБОТАННЫЙ" ДЛЯ ПАЛЛЕТА
$utilized_status_pallet_id = 2;

// СТАТУС "СРАБОТАННЫЙ" ДЛЯ РУЛОНА
$utilized_status_roll_id = 2;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_sklad.php';
        include '../include/pager_top.php';
        $rowcounter = 0;
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <h1 style="font-size: 32px; font-weight: 600;">Сработанная пленка</h1>
                </div>
                <div class="p-1">
                    <button class="btn btn-outline-dark disabled d-none" data-toggle="modal" data-target="#filterModal" data-text="Фильтр"><img src="../images/icons/filter.svg" style="margin-right: 20px;" />Фильтр</button>
                    <div style="display: inline-block; position: relative; margin-right: 55px; margin-left: 80px;">
                        <a href="javascript: void(0);"><img src="../images/icons/filter1.svg" data-toggle="modal" data-target="#filterModal" data-text="Фильтр" /></a>
                        <?php
                        $get_count = 0;
                        foreach ($_GET as $get_key=>$get_value) {
                            if(!empty($get_value) && $get_key != PAGE && $get_key != "find") {
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
            <table class="table table-hover" id="content_table">
                <thead>
                    <tr style="border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <th style="padding-left: 5px; padding-right: 5px;" class="d-none"></th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 8%;">Дата<br />срабатывания</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 16%;">Марка пленки</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 4%;">Толщина</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 4%;">Плотность</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 4%;">Ширина</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 4%;">Вес</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 6%;">Длина</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 10%;">Поставщик</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 6%;">ID от поставщика</th>
                        <th style="padding-left: 5px; padding-right: 5px;">ID пленки</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 6%;">Статус</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 15%;">Комментарий</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 2%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select distinct id, name, colour from roll_status";
                    $grabber = (new Grabber($sql));
                    $error_message = $grabber->error;
                    $roll_statuses = $grabber->result;
                    
                    $roll_statuses1 = array();
                    foreach ($roll_statuses as $status) {
                        $roll_statuses1[$status['id']] = $status;
                    }
                    
                    $wherefindpallet = '';
                    $wherefindroll = '';
                    $find = filter_input(INPUT_GET, 'find');
                    $findtrim = $find;
                    if(mb_strlen($find) > 1) {
                        $findtrim = mb_substr($find, 1);
                    }
                    $findpallet = '';
                    $findroll = '';
                    $findtrimsubstrings = mb_split("\D", $findtrim);
                    
                    if(count($findtrimsubstrings) == 2 && mb_strlen($findtrimsubstrings[0]) > 0 && mb_strlen($findtrimsubstrings[1]) > 0) {
                        $findpallet = $findtrimsubstrings[0];
                        $findroll = $findtrimsubstrings[1];
                    }
                    if(!empty($find)) {
                        $wherefindpallet = " and (p.comment like '%$find%' or (p.id='$findpallet' and pr.ordinal='$findroll'))";
                        $wherefindroll = " and (r.id='$find' or r.id='$findtrim' or r.cell='$find' or r.comment like '%$find%')";
                    }
                    
                    $sql = "select (select count(pr.id) total_count "
                            . "from pallet_roll pr "
                            . "inner join pallet p on pr.pallet_id = p.id "
                            . "left join film_brand fb on p.film_brand_id = fb.id "
                            . "left join supplier s on p.supplier_id = s.id "
                            . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                            . "where prsh.status_id = $utilized_status_roll_id$wherefindpallet)"
                            . "+"
                            . "(select count(r.id) total_count "
                            . "from roll r "
                            . "left join film_brand fb on r.film_brand_id = fb.id "
                            . "left join supplier s on r.supplier_id = s.id "
                            . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                            . "where rsh.status_id = $utilized_status_roll_id$wherefindroll)";
                    
                    $fetcher = new Fetcher($sql);
                    if($row = $fetcher->Fetch()) {
                        $pager_total_count = $row[0];
                    }
                    
                    $sql = "select 'pallet_roll' type, pr.id id, pr.pallet_id pallet_id, pr.ordinal ordinal, prsh.date timestamp, DATE_FORMAT(prsh.date, '%d.%m.%Y') date, fb.name film_brand, "
                            . "p.width width, p.thickness thickness, pr.weight net_weight, pr.length length, "
                            . "s.name supplier, p.id_from_supplier id_from_supplier, "
                            . "prsh.status_id status_id, p.comment comment, "
                            . "(select weight from film_brand_variation where film_brand_id=fb.id and thickness=p.thickness limit 1) density "
                            . "from pallet_roll pr "
                            . "inner join pallet p on pr.pallet_id = p.id "
                            . "left join film_brand fb on p.film_brand_id = fb.id "
                            . "left join supplier s on p.supplier_id = s.id "
                            . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                            . "where prsh.status_id = $utilized_status_roll_id$wherefindpallet "
                            . "union "
                            . "select 'roll' type, r.id id, 0 pallet_id, 0 ordinal, rsh.date timestamp, DATE_FORMAT(rsh.date, '%d.%m.%Y') date, fb.name film_brand, "
                            . "r.width width, r.thickness thickness, r.net_weight net_weight, r.length length, "
                            . "s.name supplier, r.id_from_supplier id_from_supplier, "
                            . "rsh.status_id status_id, r.comment comment, "
                            . "(select weight from film_brand_variation where film_brand_id=fb.id and thickness=r.thickness limit 1) density "
                            . "from roll r "
                            . "left join film_brand fb on r.film_brand_id = fb.id "
                            . "left join supplier s on r.supplier_id = s.id "
                            . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                            . "where rsh.status_id = $utilized_status_roll_id$wherefindroll "
                            . "order by timestamp desc limit $pager_skip, $pager_take";
                    
                    $fetcher = new Fetcher($sql);
                    
                    while ($row = $fetcher->Fetch()):
                        
                    $rowcounter++;
                    $status = '';
                    $colour_style = '';
                    
                    if(!empty($roll_statuses1[$row['status_id']]['name'])) {
                        $status = $roll_statuses1[$row['status_id']]['name'];
                    }
                    
                    if(!empty($roll_statuses1[$row['status_id']]['colour'])) {
                        $colour = $roll_statuses1[$row['status_id']]['colour'];
                        $colour_style = " color: $colour";
                    }
                    ?>
                    <tr style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <td style="padding-left: 5px; padding-right: 5px;" class="d-none"><input type="checkbox" id="chk<?=$row['id'] ?>" name="chk<?=$row['id'] ?>" class="form-check chkFilm" /></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['date'] /*empty($row['date']) ? '' : date_create_from_format('Y-m-d H:M:S', $row['date'])->format("d.m.Y")*/ ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?=$row['film_brand'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?=$row['thickness'] ?> мкм</td>
                        <td style="padding-left: 5px; padding-right: 5px;" class="text-nowrap"><?= round($row['density'], 2) ?> г/м<sup>2</sup></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?=$row['width'] ?> мм</td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?=$row['net_weight'] ?> кг</td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?=$row['length'] ?> м</td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?=$row['supplier'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?=$row['id_from_supplier'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?=($row['type'] == 'pallet_roll' ? 'П'.$row['pallet_id'].'Р'.$row['ordinal'] : 'Р'.$row['id']) ?></td>
                        <td style="padding-left: 5px; padding-right: 5px; font-size: 10px; line-height: 14px; font-weight: 600;<?=$colour_style ?>"><?= mb_strtoupper($status) ?></td>
                        <td style="padding-left: 5px; padding-right: 5px; white-space: pre-wrap"><?= $row['comment'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px; position: relative;">
                            <a class="black film_menu_trigger" href="javascript: void(0);"><img src="<?=APPLICATION ?>/images/icons/vertical-dots.svg" /></a>
                            <div class="film_menu">
                                <div class="command"><a href="<?=($row['type'] == 'pallet_roll' ? APPLICATION.'/pallet/roll.php'. BuildQuery('id', $row['id']) : APPLICATION.'/roll/roll.php'. BuildQuery('id', $row['id'])) ?>">Просмотреть детали</a></div>
                                <?php
                                if(IsInRole(array('technologist', 'dev'))):
                                ?>
                                <div class="command">
                                    <form method="post">
                                        <input type="hidden" id="id" name="id" value="<?=$row['id'] ?>" />
                                        <input type="hidden" id="scroll" name="scroll" />
                                        <input type="hidden" id="type" name="type" value="<?=$row['type'] ?>" />
                                        <button type="submit" class="btn btn-link p-0 m-0 h-25 confirmable" id="delete-film-submit" name="delete-film-submit" style="font-size: 14px;">Удалить</button>
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
            if($rowcounter == 0) {
                echo '<p>Ничего не найдено.</p>';
            }
            
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
                    <h1 style="margin-top: 53px; margin-bottom: 20px; font-size: 32px; font-weight: 600;">Фильтр</h1>
                    <form method="get">
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkPallet" name="chkPallet"<?= filter_input(INPUT_GET, 'chkPallet') == 'on' ? " checked='checked'" : "" ?> />
                            <label class="form-check-label" for="chkPallet">Паллет</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkRoll" name="chkRoll"<?= filter_input(INPUT_GET, 'chkRoll') == 'on' ? " checked='checked'" : "" ?> />
                            <label class="form-check-label" for="chkRoll">Рулон</label>
                        </div>
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
                        <a href="<?=APPLICATION ?>/utilized/" type="button" class="btn" name="filter_clear" style="margin-top: 20px; margin-bottom: 35px; padding: 10px; border-radius: 8px; background-color: #E4E1ED;"><img src="../images/icons/white-times.svg" />&nbsp;&nbsp;Очистить</a>
                        <button type="button" class="btn" data-dismiss="modal" style="margin-top: 20px; margin-bottom: 35px; padding: 10px; border-radius: 8px; background-color: #EEEEEE;">Отменить</button>
                        <button type="submit" class="btn" style="margin-top: 20px; margin-bottom: 35px; padding: 10px; border-radius: 8px; background-color: #CECACA;">Применить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
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
            
            /*$('#chkMain').change(function(){
                if($(this).is(':checked')) {
                    $('.chkPallet').prop('checked', true);
                }
                else {
                    $('.chkPallet').prop('checked', false);
                }
            });
            
            $('.chkPallet').change(function(){
                if($(this).is(':checked')) {
                    $('.chkPallet').not($(this)).prop('checked', false);
                    $('tr.selected').removeClass('selected');
                    $(this).closest('tr').addClass('selected');
                }
                else {
                    $(this).closest('tr').removeClass('selected');
                }
            });*/
            
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