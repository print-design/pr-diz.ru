<?php
include '../include/constants.php';

$work_type_id = filter_input(INPUT_GET, 'work_type_id');
$machines = WORK_TYPE_PRINTERS[$work_type_id];

if(count($machines) > 1):
?>
<option value="" hidden="hidden" selected="selected">Печатная машина...</option>
<?php
endif;

foreach($machines as $machine):
?>
<option value="<?=$machine ?>"><?=PRINTER_NAMES[$machine] ?> (<?=PRINTER_COLORFULLNESSES[$machine] ?> красок)</option>
<?php
endforeach;
?>