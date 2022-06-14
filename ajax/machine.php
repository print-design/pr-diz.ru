<?php
include '../include/topscripts.php';

$work_type_id = filter_input(INPUT_GET, 'work_type_id');

if(!empty($work_type_id)):
$sql = "select m.id, m.name, m.colorfulness from machine m inner join machine_work_type mwt on mwt.machine_id = m.id where mwt.work_type_id = $work_type_id order by m.position";
$grabber = new Grabber($sql);
$machines = $grabber->result;

if(count($machines) > 1):
?>
<option value="" hidden="hidden" selected="selected">Печатная машина...</option>
<?php
endif;

foreach($machines as $machine):
?>
<option value="<?=$machine['id'] ?>"><?=$machine['name'] ?> (<?=$machine['colorfulness'] ?> красок)</option>
<?php
endforeach;

endif;
?>