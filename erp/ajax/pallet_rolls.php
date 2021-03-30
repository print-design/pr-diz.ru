<?php
include '../include/topscripts.php';

$pallet_id = filter_input(INPUT_GET, 'id');
if(!empty($pallet_id)) {
    // Получение всех статусов
    $fetcher = (new Fetcher("select id, name, colour from roll_status"));
    $statuses = array();
    
    while ($row = $fetcher->Fetch()) {
        $status = array();
        $status['name'] = $row['name'];
        $status['colour'] = $row['colour'];
        $statuses[$row['id']] = $status;
    }

    // Получение объекта
    $sql = "select p.width, p.thickness, p.comment, pr.id, pr.pallet_id, pr.weight, pr.length, pr.ordinal, psh.status_id status_id "
            . "from pallet_roll pr "
            . "inner join pallet p on pr.pallet_id = p.id "
            . "left join (select * from pallet_status_history where id in (select max(id) from pallet_status_history group by pallet_id)) psh on psh.pallet_id = p.id "
            . "where pr.pallet_id = $pallet_id order by ordinal";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()):
        
    $status = '';
    if(!empty($statuses[$row['status_id']]['name'])) {
        $status = $statuses[$row['status_id']]['name'];
    }
    
    $colour_style = '';
    if(!empty($statuses[$row['status_id']]['colour'])) {
        $colour = $statuses[$row['status_id']]['colour'];
        $colour_style = " color: $colour;";
    }
    ?>
<button type="button" class="close" data-dismiss='modal' style="position: absolute; right: 10px; top: 10px; z-index: 2000;"><img src="../images/icons/close_modal.png" /></button>
<table style="margin-top: 50px; font-size: 14px;">
    <tbody>
    <tr>
        <td style="text-align: right; padding-right: 20px; padding-bottom: 10px;"><input type="checkbox" /></td>
        <td style="padding-right: 20px; padding-bottom: 10px;">Рулон <?=$row['ordinal'] ?></td>
        <td style="padding-right: 20px; padding-bottom: 10px;"><a href="roll.php?id=<?=$row['id'] ?>" <i class="fas fa-ellipsis-h"></i></td>
        <td></td>
    </tr>
    <tr>
        <td style="padding-right: 20px; padding-bottom: 10px;">Ширина</td>
        <td style="padding-right: 20px; padding-bottom: 10px;"><?=$row['width'] ?> мм</td>
        <td style="padding-right: 20px; padding-bottom: 10px;">Толщина</td>
        <td><?=$row['thickness'] ?> мкм</td>
    </tr>
    <tr>
        <td style="padding-right: 20px; padding-bottom: 10px;">Масса</td>
        <td style="padding-right: 20px; padding-bottom: 10px;"><?=$row['weight'] ?> кг</td>
        <td style="padding-right: 20px; padding-bottom: 10px;">Длина</td>
        <td><?=$row['length'] ?> м</td>
    </tr>
    <tr>
        <td style="padding-right: 20px; padding-bottom: 10px;">ID</td>
        <td style="padding-right: 20px; padding-bottom: 10px;"><?="П".$row['pallet_id']."Р".$row['ordinal'] ?></td>
        <td style="padding-right: 20px; padding-bottom: 10px;">Статус</td>
        <td style="font-size: 10px;<?=$colour_style ?>"><?=mb_strtoupper($status) ?></td>
    </tr>
    <tr>
        <td style="padding-right: 20px; padding-bottom: 10px;">Комментарий</td>
        <td colspan="3" style="padding-bottom: 10px;"><?=$row['comment'] ?></td>
    </tr>
    </tbody>
</table>
    <?php
    endwhile;
}
?>