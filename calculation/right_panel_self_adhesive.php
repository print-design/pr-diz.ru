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
}

// Берём расчёт из таблицы базы
$usd = null; $euro = null; $cost = null; $cost_per_unit = null; $shipping_cost = null; $shipping_cost_per_unit = null; $income = null; $income_per_unit = null; $cliche_cost = null; $shipping_cliche_cost = null; $total_weight_dirty = null;
$film_cost = null; $film_cost_per_unit = null; $width = null; $weight_pure = null; $length_pure = null; $weight_dirty = null; $length_dirty = null;
$film_waste_cost = null; $film_waste_weight = null; $ink_cost = null; $ink_weight = null; $work_cost = null; $work_time = null;

$id = filter_input(INPUT_GET, 'id');

if(!empty($id)) {
    $usd = 0; $euro = 0; $cost = 0; $cost_per_unit = 0; $shipping_cost = 0; $shipping_cost_per_unit = 0; $income = 0; $income_per_unit = 0; $cliche_cost = 0; $shipping_cliche_cost = 0; $total_weight_dirty = 0;
    $film_cost = 0; $film_cost_per_unit = 0; $width = 0; $weight_pure = 0; $length_pure = 0; $weight_dirty = 0; $length_dirty = 0;
    $film_waste_cost = 0; $film_waste_weight = 0; $ink_cost = 0; $ink_weight = 0; $work_cost = 0; $work_time = 0;
}
?>
<div id="calculation"<?=$calculation_class ?>>
    <div class="d-flex justify-content-between p-2">
        <div>
            <h1>Расчет</h1>
        </div>
        <div>
            <a class="btn btn-outline-dark mr-3" style="width: 3rem;" title="Скачать" href="csv_self_adhesive.php?id=<?=$id ?>"><i class="fas fa-file-csv"></i></a>
            <a class="btn btn-outline-dark" target="_blank" style="width: 3rem;" title="Печать" href="print.php?id=<?=$id ?>"><i class="fa fa-print"></i></a>
        </div>
    </div>
    <div class="d-flex justify-content-start">
        <div class="mr-4">
            <div class="p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Наценка на тираж</div>
                <form method="post" class="form-inline">
                    <input type="hidden" name="id" value="<?=$id ?>" />
                    <div class="input-group">
                        <input type="text" 
                               id="extracharge" 
                               name="extracharge" 
                               style="width: 35px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                               value="<?=$extracharge ?>" 
                               required="required"
                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                               onmouseup="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                               onkeyup="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');" 
                               onfocusout="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');"<?=DISABLED_ATTR ?> />
                        <div class="input-group-append" style="height: 28px;">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-dark d-none" id="extracharge-submit" name="extracharge-submit">Сохранить</button>
                </form>
            </div>
        </div>
        <?php if($cliche_in_price != 1): ?>
        <div class="mr-4">
            <div class="p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Наценка на ПФ</div>
                <form method="post" class="form-inline">
                    <input type="hidden" name="id" value="<?=$id ?>" />
                    <div class="input-group">
                        <input type="text" 
                               id="extracharge_cliche" 
                               name="extracharge_cliche" 
                               style="width: 35px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                               value="<?=$extracharge_cliche ?>" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                               onmouseup="javascript: $(this).attr('id', 'extracharge_cliche'); $(this).attr('name', 'extracharge_cliche');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                               onkeyup="javascript: $(this).attr('id', 'extracharge_cliche'); $(this).attr('name', 'extracharge_cliche');" 
                               onfocusout="javascript: $(this).attr('id', 'extracharge_cliche'); $(this).attr('name', 'extracharge_cliche');"<?=DISABLED_ATTR ?> />
                        <div class="input-group-append" style="height: 28px;">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-dark d-none" id="extracharge-cliche-submit" name="extracharge-cliche-submit">Сохранить</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
        <div class="mr-4">
            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Курс евро</div>
                <?= number_format($euro, 2, ',', ' ') ?>
            </div>
        </div>
        <div>
            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Курс доллара</div>
                <?= number_format($usd, 2, ',', ' ') ?>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <h2>Стоимость</h2>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <h3>Себестоимость</h3>
            <div>Себестоимость <?=$cliche_in_price == 1 ? 'с' : 'без' ?> ПФ</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;"><?= CalculationBase::Display(floatval($cost_per_unit), 3) ?> &#8381; за шт</span></div>
            <div class="mt-2">Себестоимость ПФ</div>
            <div class="value"><?= CalculationBase::Display(floatval($cliche_cost), 0) ?> &#8381;</div>
            <div class="value mb-2 font-weight-normal" id="right_panel_new_forms"><?=$new_forms_number ?>&nbsp;шт&nbsp;0&nbsp;мм&nbsp;<i class="fas fa-times" style="font-size: small;"></i>&nbsp;0&nbsp;мм</div>
        </div>
    </div>
</div>