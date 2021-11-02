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
<td class="<?=$top.' '.$dateshift['shift'] ?>">
    <?php if(!empty($techmap['grafik_id']) && !empty($techmap['machine_id'])): ?>
    <div class="d-inline-block">В графике</div>
    <div class="d-inline-block">
        <form method="post">
            <input type="hidden" name="grafik_id" value="<?=$techmap['grafik_id'] ?>" />
            <input type="hidden" name="scroll" />
            <button type="submit" name="remove-from-grafik-submit" class="btn btn-dark btn-sm confirmable">Удалить</button>
        </form>
    </div>
    <?php elseif(!empty($techmap['machine_id'])): ?>
    <form method="post">
        <input type="hidden" name="id" value="<?=$techmap['id'] ?>" />
        <input type="hidden" name="date" value="<?=$dateshift['date']->format('Y-m-d') ?>" />
        <input type="hidden" name="shift" value="<?=$dateshift['shift'] ?>" />
        <input type="hidden" name="machine_id" value="<?=$techmap['machine_id'] ?>" />
        <input type="hidden" name="scroll" />
        <button type="submit" name="grafik-submit" class="btn btn-outline-dark">В график</button>
    </form>
    <?php endif; ?>
</td>