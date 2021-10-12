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
        
    // Данные о машине
    $machine_speeds = array();
    $machine_prices = array();
        
    if(!empty($machine_id)) {
        $sql = "select machine_id, price, speed "
                . "from norm_machine "
                . "where date in (select max(date) from norm_machine where date <= '$date' group by machine_id)";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $machine_prices[$row['machine_id']] = $row['price'];
            $machine_speeds[$row['machine_id']] = $row['speed'];
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
        
    if(!empty($machine_id)) {
        $sql = "select flint, flint_currency, kodak, kodak_currency, tver, tver_currency, film, film_currency, tver_coeff, overmeasure, scotch, scotch_currency "
                . "from norm_form where machine_id = $machine_id and date <= '$date' order by date desc limit 1";
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
    $paint_solvent = null;
    $paint_solvent_l = null;
    $paint_lacquer_solvent_l = null;
    $paint_min_price = null;
                
    if(!empty($machine_id)) {
        $sql = "select c, c_currency, c_expense, m, m_currency, m_expense, y, y_currency, y_expense, k, k_currency, k_expense, white, white_currency, white_expense, panton, panton_currency, panton_expense, lacquer, lacquer_currency, lacquer_expense, paint_solvent, solvent, solvent_currency, solvent_l, solvent_l_currency, lacquer_solvent_l, min_price "
                . "from norm_paint where machine_id = $machine_id and date <= '$date' order by date desc limit 1";
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
            $paint_solvent = $row['solvent'];
                
            if($row['solvent_currency'] == USD) {
                $paint_solvent *= $usd;
            }
            else if($row['solvent_currency'] == EURO) {
                $paint_solvent *= $euro;
            }
                
            $paint_solvent_l = $row['solvent_l'];
            
            if($row['solvent_l_currency'] == USD) {
                $paint_solvent_l *= $usd;
            }
            else if($row['solvent_l_currency'] == EURO) {
                $paint_solvent_l *= $euro;
            }
                
            $paint_lacquer_solvent_l = $row['lacquer_solvent_l'];
            $paint_min_price = $row['min_price'];
        }
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
            . " from calculation_result where calculation_id = $id";
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
    <form method="post">
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell" style="width: 60%;">
                    <div class="row">
                        <div class="col-4">
                            <div class="p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px;">
                                <div class="text-nowrap" style="font-size: x-small;">Наценка</div>
                                <?php if($status_id == 1 || $status_id == 2): ?>
                                <div class="input-group">
                                    <input type="text" id="extracharge" name="extracharge" data-id="<?=$id ?>" style="width: 35px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" value="<?=$extracharge ?>" required="required" />
                                    <div class="input-group-append" style="height: 28px;">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <?php else: ?>
                                <span class="text-nowrap"><?=$extracharge ?>%</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                                <div class="text-nowrap" style="font-size: x-small;">Курс евро</div>
                                <?=number_format($euro, 2, ',', ' ') ?>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                                <div class="text-nowrap" style="font-size: x-small;">Курс доллара</div>
                                <?=number_format($usd, 2, ',', ' ') ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-cell"></div>
            </div>
        </div>
        <div class="mt-3">
            <h2>Материалы</h2>
        </div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Площадь тиража чистая</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_area, 3, ",", " "), "0"), ",") ?> м<sup>2</sup></div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Время печати тиража без приладки</div>
                    <div class="value"><?=rtrim(rtrim(number_format($print_time, 3, ",", " "), "0"), ",") ?> ч</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Вес материала ламинации 1 чистый</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_weight_lam1, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Ширина тиража обрезная</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_width, 3, ",", " "), "0"), ",") ?> мм</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Время приладки</div>
                    <div class="value"><?=rtrim(rtrim(number_format($tuning_time, 3, ",", " "), "0"), ",") ?> ч</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Вес материала ламинации 1 с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_weight_lam1, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Длина тиража чистая</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_length, 3, ",", " "), "0"), ",") ?> м</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Время печати с приладкой</div>
                    <div class="value"><?=rtrim(rtrim(number_format($print_tuning_time, 3, ",", " "), "0"), ",") ?> ч</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость материала ламинации 1</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam1_material, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Длина тиража чистая с ламинацией</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_length_lam, 3, ",", " "), "0"), ",") ?> м</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость печати</div>
                    <div class="value"><?=rtrim(rtrim(number_format($print_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость клеевого раствора 1</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam1_glue, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Длина тиража с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_length, 3, ",", " "), "0"), ",") ?> м</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Площадь печатной формы</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cliche_area, 3, ",", " "), "0"), ",") ?> м<sup>2</sup></div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость процесса ламинации 1</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam1_work, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Ширина тиража с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_width, 3, ",", " "), "0"), ",") ?> мм</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость 1 печатной формы Флинт</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cliche_flint_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Вес материала ламинации 2 чистый</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_weight_lam2, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Площадь тиража с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_area, 3, ",", " "), "0"), ",") ?> м<sup>2</sup></div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость 1 печатной формы Кодак</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cliche_kodak_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Вес материала ламинации 2 с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_weight_lam2, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Вес материала печати чистый</div>
                    <div class="value"><?=rtrim(rtrim(number_format($pure_weight, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость 1 печатной формы Тверь</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cliche_tver_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость материала ламинации 2</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam2_material, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Вес материала печати с отходами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($dirty_weight, 3, ",", " "), "0"), ",") ?> кг</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость комплекта печатных форм</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cliche_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость клеевого раствора 2</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam2_glue, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость материала печати</div>
                    <div class="value"><?=rtrim(rtrim(number_format($material_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость краски + лака + растворителя</div>
                    <div class="value"><?=rtrim(rtrim(number_format($paint_price, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Стоимость процесса ламинации 2</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam2_work, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;"></div>
                <div class="d-table-cell pb-1" style="width: 33%;"></div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Итого себестоимость ламинации</div>
                    <div class="value"><?=rtrim(rtrim(number_format($price_lam_total, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <h2>Вес материала готовой продукции</h2>
        </div>
        <div class="d-table w-100">
            <div class="d-table-cell pb-1" style="width: 33%;">
                <div>Чистый</div>
                <div class="value"><?=rtrim(rtrim(number_format($pure_weight_total, 3, ",", " "), "0"), ",") ?> кг</div>
            </div>
            <div class="d-table-cell pb-1" style="width: 33%;">
                <div>С отходами</div>
                <div class="value"><?=rtrim(rtrim(number_format($dirty_weight_total, 3, ",", " "), "0"), ",") ?> кг</div>
            </div>
            <div class="d-table-cell pb-1" style="width: 33%;"></div>
        </div>
        <div class="mt-3">
            <h2>Себестоимость</h2>
        </div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell" style="width: 33%;">
                    <h3>Всего</h3>
                </div>
                <div class="d-table-cell" style="width: 33%;">
                    <h3>За 1 кг</h3>
                </div>
                <div class="d-table-cell" style="width: 33%;">
                    <h3>За 1 шт</h3>
                </div>
            </div>
        </div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Без форм</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_no_cliche, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Без форм</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_no_cliche_kg, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>Без форм</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_no_cliche_thing, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>С формами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_with_cliche, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>С формами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_with_cliche_kg, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
                <div class="d-table-cell pb-1" style="width: 33%;">
                    <div>С формами</div>
                    <div class="value"><?=rtrim(rtrim(number_format($cost_with_cliche_thing, 3, ",", " "), "0"), ",") ?> руб</div>
                </div>
            </div>
        </div>
        <?php
        if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name) || $work_type_id == 2):
        ?>
        <button type="button" class="btn btn-light" id="show_costs" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать подробности</button>
        <div id="costs" class="d-none">
            <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть подробности</button>
            <h2 class="mt-2">Подробности</h2>
            
            <div class="font-weight-bold">Площадь тиража чистая</div>
            <div>если в кг: 1000 * вес заказа / удельный вес материала и ламинации</div>
            <div>если в шт: ширина ручья / 1000 * длина этикетки вдоль рапорта вала / 1000 * количество этикеток в заказе</div>
            <?php if($unit == 'kg'): ?>
            <div class="value mb-2"><?="1000 * $quantity / (".$c_weight.(empty($c_weight_lam1) ? "" : " + ".$c_weight_lam1).(empty($c_weight_lam2) ? "" : " + ".$c_weight_lam2).") = ".$pure_area ?></div>
            <?php elseif($unit == 'thing'): ?>
            <div class="value mb-2"><?=$unit ?></div>
            <?php endif; ?>
            
            <div class="font-weight-bold">Ширина тиража обрезная</div>
            <div>ширина ручья * количество ручьёв</div>
            <div class="value mb-2"><?="$stream_width * $streams_count = $pure_width" ?></div>
            
            <div class="font-weight-bold">Длина тиража чистая</div>
            <div>площадь тиража чистая / ширина тиража обрезная</div>
            <div class="value mb-2"><?="$pure_area / $pure_width * 1000 = $pure_length" ?></div>
            
            <div class="font-weight-bold">Длина тиража чистая с ламинацией</div>
            <div>длина тиража чистая * (процент отходов для ламинатора + 100) / 100</div>
            <div class="value mb-2"><?="$pure_length * ($tuning_waste_percents[5] + 100) / 100 = $pure_length_lam" ?></div>
            
            <div class="font-weight-bold">Длина тиража с отходами</div>
            <div>если есть печать: длина тиража чистая + (длина тиража чистая * процент отхода машины / 100</div>
            <div>+ длина приладки для машины * число красок)</div>
            <div>если нет печати, но есть ламинация: длина тиража чистая с ламинацией + длина приладки ламинации</div>
            <?php if(!empty($machine_id) && !empty($pure_length) && !empty($paints_count) && !empty($tuning_waste_percents[$machine_id])): ?>
            <div class="value mb-2"><?="$pure_length + ($pure_length * $tuning_waste_percents[$machine_id] / 100 + $tuning_lengths[$machine_id] * $paints_count) = $dirty_length" ?></div>
            <?php elseif(!empty ($lamination1_brand_name) && !empty ($pure_length_lam) && !empty ($tuning_lengths[5])): ?>
            <div class="value mb-2"><?="$pure_length_lam + $tuning_lengths[5] = $dirty_length" ?></div>
            <?php endif; ?>
            
            <div class="font-weight-bold">Ширина тиража с отходами</div>
            <div>с лыжами: ширина тиража обрезная + ширина лыж</div>
            <div>без лыж: ширина тиража обрезная</div>
            <div>затем отругляем ширину тиража с отходами до возможности деления на 5 без остатка</div>
            <?php if($no_ski): ?>
            <div class="value mb-2"><?="$pure_width / 1000 = $dirty_width" ?></div>
            <?php else: ?>
            <div class="value mb-2"><?="($pure_width / 1000) + $ski = $dirty_width" ?></div>
            <?php endif; ?>
            
            <div class="font-weight-bold">Площадь тиража с отходами</div>
            <div>длина тиража с отходами * ширина тиража с отходами</div>
            <div class="value mb-2"><?="$dirty_length * $dirty_width / 1000 = $dirty_area" ?></div>
            
            <div class="font-weight-bold">Вес материала печати чистый</div>
            <div>площадь тиража чистая * удельный вес материала</div>
            <div class="value mb-2"><?="$pure_area * $c_weight / 1000 = $pure_weight" ?></div>
            
            <div class="font-weight-bold">Вес материала печати с отходами</div>
            <div>площадь тиража с отходами * удельный вес материала</div>
            <div class="value mb-2"><?="$dirty_area * $c_weight / 1000 = $dirty_weight" ?></div>
            
            <div class="font-weight-bold">Стоимость материала печати</div>
            <div>Если сырьё заказчика: 0</div>
            <div>Иначе: вес материала печати с отходами * цена материала за 1 кг</div>
            <?php if($customers_material): ?>
            <div class="value mb-2">0</div>
            <?php else: ?>
            <div class="value mb-2"><?="$dirty_weight * $c_price = $material_price" ?></div>
            <?php endif; ?>
            
            <div class="font-weight-bold">Удельная стоимость клеевого раствора</div>
            <div>(стоимость клея * доля клея / (доля клея + доля растворителя)) </div>
            <div>+ (стоимость растворителя * доля раствора / (доля клея + доля растворителя))</div>
            <div class="value mb-2"><?="($glue_price * $glue_glue_part / ($glue_glue_part + $glue_solvent_part)) + ($glue_solvent_price * $glue_solvent_part / ($glue_glue_part + $glue_solvent_part)) = $glue_solvent_g" ?></div>
        </div>
        <?php
        endif;
        ?>
        <div style="clear:both"></div>
        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" id="change_status_submit" name="change_status_submit" />
            <?php if($status_id == 1): ?>
        <button type="submit" id="status_id" name="status_id" value="2" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Отправить КП</button>
            <?php elseif($status_id == 2): ?>
        <button type="submit" id="status_id" name="status_id" value="3" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Отправить в работу</button>
            <?php elseif ($status_id == 4): ?>
        <button type="submit" id="status_id" name="status_id" value="6" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Составить тех. карту</button>
            <?php endif; ?>
            <?php if ($techmaps_count != 0): ?>
        <a href="javascript: void(0);" class="btn btn-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
            <?php endif; ?>
            <?php if($status_id == 6): ?>
        <button type="submit" id="status_id" name="status_id" value="7" class="btn btn-outline-dark mt-3" style="width: 200px;">Завершить</button>
            <?php endif; ?>
    </form>
</div>