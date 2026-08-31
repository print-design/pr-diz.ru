<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    include '../include/_unauthorized.php';
}

// Преобразуем строковый ключ в константу статуса
$status = filter_input(INPUT_GET, 'status', FILTER_VALIDATE_INT) ?? ROLL_STATUS_FREE;

// Заголовок страницы и столбца даты
$page_title = "Рулоны";
$page_date_label = "создания";

if($status == ROLL_STATUS_CUT) {
    $page_title = "Раскроили";
    $page_date_label = "раскроя";
}
elseif($status == ROLL_STATUS_UTILIZED) {
    $page_title = "Сработанная пленка";
    $page_date_label = "срабатывания";
}

// Формирование ссылки для сортировки по столбцу
function OrderLink($param) {
    if(array_key_exists('order', $_REQUEST) && $_REQUEST['order'] == $param) {
        echo "<strong><i class='fas fa-arrow-down' style='color: black; font-size: small;'></i></strong>";
    }
    else {
        echo "<a class='gray' href='".BuildQueryAddRemove("order", $param, "page")."' style='font-size: x-small;'><i class='fas fa-arrow-down'></i></a>";
    }
}

// Собирает условия фильтра отдельно для роликов на паллетах и отдельно для обычных роликов,
// вместе с массивами параметров для prepared statement
function BuildRollFilter($status_id) {
    $wherefindpallet = "prsh.status_id = ?";
    $paramspallet = [$status_id];
    
    // Для статуса "Свободный" (страница без параметров) заодно показываем ролики, у которых
    // вообще нет записи в истории статусов -- такого в норме быть не должно, но если это
    // всё-таки случится из-за ошибки в программе, такой ролик не должен потеряться из виду
    if($status_id == ROLL_STATUS_FREE) {
        $wherefindroll = "(rsh.status_id is null or rsh.status_id = ?)";
    }
    else {
        $wherefindroll = "rsh.status_id = ?";
    }
    $paramsroll = [$status_id];
    
    $film_id = filter_input(INPUT_GET, 'film_id', FILTER_VALIDATE_INT);
    if(!empty($film_id)) {
        $wherefindpallet .= " and f.id = ?";
        $paramspallet[] = $film_id;
        $wherefindroll .= " and f.id = ?";
        $paramsroll[] = $film_id;
    }
    
    $thickness = filter_input(INPUT_GET, 'thickness', FILTER_VALIDATE_INT);
    if(!empty($thickness)) {
        $wherefindpallet .= " and fv.thickness = ?";
        $paramspallet[] = $thickness;
        $wherefindroll .= " and fv.thickness = ?";
        $paramsroll[]= $thickness;
    }
    
    $width_from = filter_input(INPUT_GET, 'width_from', FILTER_VALIDATE_INT);
    if(!empty($width_from)) {
        $wherefindpallet .= " and p.width >= ?";
        $paramspallet[] = $width_from;
        $wherefindroll .= " and r.width >= ?";
        $paramsroll[] = $width_from;
    }
    
    $width_to = filter_input(INPUT_GET, 'width_to', FILTER_VALIDATE_INT);
    if(!empty($width_to)) {
        $wherefindpallet .= " and p.width <= ?";
        $paramspallet[] = $width_to;
        $wherefindroll .= " and r.width <= ?";
        $paramsroll[]= $width_to;
    }
    
    $find = trim(filter_input(INPUT_GET, 'find') ?? '');
    $findhead = '';
    $findtrim = '';
    
    if(mb_strlen($find) > 0) {
        $findhead = mb_substr($find, 0, 1);
    }
    
    if(mb_strlen($find) > 1) {
        $findtrim = mb_substr($find, 1);
    }
    
    if(!empty($find)) {
        if(($findhead == 'п' || $findhead == 'П') && is_numeric($findtrim)) {
            $wherefindpallet .= " and p.id = ?";
            $paramspallet[] = $findtrim;
            $wherefindroll .= " and false";
        }
        elseif(($findhead == 'р' || $findhead == 'Р') && is_numeric($findtrim)) {
            $wherefindpallet .= " and false";
            $wherefindroll .= " and r.id = ?";
            $paramsroll[] = $findtrim;
        }
        else {
            $wherefindpallet .= " and false";
            $wherefindroll .= " and false";
        }
    }
    
    return [
        "where $wherefindpallet", 
        $paramspallet, 
        "where $wherefindroll", 
        $paramsroll,
    ];
}

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'delete-film-submit')) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $type = filter_input(INPUT_POST, 'type');
    
    $sql_status_history = '';
    $sql_cell_history = '';
    $sql = '';
    
    switch($type) {
        case 'pallet_roll':
            $sql_status_history = "delete from pallet_roll_status_history where pallet_roll_id = ?";
            $sql = "delete from pallet_roll where id = ?";
            break;
        case 'roll':
            $sql_status_history = "delete from roll_status_history where roll_id = ?";
            $sql_cell_history = "delete from roll_cell_history where roll_id = ?";
            $sql = "delete from roll where id = ?";
            break;
    }
    
    if(!empty($sql_status_history)) {
        $error_message = (new Executer($sql_status_history, [$id]))->error;
    }
    
    if(empty($error_message) && !empty($sql_cell_history)) {
        $error_message = (new Executer($sql_cell_history, [$id]))->error;
    }
    
    if(empty($error_message) && !empty($sql)) {
        $error_message = (new Executer($sql, [$id]))->error;
    }
    
    $sql = "delete from pallet_cell_history where pallet_id not in (select distinct pallet_id from pallet_roll)";
    
    if(empty($error_message) && !empty($sql)) {
        $error_message = (new Executer($sql, []))->error;
    }
    
    $sql = "delete from pallet where id not in (select distinct pallet_id from pallet_roll)";
    
    if(empty($error_message) && !empty($sql)) {
        $error_message = (new Executer($sql, []))->error;
    }
}

