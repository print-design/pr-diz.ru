<?php
include '../include/topscripts.php';

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

// Обработка отправки формы
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
    
    // Удаляем результаты предыдущиго расчёта по данному этапу
    $sql = "delete from rational_cut_stage_width where rational_cut_stage_id = $id";
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
$ordinal = null;

$sql = "select rcs.rational_cut_id, (select count(id) from rational_cut_stage where rational_cut_id = rcs.rational_cut_id and id <= rcs.id) ordinal from rational_cut_stage rcs where id=$id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $cut_id = $row['rational_cut_id'];
    $ordinal = $row['ordinal'];
}

$brand_name = '';
$thickness = '';
$sql = "select brand_name, thickness from rational_cut where id = $cut_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $brand_name = $row['brand_name'];
    $thickness = $row['thickness'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include 'style.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_analytics.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/rational_cut/">К списку</a>
            <h1>Раскрой <?=$cut_id ?>, этап 1</h1>
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
                        $sql = "select width, length from rational_cut_stage_stream where rational_cut_stage_id=$id order by id";
                        $fetcher = new Fetcher($sql);
                        while ($row = $fetcher->Fetch()):
                        ?>
                        <div class="row">
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="width_<?=(++$i) ?>">Ширина, мм</label>
                                    <input type="text" class="form-control" disabled="disabled" value="<?=$row['width'] ?>" />
                                    <input type="hidden" id="width_<?=$i ?>" name="width_<?=$i ?>" value="<?=$row['width'] ?>" />
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="length_<?=$i ?>">Длина, м</label>
                                    <input type="text" class="form-control" disabled="disabled" value="<?=$row['length'] ?>" />
                                    <input type="hidden" id="length_<?=$i ?>" name="length_<?=$i ?>" value="<?=$row['length'] ?>" />
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <div class="form-group mt-4">
                            <button type="submit" id="rational_cut_submit" name="rational_cut_submit" class="btn btn-dark w-50">Рассчитать</button>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <h2>Все комбинации</h2>
                    <?php
                    $sql = "select rcswc.sum, rcswc.remainder, rcsw.width, (select GROUP_CONCAT(`width` SEPARATOR ' + ') from rational_cut_stage_width_combination_element where rational_cut_stage_width_combination_id = rcswc.id) elements "
                            . "from rational_cut_stage_width_combination rcswc "
                            . "inner join rational_cut_stage_width rcsw on rcswc.rational_cut_stage_width_id = rcsw.id "
                            . "where rcsw.rational_cut_stage_id = $id";
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
                    <p class="font-weight-bold">Ширина: <?=$width_key ?></p>
                    <?php
                    foreach ($widths[$width_key] as $combination) {
                        echo $combination['elements'].' (='.$combination['sum'].'), отход '.$combination['remainder'];
                        echo '<br />';
                    }
                    ?>
                    <hr />
                    <?php endforeach; ?>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <h2>Рациональные комбинации</h2>
                    <?php if(null !== filter_input(INPUT_POST, 'rational_cut_submit')): ?>
                    <p>
                        <?php
                        if(!empty($rational_combination) && !empty($rational_width)) {
                            echo implode(' + ', $rational_combination).' (='. array_sum($rational_combination).'), ширина'.$rational_width.', отход '.$min_waiste;
                        }
                        ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>