<?php
$material = "";

if(!empty($techmap['other_brand_name']) && !empty($techmap['other_thickness'])) {
    $material = $techmap['other_brand_name']."(".$techmap['other_thickness']." мкм)";
}
else {
    $material = $techmap['brand_name']." (".$techmap['thickness']." мкм)";
}

if(!empty($techmap['lamination1_other_brand_name']) && !empty($techmap['lamination1_other_thickness'])) {
    $material .= ' + '.$techmap['lamination1_other_brand_name']."(".$techmap['lamination1_other_thickness']." мкм)";
}
elseif(!empty($techmap['lamination1_brand_name']) && !empty($techmap['lamination1_thickness'])) {
    $material .= ' + '.$techmap['lamination1_brand_name']." (".$techmap['lamination1_thickness']." мкм)";
}

if(!empty($techmap['lamination2_other_brand_name']) && !empty($techmap['lamination2_other_thickness'])) {
    $material .= ' + '.$techmap['lamination2_other_brand_name']."(".$techmap['lamination2_other_thickness']." мкм)";
}
elseif(!empty($techmap['lamination2_brand_name']) && !empty($techmap['lamination2_thickness'])) {
    $material .= ' + '.$techmap['lamination2_brand_name']." (".$techmap['lamination2_thickness']." мкм)";
}

$quantity = $techmap['quantity'];
if($techmap['unit'] == 'kg') {
    $quantity .= ' кг';
}
else {
    $quantity .= ' шт';
}
?>
<td class="<?=$top.' '.$dateshift['shift'] ?>"><?=$techmap['customer'] ?></td>
<td class="<?=$top.' '.$dateshift['shift'] ?>"><?=$techmap['name'] ?></td>
<td class="<?=$top.' '.$dateshift['shift'] ?>"><?=$material ?></td>
<td class="<?=$top.' '.$dateshift['shift'] ?>"><?=$quantity ?></td>
<td class="<?=$top.' '.$dateshift['shift'] ?>"><?=$techmap['machine'] ?></td>