// Фильтр для данных
list($wherefindpallet, $paramspallet, $wherefindroll, $paramsroll) = BuildRollFilter($status);

// Получение общей массы рулонов
$sql = "select ifnull((select sum(r.net_weight) total_weight "
        . "from roll r "
        . "left join film_variation fv on r.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join supplier s on r.supplier_id = s.id "
        . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id $wherefindroll), 0)";
if($status == ROLL_STATUS_CUT || $status == ROLL_STATUS_UTILIZED) {
    $sql .= "+ifnull((select sum(pr.weight) total_weight "
            . "from pallet_roll pr "
            . "inner join pallet p on pr.pallet_id = p.id "
            . "left join film_variation fv on p.film_variation_id = fv.id "
            . "left join film f on fv.film_id = f.id "
            . "left join supplier s on p.supplier_id = s.id "
            . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id $wherefindpallet), 0)";
}

$total_weight = 0;

if($status == ROLL_STATUS_CUT || $status == ROLL_STATUS_UTILIZED) {
    $row = (new Fetcher($sql, array_merge($paramsroll, $paramspallet)))->Fetch();
    $total_weight = $row[0];
}
else {
    $row = (new Fetcher($sql, $paramsroll))->Fetch();
    $total_weight = $row[0];
}
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            <?php if(IsInRole(ROLE_NAMES[ROLE_STOREKEEPER])): ?>
            .non_storekeeper { display: none; }
            <?php else: ?>
            .storekeeper { display: none; }
            <?php endif; ?>
        </style>
    </head>
    <body>
        <?php
        include 'header.php';
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
                <div class="p-0">
                    <table>
                        <tr>
                            <td><h1 style="font-size: 32px; font-weight: 600;"><?=$page_title ?></h1></td>
                            <td style="padding-left: 20px; padding-right: 20px; font-weight: bold;">(<?= number_format($total_weight ?? 0, 0, ',', ' ') ?> кг)</td>
                        </tr>
                    </table>
                </div>
                <div class="pt-1">
                    <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER])) && $status == ROLL_STATUS_FREE): ?>
                    <a href="new.php" class="btn btn-light"><i class="fas fa-plus"></i>&nbsp;Новый ролик</a>
                    <a href="excel.php" class="btn btn-light ml-3"><i class="fas fa-file-download"></i>&nbsp;Выгрузить</a>
                    <?php endif; ?>
                    <div class="ml-5" style="display: inline-block; position: relative;">
                        <a href="javascript: void(0);"><img src="../images/icons/filter1.svg" data-toggle="modal" data-target="#filterModal" data-text="Фильтр" /></a>
                        <?php
                        $get_count = 0;
                        foreach ($_GET as $get_key => $get_value) {
                            if(!empty($get_value) && $get_key != PAGE && $get_key != "order" && $get_key != "find" && $get_key != "status") {
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
                        <th style="padding-left: 5px; padding-right: 5px; width: 8%;">Дата<br /><?=$page_date_label ?></th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 16%;">Марка пленки</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 5%;">Толщина</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 5%;">Плотность</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 5%;">Ширина</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 5%;">Вес</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 6%;">Длина</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 10%;">Поставщик</th>
                        <th style="padding-left: 5px; padding-right: 5px;">ID рулона</th>
                        <th style="padding-left: 5px; padding-right: 5px;">№ ячейки&nbsp;&nbsp;<?= OrderLink('cell') ?></th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 6%;">Статус</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 16%;" class="storekeeper">Комментарий</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 16%;" class="non_storekeeper">Комментарий</th>
                        <th style="padding-left: 5px; padding-right: 5px; width: 3%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select (select count(r.id) total_count "
                            . "from roll r "
                            . "left join film_variation fv on r.film_variation_id = fv.id "
                            . "left join film f on fv.film_id = f.id "
                            . "left join supplier s on r.supplier_id = s.id "
                            . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                            . "left join (select * from roll_cell_history where id in (select max(id) from roll_cell_history group by roll_id)) rch on rch.roll_id = r.id $wherefindroll)";
                    if($status == ROLL_STATUS_CUT || $status == ROLL_STATUS_UTILIZED) {
                        $sql .= "+(select count(pr.id) total_count "
                                . "from pallet_roll pr "
                                . "inner join pallet p on pr.pallet_id = p.id "
                                . "left join film_variation fv on p.film_variation_id = fv.id "
                                . "left join film f on fv.film_id = f.id "
                                . "left join supplier s on p.supplier_id = s.id "
                                . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                                . "left join (select * from pallet_cell_history where id in (select max(id) from pallet_cell_history group by pallet_id)) pch on pch.pallet_id = p.id $wherefindpallet)";
                    }
                    
                    if($status == ROLL_STATUS_CUT || $status == ROLL_STATUS_UTILIZED) {
                        $row = (new Fetcher($sql, array_merge($paramsroll, $paramspallet)))->Fetch();
                        $pager_total_count = $row[0];
                    }
                    else {
                        $row = (new Fetcher($sql, $paramsroll))->Fetch();
                        $pager_total_count = $row[0];
                    }
                    
                    // Сортировка
                    $orderby = "";
                    
                    if(array_key_exists('order', $_REQUEST)) {
                        $orderby = "if(cast(replace(rch.cell, ' ', '') as unsigned) > 0, 1, 0) desc, "
                                . "substring(replace(rch.cell, ' ', ''), length(cast(replace(rch.cell, ' ', '') as unsigned)) + 1, 1) collate utf8_general_ci asc, "
                                . "cast(replace(rch.cell, ' ', '') as unsigned) asc, "
                                . "trim(rch.cell) collate utf8_general_ci asc, ";
                    }
                    
                    $sql = "select 'roll' type, r.id id, 0 pallet_id, 0 ordinal, rsh.date timestamp, DATE_FORMAT(rsh.date, '%d.%m.%Y') date, f.name film, "
                            . "r.width width, fv.thickness thickness, fv.weight density, rch.cell, r.net_weight net_weight, r.length length, "
                            . "s.name supplier, rsh.status_id status_id, r.comment comment "
                            . "from roll r "
                            . "left join film_variation fv on r.film_variation_id = fv.id "
                            . "left join film f on fv.film_id = f.id "
                            . "left join supplier s on r.supplier_id = s.id "
                            . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                            . "left join (select * from roll_cell_history where id in (select max(id) from roll_cell_history group by roll_id)) rch on rch.roll_id = r.id $wherefindroll ";
                    if($status == ROLL_STATUS_CUT || $status == ROLL_STATUS_UTILIZED) {
                        $sql .= " union select 'pallet_roll' type, pr.id id, pr.pallet_id pallet_id, pr.ordinal ordinal, prsh.date timestamp, DATE_FORMAT(prsh.date, '%d.%m.%Y') date, f.name film, "
                                . "p.width width, fv.thickness thickness, fv.weight density, pch.cell cell, pr.weight net_weight, pr.length length, "
                                . "s.name supplier, prsh.status_id status_id, p.comment comment "
                                . "from pallet_roll pr "
                                . "inner join pallet p on pr.pallet_id = p.id "
                                . "left join film_variation fv on p.film_variation_id = fv.id "
                                . "left join film f on fv.film_id = f.id "
                                . "left join supplier s on p.supplier_id = s.id "
                                . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                                . "left join (select * from pallet_cell_history where id in (select max(id) from pallet_cell_history group by pallet_id)) pch on pch.pallet_id = p.id $wherefindpallet ";
                    }
                    
                    $list_params = [];
                    
                    if($status == ROLL_STATUS_CUT || $status == ROLL_STATUS_UTILIZED) {
                        $sql .= "order by ".$orderby."timestamp desc limit ?, ?";
                        $list_params = array_merge($paramsroll, $paramspallet, [$pager_skip, $pager_take]);
                    }
                    else {
                        $sql .= "order by r.id desc limit ?, ?";
                        $list_params = array_merge($paramsroll, [$pager_skip, $pager_take]);
                    }
                    
                    $fetcher = new Fetcher($sql, $list_params);
                    
                    while ($row = $fetcher->Fetch()):
                    $rowcounter++;
                    ?>
                    <tr style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['date'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['film'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['thickness'] ?> мкм</td>
                        <td style="padding-left: 5px; padding-right: 5px;" class="text-nowrap"><?= round($row['density'] ?? 0, 2) ?> г/м<sup>2</sup></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['width'] ?> мм</td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['net_weight'] ?> кг</td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['length'] ?> м</td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['supplier'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?=($row['type'] == 'pallet_roll' ? 'П'.$row['pallet_id'].'Р'.$row['ordinal'] : 'Р'.$row['id']) ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;"><?= $row['cell'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px; font-size: 10px; line-height: 14px; font-weight: 600; color: <?=ROLL_STATUS_COLOURS[$row['status_id']] ?>;"><?= mb_strtoupper(ROLL_STATUS_NAMES[$row['status_id']]) ?></td>
                        <td style="padding-left: 5px; padding-right: 5px;" class="storekeeper">
                            <div class="d-flex justify-content-start">
                                <div class="pr-2 comment_pen foredit">
                                    <a href="javascript: void(0);" onclick="EditComment(event);"><image src="../images/icons/edit1.svg" title="Редактировать" /></a>
                                </div>
                                <div class="comment_text comment_text_<?=$row['type'].'_'.($row['type'] == 'roll' ? $row['id'] : $row['pallet_id']) ?>"><?= htmlentities($row['comment'] ?? '') ?></div>
                            </div>
                            <div class="d-none comment_input">
                                <input type="text" 
                                       class="form-control comment_input_<?=$row['type'].'_'.($row['type'] == 'roll' ? $row['id'] : $row['pallet_id']) ?>" 
                                       value="<?= htmlentities($row['comment'] ?? '') ?>" 
                                       onkeydown="if(event.key === 'Enter') { SaveComment(event, '<?=$row['type'] ?>', <?=$row['id'] ?>, <?=$row['pallet_id'] ?>); }" 
                                       onfocusout="SaveComment(event, '<?=$row['type'] ?>', <?=$row['id'] ?>, <?=$row['pallet_id'] ?>);" />
                            </div>
                        </td>
                        <td style="padding-left: 5px; padding-right: 5px;" class="non_storekeeper"><?= $row['comment'] ?></td>
                        <td style="padding-left: 5px; padding-right: 5px; position: relative;">
                            <a class="black film_menu_trigger" href="javascript: void(0);"><img src="<?=APPLICATION ?>/images/icons/vertical-dots.svg" /></a>
                            <div class="film_menu">
                                <div class="command"><a href="<?=($row['type'] == 'pallet_roll' ? APPLICATION.'/pallet/roll.php'.BuildQuery('id', $row['id']) : APPLICATION.'/roll/roll.php'.BuildQuery('id', $row['id'])) ?>">Просмотреть детали</a></div>
                                <?php
                                if(IsInRole(ROLE_NAMES[ROLE_TECHNOLOGIST])):
                                ?>
                                <div class="command">
                                    <form method="post">
                                        <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
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
        $film_id = filter_input(INPUT_GET, 'film_id', FILTER_VALIDATE_INT);
        $thicknesses = array();
        $slider_value = 0;
        $slider_index = 0;
        
        if(!empty($film_id)) {
            $grabber = (new Grabber("select thickness from film_variation where film_id = ? order by thickness", [$film_id]))->result;
            
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
                    <button type="button" class="close" data-dismiss="modal" style="position: absolute; right: 32px; top: 55px;"><img src="../images/icons/close_modal.png" /></button>
                    <h1 style="margin-top: 53px; margin-bottom: 20px; font-size: 32px; font-weight: 600;">Фильтр</h1>
                    <form method="get">
                        <div class="form-group">
                            <select id="film_id" name="film_id" class="form-control" style="margin-top: 30px; margin-bottom: 30px;">
                                <option value="">МАРКА ПЛЕНКИ</option>
                                <?php
                                $films = (new Grabber("select id, name from film order by name", []))->result;
                                foreach ($films as $film) {
                                    $film_id = $film['id'];
                                    $name = $film['name'];
                                    $selected = '';
                                    if(filter_input(INPUT_GET, 'film_id', FILTER_VALIDATE_INT) == $film_id) $selected = " selected='selected'";
                                    echo "<option value='$film_id'$selected>$name</option>";
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
                            <input type="range" id="slider" class="custom-range" min="0" max="<?= count($thicknesses) ?>" step="1" value="<?= $slider_value ?>" />
                        </div>
                        <input type="hidden" id="thickness" name="thickness" value="<?= filter_input(INPUT_GET, 'thickness', FILTER_VALIDATE_INT) ?>" />
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
                        <a href="<?=APPLICATION ?>/roll/<?= empty(filter_input(INPUT_GET, 'status', FILTER_VALIDATE_INT)) ? '' : '?status='. filter_input(INPUT_GET, 'status', FILTER_VALIDATE_INT) ?>" type="button" class="btn" name="filter_clear" style="margin-top: 20px; margin-bottom: 35px; padding: 5px; border-radius: 8px; background-color: #E4E1ED;"><img src="../images/icons/white-times.svg" />&nbsp;&nbsp;Очистить</a>
                        <button type="button" class="btn" data-dismiss="modal" style="margin-top: 20px; margin-bottom: 35px; padding: 5px; border-radius: 8px; background-color: #EEEEEE;">Отменить</button>
                        <button type="submit" class="btn" style="margin-top: 20px; margin-bottom: 35px; padding: 5px; border-radius: 8px; background-color: #CECACA;">Применить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            function EditComment(ev) {
                $(ev.target).parents('td').children('.d-flex').children('.comment_pen').addClass('d-none');
                $(ev.target).parents('td').children('.d-flex').children('.comment_text').addClass('d-none');
                $(ev.target).parents('td').children('.comment_input').removeClass('d-none');
                $(ev.target).parents('td').children('.comment_input').children('input').focus();
                
                input = $(ev.target).parents('td').children('.comment_input').children('input');
                input.prop("selectionStart", input.val().length);
                input.prop("selectionEnd", input.val().length);
            }
            
            function SaveComment(ev, type, id, pallet_id) {
                text = $(ev.target).val();
                $(ev.target).val('');
                ajax_path = "";
                if(type === 'pallet_roll') {
                    ajax_path = "../pallet/_edit_comment.php?id=" + pallet_id;
                    comment_text_class = '.comment_text_' + type + '_' + pallet_id;
                    comment_input_class = '.comment_input_' + type + '_' + pallet_id;
                }
                else if(type === 'roll') {
                    ajax_path = "../roll/_edit_comment.php?id=" + id;
                    comment_text_class = '.comment_text_' + type + '_' + id;
                    comment_input_class = '.comment_input_' + type + '_' + id;
                }
                
                $.ajax({ url: ajax_path + "&text=" + text })
                        .done(function(data) {
                            $(ev.target).val(data);
                            $(ev.target).parents('.comment_input').addClass('d-none');
                            
                            $(comment_text_class).html(data);
                            $(comment_input_class).val(data);
                            
                            $(ev.target).parents('td').children('.d-flex').children('.comment_pen').removeClass('d-none');
                            $(ev.target).parents('td').children('.d-flex').children('.comment_text').removeClass('d-none');
                        })
                        .fail(function() {
                            alert('Ошибка при редактировании комментария');
                        });
            }
            
            var thicknesses = JSON.parse('<?=$json_thicknesses ?>');
            
            // Нативный HTML5 range-слайдер вместо виджета jQuery UI: убирает зависимость от jquery-ui.js
            $('#slider').on('input', function() {
                var val = parseInt($(this).val());
                if(val === 0) {
                    $("#thickness").val('');
                }
                else {
                    $("#thickness").val(thicknesses[val - 1]);
                }
            });
            
            $('#film_id').change(function(){
                if($(this).val() === '') {
                    $('#width_slider_values').html("<div class='p-1'>все</div>");
                    $('#slider').attr('max', 0).val(0);
                    $("#thickness").val('');
                }
                else {
                    $.ajax({ url: "../supplier/_thickness.php?film="+$(this).val() })
                            .done(function(data){
                                thicknesses = JSON.parse(data);
                        
                                var slider_labels = "<div class='p-1'>все</div>";
                                thicknesses.forEach(thickness => slider_labels = slider_labels + "<div class='p-1'>" + thickness + "</div>");
                                $('#width_slider_values').html(slider_labels);
                                
                                $('#slider').attr('max', thicknesses.length).val(0);
                                
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