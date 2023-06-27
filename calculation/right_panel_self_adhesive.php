<?php
$calculation_class = "";

if(isset($create_calculation_submit_class) && empty($create_calculation_submit_class)) {
    $calculation_class = " class='d-none'";    
}

// Редактирование наценки на тираж
if(null !== filter_input(INPUT_POST, 'extracharge-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $extracharge = filter_input(INPUT_POST, 'extracharge');
    $quantity = 0;
    
    $sql = "select sum(quantity) from calculation_quantity where calculation_id = $id order by id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $quantity = $row[0];
    }
    
    $sql = "update calculation set extracharge=$extracharge where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost = cr.cost + (cr.cost * c.extracharge / 100) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost_per_unit = cr.shipping_cost / $quantity where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set income = shipping_cost - cost";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.income_per_unit = cr.income / $quantity where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Редактирование наценки на ПФ
if(null !== filter_input(INPUT_POST, 'extracharge-cliche-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $extracharge_cliche = filter_input(INPUT_POST, 'extracharge_cliche');
    
    $sql = "update calculation set extracharge_cliche=$extracharge_cliche where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cliche_cost = cr.cliche_cost + (cr.cliche_cost * c.extracharge_cliche / 100) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set income_cliche = shipping_cliche_cost - cliche_cost";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Берём расчёт из таблицы базы
$usd = null; $euro = null; $cost = null; $cost_per_unit = null; $shipping_cost = null; $shipping_cost_per_unit = null; $income = null; $income_per_unit = null; 
$cliche_cost = null; $shipping_cliche_cost = null; $income_cliche = null; 
$knife_cost = null; $shipping_knife_cost = null; $income_knife = null; 
$total_weight_dirty = null;
$film_cost = null; $film_cost_per_unit = null; $width = null; $weight_pure = null; $length_pure = null; $weight_dirty = null; $length_dirty = null;
$film_waste_cost = null; $film_waste_weight = null; $ink_cost = null; $ink_weight = null; $work_cost = null; $work_time = null; $priladka_printing;

$id = filter_input(INPUT_GET, 'id');

if(!empty($id)) {
    $sql_calculation_result = "select usd, euro, cost, cost_per_unit, shipping_cost, shipping_cost_per_unit, income, income_per_unit, "
            . "cliche_cost, shipping_cliche_cost, income_cliche, "
            . "knife_cost, shipping_knife_cost, income_knife, "
            . "total_weight_dirty, "
            . "film_cost_1, film_cost_per_unit_1, width_1, weight_pure_1, length_pure_1, weight_dirty_1, length_dirty_1, "
            . "film_waste_cost_1, film_waste_weight_1, ink_cost, ink_weight, work_cost_1, work_time_1, priladka_printing "
            . "from calculation_result where calculation_id = $id order by id desc limit 1";
    $fetcher = new Fetcher($sql_calculation_result);
    
    if($row = $fetcher->Fetch()) {
        $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $shipping_cost = $row['shipping_cost']; $shipping_cost_per_unit = $row['shipping_cost_per_unit']; $income = $row['income']; $income_per_unit = $row['income_per_unit']; 
        $cliche_cost = $row['cliche_cost']; $shipping_cliche_cost = $row['shipping_cliche_cost']; $income_cliche = $row['income_cliche']; 
        $knife_cost = $row['knife_cost']; $shipping_knife_cost = $row['shipping_knife_cost']; $income_knife = $row['income_knife'];
        $total_weight_dirty = $row['total_weight_dirty'];
        $film_cost = $row['film_cost_1']; $film_cost_per_unit = $row['film_cost_per_unit_1']; $width = $row['width_1']; $weight_pure = $row['weight_pure_1']; $length_pure = $row['length_pure_1']; $weight_dirty = $row['weight_dirty_1']; $length_dirty = $row['length_dirty_1'];
        $film_waste_cost = $row['film_waste_cost_1']; $film_waste_weight = $row['film_waste_weight_1']; $ink_cost = $row['ink_cost']; $ink_weight = $row['ink_weight']; $work_cost = $row['work_cost_1']; $work_time = $row['work_time_1']; $priladka_printing = $row['priladka_printing'];
    }
    else {
        // Новый расчёт
        $calculation = CalculationBase::Create($id);
        
        // Курс доллара
        $new_usd = $calculation->usd;
        if($new_usd === null) $new_usd = "NULL";
        
        // Курс евро
        $new_euro = $calculation->euro;
        if($new_euro === null) $new_euro = "NULL";
        
        // Себестоимость
        $new_cost = $calculation->cost;
        if($new_cost === null) $new_cost = "NULL";
    
        // Себестоимость на 1 шт/кг = Себестоимость / массу тиража или кол-во штук
        $new_cost_per_unit = $calculation->cost_per_unit;
        if($new_cost_per_unit === null) $new_cost_per_unit = "NULL";
        
        // Наценка на тираж
        $new_extracharge = $calculation->extracharge;
        
        // Наценка на ПФ
        $new_extracharge_cliche = $calculation->extracharge_cliche;
        
        // Наценка на нож
        $new_extracharge_knife = $calculation->extracharge_knife;
        
        // Отгрузочная стоимость
        $new_shipping_cost = $calculation->shipping_cost;
        if($new_shipping_cost === null) $new_shipping_cost = "NULL";
        
        // Отгрузочная стоимость за единицу
        $new_shipping_cost_per_unit = $calculation->shipping_cost_per_unit;
        if($new_shipping_cost_per_unit === null) $new_shipping_cost_per_unit = "NULL";
        
        // Прибыль
        $new_income = $calculation->income;
        if($new_income === null) $new_income = "NULL";
        
        // Прибыль за единицу
        $new_income_per_unit = $calculation->income_per_unit;
        if($new_income_per_unit === null) $new_income_per_unit = "NULL";
        
        // Себестоимость ПФ
        $new_cliche_cost = $calculation->cliche_cost;
        if($new_cliche_cost === null) $new_cliche_cost = "NULL";
        
        // Отгрузочная стоимость ПФ
        $new_shipping_cliche_cost = $calculation->shipping_cliche_cost;
        if($new_shipping_cliche_cost === null) $new_shipping_cliche_cost = "NULL";
        
        // Прибыль ПФ
        $new_income_cliche = $calculation->income_cliche;
        if($new_income_cliche === null) $new_income_cliche = "NULL";
        
        // Себестоимость ножа
        $new_knife_cost = $calculation->knife_cost;
        if($new_knife_cost === null) $new_knife_cost = "NULL";
        
        // Отгрузочная стоимость ножа
        $new_shipping_knife_cost = $calculation->shipping_knife_cost;
        if($new_shipping_knife_cost === null) $new_shipping_knife_cost = "NULL";
        
        // Прибыль на нож
        $new_income_knife = $calculation->income_knife;
        if($new_income_knife === null) $new_income_knife = "NULL";
    
        // Материалы = масса с приладкой осн. + масса с приладкой лам. 1 + масса с приладкой лам. 2
        $new_total_weight_dirty = $calculation->total_weight_dirty;
        if($new_total_weight_dirty === null) $new_total_weight_dirty = "NULL";
    
        // Цена материала
        $new_film_cost = $calculation->film_cost;
        if($new_film_cost === null) $new_film_cost = "NULL";
    
        // Цена материала за 1 шт
        $new_film_cost_per_unit = $calculation->film_cost_per_unit;
        if($new_film_cost_per_unit === null) $new_film_cost_per_unit = "NULL";
    
        // Ширина материала
        $new_width = $calculation->width_mat;
        if($new_width === null) $new_width = "NULL";
    
        // Масса без приладки = масса плёнки чистая
        $new_weight_pure = $calculation->weight_pure;
        if($new_weight_pure === null) $new_weight_pure = "NULL";
    
        // Длина без приладки = длина плёнки чистая
        $new_length_pure = $calculation->length_pure;
        if($new_length_pure === null) $new_length_pure = "NULL";
    
        // Масса с приладкой = масса плёнки грязная
        $new_weight_dirty = $calculation->weight_dirty;
        if($new_weight_dirty === null) $new_weight_dirty = "NULL";
    
        // Длина с приладкой = метры погонные грязные
        $new_length_dirty = $calculation->length_dirty;
        if($new_length_dirty === null) $new_length_dirty = "NULL";
    
        // Отходы плёнка цена = (масса грязная - масса чистая) * стоимость за 1 кг * курс валюты
        $new_film_waste_cost = $calculation->film_waste_cost;
        if($new_film_waste_cost === null) $new_film_waste_cost = "NULL";
    
        // Отходы плёнка масса = масса грязная - масса чистая
        $new_film_waste_weight = $calculation->film_waste_weight;
        if($new_film_waste_weight === null) $new_film_waste_weight = "NULL";
    
        // Стоимость всех красок
        $new_ink_cost = null;
        if(!empty($calculation->ink_cost)) $new_ink_cost = $calculation->ink_cost;
        if($new_ink_cost === null) $new_ink_cost = "NULL";
    
        // Расход всех красок
        $new_ink_weight = $calculation->ink_expense;
        if($new_ink_weight === null) $new_ink_weight = "NULL";
    
        // Работа по печати тиража, руб
        $new_work_cost = $calculation->work_cost;
        if($new_work_cost === null) $new_work_cost = "NULL";
    
        // Работа по печати тиража, ч
        $new_work_time = $calculation->work_time;
        if($new_work_time === null) $new_work_time = "NULL";
        
        // Фактический зазор, мм
        $new_gap = $calculation->gap;
        if($new_gap === null) $new_gap = "NULL";
        
        // Метраж приладки одного тиража, м
        $new_priladka_printing = $calculation->priladka_printing;
        if($new_priladka_printing === null) $new_priladka_printing = "NULL";
        
        //****************************************************
        // ПОМЕЩАЕМ НАЦЕНКУ В БАЗУ
        if(empty($error_message)) {
            $sql = "update calculation set extracharge = $new_extracharge, extracharge_cliche = $new_extracharge_cliche, extracharge_knife = $new_extracharge_knife where id = $id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        //****************************************************
        // Присваиваем новые значения наценки для отображения в правой панели
        $extracharge = intval($new_extracharge);
        $extracharge_cliche = intval($new_extracharge_cliche);
        $extracharge_knife = intval($new_extracharge_knife);
        
        //****************************************************
        // ПОМЕЩАЕМ РЕЗУЛЬТАТЫ ВЫЧИСЛЕНИЙ В БАЗУ
        if(empty($error_message)) {
            $sql = "insert into calculation_result (calculation_id, usd, euro, cost, cost_per_unit, shipping_cost, shipping_cost_per_unit, income, income_per_unit, "
                    . "cliche_cost, shipping_cliche_cost, income_cliche, "
                    . "knife_cost, shipping_knife_cost, income_knife, "
                    . "total_weight_dirty, "
                    . "film_cost_1, film_cost_per_unit_1, width_1, weight_pure_1, length_pure_1, weight_dirty_1, length_dirty_1, "
                    . "film_waste_cost_1, film_waste_weight_1, ink_cost, ink_weight, work_cost_1, work_time_1, gap, priladka_printing) "
                    . "values ($id, $new_usd, $new_euro, $new_cost, $new_cost_per_unit, $new_shipping_cost, $new_shipping_cost_per_unit, $new_income, $new_income_per_unit, "
                    . "$new_cliche_cost, $new_shipping_cliche_cost, $new_income_cliche, "
                    . "$new_knife_cost, $new_shipping_knife_cost, $new_income_knife, "
                    . "$new_total_weight_dirty, "
                    . "$new_film_cost, $new_film_cost_per_unit, $new_width, $new_weight_pure, $new_length_pure, $new_weight_dirty, $new_length_dirty, "
                    . "$new_film_waste_cost, $new_film_waste_weight, $new_ink_cost, $new_ink_weight, $new_work_cost, $new_work_time, $new_gap, $new_priladka_printing)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message)) {
            foreach($calculation->lengths as $key => $value) {
                $sql = "update calculation_quantity set length = ".$value." where id = $key";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
        
        //***************************************************
        // ЧИТАЕМ СОХРАНЁННЫЕ РЕЗУЛЬТАТЫ ИЗ БАЗЫ
        $fetcher = new Fetcher($sql_calculation_result);
    
        if($row = $fetcher->Fetch()) {
            $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $shipping_cost = $row['shipping_cost']; $shipping_cost_per_unit = $row['shipping_cost_per_unit']; $income = $row['income']; $income_per_unit = $row['income_per_unit']; 
            $cliche_cost = $row['cliche_cost']; $shipping_cliche_cost = $row['shipping_cliche_cost']; $income_cliche = $row['income_cliche'];
            $knife_cost = $row['knife_cost']; $shipping_knife_cost = $row['shipping_knife_cost']; $income_knife = $row['income_knife'];
            $total_weight_dirty = $row['total_weight_dirty'];
            $film_cost = $row['film_cost_1']; $film_cost_per_unit = $row['film_cost_per_unit_1']; $width = $row['width_1']; $weight_pure = $row['weight_pure_1']; $length_pure = $row['length_pure_1']; $weight_dirty = $row['weight_dirty_1']; $length_dirty = $row['length_dirty_1'];
            $film_waste_cost = $row['film_waste_cost_1']; $film_waste_weight = $row['film_waste_weight_1']; $ink_cost = $row['ink_cost']; $ink_weight = $row['ink_weight']; $work_cost = $row['work_cost_1']; $work_time = $row['work_time_1']; $priladka_printing = $row['priladka_printing'];
        }
        else {
            $error_message = "Ошибка при чтении из базы сохранённых данных";
        }
    }
}
?>
<div id="calculation"<?=$calculation_class ?>>
    <div class="d-flex justify-content-between">
        <div>
            <h1>Расчет</h1>
        </div>
        <div>
            <a class="btn btn-outline-dark mr-3" style="width: 3rem;" title="Скачать" href="csv_self_adhesive.php?id=<?=$id ?>"><i class="fas fa-file-csv"></i></a>
            <a class="btn btn-outline-dark" target="_blank" style="width: 3rem;" title="Печать" href="print.php?id=<?=$id ?>"><i class="fa fa-print"></i></a>
        </div>
    </div>
    <div class="d-flex justify-content-start mb-4">
        <div class="mr-4">
            <div class="text-nowrap">Наценка на тираж</div>
            <form>
                <div class="input-group mb-2">
                    <input type="text" 
                           id="extracharge" 
                           name="extracharge" 
                           style="width: 75px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                           value="<?= round($extracharge) ?>" 
                           required="required"
                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onmouseup="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');" 
                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                           onkeyup="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');" 
                           onfocusout="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');"<?=$disabled_attr ?> />
                    <div class="input-group-append" style="height: 28px;">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="input-group">
                    <input type="text"
                           class="float-only"
                           id="input_shipping_cost_per_unit"
                           name="input_shipping_cost_per_unit"
                           style="width: 75px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                           value="<?= round(floatval($shipping_cost_per_unit), 3)  ?>" 
                           required="required"
                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onmouseup="javascript: $(this).attr('id', 'input_shipping_cost_per_unit'); $(this).attr('name', 'input_shipping_cost_per_unit');" 
                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                           onkeyup="javascript: $(this).attr('id', 'input_shipping_cost_per_unit'); $(this).attr('name', 'input_shipping_cost_per_unit');" 
                           onfocusout="javascript: $(this).attr('id', 'input_shipping_cost_per_unit'); $(this).attr('name', 'input_shipping_cost_per_unit');"<?=$disabled_attr ?> />
                    <div class="input-group-append" style="height: 28px;">
                        <span class="input-group-text">&#8381;</span>
                    </div>
                </div>
            </form>
        </div>
        <?php if($cliche_in_price != 1): ?>
        <div class="mr-4 p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px; margin-top: 25px;">
            <div class="text-nowrap" style="font-size: x-small;">Наценка на ПФ</div>
            <form>
                <input type="hidden" name="id" value="<?=$id ?>" />
                <div class="input-group">
                    <input type="text" 
                           id="extracharge_cliche" 
                           name="extracharge_cliche" 
                           style="width: 35px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                           value="<?= round($extracharge_cliche) ?>" 
                           required="required" 
                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onmouseup="javascript: $(this).attr('id', 'extracharge_cliche'); $(this).attr('name', 'extracharge_cliche');" 
                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                           onkeyup="javascript: $(this).attr('id', 'extracharge_cliche'); $(this).attr('name', 'extracharge_cliche');" 
                           onfocusout="javascript: $(this).attr('id', 'extracharge_cliche'); $(this).attr('name', 'extracharge_cliche');"<?=$disabled_attr ?> />
                    <div class="input-group-append" style="height: 28px;">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
        <?php if($knife_in_price != 1): ?>
        <div class="mr-4 p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px; margin-top: 25px;">
            <div class="text-nowrap" style="font-size: x-small;">Наценка на нож</div>
            <form>
                <input type="hidden" name="id" value="<?=$id ?>" />
                <div class="input-group">
                    <input type="text" 
                           id="extracharge_knife" 
                           name="extracharge_knife" 
                           style="width: 35px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                           value="<?= round($extracharge_knife) ?>" 
                           required="required" 
                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onmouseup="javascript: $(this).attr('id', 'extracharge_knife'); $(this).attr('name', 'extracharge_knife');" 
                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                           onkeyup="javascript: $(this).attr('id', 'extracharge_knife'); $(this).attr('name', 'extracharge_knife');" 
                           onfocusout="javascript: $(this).attr('id', 'extracharge_knife'); $(this).attr('name', 'extracharge_knife');"<?=$disabled_attr ?> />
                    <div class="input-group-append" style="height: 28px;">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
        <div class="mr-4" style="margin-top: 29px;">
            <div class="text-nowrap">Курс &#8364;</div>
            <div class="font-weight-bold" style="font-size: larger;"><?= number_format($euro, 2, ',', ' ') ?></div>
        </div>
        <div class="mr-4" style="margin-top: 29px;">
            <div class="text-nowrap">Курс &#36;</div>
            <div class="font-weight-bold" style="font-size: larger;"><?= number_format($usd, 2, ',', ' ') ?></div>
        </div>
    </div>
    <div class="mt-3">
        <h2>Стоимость</h2>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <h3>Себестоимость</h3>
            <div>Себестоимость</div>
            <div class="value mb-2"><?= DisplayNumber(floatval($cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;"><?= DisplayNumber(floatval($cost_per_unit), 3) ?> &#8381; за шт</span></div>
            <div class="mt-2">Себестоимость ПФ</div>
            <div class="value"><?= DisplayNumber(floatval($cliche_cost), 0) ?> &#8381;</div>
            <div class="value mb-2 font-weight-normal" id="right_panel_new_forms"><?=$new_forms_number ?>&nbsp;шт&nbsp;<?= (empty($stream_width) || empty($streams_number)) ? "" : DisplayNumber($stream_width * $streams_number + 20, 0) ?>&nbsp;мм&nbsp;<i class="fas fa-times" style="font-size: small;"></i>&nbsp;<?= DisplayNumber((intval($raport) + 20) + 20, 0) ?>&nbsp;мм</div>
        </div>
        <div class="col-4 pr-4">
            <h3>Отгрузочная стоимость</h3>
            <div>Отгрузочная стоимость</div>
            <div class="value"><span id="shipping_cost"><?= DisplayNumber(floatval($shipping_cost), 0) ?></span> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><span id="shipping_cost_per_unit"><?= DisplayNumber(floatval($shipping_cost_per_unit), 3) ?></span> &#8381; за шт</span></div>
            <div class="mt-2">Отгрузочная стоимость ПФ</div>
            <div class="value"><span id="shipping_cliche_cost"><?= DisplayNumber(floatval($shipping_cliche_cost), 0) ?></span> &#8381;</div>
        </div>
        <div class="col-4">
            <h3>Прибыль</h3>
            <div>Прибыль</div>
            <div class="value mb-2"><span id="income"><?= DisplayNumber(floatval($income), 0) ?></span> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><span id="income_per_unit"><?= DisplayNumber(floatval($income_per_unit), 3) ?></span> &#8381; за шт</span></div>
            <div class="mt-2">Прибыль ПФ</div>
            <div class="value"><span id="income_cliche"><?= DisplayNumber(floatval($income_cliche), 0) ?></span> &#8381;</div>
        </div>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <div>Себестоимость ножа</div>
            <div class="value"><?= DisplayNumber(floatval($knife), 0) ?> &#8381;</div>
        </div>
        <div class="col-4 pr-4">
            <div>Отгрузочная стоимость ножа</div>
            <div class="value"><span id="shipping_knife_cost"><?= DisplayNumber(floatval($shipping_knife_cost), 0) ?></span> &#8381;</div>
        </div>
        <div class="col-4 pr-4">
            <div>Прибыль на нож</div>
            <div class="value"><span id="income_knife"><?= DisplayNumber(floatval($income_knife), 0) ?></span> &#8381;</div>
        </div>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4"></div>
        <div class="col-4 pr-4"></div>
        <div class="col-4">
            <div>Итоговая прибыль</div>
            <div class="value mb-2"><span id="income_total"><?= DisplayNumber(round(floatval($income), 0) + round(floatval($income_cliche), 0) + round(floatval($income_knife), 0), 0) ?></span> &#8381;</div>
        </div>
    </div>
    <div class="mt-3 row text-nowrap">
        <div class="col-4">
            <h2>Материалы&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;"><?= DisplayNumber(floatval($total_weight_dirty), 0) ?> кг</span></h2>
        </div>
        <div class="col-8">
            <?php
            $sql = "select quantity, length from calculation_quantity where calculation_id = $id";
            $grabber = new Grabber($sql);
            $rows = $grabber->result;
            $printings_number = count($rows);
            ?>
            <h2>Тиражей&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?=$printings_number ?></span></h2>
        </div>
    </div>
    <h3>Самоклеящийся материал&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= DisplayNumber(floatval($weight_dirty), 0) ?> кг</span></h3>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <div>Закупочная стоимость</div>
            <div class="value mb-2"><?= DisplayNumber(floatval($film_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= DisplayNumber(floatval($film_cost_per_unit), 3) ?> &#8381; за м<sup>2</sup></div>
            <div>Ширина</div>
            <div class="value mb-2"><?= DisplayNumber(intval($width), 0) ?> мм</div>
            <div>На приладку тиража</div>
            <div class="value mb-2"><?= DisplayNumber(intval($priladka_printing), 0) ?> м</div>
            <div>Масса без приладки</div>
            <div class="value mb-2"><?= DisplayNumber(floatval($weight_pure), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= DisplayNumber(intval($length_pure), 0) ?> м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2"><?= DisplayNumber(floatval($weight_dirty), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= DisplayNumber(intval($length_dirty), 0) ?> м</span></div>
        </div>
        <div class="col-8">
            <div class="row">
                <div class="col-6">
                <?php
                $half = ceil(count($rows) / 2);
                $i = 1;
                foreach($rows as $row):
                ?>
                    <div class='value mb-2'><span class='font-weight-normal'><?=$i ?>.&nbsp;&nbsp;&nbsp;</span><?= DisplayNumber(intval($row['quantity']), 0) ?> шт&nbsp;&nbsp;&nbsp;<span class='font-weight-normal'><?= DisplayNumber(intval($row['length']), 0) ?> м</span></div>
                <?php if($i == $half): ?>
                </div>
                <div class="col-6">
                <?php
                endif;
                $i++;
                endforeach;
                ?>
                </div>
            </div>
        </div>
    </div>
    <div id="show_costs">
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <button type="button" class="btn btn-light" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать расходы</button>
            </div>
        </div>
    </div>
    <div id="costs" class="d-none">
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть расходы</button>
                <h2 class="mt-2">Расходы</h2>
            </div>
        </div>
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <div>Отходы</div>
                <div class="value mb-2"><?= DisplayNumber(floatval($film_waste_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= DisplayNumber(floatval($film_waste_weight), 2) ?> кг</span></div>
                <div>Краска</div>
                <div class="value mb-2"><?= DisplayNumber(floatval($ink_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= DisplayNumber(floatval($ink_weight), 2) ?> кг</span></div>
                <div>Печать тиража</div>
                <div class="value mb-2"><?= DisplayNumber(floatval($work_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= DisplayNumber(floatval($work_time), 2) ?> ч</span></div>
            </div>
        </div>
    </div>
    <div style="clear: both"></div>
    <?php include 'change_status_buttons.php'; ?>
</div>