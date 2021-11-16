<?php 
$calculation_class = "";

if(isset($create_calculation_submit_class) && empty($create_calculation_submit_class)) {
    $calculation_class = " class='d-none'";
}
elseif(!empty ($id) && !empty ($date)) {
    // Курс доллара и евро
    $euro = null;
    $usd = null;
        
    $sql = "select euro, usd from currency where date <= '$date' order by date desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $euro = $row['euro'];
        $usd = $row['usd'];
    }
            
    if(empty($euro) || empty($usd)) {
        $error_message = "Не заданы курсы валют";
    }
        
    // Удельный вес
    $c_weight = null;
        
    if(!empty($other_weight)) {
        $c_weight = $other_weight;
    }
    elseif(!empty ($brand_name) && !empty ($thickness)) {
        $sql = "select fbv.weight "
                . "from film_brand_variation fbv "
                . "inner join film_brand fb on fbv.film_brand_id = fb.id "
                . "where fb.name = '$brand_name' and fbv.thickness = $thickness limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $c_weight = $row['weight'];
        }
    }
            
    if(empty($c_weight)) {
        $error_message = "Для данной толщины плёнки не задан удельный вес";
    }
    
    // Цена материала
    $c_price = null;
        
    if(!empty($other_price)) {
        $c_price = $other_price;
    }
    elseif(!empty ($brand_name) && !empty ($thickness)) {
        $sql = "select price, currency from film_price where brand_name = '$brand_name' and thickness = $thickness and date <= '$date' order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $c_price = $row['price'];
                    
            if($row['currency'] == USD) {
                $c_price *= $usd;
            }
            elseif($row['currency'] == EURO) {
                $c_price *= $euro;
            }
        }
    }
            
    if(empty($c_price)) {
        $error_message = "Для данной толщины плёнки не указана цена";
    }
        
    // Удельный вес ламинации 1
    $c_weight_lam1 = null;
        
    if(!empty($lamination1_other_weight)) {
        $c_weight_lam1 = $lamination1_other_weight;
    }
    else if(!empty ($lamination1_brand_name) && !empty ($lamination1_thickness)) {
        $sql = "select fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name = '$lamination1_brand_name' and fbv.thickness = $lamination1_thickness limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $c_weight_lam1 = $row['weight'];
        }
    }
            
    if(!empty($lamination1_brand_name) && !empty($lamination1_thickness) && empty($c_weight_lam1)) {
        $error_message = "Для данной толщина ламинации 1 не задан удельный вес";
    }
        
    // Цена ламинации 1
    $c_price_lam1 = null;
        
    if(!empty($lamination1_other_price)) {
        $c_price_lam1 = $lamination1_other_price;
    }
    elseif(!empty ($lamination1_brand_name) && !empty ($lamination1_thickness)) {
        $sql = "select price, currency from film_price where brand_name = '$lamination1_brand_name' and thickness = $lamination1_thickness and date <= '$date' order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $c_price_lam1 = $row['price'];
                    
            if($row['currency'] == USD) {
                $c_price_lam1 *= $usd;
            }
            else if($row['currency'] == EURO) {
                $c_price_lam1 *= $euro;
            }
        }
    }
            
    if(empty($c_price_lam1) && !empty($c_weight_lam1)) {
        $error_message = "Для данной толщины ламинации 1 не указана цена";
    }
        
    // Удельный вес ламинации 2
    $c_weight_lam2 = null;
        
    if(!empty($lamination2_other_weight)) {
        $c_weight_lam2 = $lamination2_other_weight;
    }
    else if(!empty ($lamination2_brand_name) && !empty ($lamination2_thickness)) {
        $sql = "select fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name = '$lamination2_brand_name' and fbv.thickness = $lamination2_thickness limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $c_weight_lam2 = $row['weight'];
        }
    }
            
    if(!empty($lamination2_brand_name) && !empty($lamination2_thickness) && empty($c_weight_lam2)) {
        $error_message = "Для данной толщины ламинации 2 не задан удельный вес";
    }
        
    // Цена ламинации 2
    $c_price_lam2 = null;
    
    if(!empty($lamination2_other_price)) {
        $c_price_lam2 = $lamination2_other_price;
    }
    else if(!empty ($lamination2_brand_name) && !empty ($lamination2_thickness)) {
        $sql = "select price, currency from film_price where brand_name = '$lamination2_brand_name' and thickness = $lamination2_thickness and date <= '$date' order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $c_price_lam2 = $row['price'];
                    
            if($row['currency'] == USD) {
                $c_price_lam2 *= $usd;
            }
            else if($row['currency'] == EURO) {
                $c_price_lam2 *= $euro;
            }
        }
    }
            
    if(empty($c_price_lam2) && !empty($c_weight_lam2)) {
        $error_message = "Для данной толщины ламинации 2 не указана цена";
    }
        
    // Данные о приладке (время приладки, метраж приладки, процент отходов)
    $tuning_times = array();
    $tuning_lengths = array();
    $tuning_waste_percents = array();
    
    $sql = "select machine_id, time, length, waste_percent "
            . "from norm_fitting "
            . "where date in (select max(date) from norm_fitting where date <= '$date' group by machine_id)";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()) {
        $tuning_times[$row['machine_id']] = $row['time'];
        $tuning_lengths[$row['machine_id']] = $row['length'];
        $tuning_waste_percents[$row['machine_id']] = $row['waste_percent'];
    }
    
    $laminator_tuning_time = null;
    $laminator_tuning_length = null;
    $laminator_tuning_waste_percent = null;
    
    $sql = "select time, length, waste_percent from norm_laminator_fitting order by id desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $laminator_tuning_time = $row['time'];
        $laminator_tuning_length = $row['length'];
        $laminator_tuning_waste_percent = $row['waste_percent'];
    }
    
    // Данные о машинах
    $machine_speeds = array();
    $machine_prices = array();
        
    $sql = "select machine_id, price, speed "
            . "from norm_machine "
            . "where date in (select max(date) from norm_machine where date <= '$date' group by machine_id)";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()) {
        $machine_prices[$row['machine_id']] = $row['price'];
        $machine_speeds[$row['machine_id']] = $row['speed'];
    }
    
    $laminator_price = null;
    $laminator_speed = null;
    
    $sql = "select price, speed from norm_laminator order by id desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $laminator_price = $row['price'];
        $laminator_speed = $row['speed'];
    }
    
    $machine_ids = array();
    $machine_shortnames = array();
    
    $sql = "select id, shortname from machine";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        $machine_ids[$row['shortname']] = $row['id'];
        $machine_shortnames[$row['id']] = $row['shortname'];
    }
    
    $machine_id = null;
    
    if(!empty($machine) && !empty($paints_count)) {
        if($machine == 'comiflex') {
            $machine_id = $machine_ids['comiflex'];
        }
        elseif($paints_count > 6) {
            $machine_id = $machine_ids['zbs3'];
        }
        else {
            $machine_id = $machine_ids['zbs1'];
        }
    }
    
    // Данные о форме
    $cliche_flint = null;
    $cliche_kodak = null;
    $cliche_tver = null;
    $cliche_film = null;
    $cliche_tver_coeff = null;
    $cliche_additional_size = null;
    $cliche_scotch = null;
        
    $sql = "select flint, flint_currency, kodak, kodak_currency, tver, tver_currency, film, film_currency, tver_coeff, overmeasure, scotch, scotch_currency "
            . "from norm_form where date <= '$date' order by date desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $cliche_flint = $row['flint'];
            
        if($row['flint_currency'] == USD) {
            $cliche_flint *= $usd;
        }
        else if($row['flint_currency'] == EURO) {
            $cliche_flint *= $euro;
        }
            
        $cliche_kodak = $row['kodak'];
            
        if($row['kodak_currency'] == USD) {
            $cliche_kodak *= $usd;
        }
        else if($row['kodak_currency'] == EURO) {
            $cliche_kodak *= $euro;
        }
            
        $cliche_tver = $row['tver'];
            
        if($row['tver_currency'] == USD) {
            $cliche_tver *= $usd;
        }
        else if($row['tver_currency'] == EURO) {
            $cliche_tver *= $euro;
        }
            
        $cliche_film = $row['film'];
                
        if($row['film_currency'] == USD) {
            $cliche_film *= $usd;
        }
        if($row['film_currency'] == EURO) {
            $cliche_film *= $euro;
        }
            
        $cliche_tver_coeff = $row['tver_coeff'];
        $cliche_additional_size = $row['overmeasure'];
            
        $cliche_scotch = $row['scotch'];
        
        if($row['scotch_currency'] == USD) {
            $cliche_scotch *= $usd;
        }
        if($row['scotch_currency'] == EURO) {
            $cliche_scotch *= $euro;
        }
    }
    
    // Данные о красках
    $paint_c = null;
    $paint_c_expense = null;
    $paint_m = null;
    $paint_m_expense = null;
    $paint_y = null;
    $paint_y_expense = null;
    $paint_k = null;
    $paint_k_expense = null;
    $paint_white = null;
    $paint_white_expense = null;
    $paint_panton = null;
    $paint_panton_expense = null;
    $paint_lacquer = null;
    $paint_lacquer_expense = null;
    $paint_paint_solvent = null;
    $paint_solvent_etoxipropanol = null;
    $paint_solvent_flexol82 = null;
    $paint_lacquer_solvent = null;
    $paint_min_price = null;
    
    $sql = "select c, c_currency, c_expense, m, m_currency, m_expense, y, y_currency, y_expense, k, k_currency, k_expense, white, white_currency, white_expense, panton, panton_currency, panton_expense, lacquer, lacquer_currency, lacquer_expense, paint_solvent, solvent_etoxipropanol, solvent_etoxipropanol_currency, solvent_flexol82, solvent_flexol82_currency, lacquer_solvent, min_price "
            . "from norm_paint where date <= '$date' order by date desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $paint_c = $row['c'];
                
        if($row['c_currency'] == USD) {
            $paint_c *= $usd;
        }
        else if($row['c_currency'] == EURO) {
            $paint_c *= $euro;
        }
                
        $paint_c_expense = $row['c_expense'];
        $paint_m = $row['m'];
                
        if($row['m_currency'] == USD) {
            $paint_m *= $usd;
        }
        else if($row['m_currency'] == EURO) {
            $paint_m *= $euro;
        }
                
        $paint_m_expense = $row['m_expense'];
        $paint_y = $row['y'];
                
        if($row['y_currency'] == USD) {
            $paint_y *= $usd;
        }
        else if($row['y_currency'] == EURO) {
            $paint_y *= $euro;
        }
                
        $paint_y_expense = $row['y_expense'];
        $paint_k = $row['k'];
                
        if($row['k_currency'] == USD) {
            $paint_k *= $usd;
        }
        else if($row['k_currency'] == EURO) {
            $paint_k *= $euro;
        }
                
        $paint_k_expense = $row['k_expense'];
        $paint_white = $row['white'];
                
        if($row['white_currency'] == USD) {
            $paint_white *= $usd;
        }
        else if($row['white_currency'] == EURO) {
            $paint_white *= $euro;
        }
                
        $paint_white_expense = $row['white_expense'];
        $paint_panton = $row['panton'];
                
        if($row['panton_currency'] == USD) {
            $paint_panton *= $usd;
        }
        else if($row['panton_currency'] == EURO) {
            $paint_panton *= $euro;
        }
                
        $paint_panton_expense = $row['panton_expense'];
        $paint_lacquer = $row['lacquer'];

        if($row['lacquer_currency'] == USD) {
            $paint_lacquer *= $usd;
        }
        else if($row['lacquer_currency'] == EURO) {
            $paint_lacquer *= $euro;
        }
                
        $paint_lacquer_expense = $row['lacquer_expense'];
        $paint_paint_solvent = $row['paint_solvent'];
        $paint_solvent_etoxipropanol = $row['solvent_etoxipropanol'];
                
        if($row['solvent_etoxipropanol_currency'] == USD) {
            $paint_solvent_etoxipropanol *= $usd;
        }
        else if($row['solvent_etoxipropanol_currency'] == EURO) {
            $paint_solvent_etoxipropanol *= $euro;
        }
                
        $paint_solvent_flexol82 = $row['solvent_flexol82'];
            
        if($row['solvent_flexol82_currency'] == USD) {
            $paint_solvent_flexol82 *= $usd;
        }
        else if($row['solvent_flexol82_currency'] == EURO) {
            $paint_solvent_flexol82 *= $euro;
        }
                
        $paint_lacquer_solvent = $row['lacquer_solvent'];
        $paint_min_price = $row['min_price'];
    }
        
    // Данные о клее при ламинации
    $glue_price = null;
    $glue_expense = null;
    $glue_expense_pet = null;
    $glue_solvent_price = null;
    $glue_glue_part = null;
    $glue_solvent_part = null;
    
    // Удельная стоимость клеевого раствора
    $glue_solvent_g = null;
        
    $sql = "select glue, glue_currency, glue_expense, glue_expense_pet, solvent, solvent_currency, glue_part, solvent_part from norm_glue where date <= '$date' order by date desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $glue_price = $row['glue'];
            
        if($row['glue_currency'] == USD) {
            $glue_price *= $usd;
        }
        else if($row['glue_currency'] == EURO) {
            $glue_price *= $euro;
        }

        $glue_expense = $row['glue_expense'];
        $glue_expense_pet = $row['glue_expense_pet'];
        $glue_solvent_price = $row['solvent'];
            
        if($row['solvent_currency'] == USD) {
            $glue_solvent_price *= $usd;
        }
        else if($row['solvent_currency'] == EURO) {
            $glue_solvent_price *= $euro;
        }
                
        $glue_glue_part = $row['glue_part'];
        $glue_solvent_part = $row['solvent_part'];
        
        // Удельная стоимость клеевого раствора
        $glue_solvent_g = ($glue_price * $glue_glue_part / ($glue_glue_part + $glue_solvent_part)) + ($glue_solvent_price * $glue_solvent_part / ($glue_glue_part + $glue_solvent_part));
    }

    // Результаты расчёта
    $pure_area = null;    $pure_width = null;    $pure_length = null;
    $pure_length_lam = null;    $dirty_length = null;    $dirty_width = null;
    $dirty_area = null;    $pure_weight = null;    $dirty_weight = null;
    $material_price = null;    $print_time = null;    $tuning_time = null;
    $print_tuning_time = null;    $print_price = null;    $cliche_area = null;
    $cliche_flint_price = null;    $cliche_kodak_price = null;    $cliche_tver_price = null;
    $cliche_price = null;    $paint_price = null;    $pure_weight_lam1 = null;
    $dirty_weight_lam1 = null;    $price_lam1_material = null;    $price_lam1_glue = null;
    $price_lam1_work = null;    $pure_weight_lam2 = null;    $dirty_weight_lam2 = null;
    $price_lam2_material = null;    $price_lam2_glue = null;    $price_lam2_work = null;
    $price_lam_total = null;    $pure_weight_total = null;    $dirty_weight_total = null;
    $cost_no_cliche = null;    $cost_with_cliche = null;    $cost_no_cliche_kg = null;
    $cost_with_cliche_kg = null;    $cost_no_cliche_thing = null;    $cost_with_cliche_thing = null;
    
    $sql = "select pure_area, pure_width, pure_length, pure_length_lam, "
            . "dirty_length, dirty_width, dirty_area, pure_weight, dirty_weight, material_price, print_time, tuning_time, "
            . "print_tuning_time, print_price, cliche_area, cliche_flint_price, cliche_kodak_price, cliche_tver_price, "
            . "cliche_price, paint_price, pure_weight_lam1, dirty_weight_lam1, "
            . "price_lam1_material, price_lam1_glue, price_lam1_work, pure_weight_lam2, dirty_weight_lam2, price_lam2_material, "
            . "price_lam2_glue, price_lam2_work, price_lam_total, pure_weight_total, dirty_weight_total, cost_no_cliche, "
            . "cost_with_cliche, cost_no_cliche_kg, cost_with_cliche_kg, cost_no_cliche_thing, cost_with_cliche_thing"
            . " from calculation_result where calculation_id = $id order by id desc limit 1";
    $fetcher = new Fetcher($sql);

    if($row = $fetcher->Fetch()) {
        $pure_area = $row['pure_area'];
        $pure_width = $row['pure_width'];
        $pure_length = $row['pure_length'];
        $pure_length_lam = $row['pure_length_lam'];
        $dirty_length = $row['dirty_length'];
        $dirty_width = $row['dirty_width'];
        $dirty_area = $row['dirty_area'];
        $pure_weight = $row['pure_weight'];
        $dirty_weight = $row['dirty_weight'];
        $material_price = $row['material_price'];
        $print_time = $row['print_time'];
        $tuning_time = $row['tuning_time'];
        $print_tuning_time = $row['print_tuning_time'];
        $print_price = $row['print_price'];
        $cliche_area = $row['cliche_area'];
        $cliche_flint_price = $row['cliche_flint_price'];
        $cliche_kodak_price = $row['cliche_kodak_price'];
        $cliche_tver_price = $row['cliche_tver_price'];
        $cliche_price = $row['cliche_price'];
        $paint_price = $row['paint_price'];
        $pure_weight_lam1 = $row['pure_weight_lam1'];
        $dirty_weight_lam1 = $row['dirty_weight_lam1'];
        $price_lam1_material = $row['price_lam1_material'];
        $price_lam1_glue = $row['price_lam1_glue'];
        $price_lam1_work = $row['price_lam1_work'];
        $pure_weight_lam2 = $row['pure_weight_lam2'];
        $dirty_weight_lam2 = $row['dirty_weight_lam2'];
        $price_lam2_material = $row['price_lam2_material'];
        $price_lam2_glue = $row['price_lam2_glue'];
        $price_lam2_work = $row['price_lam2_work'];
        $price_lam_total = $row['price_lam_total'];
        $pure_weight_total = $row['pure_weight_total'];
        $dirty_weight_total = $row['dirty_weight_total'];
        $cost_no_cliche = $row['cost_no_cliche'];
        $cost_with_cliche = $row['cost_with_cliche'];
        $cost_no_cliche_kg = $row['cost_no_cliche_kg'];
        $cost_with_cliche_kg = $row['cost_with_cliche_kg'];
        $cost_no_cliche_thing = $row['cost_no_cliche_thing'];
        $cost_with_cliche_thing = $row['cost_with_cliche_thing'];
    }
}
?>
<div id="calculation"<?=$calculation_class ?> style="position: absolute; top: 0px; bottom: auto;">
    <h1>Расчет</h1>
    <div class="row">
        <div class="col-3">
            <div class="p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Наценка</div>
                <?php if(IsInRole(array('technologist', 'dev', 'manager', 'administrator'))): ?>
                <div class="input-group">
                    <input type="text" id="extracharge" name="extracharge" data-id="<?=$id ?>" style="width: 35px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" value="<?=$extracharge ?>" required="required" />
                    <div class="input-group-append" style="height: 28px;">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <?php else: ?>
                <?=$extracharge ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Курс евро</div>
                <?=number_format($euro, 2, ',', ' ') ?>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Курс доллара</div>
                <?=number_format($usd, 2, ',', ' ') ?>
            </div>
        </div>
        <div class="col-3 text-right">
            <?php if(IsInRole(array('technologist', 'dev', 'manager', 'administrator'))): ?>
            <form method="post" action="<?=APPLICATION ?>/calculation/export.php">
                <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                <button type="submit" class="btn btn-dark" id="export_calculation_submit" name="export_calculation_submit">Экспорт</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="mt-3">
        <h2>Материалы</h2>
    </div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <h3>Плёнка</h3>
                    <div class="param-name">Площадь тиража чистая</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_area, 3, ",", " "), "0"), ",") ?> м<sup>2</sup></div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <h3>Печать</h3>
                    <div class="param-name">Время печати тиража без приладки</div>
                    <div class="value"><?=rtrim(rtrim(number_format($print_time, 3, ",", " "), "0"), ",") ?> ч</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination1_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <h3>Ламинация</h3>
                    <div class="param-name">Вес материала ламинации 1 чистый</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_weight_lam1, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">Ширина тиража обрезная</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_width, 3, ",", " "), "0"), ",") ?> мм</div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Время приладки</div>
                    <div class="value"><?=rtrim(rtrim(number_format($tuning_time, 3, ",", " "), "0"), ",") ?> ч</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination1_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Вес материала ламинации 1 с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_weight_lam1, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">Длина тиража чистая</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_length, 3, ",", " "), "0"), ",") ?> м</div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Время печати с приладкой</div>
                    <div class="value"><?=rtrim(rtrim(number_format($print_tuning_time, 3, ",", " "), "0"), ",") ?> ч</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination1_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость материала ламинации 1</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam1_material, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">Длина тиража чистая с ламинацией</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_length_lam, 3, ",", " "), "0"), ",") ?> м</div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость печати</div>
                    <div class="value"><?=rtrim(rtrim(number_format($print_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination1_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость клеевого раствора 1</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam1_glue, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">Длина тиража с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_length, 3, ",", " "), "0"), ",") ?> м</div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Площадь печатной формы</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cliche_area, 3, ",", " "), "0"), ",") ?> м<sup>2</sup></div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination1_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость процесса ламинации 1</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam1_work, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">Ширина тиража с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_width, 3, ",", " "), "0"), ",") ?> мм</div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость 1 новой формы Флинт</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cliche_flint_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination2_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Вес материала ламинации 2 чистый</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_weight_lam2, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">Площадь тиража с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_area, 3, ",", " "), "0"), ",") ?> м<sup>2</sup></div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость 1 новой формы Кодак</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cliche_kodak_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination2_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Вес материала ламинации 2 с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_weight_lam2, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">Вес материала печати чистый</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_weight, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость 1 новой формы Тверь</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cliche_tver_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination2_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость материала ламинации 2</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam2_material, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">Вес материала печати с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_weight, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость комплекта печатных форм</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cliche_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination2_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость клеевого раствора 2</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam2_glue, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">Стоимость материала печати</div>
                    <div class="value"><?=rtrim(rtrim(number_format($material_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость скотча для наклейки форм</div>
                    <div class="value"><?=rtrim(rtrim(number_format((($cliche_scotch ?? 0) * ($paints_count ?? 0) * ($cliche_area ?? 0) / 10000), 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination2_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость процесса ламинации 2</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam2_work, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;"></div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1">
                    <div class="param-name">Длина приладки</div>
                    <div class="value"><?= rtrim(rtrim(number_format($tuning_lengths[$machine_id], 3, ",", " "), "0"), ",") ?> м</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination1_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Длина приладки</div>
                    <div class="value"><?= rtrim(rtrim(number_format($laminator_tuning_length, 3, ",", " "), "0"), ",") ?> м</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;"></div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Стоимость краски + лака + растворителя</div>
                    <div class="value"><?=rtrim(rtrim(number_format($paint_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
                <?php if(!empty($lamination1_brand_name)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Итого себестоимость ламинации</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam_total, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-3">
            <h2>Вес материала готовой продукции</h2>
        </div>
        <div class="d-table w-100">
            <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                <div class="param-name">Чистый</div>
                <div class="value"><?=rtrim(rtrim(number_format($pure_weight_total, 3, ",", " "), "0"), ",") ?> кг</div>
            </div>
            <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                <div class="param-name">С отходами</div>
                <div class="value"><?=rtrim(rtrim(number_format($dirty_weight_total, 3, ",", " "), "0"), ",") ?> кг</div>
            </div>
            <div class="d-table-cell pb-1 pr-4" style="width: 33%;"></div>
        </div>
        <div class="mt-3">
            <h2>Себестоимость</h2>
        </div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell" style="width: 33%;">
                    <h3>Всего</h3>
                </div>
                <?php if($unit == "kg" && !empty($quantity)): ?>
                <div class="d-table-cell">
                    <h3>За 1 кг</h3>
                </div>
                <?php elseif($unit == "thing" && !empty($quantity)): ?>
                <div class="d-table-cell">
                    <h3>За 1 шт</h3>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">Без форм</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_no_cliche, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php if($unit == "kg" && !empty($quantity)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Без форм</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_no_cliche_kg, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php elseif($unit == "thing" && !empty($quantity)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">Без форм</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_no_cliche_thing, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1 pr-4" style="width: 33%;">
                    <div class="param-name">С формами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_with_cliche, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php if($unit == "kg" && !empty($quantity)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">С формами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_with_cliche_kg, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php elseif($unit == "thing" && !empty($quantity)): ?>
                <div class="d-table-cell pb-1 pr-4">
                    <div class="param-name">С формами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_with_cliche_thing, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name) || $work_type_id == 2):
        ?>
        <button type="button" class="btn btn-light" id="show_costs" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать подробности</button>
        <div id="costs" class="d-none" style="word-break: break-all; word-wrap: break-word;">
            <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть подробности</button>
            <h2 class="mt-2">Подробности</h2>
            
            <div class="param-name">Площадь тиража чистая</div>
            <div class="algorithm">если в кг: 1000 * объём заказа / удельный вес материала и ламинации</div>
            <div class="algorithm">если в шт: ширина ручья / 1000 * длина этикетки вдоль рапорта вала / 1000 * количество этикеток в заказе</div>
            <?php if($unit == 'kg'): ?>
            <div class="value mb-2"><?="1000 * $quantity / (".$c_weight.(empty($c_weight_lam1) ? "" : " + ".$c_weight_lam1).(empty($c_weight_lam2) ? "" : " + ".$c_weight_lam2).") = ".$pure_area ?></div>
            <?php elseif($unit == 'thing'): ?>
            <div class="value mb-2"><?="$stream_width / 1000 * $length / 1000 * $quantity = $pure_area" ?></div>
            <?php endif; ?>
            
            <div class="param-name">Ширина тиража обрезная</div>
            <div class="algorithm">ширина ручья * количество ручьёв</div>
            <div class="value mb-2"><?="$stream_width * $streams_count = $pure_width" ?></div>
            
            <div class="param-name">Длина тиража чистая</div>
            <div class="algorithm">площадь тиража чистая / ширина тиража обрезная * 1000</div>
            <div class="value mb-2"><?="$pure_area / $pure_width * 1000 = $pure_length" ?></div>
            
            <div class="param-name">Длина тиража чистая с ламинацией</div>
            <div class="algorithm">длина тиража чистая * (процент отходов для ламинатора + 100) / 100</div>
            <div class="value mb-2"><?="$pure_length * ($laminator_tuning_waste_percent + 100) / 100 = $pure_length_lam" ?></div>
            
            <div class="param-name">Длина тиража с отходами</div>
            <div class="algorithm">если есть печать: длина тиража чистая + (длина тиража чистая * процент отхода машины / 100</div>
            <div class="algorithm">+ длина приладки для машины * число красок)</div>
            <div class="algorithm">если нет печати, но есть ламинация: длина тиража чистая с ламинацией + длина приладки ламинации</div>
            <?php if(!empty($machine_id) && !empty($pure_length) && !empty($paints_count) && !empty($tuning_waste_percents[$machine_id])): ?>
            <div class="value mb-2"><?="$pure_length + ($pure_length * $tuning_waste_percents[$machine_id] / 100 + $tuning_lengths[$machine_id] * $paints_count) = $dirty_length" ?></div>
            <?php elseif(!empty ($lamination1_brand_name) && !empty ($pure_length_lam) && !empty ($laminator_tuning_length)): ?>
            <div class="value mb-2"><?="$pure_length_lam + $laminator_tuning_length = $dirty_length" ?></div>
            <?php endif; ?>
            
            <div class="param-name">Ширина тиража с отходами</div>
            <div class="algorithm">с лыжами: ширина тиража обрезная + ширина лыж</div>
            <div class="algorithm">без лыж: ширина тиража обрезная</div>
            <div class="algorithm">затем отругляем ширину тиража с отходами до возможности деления на 5 без остатка</div>
            <?php if($no_ski): ?>
            <div class="value mb-2"><?="$pure_width = $dirty_width" ?></div>
            <?php else: ?>
            <div class="value mb-2"><?="$pure_width + $ski = $dirty_width" ?></div>
            <?php endif; ?>
            
            <div class="param-name">Площадь тиража с отходами</div>
            <div class="algorithm">длина тиража с отходами * ширина тиража с отходами / 1000</div>
            <div class="value mb-2"><?="$dirty_length * $dirty_width / 1000 = $dirty_area" ?></div>
            
            <div class="param-name">Вес материала печати чистый</div>
            <div class="algorithm">площадь тиража чистая * удельный вес материала / 1000</div>
            <div class="value mb-2"><?="$pure_area * $c_weight / 1000 = $pure_weight" ?></div>
            
            <div class="param-name">Вес материала печати с отходами</div>
            <div class="algorithm">площадь тиража с отходами * удельный вес материала / 1000</div>
            <div class="value mb-2"><?="$dirty_area * $c_weight / 1000 = $dirty_weight" ?></div>
            
            <div class="param-name">Стоимость материала печати</div>
            <div class="algorithm">Если сырьё заказчика: 0</div>
            <div class="algorithm">Иначе: вес материала печати с отходами * цена материала за 1 кг</div>
            <?php if($customers_material): ?>
            <div class="value mb-2">0</div>
            <?php else: ?>
            <div class="value mb-2"><?="$dirty_weight * $c_price = $material_price" ?></div>
            <?php endif; ?>
            
            <!-------------------------------------------------------------------->
            
            <?php if($work_type_id == 2): ?>
            <div class="param-name">Время печати тиража без приладки</div>
            <div class="algorithm">длина тиража чистая / 1000 / скорость работы флекс машины</div>
            <div class="value mb-2"><?="$pure_length / 1000 / $machine_speeds[$machine_id] = $print_time" ?></div>
            
            <div class="param-name">Время приладки</div>
            <div class="algorithm">время приладки каждой краски / 60 * число красок</div>
            <div class="value mb-2"><?="$tuning_times[$machine_id] / 60 * $paints_count = $tuning_time" ?></div>
            
            <div class="param-name">Время печати с приладкой</div>
            <div class="algorithm">время печати + время приладки</div>
            <div class="value mb-2"><?="$print_time + $tuning_time = $print_tuning_time" ?></div>
            
            <div class="param-name">Стоимость печати</div>
            <div class="algorithm">время печати с приладкой * стоимость работы машины</div>
            <div class="value mb-2"><?="$print_tuning_time * $machine_prices[$machine_id] = $print_price" ?></div>
            
            <div class="param-name">Площадь печатной формы</div>
            <div class="algorithm">(припуск * 2 + ширина тиража с отходами / 1000 * 100) * (припуск * 2 + рапорт вала / 10)</div>
            <div class="value mb-2"><?="($cliche_additional_size * 2 + $dirty_width / 1000 * 100) * ($cliche_additional_size * 2 + $raport / 10) = $cliche_area" ?></div>
            
            <div class="param-name">Стоимость 1 печатной формы Флинт</div>
            <div class="algorithm">площадь печатной формы * стоимость 1 см2 формы</div>
            <div class="value mb-2"><?="$cliche_area * $cliche_flint = $cliche_flint_price" ?></div>
            
            <div class="param-name">Стоимость 1 печатной формы Кодак</div>
            <div class="algorithm">площадь печатной формы * стоимость 1 см2 формы</div>
            <div class="value mb-2"><?="$cliche_area * $cliche_kodak = $cliche_kodak_price" ?></div>
            
            <div class="param-name">Стоимость 1 печатной формы Тверь</div>
            <div class="algorithm">площадь печатной формы</div>
            <div class="algorithm">* (стоимость 1 см2 формы + стоимость 1 см2 плёнок * коэфф. удорожания для тверских форм)</div>
            <div class="value mb-2"><?="$cliche_area * ($cliche_tver + $cliche_film * $cliche_tver_coeff) = $cliche_tver_price" ?></div>
            
            <div class="param-name">Стоимость комплекта печатных форм</div>
            <div class="algorithm">сумма стоимости форм для каждой краски</div>
            <?php
            $cliche_price = 0;
            for($i=1; $i<=8; $i++) {
                if(!empty($paints_count) && $paints_count >= $i) {
                    $paint_var = "paint_$i";
                    $cliche_var = "form_$i";
                    if(!empty($$paint_var)) {
                        if($$cliche_var == 'old') {
                            echo "<div class='value mb-2'>$cliche_price + 0 = ".$cliche_price."</div>";
                        }
                        elseif($$cliche_var == 'flint') {
                            echo "<div class='value mb-2'>$cliche_price + $cliche_flint_price = ".($cliche_price + $cliche_flint_price)."</div>";
                            $cliche_price += $cliche_flint_price;
                        }
                        elseif($$cliche_var == 'kodak') {
                            echo "<div class='value mb-2'>$cliche_price + $cliche_kodak_price = ".($cliche_price + $cliche_kodak_price)."</div>";
                            $cliche_price += $cliche_kodak_price;
                        }
                        elseif($$cliche_var == 'tver') {
                            echo "<div class='value mb-2'>$cliche_price + $cliche_tver_price = ".($cliche_price + $cliche_tver_price)."</div>";
                            $cliche_price += $cliche_tver_price;
                        }
                    }
                }
            }
            ?>
            
            <div class="param-name">Стоимость скотча для наклейки форм</div>
            <div class="algorithm">стоимость скотча для наклейки форм * число красок * площадь печатной формы / 10000</div>
            <div class="value mb-2"><?=($cliche_scotch ?? 0)." * ".($paints_count ?? 0)." * ".($cliche_area ?? 0)." / 10000 = ".(($cliche_scotch ?? 0) * ($paints_count ?? 0) * ($cliche_area ?? 0) / 10000) ?></div>
            
            <div class="param-name">Стоимость краски + лака + растворителя</div>
            <div class="algorithm">Для каждой краски:</div>
            <div class="algorithm">1) Площадь запечатки (м2) = площадь тиража с отходами * процент краски / 100</div>
            <div class="algorithm">2) Количество краски (кг) = площадь запечатки * расход краски / 1000</div>
            <div class="algorithm">3) Стоимость краски (неразведённой, руб) = количество краски * стоимость краски за 1 кг</div>
            <div class="algorithm">(Проверяем, чтобы эта цифра была не меньше минимальной стоимости.)</div>
            <div class="algorithm">Если меньше, то вместо неё ставим минимальную.)</div>
            <div class="algorithm">4) Стоимость растворителя = количество краски * стоимость растворителя за 1 кг</div>
            <div class="algorithm">5) Стоимость разведённой краски =</div>
            <div class="algorithm">(стоимость краски * процент краски / 100) + (стоимость растворителя * (100 - процент краски) / 100)</div>
            <?php
            $paint_price = 0;
            
            for($i=1; $i<=8; $i++) {
                if(!empty($paints_count) && $paints_count >= $i) {
                    $paint_var = "paint_$i";
                    $percent_var = "percent_$i";
                    $cmyk_var = "cmyk_$i";
                
                    if(!empty($$paint_var)) {
                        // Площадь запечатки, м2
                        // площадь тиража с отходами * процент краски / 100
                        $paint_area = $dirty_area * $$percent_var / 100;
                        echo "<div class='value mb-2'>1) $dirty_area * ".$$percent_var." / 100 = $paint_area</div>";
                    
                        // Расход краски, г/м2
                        $paint_expense_final = 0;
                    
                        // Стоимость краски за 1 кг, руб
                        $paint_price_final = 0;
                    
                        // Стоимость растворителя за 1 кг, руб
                        $solvent_price_final = 0;
                    
                        // Процент краски по отношению к растворителю
                        $paint_solvent_final = 0;
                    
                        switch ($$paint_var) {
                            case CMYK:
                                switch ($$cmyk_var) {
                                    case CYAN:
                                        $paint_expense_final = $paint_c_expense;
                                        $paint_price_final = $paint_c;
                                        $solvent_price_final = $machine_shortnames[$machine_id] == 'comiflex' ? $paint_solvent_flexol82 : $paint_solvent_etoxipropanol;
                                        $paint_solvent_final = $paint_paint_solvent;
                                        break;
                                    case MAGENTA:
                                        $paint_expense_final = $paint_m_expense;
                                        $paint_price_final = $paint_m;
                                        $solvent_price_final = $machine_shortnames[$machine_id] == 'comiflex' ? $paint_solvent_flexol82 : $paint_solvent_etoxipropanol;
                                        $paint_solvent_final = $paint_paint_solvent;
                                        break;
                                    case YELLOW:
                                        $paint_expense_final = $paint_y_expense;
                                        $paint_price_final = $paint_y;
                                        $solvent_price_final = $machine_shortnames[$machine_id] == 'comiflex' ? $paint_solvent_flexol82 : $paint_solvent_etoxipropanol;
                                        $paint_solvent_final = $paint_paint_solvent;
                                        break;
                                    case KONTUR:
                                        $paint_expense_final = $paint_k_expense;
                                        $paint_price_final = $paint_k;
                                        $solvent_price_final = $machine_shortnames[$machine_id] == 'comiflex' ? $paint_solvent_flexol82 : $paint_solvent_etoxipropanol;
                                        $paint_solvent_final = $paint_paint_solvent;
                                        break;
                                };
                                break;
                            case PANTON:
                                $paint_expense_final = $paint_panton_expense;
                                $paint_price_final = $paint_panton;
                                $solvent_price_final = $machine_shortnames[$machine_id] == 'comiflex' ? $paint_solvent_flexol82 : $paint_solvent_etoxipropanol;
                                $paint_solvent_final = $paint_paint_solvent;
                                break;
                            case WHITE:
                                $paint_expense_final = $paint_white_expense;
                                $paint_price_final = $paint_white;
                                $solvent_price_final = $machine_shortnames[$machine_id] == 'comiflex' ? $paint_solvent_flexol82 : $paint_solvent_etoxipropanol;
                                $paint_solvent_final = $paint_paint_solvent;
                                break;
                            case LACQUER:
                                $paint_expense_final = $paint_lacquer_expense;
                                $paint_price_final = $paint_lacquer;
                                $solvent_price_final = $paint_solvent_flexol82;
                                $paint_solvent_final = $paint_lacquer_solvent;
                                break;
                        }
                    
                        // Количество краски, кг
                        // площадь запечатки * расход краски / 1000
                        $paint_quantity = $paint_area * $paint_expense_final / 1000;
                        echo "<div class='value mb-2'>2) $paint_area * $paint_expense_final / 1000 = $paint_quantity</div>";
                    
                        // Стоимость неразведённой краски, руб
                        // количество краски * стоимость краски за 1 кг
                        $paint_price_sum = $paint_quantity * $paint_price_final;
                        echo "<div class='value mb-2'>3) $paint_quantity * $paint_price_final = $paint_price_sum</div>";
                    
                        // Проверяем, чтобы стоимость была не меньше минимальной стоимости
                        if($paint_price_sum < $paint_min_price) {
                            $paint_price_sum = $paint_min_price;
                        }
                    
                        // Стоимость растворителя
                        // количество краски * стоимость растворителя за 1 кг
                        $solvent_price_sum = $paint_quantity * $solvent_price_final;
                        echo "<div class='value mb-2'>4) $paint_quantity * $solvent_price_final = $solvent_price_sum</div>";
                    
                        // Стоимость разведённой краски
                        // (стоимость краски * процент краски / 100) + (стоимость растворителя * (100 - процент краски) / 100)
                        $paint_solvent_price_sum = ($paint_price_sum * $paint_solvent_final / 100) + ($solvent_price_sum * (100 - $paint_solvent_final) / 100);
                        echo "<div class='value mb-2'>5) ($paint_price_sum * $paint_solvent_final / 100) + ($solvent_price_sum * (100 - $paint_solvent_final) / 100) = $paint_solvent_price_sum</div>";
                    
                        echo "<div class='value mb-2'>$paint_solvent_price_sum + $paint_price = ".($paint_solvent_price_sum + $paint_price)."</div><div>---</div>";
                        $paint_price += $paint_solvent_price_sum;
                    }
                }
            }
            ?>
            
            <?php endif; ?>
            
            <!-------------------------------------------------------------------->
            
            <?php if(!empty($lamination1_brand_name)): ?>
            <div class="param-name">Удельная стоимость клеевого раствора</div>
            <div class="algorithm">(стоимость клея * доля клея / (доля клея + доля растворителя)) </div>
            <div class="algorithm">+ (стоимость растворителя * доля растворителя / (доля клея + доля растворителя))</div>
            <div class="value mb-2"><?="($glue_price * $glue_glue_part / ($glue_glue_part + $glue_solvent_part)) + ($glue_solvent_price * $glue_solvent_part / ($glue_glue_part + $glue_solvent_part)) = $glue_solvent_g" ?></div>
            
            <div class="param-name">Вес материала ламинации 1 чистый</div>
            <div class="algorithm">площадь тиража чистая * удельный вес ламинации 1 / 1000</div>
            <div class="value mb-2"><?="$pure_area * $c_weight_lam1 / 1000 = $pure_weight_lam1" ?></div>
            
            <div class="param-name">Вес материала ламинации 1 с отходами</div>
            <div class="algorithm">(длина тиража с ламинацией + длина материала для приладки при ламинации)</div>
            <div class="algorithm"> * ширина тиража с отходами / 1000 * удельный вес ламинации 1 / 1000</div>
            <div class="value mb-2"><?="($pure_length_lam + $laminator_tuning_length) * $dirty_width / 1000 * $c_weight_lam1 / 1000 = $dirty_weight_lam1" ?></div>
            
            <div class="param-name">Стоимость материала ламинации 1</div>
            <div class="algorithm">если сырьё заказчика: 0</div>
            <div class="algorithm">иначе: удельная стоимость материала ламинации * вес материала с отходами</div>
            <?php if($lamination1_customers_material): ?>
            <div class="value mb-2">0</div>
            <?php else: ?>
            <div class="value mb-2"><?="$c_price_lam1 * $dirty_weight_lam1 = $price_lam1_material" ?></div>
            <?php endif; ?>
            
            <div class="param-name">Стоимость клеевого раствора 1</div>
            <div class="algorithm">если марка плёнки начинается на pet:</div>
            <div class="algorithm">удельная стоимость клеевого раствора / 1000 * расход клея для ламинации ПЭТ</div>
            <div class="algorithm">* (чистая длина с ламинацией * ширина вала / 1000 + длина материала для приладки при ламинации)</div>
            <div class="algorithm">иначе:</div>
            <div class="algorithm">удельная стоимость клеевого раствора / 1000 * расход клея</div>
            <div class="algorithm">* (чистая длина с ламинацией * ширина вала / 1000 + длина материала для приладки при ламинации)</div>
            <?php if(stripos($brand_name, 'pet') === 0 || stripos($lamination1_brand_name, 'pet') === 0): ?>
            <div class="value mb-2"><?="$glue_solvent_g / 1000 * $glue_expense_pet * ($pure_length_lam * $lamination_roller / 1000 + $laminator_tuning_length) = $price_lam1_glue" ?></div>
            <?php else: ?>
            <div class="value mb-2"><?="$glue_solvent_g / 1000 * $glue_expense * ($pure_length_lam * $lamination_roller / 1000 + $laminator_tuning_length) = $price_lam1_glue" ?></div>
            <?php endif; ?>
            
            <div class="param-name">Стоимость процесса ламинации 1</div>
            <div class="algorithm">стоимость работы оборудования</div>
            <div class="algorithm">+ (длина чистая с ламинацией / 1000 / скорость работы оборудования) * стоимость работы оборудования</div>
            <div class="value mb-2"><?="$laminator_price + ($pure_length_lam / 1000 / $laminator_speed) * $laminator_price = $price_lam1_work" ?></div>
            
            <?php if(!empty($lamination2_brand_name)): ?>
            <div class="param-name">Вес материала ламинации 2 чистый</div>
            <div class="algorithm">площадь тиража чистая * удельный вес ламинации 1 / 1000</div>
            <div class="value mb-2"><?="$pure_area * $c_weight_lam2 / 1000 = $pure_weight_lam2" ?></div>
            
            <div class="param-name">Вес материала ламинации 2 с отходами 2</div>
            <div class="algorithm">(длина тиража с ламинацией + длина материала для приладки при ламинации)</div>
            <div class="algorithm">* ширина тиража с отходами / 1000 * удельный вес ламинации 1 / 1000</div>
            <div class="value mb-2"><?="($pure_length_lam + $laminator_tuning_length) * $dirty_width / 1000 * $c_weight_lam2 / 1000 = $dirty_weight_lam2" ?></div>
            
            <div class="param-name">Стоимость материала ламинации 2</div>
            <div class="algorithm">если сырьё заказчика: 0</div>
            <div class="algorithm">иначе: удельная стоимость материала ламинации * вес материала с отходами</div>
            <?php if($lamination2_customers_material): ?>
            <div class="value mb-2">0</div>
            <?php else: ?>
            <div class="value mb-2"><?="$c_price_lam2 * $dirty_weight_lam2 = $price_lam2_material" ?></div>
            <?php endif; ?>
            
            <div class="param-name">Стоимость клеевого раствора 2</div>
            <div class="algorithm">если марка плёнки начинается на pet:</div>
            <div class="algorithm">удельная стоимость клеевого раствора / 1000 * расход клея для ламинации ПЭТ</div>
            <div class="algorithm">* (чистая длина с ламинацией * ширина вала / 1000 + длина материала для приладки при ламинации)</div>
            <div class="algorithm">иначе:</div>
            <div class="algorithm">удельная стоимость клеевого раствора / 1000 * расход клея</div>
            <div class="algorithm">* (чистая длина с ламинацией * ширина вала / 1000 + длина материала для приладки при ламинации)</div>
            <?php if(stripos($lamination2_brand_name, 'pet') === 0): ?>
            <div class="value mb-2"><?="$glue_solvent_g / 1000 * $glue_expense_pet * ($pure_length_lam * $lamination_roller / 1000 + $laminator_tuning_length) = $price_lam2_glue" ?></div>
            <?php else: ?>
            <div class="value mb-2"><?="$glue_solvent_g / 1000 * $glue_expense * ($pure_length_lam * $lamination_roller / 1000 + $laminator_tuning_length) = $price_lam2_glue" ?></div>
            <?php endif; ?>
            
            <div class="param-name">Стоимость процесса ламинации 2</div>
            <div class="algorithm">стоимость работы оборудования</div>
            <div class="algorithm">+ (длина чистая с ламинацией / 1000 / скорость работы оборудования) * стоимость работы оборудования</div>
            <div class="value mb-2"><?="$laminator_price + ($pure_length_lam / 1000 / $laminator_speed) * $laminator_price = $price_lam2_work" ?></div>
            <?php endif; ?>
            
            <div class="param-name">Итого себестоимость ламинации</div>
            <div class="algorithm">материал1 + материал2 + клей1 + клей2 + процесс1 + процесс2</div>
            <div class="value mb-2"><?=($price_lam1_material ?? 0)." + ".($price_lam2_material ?? 0)." + ".($price_lam1_glue ?? 0)." + ".($price_lam2_glue ?? 0)." + ".($price_lam1_work ?? 0)." + ".($price_lam2_work ?? 0)." = ".($price_lam_total ?? 0) ?></div>
            <?php endif; ?>
            
            <!---------------------------------------------------------->
            
            <div class="param-name">Вес материала готовой продукции чистый</div>
            <div class="algorithm">площадь тиража чистая * (удельный вес материала + удельный вес ламинации 1 + удельный вес ламинации 2) / 1000</div>
            <div class="value mb-2"><?="$pure_area * ($c_weight + (".($c_weight_lam1 ?? 0).") + (".($c_weight_lam2 ?? 0).")) / 1000 = $pure_weight_total" ?></div>
            
            <div class="param-name">Вес материала готовой продукции с отходами</div>
            <div class="algorithm">площадь тиража с отходами * (удельный вес материала + удельный вес ламинации 1 + удельный вес ламинации 2) / 1000</div>
            <div class="value mb-2"><?="$dirty_area * ($c_weight + (".($c_weight_lam1 ?? 0).") + (".($c_weight_lam2 ?? 0).")) / 1000 = $dirty_weight_total" ?></div>
            
            <div class="param-name">Итого себестоимость без форм</div>
            <div class="algorithm">стоимость материала печати + стоимость печати + стоимость красок, лака и растворителя</div>
            <div class="algorithm">+ итого себестоимость ламинации + (стоимость скотча для наклейки форм * число красок * площадь печатной формы / 10000)</div>
            <div class="value mb-2"><?=($material_price ?? 0)." + ".($print_price ?? 0)." + ".($paint_price ?? 0)." + ".($price_lam_total ?? 0)." + (".($cliche_scotch ?? 0)." * ".($paints_count ?? 0)." * ".($cliche_area ?? 0)." / 10000) = $cost_no_cliche" ?></div>
            
            <div class="param-name">Итого себестоимость с формами</div>
            <div class="algorithm">итого стоимость без форм + стоимость комплекта печатных форм</div>
            <div class="value mb-2"><?=($cost_no_cliche ?? 0)." + ".($cliche_price ?? 0)." = $cost_with_cliche" ?></div>
            
            <?php if($unit == "kg" && !empty($quantity)): ?>
            <div class="param-name">Итого себестоимость за 1 кг без форм</div>
            <div class="algorithm">итого себестоимость без форм / объём заказа</div>
            <div class="value mb-2"><?=($cost_no_cliche ?? 0)." / ".($quantity ?? 0)." = $cost_no_cliche_kg" ?></div>
            
            <div class="param-name">Итого себестоимость за 1 кг с формами</div>
            <div class="algorithm">итого стоимость с формами / объём заказа</div>
            <div class="value mb-2"><?=($cost_with_cliche ?? 0)." / ".($quantity ?? 0)." = $cost_with_cliche_kg" ?></div>
            <?php endif; ?>
            
            <?php if($unit == "thing" && !empty($quantity)): ?>
            <div class="param-name">Итого себестоимость за 1 шт без форм</div>
            <div class="algorithm">итого себестоимость без форм / объём заказа</div>
            <div class="value mb-2"><?=($cost_no_cliche ?? 0)." / ".($quantity ?? 0)." = $cost_no_cliche_thing" ?></div>
            
            <div class="param-name">Итого себестоимость за 1 шт с формами</div>
            <div class="algorithm">итого стоимость с формами / объём заказа</div>
            <div class="value mb-2"><?=($cost_with_cliche ?? 0)." / ".($quantity ?? 0)." = $cost_with_cliche_thing" ?></div>
            <?php endif; ?>
        </div>
        <?php
        endif;
        ?>
</div>