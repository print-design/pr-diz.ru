<?php
include '../include/constants.php';

$work_type_id = filter_input(INPUT_GET, 'work_type_id');

if(count(WORK_TYPE_PRINTERS[$work_type_id]) > 1):
?>
<option value="" hidden="hidden" selected="selected">Печатная машина...</option>
<?php
endif;

foreach(WORK_TYPE_PRINTERS[$work_type_id] as $printer):
?>
<option value="<?=$printer ?>"><?=PRINTER_NAMES[$printer] ?> (<?=PRINTER_COLORFULLNESSES[$printer] ?> красок)</option>
<?php
endforeach;
?>