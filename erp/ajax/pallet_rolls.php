<?php
include '../include/topscripts.php';

$pallet_id = filter_input(INPUT_GET, 'id');
if(!empty($pallet_id)) {
    $sql = "select p.width, p.thickness, p.comment, pr.pallet_id, pr.weight, pr.length, pr.ordinal from pallet_roll pr inner join pallet p on pr.pallet_id = p.id where pr.pallet_id = $pallet_id order by ordinal";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()):
    ?>
<button type="button" class="close" data-dismiss='modal' style="position: absolute; right: 10px; top: 10px; z-index: 2000;"><img src="../images/icons/close_modal.png" /></button>
<table style="margin-top: 50px;">
    <tbody>
    <tr>
        <td style="text-align: right; padding-right: 20px;"><input type="checkbox" /></td>
        <td style="padding-right: 20px;">Рулон <?=$row['ordinal'] ?></td>
        <td style="padding-right: 20px;"><i class="fas fa-ellipsis-h"></i></td>
        <td></td>
    </tr>
    <tr>
        <td style="padding-right: 20px;">Ширина</td>
        <td style="padding-right: 20px;"><?=$row['width'] ?> мм</td>
        <td style="padding-right: 20px;">Толщина</td>
        <td><?=$row['thickness'] ?> мкм</td>
    </tr>
    <tr>
        <td style="padding-right: 20px;">Масса</td>
        <td style="padding-right: 20px;"><?=$row['weight'] ?> кг</td>
        <td style="padding-right: 20px;">Длина</td>
        <td><?=$row['length'] ?> м</td>
    </tr>
    <tr>
        <td style="padding-right: 20px;">ID</td>
        <td style="padding-right: 20px;"><?="П".$row['pallet_id']."Р".$row['ordinal'] ?></td>
        <td style="padding-right: 20px;">Статус</td>
        <td></td>
    </tr>
    <tr>
        <td style="padding-right: 20px;">Комментарий</td>
        <td colspan="3"><?=$row['comment'] ?></td>
    </tr>
    </tbody>
</table>
    <?php
    endwhile;
}
?>