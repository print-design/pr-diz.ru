<?php
require_once '../include/topscripts.php';
require_once '../calculation/calculation.php';

$calculation_id = filter_input(INPUT_GET, 'id');

$length_dirty_1 = 0;

$sql = "select c.id, cr.length_dirty_1 "
        . "from calculation c "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "where c.id = $calculation_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $length_dirty_1 = $row['length_dirty_1'];
}
?>
<input type="hidden" name="calculation_id" value="<?=$calculation_id ?>" />
<input type="hidden" id="divide_total" value="<?=$length_dirty_1 ?>" />
<p><strong>Метраж исходного тиража:</strong> <?= CalculationBase::Display(floatval($length_dirty_1), 0) ?> м</p>
<div class="form-group">
    <label for="length1">Метраж первого тиража</label>
    <input type="text" 
           name="length1" 
           class="form-control int-only" 
           required="required" 
           autocomplete="off" 
           onmousedown="javascript: $(this).removeAttr('name');" 
           onfocus="javascript: $(this).removeAttr('name');" 
           onmouseup="javascript: $(this).attr('name', 'length1');" 
           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); }" 
           onkeyup="javascript: $(this).attr('name', 'length1');" 
           onfocusout="javascript: $(this).attr('name', 'length1');" />
</div>
<p><strong>Остаток тиража:</strong> <span id="divide_rest" class="text-danger"></span></p>