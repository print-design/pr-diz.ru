<?php
$calculation_class = "";

if(isset($create_calculation_submit_class) && empty($create_calculation_submit_class)) {
    $calculation_class = " class='d-none'";    
}

// Редактирование наценки на тираж
if(null !== filter_input(INPUT_POST, 'extracharge-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $extracharge = filter_input(INPUT_POST, 'extracharge');
    
    $sql = "update calculation set extracharge=$extracharge where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost = cr.cost + (cr.cost * c.extracharge / 100) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost_per_unit = cr.shipping_cost / c.quantity where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set income = shipping_cost - cost";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.income_per_unit = cr.income / c.quantity where c.id = $id";
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
$usd = null; $euro = null; $cost = null; $cost_per_unit = null; $shipping_cost = null; $shipping_cost_per_unit = null; $income = null; $income_per_unit = null; $cliche_cost = null; $shipping_cliche_cost = null; $income_cliche = null; $total_weight_dirty = null;
$film_cost_1 = null; $film_cost_per_unit_1 = null; $width_1 = null; $weight_pure_1 = null; $length_pure_1 = null; $weight_dirty_1 = null; $length_dirty_1 = null;
$film_cost_2 = null; $film_cost_per_unit_2 = null; $width_2 = null; $weight_pure_2 = null; $length_pure_2 = null; $weight_dirty_2 = null; $length_dirty_2 = null;
$film_cost_3 = null; $film_cost_per_unit_3 = null; $width_3 = null; $weight_pure_3 = null; $length_pure_3 = null; $weight_dirty_3 = null; $length_dirty_3 = null;
$film_waste_cost_1 = null; $film_waste_weight_1 = null; $ink_cost = null; $ink_weight = null; $work_cost_1 = null; $work_time_1 = null;
$film_waste_cost_2 = null; $film_waste_weight_2 = null; $glue_cost_2 = null; $glue_expense_2 = null; $work_cost_2 = null; $work_time_2 = null;
$film_waste_cost_3 = null; $film_waste_weight_3 = null; $glue_cost_3 = null; $glue_expense_3 = null; $work_cost_3 = null; $work_time_3 = null;

$id = filter_input(INPUT_GET, 'id');

if(!empty($id)) {
    $sql_calculation_result = "select usd, euro, cost, cost_per_unit, shipping_cost, shipping_cost_per_unit, income, income_per_unit, cliche_cost, shipping_cliche_cost, income_cliche, total_weight_dirty, "
            . "film_cost_1, film_cost_per_unit_1, width_1, weight_pure_1, length_pure_1, weight_dirty_1, length_dirty_1, "
            . "film_cost_2, film_cost_per_unit_2, width_2, weight_pure_2, length_pure_2, weight_dirty_2, length_dirty_2, "
            . "film_cost_3, film_cost_per_unit_3, width_3, weight_pure_3, length_pure_3, weight_dirty_3, length_dirty_3, "
            . "film_waste_cost_1, film_waste_weight_1, ink_cost, ink_weight, work_cost_1, work_time_1, "
            . "film_waste_cost_2, film_waste_weight_2, glue_cost_2, glue_expense_2, work_cost_2, work_time_2, "
            . "film_waste_cost_3, film_waste_weight_3, glue_cost_3, glue_expense_3, work_cost_3, work_time_3 "
            . "from calculation_result where calculation_id = $id order by id desc limit 1";
    $fetcher = new Fetcher($sql_calculation_result);

    if($row = $fetcher->Fetch()) {
        $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $shipping_cost = $row['shipping_cost']; $shipping_cost_per_unit = $row['shipping_cost_per_unit']; $income = $row['income']; $income_per_unit = $row['income_per_unit']; $cliche_cost = $row['cliche_cost']; $shipping_cliche_cost = $row['shipping_cliche_cost']; $income_cliche = $row['income_cliche']; $total_weight_dirty = $row['total_weight_dirty'];
        $film_cost_1 = $row['film_cost_1']; $film_cost_per_unit_1 = $row['film_cost_per_unit_1']; $width_1 = $row['width_1']; $weight_pure_1 = $row['weight_pure_1']; $length_pure_1 = $row['length_pure_1']; $weight_dirty_1 = $row['weight_dirty_1']; $length_dirty_1 = $row['length_dirty_1'];
        $film_cost_2 = $row['film_cost_2']; $film_cost_per_unit_2 = $row['film_cost_per_unit_2']; $width_2 = $row['width_2']; $weight_pure_2 = $row['weight_pure_2']; $length_pure_2 = $row['length_pure_2']; $weight_dirty_2 = $row['weight_dirty_2']; $length_dirty_2 = $row['length_dirty_2'];
        $film_cost_3 = $row['film_cost_3']; $film_cost_per_unit_3 = $row['film_cost_per_unit_3']; $width_3 = $row['width_3']; $weight_pure_3 = $row['weight_pure_3']; $length_pure_3 = $row['length_pure_3']; $weight_dirty_3 = $row['weight_dirty_3']; $length_dirty_3 = $row['length_dirty_3'];
        $film_waste_cost_1 = $row['film_waste_cost_1']; $film_waste_weight_1 = $row['film_waste_weight_1']; $ink_cost = $row['ink_cost']; $ink_weight = $row['ink_weight']; $work_cost_1 = $row['work_cost_1']; $work_time_1 = $row['work_time_1'];
        $film_waste_cost_2 = $row['film_waste_cost_2']; $film_waste_weight_2 = $row['film_waste_weight_2']; $glue_cost_2 = $row['glue_cost_2']; $glue_expense_2 = $row['glue_expense_2']; $work_cost_2 = $row['work_cost_2']; $work_time_2 = $row['work_time_2'];
        $film_waste_cost_3 = $row['film_waste_cost_3']; $film_waste_weight_3 = $row['film_waste_weight_3']; $glue_cost_3 = $row['glue_cost_3']; $glue_expense_3 = $row['glue_expense_3']; $work_cost_3 = $row['work_cost_3']; $work_time_3 = $row['work_time_3'];
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
    
        // Себестоимость форм
        $new_cliche_cost = $calculation->cliche_cost;
        if($new_cliche_cost === null) $new_cliche_cost = "NULL";
        
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
        
        // Отгрузочная стоимость ПФ
        $new_shipping_cliche_cost = $calculation->shipping_cliche_cost;
        if($new_shipping_cliche_cost === null) $new_shipping_cliche_cost = "NULL";
        
        // Прибыль ПФ
        $new_income_cliche = $calculation->income_cliche;
        if($new_income_cliche === null) $new_income_cliche = "NULL";
    
        // Материалы = масса с приладкой осн. + масса с приладкой лам. 1 + масса с приладкой лам. 2
        $new_total_weight_dirty = $calculation->total_weight_dirty;
        if($new_total_weight_dirty === null) $new_total_weight_dirty = "NULL";
    
        // Основная пленка цена = стоимость основной плёнки
        $new_film_cost_1 = $calculation->film_cost_1;
        if($new_film_cost_1 === null) $new_film_cost_1 = "NULL";
    
        // Основная плёнка цена за кг = стоимость основной плёнки / вес
        $new_film_cost_per_unit_1 = $calculation->film_cost_per_unit_1;
        if($new_film_cost_per_unit_1 === null) $new_film_cost_per_unit_1 = "NULL";
    
        // Ширина основной плёнки = ширина осн. плёнки
        $new_width_1 = $calculation->width_1;
        if($new_width_1 === null) $new_width_1 = "NULL";
    
        // Масса без приладки = масса плёнки чистая
        $new_weight_pure_1 = $calculation->weight_pure_1;
        if($new_weight_pure_1 === null) $new_weight_pure_1 = "NULL";
    
        // Длина без приладки = длина плёнки чистая
        $new_length_pure_1 = $calculation->length_pure_1;
        if($new_length_pure_1 === null) $new_length_pure_1 = "NULL";
    
        // Масса с приладкой = масса плёнки грязная
        $new_weight_dirty_1 = $calculation->weight_dirty_1;
        if($new_weight_dirty_1 === null) $new_weight_dirty_1 = "NULL";
    
        // Длина с приладкой = метры погонные грязные
        $new_length_dirty_1 = $calculation->length_dirty_1;
        if($new_length_dirty_1 === null) $new_length_dirty_1 = "NULL";
    
        // Лам 1 цена = лам 1 цена
        $new_film_cost_2 = $calculation->film_cost_2;
        if($new_film_cost_2 === null) $new_film_cost_2 = "NULL";
    
        // Лам 1 цена за кг = лам 1 цена / вес
        $new_film_cost_per_unit_2 = $calculation->film_cost_per_unit_2;
        if($new_film_cost_per_unit_2 === null) $new_film_cost_per_unit_2 = "NULL";
    
        // Лам 1 ширина = лам 1 ширина
        $new_width_2 = $calculation->width_2;
        if($new_width_2 === null) $new_width_2 = "NULL";
        
        // Лам 1 масса без приладки = лам 1 масса чистая
        $new_weight_pure_2 = $calculation->weight_pure_2;
        if($new_weight_pure_2 === null) $new_weight_pure_2 = "NULL";
    
        // Лам 1 длина без приладки = лам 1 длина чистая
        $new_length_pure_2 = $calculation->length_pure_2;
        if($new_length_pure_2 === null) $new_length_pure_2 = "NULL";
    
        // Лам 1 масса с приладкой = лам 1 масса грязная
        $new_weight_dirty_2 = $calculation->weight_dirty_2;
        if($new_weight_dirty_2 === null) $new_weight_dirty_2 = "NULL";
    
        // Лам 1 длина с приладкой = лам 1 длина грязная
        $new_length_dirty_2 = $calculation->length_dirty_2;
        if($new_length_dirty_2 === null) $new_length_dirty_2 = "NULL";
    
        // Лам 2 плёнка цена
        $new_film_cost_3 = $calculation->film_cost_3;
        if($new_film_cost_3 === null) $new_film_cost_3 = "NULL";
    
        // Лам 2 цена за кг = лам 2 плёнка цена / вес
        $new_film_cost_per_unit_3 = $calculation->film_cost_per_unit_3;
        if($new_film_cost_per_unit_3 === null) $new_film_cost_per_unit_3 = "NULL";
    
        // Лам 2 ширина = лам 2 ширина
        $new_width_3 = $calculation->width_3;
        if($new_width_3 === null) $new_width_3 = "NULL";
    
        // Лам 2 масса без приладки
        $new_weight_pure_3 = $calculation->weight_pure_3;
        if($new_weight_pure_3 === null) $new_weight_pure_3 = "NULL";
    
        // Лам 2 длина без приладки
        $new_length_pure_3 = $calculation->length_pure_3;
        if($new_length_pure_3 === null) $new_length_pure_3 = "NULL";
    
        // Лам 2 масса с приладкой = лам 2 масса грязная
        $new_weight_dirty_3 = $calculation->weight_dirty_3;
        if($new_weight_dirty_3 === null) $new_weight_dirty_3 = "NULL";
    
        // Лам 2 длина с приладкой = лам 2 длина грязная
        $new_length_dirty_3 = $calculation->length_dirty_3;
        if($new_length_dirty_3 === null) $new_length_dirty_3 = "NULL";
    
        // Отходы плёнка цена = (масса грязная - масса чистая) * стоимость за 1 кг * курс валюты
        $new_film_waste_cost_1 = $calculation->film_waste_cost_1;
        if($new_film_waste_cost_1 === null) $new_film_waste_cost_1 = "NULL";
    
        // Отходы плёнка масса = масса грязная - масса чистая
        $new_film_waste_weight_1 = $calculation->film_waste_weight_1;
        if($new_film_waste_weight_1 === null) $new_film_waste_weight_1 = "NULL";
    
        // Стоимость всех красок
        $new_ink_cost = null;
        if(!empty($calculation->ink_cost)) $new_ink_cost = $calculation->ink_cost;
        if($new_ink_cost === null) $new_ink_cost = "NULL";
    
        // Расход всех красок
        $new_ink_weight = $calculation->ink_expense;
        if($new_ink_weight === null) $new_ink_weight = "NULL";
    
        // Работа по печати тиража, руб
        $new_work_cost_1 = $calculation->work_cost_1;
        if($new_work_cost_1 === null) $new_work_cost_1 = "NULL";
    
        // Работа по печати тиража, ч
        $new_work_time_1 = $calculation->work_time_1;
        if($new_work_time_1 === null) $new_work_time_1 = "NULL";
    
        // Отходы плёнки ламинации 1, руб
        $new_film_waste_cost_2 = $calculation->film_waste_cost_2;
        if($new_film_waste_cost_2 === null) $new_film_waste_cost_2 = "NULL";
    
        // Отходы плёнки ламинации 1, кг
        $new_film_waste_weight_2 = $calculation->film_waste_weight_2;
        if($new_film_waste_weight_2 === null) $new_film_waste_weight_2 = "NULL";
    
        // Стоимость клея лам 1
        $new_glue_cost_2 = $calculation->glue_cost2;
        if($new_glue_cost_2 === null) $new_glue_cost_2 = "NULL";
    
        // Расход клея лам 1
        $new_glue_expense_2 = $calculation->glue_expense2;
        if($new_glue_expense_2 === null) $new_glue_expense_2 = "NULL";
    
        // Работа лам 1, руб
        $new_work_cost_2 = $calculation->work_cost_2;
        if($new_work_cost_2 === null) $new_work_cost_2 = "NULL";
    
        // Работа лам 1, ч
        $new_work_time_2 = $calculation->work_time_2;
        if($new_work_time_2 === null) $new_work_time_2 = "NULL";
    
        // Отходы плёнки лам 2, руб
        $new_film_waste_cost_3 = $calculation->film_waste_cost_3;
        if($new_film_waste_cost_3 === null) $new_film_waste_cost_3 = "NULL";
    
        // Отходы плёнки лам 2, кг
        $new_film_waste_weight_3 = $calculation->film_waste_weight_3;
        if($new_film_waste_weight_3 === null) $new_film_waste_weight_3 = "NULL";
    
        // Стоимость клея лам 2
        $new_glue_cost_3 = $calculation->glue_cost3;
        if($new_glue_cost_3 === null) $new_glue_cost_3 = "NULL";
    
        // Расход клея лам 2
        $new_glue_expense_3 = $calculation->glue_expense3;
        if($new_glue_expense_3 === null) $new_glue_expense_3 = "NULL";
    
        // Работа лам 2, руб
        $new_work_cost_3 = $calculation->work_cost_3;
        if($new_work_cost_3 === null) $new_work_cost_3 = "NULL";
    
        // Работа лам 2, ч
        $new_work_time_3 = $calculation->work_time_3;
        if($new_work_time_3 === null) $new_work_time_3 = "NULL";
        
        //****************************************************
        // ПОМЕЩАЕМ НАЦЕНКУ В БАЗУ
        if(empty($error_message)) {
            $sql = "update calculation set extracharge = $new_extracharge, extracharge_cliche = $new_extracharge_cliche where id = $id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        //****************************************************
        // Присваиваем новые значения наценки для отображения в правой панели
        $extracharge = intval($new_extracharge);
        $extracharge_cliche = intval($new_extracharge_cliche);
        
        //****************************************************
        // ПОМЕЩАЕМ РЕЗУЛЬТАТЫ ВЫЧИСЛЕНИЙ В БАЗУ
        if(empty($error_message)) {
            $sql = "insert into calculation_result (calculation_id, usd, euro, cost, cost_per_unit, shipping_cost, shipping_cost_per_unit, income, income_per_unit, cliche_cost, shipping_cliche_cost, income_cliche, total_weight_dirty, "
                    . "film_cost_1, film_cost_per_unit_1, width_1, weight_pure_1, length_pure_1, weight_dirty_1, length_dirty_1, "
                    . "film_cost_2, film_cost_per_unit_2, width_2, weight_pure_2, length_pure_2, weight_dirty_2, length_dirty_2, "
                    . "film_cost_3, film_cost_per_unit_3, width_3, weight_pure_3, length_pure_3, weight_dirty_3, length_dirty_3, "
                    . "film_waste_cost_1, film_waste_weight_1, ink_cost, ink_weight, work_cost_1, work_time_1, "
                    . "film_waste_cost_2, film_waste_weight_2, glue_cost_2, glue_expense_2, work_cost_2, work_time_2, "
                    . "film_waste_cost_3, film_waste_weight_3, glue_cost_3, glue_expense_3, work_cost_3, work_time_3) "
                    . "values ($id, $new_usd, $new_euro, $new_cost, $new_cost_per_unit, $new_shipping_cost, $new_shipping_cost_per_unit, $new_income, $new_income_per_unit, $new_cliche_cost, $new_shipping_cliche_cost, $new_income_cliche, $new_total_weight_dirty, "
                    . "$new_film_cost_1, $new_film_cost_per_unit_1, $new_width_1, $new_weight_pure_1, $new_length_pure_1, $new_weight_dirty_1, $new_length_dirty_1, "
                    . "$new_film_cost_2, $new_film_cost_per_unit_2, $new_width_2, $new_weight_pure_2, $new_length_pure_2, $new_weight_dirty_2, $new_length_dirty_2, "
                    . "$new_film_cost_3, $new_film_cost_per_unit_3, $new_width_3, $new_weight_pure_3, $new_length_pure_3, $new_weight_dirty_3, $new_length_dirty_3, "
                    . "$new_film_waste_cost_1, $new_film_waste_weight_1, $new_ink_cost, $new_ink_weight, $new_work_cost_1, $new_work_time_1, "
                    . "$new_film_waste_cost_2, $new_film_waste_weight_2, $new_glue_cost_2, $new_glue_expense_2, $new_work_cost_2, $new_work_time_2, "
                    . "$new_film_waste_cost_3, $new_film_waste_weight_3, $new_glue_cost_3, $new_glue_expense_3, $new_work_cost_3, $new_work_time_3)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
    
        //***************************************************
        // ЧИТАЕМ СОХРАНЁННЫЕ РЕЗУЛЬТАТЫ ИЗ БАЗЫ
        $fetcher = new Fetcher($sql_calculation_result);
    
        if($row = $fetcher->Fetch()) {
            $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $shipping_cost = $row['shipping_cost']; $shipping_cost_per_unit = $row['shipping_cost_per_unit']; $income = $row['income']; $income_per_unit = $row['income_per_unit']; $cliche_cost = $row['cliche_cost']; $shipping_cliche_cost = $row['shipping_cliche_cost']; $income_cliche = $row['income_cliche']; $total_weight_dirty = $row['total_weight_dirty'];
            $film_cost_1 = $row['film_cost_1']; $film_cost_per_unit_1 = $row['film_cost_per_unit_1']; $width_1 = $row['width_1']; $weight_pure_1 = $row['weight_pure_1']; $length_pure_1 = $row['length_pure_1']; $weight_dirty_1 = $row['weight_dirty_1']; $length_dirty_1 = $row['length_dirty_1'];
            $film_cost_2 = $row['film_cost_2']; $film_cost_per_unit_2 = $row['film_cost_per_unit_2']; $width_2 = $row['width_2']; $weight_pure_2 = $row['weight_pure_2']; $length_pure_2 = $row['length_pure_2']; $weight_dirty_2 = $row['weight_dirty_2']; $length_dirty_2 = $row['length_dirty_2'];
            $film_cost_3 = $row['film_cost_3']; $film_cost_per_unit_3 = $row['film_cost_per_unit_3']; $width_3 = $row['width_3']; $weight_pure_3 = $row['weight_pure_3']; $length_pure_3 = $row['length_pure_3']; $weight_dirty_3 = $row['weight_dirty_3']; $length_dirty_3 = $row['length_dirty_3'];
            $film_waste_cost_1 = $row['film_waste_cost_1']; $film_waste_weight_1 = $row['film_waste_weight_1']; $ink_cost = $row['ink_cost']; $ink_weight = $row['ink_weight']; $work_cost_1 = $row['work_cost_1']; $work_time_1 = $row['work_time_1'];
            $film_waste_cost_2 = $row['film_waste_cost_2']; $film_waste_weight_2 = $row['film_waste_weight_2']; $glue_cost_2 = $row['glue_cost_2']; $glue_expense_2 = $row['glue_expense_2']; $work_cost_2 = $row['work_cost_2']; $work_time_2 = $row['work_time_2'];
            $film_waste_cost_3 = $row['film_waste_cost_3']; $film_waste_weight_3 = $row['film_waste_weight_3']; $glue_cost_3 = $row['glue_cost_3']; $glue_expense_3 = $row['glue_expense_3']; $work_cost_3 = $row['work_cost_3']; $work_time_3 = $row['work_time_3'];
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
            <a class="btn btn-outline-dark mr-3" style="width: 3rem;" title="Скачать" href="csv.php?id=<?=$id ?>"><i class="fas fa-file-csv"></i></a>
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
            <div class="value mb-2"><?= CalculationBase::Display(floatval($cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($cost_per_unit), 3) ?> &#8381; за <?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
            <div class="mt-2">Себестоимость ПФ</div>
            <div class="value"><?= CalculationBase::Display(floatval($cliche_cost), 0) ?> &#8381;</div>
            <div class="value mb-2 font-weight-normal" id="right_panel_new_forms"><?=$new_forms_number ?>&nbsp;шт&nbsp;<?= CalculationBase::Display(($stream_width * $streams_number + 20) + ($ski == CalculationBase::NO_SKI ? 0 : 20), 0) ?>&nbsp;мм&nbsp;<i class="fas fa-times" style="font-size: small;"></i>&nbsp;<?= (intval($raport) + 20) ?>&nbsp;мм</div>            
        </div>
        <div class="col-4 pr-4">
            <h3>Отгрузочная стоимость</h3>
            <div>Отгрузочная стоимость</div>
            <div class="value"><span id="shipping_cost"><?= CalculationBase::Display(floatval($shipping_cost), 0) ?></span> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><span id="shipping_cost_per_unit"><?= CalculationBase::Display(floatval($shipping_cost_per_unit), 3) ?></span> &#8381; за <?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
            <div class="mt-2">Отгрузочная стоимость ПФ</div>
            <div class="value"><span id="shipping_cliche_cost"><?= CalculationBase::Display(floatval($shipping_cliche_cost), 0) ?></span> &#8381;</div>
        </div>
        <div class="col-4">
            <h3>Прибыль</h3>
            <div>Прибыль</div>
            <div class="value mb-2"><span id="income"><?= CalculationBase::Display(floatval($income), 0) ?></span> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><span id="income_per_unit"><?= CalculationBase::Display(floatval($income_per_unit), 3) ?></span> &#8381; за <?=(empty($unit) || $unit == 'kg' ? 'кг' : 'шт') ?></span></div>
            <div class="mt-2">Прибыль ПФ</div>
            <div class="value"><span id="income_cliche"><?= CalculationBase::Display(floatval($income_cliche), 0) ?></span> &#8381;</div>
        </div>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4"></div>
        <div class="col-4 pr-4"></div>
        <div class="col-4">
            <div>Итоговая прибыль</div>
            <div class="value mb-2"><span id="income_total"><?=CalculationBase::Display(round(floatval($income), 0) + round(floatval($income_cliche), 0), 0) ?></span> &#8381;</div>
        </div>
    </div>
    <div class="mt-3">
        <h2>Материалы&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;"><?= CalculationBase::Display(floatval($total_weight_dirty), 0) ?> кг</span></h2>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <h3>Основная пленка&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($weight_dirty_1), 0) ?> кг</span></h3>
            <div>Закупочная стоимость</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($film_cost_1), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_cost_per_unit_1), 3) ?> &#8381; за кг</span></div>
            <div>Ширина</div>
            <div class="value mb-2"><?= CalculationBase::Display(intval($width_1), 0) ?> мм</div>
            <div>Масса без приладки</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_pure_1), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_pure_1), 0) ?> м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_dirty_1), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_dirty_1), 0) ?> м</span></div>
        </div>
        <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
            <h3>Ламинация 1&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($weight_dirty_2), 0) ?> кг</span></h3>
            <div>Закупочная стоимость</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($film_cost_2), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_cost_per_unit_2), 3) ?> &#8381; за кг</span></div>
            <div>Ширина</div>
            <div class="value mb-2"><?= CalculationBase::Display(intval($width_2), 0) ?> мм</div>
            <div>Масса без приладки</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_pure_2), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_pure_2), 0) ?> м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_dirty_2), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_dirty_2), 0) ?> м</span></div>
        </div>
        <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
            <h3>Ламинация 2&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($weight_dirty_3), 0) ?> кг</span></h3>
            <div>Закупочная стоимость</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($film_cost_3), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_cost_per_unit_3), 3) ?> &#8381; за кг</span></div>
            <div>Ширина</div>
            <div class="value mb-2"><?= CalculationBase::Display(intval($width_3), 0) ?> мм</div>
            <div>Масса без приладки</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_pure_3), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_pure_3), 0) ?> м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_dirty_3), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_dirty_3), 0) ?> м</span></div>
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
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;"></div>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;"></div>
        </div>
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <div>Отходы</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($film_waste_cost_1), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_waste_weight_1), 2) ?> кг</span></div>
                <div>Краска</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($ink_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($ink_weight), 2) ?> кг</span></div>
                <div>Печать тиража</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($work_cost_1), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($work_time_1), 2) ?> ч</span></div>
            </div>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
                <div>Отходы</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($film_waste_cost_2), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_waste_weight_2), 2) ?> кг</span></div>
                <div>Клей</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($glue_cost_2), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($glue_expense_2), 2) ?> кг</span></div>
                <div>Работа ламинатора</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($work_cost_2), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($work_time_2), 2) ?> ч</span></div>
            </div>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
                <div>Отходы</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($film_waste_cost_3), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_waste_weight_3), 2) ?> кг</span></div>
                <div>Клей</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($glue_cost_3), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($glue_expense_3), 2) ?> кг</span></div>
                <div>Работа ламинатора</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($work_cost_3), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($work_time_3), 2) ?> ч</span></div>
            </div>
        </div>
    </div>
    <div style="clear:both"></div>
    <?php include 'change_status_buttons.php'; ?>
</div>