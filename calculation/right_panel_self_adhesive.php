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
$usd = null; $euro = null;
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
</div>