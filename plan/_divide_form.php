<?php
require_once '../include/topscripts.php';

$calculation_id = filter_input(INPUT_GET, 'id');
$lamination = filter_input(INPUT_GET, 'lamination');
$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');

$length_dirty = 0;

$sql = "select c.id, cr.length_dirty_1, cr.length_dirty_2, cr.length_dirty_3 "
        . "from calculation c "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "where c.id = $calculation_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    if($work_id == WORK_LAMINATION && $lamination == 1) {
        $length_dirty = $row['length_dirty_2'];
    }
    elseif($work_id == WORK_LAMINATION && $lamination == 2) {
        $length_dirty = $row['length_dirty_3'];
    }
    else {
        $length_dirty = $row['length_dirty_1'];
    }
}
?>
<input type="hidden" name="calculation_id" value="<?=$calculation_id ?>" />
<input type="hidden" name="work_id" value="<?=$work_id ?>" />
<input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
<input type="hidden" name="lamination" value="<?=$lamination ?>" />
<input type="hidden" id="divide_total" value="<?=$length_dirty ?>" />
<input type="hidden" name="scroll" />
<p><strong>Метраж исходного тиража:</strong> <?= DisplayNumber(floatval($length_dirty), 0) ?> м</p>
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