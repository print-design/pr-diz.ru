<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id, перенаправляем на основную страницу
if(empty(filter_input(INPUT_GET, 'id'))) {
    header("Location: ".APPLICATION.'/rational_cut/');
}

// Статус "СВОБОДНЫЙ"
$free_status_id = 1;

$brand_name = "";
$thickness = null;
$widths = array();
$width_combinations = array();

// Обработка формы выбора рационального отхода
if(null !== filter_input(INPUT_POST, 'remainder_submit')) {    
    $id = filter_input(INPUT_POST, 'id');
    
    // Отменяем выбор плёнки
    $sql = "update rational_cut_stage set selected_is_pallet = null, selected_id = null, remainder = null where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    // Удаляем все следующие этапы
    $sql = "delete from rational_cut_stage where rational_cut_id = (select rational_cut_id from rational_cut_stage where id = $id) and id > $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    $remainder = filter_input(INPUT_POST, 'remainder');
    $sql = "update rational_cut_stage set remainder = $remainder where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Обработка формы создания следующего этапа
if(null !== filter_input(INPUT_POST, 'next_stage_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $rational_cut_id = null;
    $selected_is_pallet = null;
    $selected_id = null;
    $remainder = null;
    
    $sql = "select rational_cut_id, selected_is_pallet, selected_id, remainder from rational_cut_stage where id = $id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $rational_cut_id = $row['rational_cut_id'];
        $selected_is_pallet = $row['selected_is_pallet'];
        $selected_id = $row['selected_id'];
        $remainder = $row['remainder'];
    }
    
    // Получаем длину и ширину выбранного ролика
    $width = null;
    $length = null;
    $sql = "";
    if($selected_is_pallet == 1) {
        $sql = "select p.width, pr.length from pallet_roll pr inner join pallet p on pr.pallet_id = p.id where pr.id = $selected_id";
    }
    elseif($selected_is_pallet == 0) {
        $sql = "select width, length from roll where id = $selected_id";
    }
    
    if(!empty($sql)) {
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $width = $row['width'];
            $length = $row['length'];
        }
    }
    
    $combination_elements = null;
    
    // Получение ширин результатов раскроя
    if(!empty($width)) {
        $sql = "select width "
                . "from rational_cut_stage_width_combination_element "
                . "where rational_cut_stage_width_combination_id = "
                . "(select min(rcswc.id) "
                . "from rational_cut_stage_width_combination rcswc "
                . "inner join rational_cut_stage_width rcsw on rcswc.rational_cut_stage_width_id = rcsw.id "
                . "inner join rational_cut_stage rcs on rcsw.rational_cut_stage_id = rcs.id "
                . "where rcs.id = $id "
                . "and rcsw.width = $width "
                . "and rcswc.remainder = $remainder)";
        $grabber = new Grabber($sql);
        $error_message = $grabber->error;
        $combination_elements = $grabber->result;
    }
    
    if(!empty($combination_elements)) {
        // Создание списка конечных плёнок для следующего этапа.
        // Из каждой плёнки нынешнего этапа вычитаем длину исходного ролика нынешнего этапа.
        // Таким образом, на следующем этапе кроить понадобится только плёнку, оставшуюся после нынешнего этапа.
    
        // Получаем конечные ролики нынешнего этапа
        $current_targets = null;
        $sql = "select width, length from rational_cut_stage_stream where rational_cut_stage_id = $id";
        $grabber = new Grabber($sql);
        $error_message = $grabber->error;
        $current_targets = $grabber->result;
        
        $next_targets = array();
        
        $combination_elements_widths_counts = array();
        
        foreach ($combination_elements as $combination_element) {
            if(!isset($combination_elements_widths_counts[$combination_element['width']])) {
                $combination_elements_widths_counts[$combination_element['width']] = 1;
            }
            else {
                $combination_elements_widths_counts[$combination_element['width']] = intval($combination_elements_widths_counts[$combination_element['width']]) + 1;
            }
        }
        
        foreach ($current_targets as $current_target) {
            $has_been_cut = false;
            foreach ($combination_elements as $combination_element) {
                if($current_target['width'] == $combination_element['width'] && $combination_elements_widths_counts[$current_target['width']] > 0) {
                    $width_diff = intval($current_target['length']) - intval($length);
                    
                    if($width_diff > 0) {
                        $next_target = array('width' => $current_target['width'], 'length' => intval($current_target['length']) - intval($length));
                        array_push($next_targets, $next_target);
                    }
                    
                    $combination_elements_widths_counts[$current_target['width']] = intval($combination_elements_widths_counts[$current_target['width']]) - 1;
                    $has_been_cut = true;
                    break;
                }
            }
            
            if(!$has_been_cut) {
                $next_target = array('width' => $current_target['width'], 'length' => $current_target['length']);
                array_push($next_targets, $next_target);
            }
        }
        
        // Создаём следующий этап
        $sql = "insert into rational_cut_stage (rational_cut_id) values($rational_cut_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $next_stage_id = $executer->insert_id;
        
        if(empty($error_message) && !empty($next_stage_id)) {
            foreach ($next_targets as $next_target) {
                $width = $next_target['width'];
                $length = $next_target['length'];
                $sql = "insert into rational_cut_stage_stream (rational_cut_stage_id, width, length) values($next_stage_id, $width, $length)";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
        
        if(empty($error_message)) {
            header("Location:  stage.php?id=$next_stage_id");
        }
    }
}

// Обработка выбора плёнки для раскроя
if(null !== filter_input(INPUT_POST, 'select_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $selected_is_pallet = filter_input(INPUT_POST, 'selected_is_pallet');
    $selected_id = filter_input(INPUT_POST, 'selected_id');
    $remainder = filter_input(INPUT_POST, 'remainder');
    
    // Удаляем все следующие этапы
    $sql = "delete from rational_cut_stage where rational_cut_id = (select rational_cut_id from rational_cut_stage where id = $id) and id > $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    // Устанавливаем для текущего этапа ID плёнки и рациональный отход
    $sql = "update rational_cut_stage set selected_is_pallet = $selected_is_pallet, selected_id = $selected_id, remainder = $remainder where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Обработка формы расчёта рационального раскроя для данного этапа
if(null !== filter_input(INPUT_POST, 'rational_cut_submit')) {
    // ID этапа аскроя
    $id = filter_input(INPUT_POST, 'id');
    
    // Марка плёнки
    $brand_name = addslashes(filter_input(INPUT_POST, 'brand_name'));
    
    // Толщина
    $thickness = filter_input(INPUT_POST, 'thickness');
    
    // Создаём список конечных плёнок
    $targets = array();
    $i = 0;
    while (null !== filter_input(INPUT_POST, 'width_'.(++$i))) {
        $target = array();
        $target = filter_input(INPUT_POST, 'width_'.$i);
        array_push($targets, $target);
    }
    
    if(count($targets) > 0) {
        // Получаем все ширины плёнок данного типа
        // ... не из паллетов
        $sql = "select distinct r.width from roll r "
                . "inner join film_brand fb on r.film_brand_id = fb.id "
                . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                . "where trim(fb.name) = '$brand_name' and r.thickness = $thickness and (rsh.status_id is null or rsh.status_id = $free_status_id)";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            array_push($widths, $row[0]);
        }
    
        // ... из паллетов
        $sql = "select distinct p.width from pallet_roll pr "
                . "inner join pallet p on pr.pallet_id = p.id "
                . "inner join film_brand fb on p.film_brand_id = fb.id "
                . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                . "where trim(fb.name) = '$brand_name' and p.thickness = $thickness and (prsh.status_id is null or prsh.status_id = $free_status_id)";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()) {
            array_push($widths, $row[0]);
        }
    
        // Составляем список ширин конечных плёнок (чтобы при обходе исключить лишние сочетания)
        $target_widths_counts = GetWidthsCounts($targets);
        $targets_count = count($targets);
    
        // Перебираем все возможные сочетания ширин, чтобы их сумма была не больше максимальной
        foreach($widths as $width) {
            GetCutsByWidth($targets, $targets_count, $width, $target_widths_counts, $width_combinations);
        }
    
        // Удаляем результаты предыдущиго расчёта по данному этапу и следующим этапам
        $sql = "delete from rational_cut_stage_width where rational_cut_stage_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    
        // Отменяем выбор плёнки
        $sql = "update rational_cut_stage set selected_is_pallet = null, selected_id = null, remainder = null where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    
        // Удаляем все следующие этапы
        $sql = "delete from rational_cut_stage where rational_cut_id = (select rational_cut_id from rational_cut_stage where id = $id) and id > $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    
        // Сохраняем данные в базу
        foreach (array_keys($width_combinations) as $width_key) {
            // Сохраняем длины
            $sql_width = "insert into rational_cut_stage_width (rational_cut_stage_id, width) values ($id, $width_key)";
            $executer_width = new Executer($sql_width);
            $error_message = $executer_width->error;
            $width_id = $executer_width->insert_id;
        
            if(empty($error_message) && !empty($width_id)) {
                // Сохраняем комбинации
                foreach($width_combinations[$width_key] as $combination) {
                    $sum_width = array_sum($combination);
                    $remainder = $width_key - $sum_width;
                
                    $sql_combination = "insert into rational_cut_stage_width_combination (rational_cut_stage_width_id, sum, remainder) values ($width_id, $sum_width, $remainder)";
                    $executer_combination = new Executer($sql_combination);
                    $error_message = $executer_combination->error;
                    $combination_id = $executer_combination->insert_id;
                
                    if(empty($error_message) && !empty($combination_id)) {
                        // Сохраняем элементы комбинаций
                        foreach ($combination as $element) {
                            $sql_element = "insert into rational_cut_stage_width_combination_element (rational_cut_stage_width_combination_id, width) values ($combination_id, $element)";
                            $executer_element = new Executer($sql_element);
                            $error_message = $executer_element->error;
                        }
                    }
                }
            }
        }
    }
}

function GetCutsByWidth($targets, $targets_count, $width, $target_widths_counts, &$width_combinations) {
    // Перебираем все возможные сочетания ширин, чтобы их сумма была не больше максимальной
    $combinations = array();
    $combination = array();
    WalkTargets($combinations, $combination, $targets, $targets_count, $width, $target_widths_counts);
    $width_combinations[$width] = $combinations;
}

function WalkTargets(&$combinations, &$combination, &$targets, $targets_count, $width, $target_widths_counts) {
    for($i=0; $i<$targets_count; $i++) {
        $current_combination = $combination;
        array_push($current_combination, $targets[$i]);
        $sum_width = GetWidthsSum($current_combination);
        
        if($sum_width <= $width) {
            $valid = true;
            
            if(in_array($current_combination, $combinations)) {
                $valid = false;
            }
            
            if($valid) {
                $widths_counts = GetWidthsCounts($current_combination);
                
                foreach (array_keys($target_widths_counts) as $key) {
                    if(isset($widths_counts[$key]) && $widths_counts[$key] > $target_widths_counts[$key]) {
                        $valid = false;
                    }
                }
            }
            
            if($valid) {
                array_push($combinations, $current_combination);
                WalkTargets($combinations, $current_combination, $targets, $targets_count, $width, $target_widths_counts);
            }
        }
    }
}

function GetWidthsSum($combination) {
    $sum = 0;
    
    foreach ($combination as $width) {
        $sum += intval($width);
    }
    
    return $sum;
}

function GetWidthsCounts($combination) {
    $widths_counts = array();
    foreach ($combination as $width) {
        if(!isset($widths_counts[$width])) {
            $widths_counts[$width] = 0;
        }
        
        $widths_counts[$width]++;
    }
    
    return $widths_counts;
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');
$cut_id = null;
$selected_is_pallet = null;
$selected_id = null;
$remainder = null;
$ordinal = null;
$prev_id = null;
$next_id = null;

$sql = "select rcs.rational_cut_id, rcs.selected_is_pallet, rcs.selected_id, rcs.remainder, "
        . "(select count(id) from rational_cut_stage where rational_cut_id = rcs.rational_cut_id and id <= rcs.id) ordinal, "
        . "(select max(id) from rational_cut_stage where rational_cut_id = rcs.rational_cut_id and id < rcs.id) prev_id, "
        . "(select min(id) from rational_cut_stage where rational_cut_id = rcs.rational_cut_id and id > rcs.id) next_id "
        . "from rational_cut_stage rcs where id=$id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $cut_id = $row['rational_cut_id'];
    $selected_is_pallet = $row['selected_is_pallet'];
    $selected_id = $row['selected_id'];
    $remainder = $row['remainder'];
    $ordinal = $row['ordinal'];
    $prev_id = $row['prev_id'];
    $next_id = $row['next_id'];
}

$brand_name = '';
$thickness = '';
$sql = "select brand_name, thickness from rational_cut where id = $cut_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $brand_name = $row['brand_name'];
    $thickness = $row['thickness'];
}

$sql = "select width, length from rational_cut_stage_stream where rational_cut_stage_id=$id order by id";
$grabber = new Grabber($sql);
$streams = $grabber->result;

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
    </head>
    <body>
        <?php
        include '../include/header_sklad.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/rational_cut/">К списку</a>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <h1>Раскрой <?=$cut_id ?>, этап <?=$ordinal ?></h1>
                </div>
                <div class="p-1">
                    <table>
                        <tr>
                            <td style="width: 100px;">
                                <?php if(!empty($prev_id)): ?>
                                <a class="btn btn-outline-dark" href="stage.php?id=<?=$prev_id ?>"><i class="fas fa-arrow-left"></i>&nbsp;Этап <?=($ordinal - 1) ?></a>
                                <?php endif; ?>
                            </td>
                            <td style="width: 100px;">
                                <?php if(!empty($next_id)): ?>
                                <a class="btn btn-outline-dark" href="stage.php?id=<?=$next_id ?>">Этап <?=($ordinal + 1) ?>&nbsp;<i class="fas fa-arrow-right"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                        <div class="form-group">
                            <label for="brand_name">Марка плёнки</label>
                            <select id="brand_name_disabled" name="brand_name_disabled" class="form-control" disabled="disabled">
                                <option value=""><?=$brand_name ?></option>
                            </select>
                            <input type="hidden" id="brand_name" name="brand_name" value="<?=$brand_name ?>" />
                        </div>
                        <div class="form-group">
                            <label for="thickness">Толщина</label>
                            <select id="thickness_disabled" name="thickness_disabled" class="form-control" disabled="disabled">
                                <?php
                                $weight = '';
                                $brand_name = addslashes($brand_name);
                                $sql = "select fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$brand_name' and fbv.thickness=$thickness";
                                $fetcher = new Fetcher($sql);
                                if($row = $fetcher->Fetch()) {
                                    $weight = $row[0];
                                }
                                ?>
                                <option value=""><?=$thickness ?> мкм <?=$weight ?> г/м<sup>2</sup></option>
                            </select>
                            <input type="hidden" id="thickness" name="thickness" value="<?=$thickness ?>" />
                        </div>
                        <?php
                        $i = 0;
                        foreach ($streams as $stream):
                        ?>
                        <div class="row">
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="width_<?=(++$i) ?>">Ширина, мм</label>
                                    <input type="text" class="form-control" disabled="disabled" value="<?=$stream['width'] ?>" />
                                    <input type="hidden" id="width_<?=$i ?>" name="width_<?=$i ?>" value="<?=$stream['width'] ?>" />
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="length_<?=$i ?>">Длина, м</label>
                                    <input type="text" class="form-control" disabled="disabled" value="<?=$stream['length'] ?>" />
                                    <input type="hidden" id="length_<?=$i ?>" name="length_<?=$i ?>" value="<?=$stream['length'] ?>" />
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="row mt-4">
                            <div class="col-5">
                                <div class="form-group">
                                    <button type="submit" id="rational_cut_submit" name="rational_cut_submit" class="btn btn-dark form-control">Рассчитать</button>
                                </div>
                            </div>
                            <?php if($selected_is_pallet !== null && $selected_id !== null && $next_id == null): ?>
                            <div class="col-5">
                                <div class="form-group">
                                    <button type="submit" id="next_stage_submit" name="next_stage_submit" class="btn btn-outline-dark form-control">Следующий этап</button>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <h2>Все комбинации</h2>
                    <?php
                    $sql = "select rcswc.sum, rcswc.remainder, rcsw.width, (select GROUP_CONCAT(`width` SEPARATOR ' + ') from rational_cut_stage_width_combination_element where rational_cut_stage_width_combination_id = rcswc.id) elements "
                            . "from rational_cut_stage_width_combination rcswc "
                            . "inner join rational_cut_stage_width rcsw on rcswc.rational_cut_stage_width_id = rcsw.id "
                            . "where rcsw.rational_cut_stage_id = $id "
                            . "order by rcsw.width";
                    $grabber = new Grabber($sql);
                    $result = $grabber->result;
                    
                    $widths = array();
                    
                    foreach ($result as $row) {
                        if(isset($widths[$row['width']])) {
                            $combinations = $widths[$row['width']];
                        }
                        else {
                            $combinations = array();
                        }
                        
                        $combination = array();
                        $combination['sum'] = $row['sum'];
                        $combination['remainder'] = $row['remainder'];
                        $combination['elements'] = $row['elements'];
                        array_push($combinations, $combination);
                        $widths[$row['width']] = $combinations;
                    }
                    
                    foreach (array_keys($widths) as $width_key):
                    ?>
                    <p class="font-weight-bold">Ширина: <?=$width_key ?> мм</p>
                    <?php
                    foreach ($widths[$width_key] as $combination) {
                        echo $combination['elements'].' (='.$combination['sum'].' мм), отход: '.$combination['remainder'].' мм';
                        echo '<br />';
                    }
                    ?>
                    <hr />
                    <?php endforeach; ?>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <h2>Рациональные комбинации</h2>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <input type="hidden" name="remainder_submit" value="1" />
                        <div class="input-group w-50 mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Отход</span>
                            </div>
                            <select id="remainder" name="remainder" class="form-control w-25" onchange="javascript: this.form.submit();">
                                <?php
                                $sql = "select distinct rcswc.remainder "
                                        . "from rational_cut_stage_width_combination rcswc "
                                        . "inner join rational_cut_stage_width rcsw on rcswc.rational_cut_stage_width_id = rcsw.id "
                                        . "where rcsw.rational_cut_stage_id = $id "
                                        . "order by rcswc.remainder asc";
                                $grabber = new Grabber($sql);
                                $result = $grabber->result;
                                $min_reminder = null;
                                
                                if(count($result) > 0) {
                                    $min_reminder = $result[0]['remainder'];
                                }
                                
                                foreach($result as $row):
                                    $selected = "";
                                    if($remainder == $row['remainder']) {
                                        $selected = " selected='selected'";
                                    }
                                ?>
                                <option<?=$selected ?>><?=$row['remainder'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-append">
                                <span class="input-group-text">мм</span>
                            </div>
                        </div>
                    </form>
                    <?php
                    $rac_remainder = $remainder;
                    
                    if($rac_remainder == null) {
                        $rac_remainder = $min_reminder;
                    }
                    
                    if(null !== $rac_remainder):
                    $sql = "select rcswc.sum, rcswc.remainder, rcsw.width, (select GROUP_CONCAT(`width` SEPARATOR ' + ') from rational_cut_stage_width_combination_element where rational_cut_stage_width_combination_id = rcswc.id) elements "
                            . "from rational_cut_stage_width_combination rcswc "
                            . "inner join rational_cut_stage_width rcsw on rcswc.rational_cut_stage_width_id = rcsw.id "
                            . "where rcsw.rational_cut_stage_id = $id and rcswc.remainder = $rac_remainder";
                    $grabber = new Grabber($sql);
                    $result = $grabber->result;
                    
                    $widths = array();
                    
                    foreach ($result as $row) {
                        if(isset($widths[$row['width']])) {
                            $combinations = $widths[$row['width']];
                        }
                        else {
                            $combinations = array();
                        }
                        
                        $combination = array();
                        $combination['sum'] = $row['sum'];
                        $combination['remainder'] = $row['remainder'];
                        $combination['elements'] = $row['elements'];
                        array_push($combinations, $combination);
                        $widths[$row['width']] = $combinations;
                    }
                    
                    foreach (array_keys($widths) as $width_key):
                    ?>
                    <p class="font-weight-bold">Ширина: <?=$width_key ?> мм</p>
                    <?php
                    foreach ($widths[$width_key] as $combination) {
                        echo $combination['elements'].' (='.$combination['sum'].' мм), отход: '.$combination['remainder'].' мм';
                        echo '<br />';
                    }
                    ?>
                    <p class="font-weight-bold mt-3">Плёнки:</p>
                    <table>
                        <?php
                        // Среди подходящих плёнок исключаем те, которые были уже выбраны в предядущих этапах
                        $sql = "select pr.id, concat('П', p.id, 'Р', pr.ordinal) nr, DATE_FORMAT(p.date, '%d.%m.%Y') date, pr.length, prsh.status_id "
                                . "from pallet_roll pr "
                                . "inner join pallet p on pr.pallet_id = p.id "
                                . "inner join film_brand fb on p.film_brand_id = fb.id "
                                . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                                . "where trim(fb.name) = '$brand_name' and p.thickness = $thickness and p.width = $width_key "
                                . "and pr.id not in (select selected_id from rational_cut_stage where id < $id and selected_is_pallet = 1 and rational_cut_id = (select rational_cut_id from rational_cut_stage where id = $id))";
                        $fetcher = new Fetcher($sql);
                        while ($row = $fetcher->Fetch()):
                        if($row['status_id'] == $free_status_id || empty($row['status_id'])):
                        $status = '';
                        $colour_style = '';
                        $status_id = $row['status_id'];
                        if(empty($status_id)) {
                            $status_id = $free_status_id;
                        }
                        
                        if(!empty($statuses[$status_id]['name'])) {
                            $status = $statuses[$status_id]['name'];
                        }
                    
                        if(!empty($statuses[$status_id]['colour'])) {
                            $colour = $statuses[$status_id]['colour'];
                            $colour_style = " color: $colour";
                        }
                        
                        $selected_color = "white";
                        if($selected_is_pallet !== null && $selected_id == $row['id']) {
                            $selected_color = "silver";
                        }
                        ?>
                        <tr style="background-color: <?=$selected_color ?>">
                            <td class="pr-3"><a href="<?=APPLICATION.'/pallet/roll.php?id='.$row['id'] ?>" target="_blank"><?=$row['nr'] ?></a></td>
                            <td class="pr-3"><?=$row['date'] ?></td>
                            <td class="pr-3"><?=$row['length'] ?> м</td>
                            <td class="pr-3" style="font-size: 10px;<?=$colour_style ?>"><?= mb_strtoupper($status) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="selected_is_pallet" value="1" />
                                    <input type="hidden" name="selected_id" value="<?=$row['id'] ?>" />
                                    <input type="hidden" name="remainder" value="<?=$rac_remainder ?>" />
                                    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                                    <button type="submit" class="btn btn-sm" name="select_submit">Выбрать</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                        endif;
                        endwhile;
                        
                        // Среди подходящих плёнок исключаем те, которые были уже выбраны в предядущих этапах
                        $sql = "select r.id, concat('Р', r.id) nr, DATE_FORMAT(r.date, '%d.%m.%Y') date, r.length, rsh.status_id "
                                . "from roll r "
                                . "inner join film_brand fb on r.film_brand_id = fb.id "
                                . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                                . "where trim(fb.name) = '$brand_name' and r.thickness = $thickness and r.width = $width_key "
                                . "and r.id not in (select selected_id from rational_cut_stage where id < $id and selected_is_pallet = 0 and rational_cut_id = (select rational_cut_id from rational_cut_stage where id = $id))";
                        $fetcher = new Fetcher($sql);
                        while ($row = $fetcher->Fetch()):
                        if($row['status_id'] == $free_status_id || empty($row['status_id'])):
                        $status = '';
                        $colour_style = '';
                        $status_id = $row['status_id'];
                        if(empty($status_id)) {
                            $status_id = $free_status_id;
                        }
                        
                        if(!empty($statuses[$status_id]['name'])) {
                            $status = $statuses[$status_id]['name'];
                        }
                    
                        if(!empty($statuses[$status_id]['colour'])) {
                            $colour = $statuses[$status_id]['colour'];
                            $colour_style = " color: $colour";
                        }
                        
                        $selected_color = "white";
                        if($selected_is_pallet !== null && $selected_id == $row['id']) {
                            $selected_color = "silver";
                        }
                        ?>
                        <tr style="background-color: <?=$selected_color ?>">
                            <td class="pr-3"><a href="<?=APPLICATION.'/roll/roll.php?id='.$row['id'] ?>" target="_blank"><?=$row['nr'] ?></a></td>
                            <td class="pr-3"><?=$row['date'] ?></td>
                            <td class="pr-3"><?=$row['length'] ?> м</td>
                            <td class="pr-3" style="font-size: 10px;<?=$colour_style ?>"><?= mb_strtoupper($status) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="selected_is_pallet" value="0" />
                                    <input type="hidden" name="selected_id" value="<?=$row['id'] ?>" />
                                    <input type="hidden" name="remainder" value="<?=$rac_remainder ?>" />
                                    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                                    <button type="submit" class="btn btn-sm" name="select_submit">Выбрать</button>
                                </form>
                            </td>
                        </tr>
                        <?php endif; endwhile; ?>
                    </table>
                    <hr />
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>