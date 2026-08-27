<?php
include '../include/topscripts.php';

$laminator_id = filter_input(INPUT_GET, 'laminator_id', FILTER_VALIDATE_INT);
$min_width = filter_input(INPUT_GET, 'min_width');

$sql = "select value from norm_laminator_roller where laminator_id = ? and active = 1 ";
$params = [$laminator_id];

if(!empty($min_width)) {
    $sql .= "and value >= ? and value <= ? ";
    $params[] = $min_width + 5;
    $params[] = $min_width + 12;
}

$sql .= "order by value";

$grabber = new Grabber($sql, $params);
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