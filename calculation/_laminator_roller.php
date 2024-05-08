<?php
include '../include/topscripts.php';

$laminator_id = filter_input(INPUT_GET, 'laminator_id');
$min_width = filter_input(INPUT_GET, 'min_width');

$sql = "select value from norm_laminator_roller where laminator_id = $laminator_id and active = 1 ";

if(!empty($min_width)) {
    $sql .= "and value >= $min_width + 5 and value <= $min_width + 12 ";
}

$sql .= "order by value";

$grabber = new Grabber($sql);
$result = $grabber->result;

if(count($result) == 0):
?>
<option value="" hidden="hidden">Нет вала</option>
<?php else: ?>
<option value='' hidden='hidden'>Ширина ламинирующего вала...</option>
<?php foreach ($result as $row): ?>
<option><?=$row['value'] ?></option>
<?php
endforeach;
endif;
?>
<option disabled="disabled">-</option>
<option value="-1">Ввести вручную...</option>