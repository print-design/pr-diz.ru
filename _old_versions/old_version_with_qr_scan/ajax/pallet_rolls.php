<?php
include '../include/topscripts.php';

// СТАТУС "СВОБОДНЫЙ" ДЛЯ РУЛОНА
$free_status_id = 1;

$pallet_id = filter_input(INPUT_GET, 'id');
$getstring = filter_input(INPUT_GET, 'getstring');
$decoded = rawurldecode($getstring);
$previous_params_string = "?";

if(!empty($decoded)) {
    $previous_params_string = $getstring."&";
}

if(!empty($pallet_id)) {
    // Получение марки плёнки
    $film = '';
    $sql = "select f.name, fv.thickness from film f inner join film_variation fv on fv.film_id = f.id inner join pallet p on p.film_variation_id = fv.id where p.id = $pallet_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $film = $row['name'];
        $thickness = $row['thickness'];
    }
    
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
    $sql = "select 0 utilized,  p.width, p.comment, pr.id, pr.pallet_id, pr.weight, pr.length, pr.ordinal, pr.id_from_supplier, IFNULL(prsh.status_id, $free_status_id) status_id "
            . "from pallet_roll pr "
            . "inner join pallet p on pr.pallet_id = p.id "
            . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
            . "where pr.pallet_id = $pallet_id and (prsh.status_id is null or prsh.status_id = $free_status_id) "
            . "union "
            . "select 1 utilized,  p.width, p.comment, pr.id, pr.pallet_id, pr.weight, pr.length, pr.ordinal, pr.id_from_supplier, IFNULL(prsh.status_id, $free_status_id) status_id "
            . "from pallet_roll pr "
            . "inner join pallet p on pr.pallet_id = p.id "
            . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
            . "where pr.pallet_id = $pallet_id and prsh.status_id is not null and prsh.status_id <> $free_status_id "
            . "order by utilized, ordinal";
    $fetcher = new Fetcher($sql);
    
    $inutilized = true;
    $utilized = false;
    $utilized_style = '';
    
    while ($row = $fetcher->Fetch()):
        
    if($row['utilized'] == 1) {
        $utilized = true;
    }
    
    if($utilized) {
        $inutilized = false;
        $utilized_style = " background-color: #EEEEEE; border-top: solid 4px #FFFFFF;";
    }

    $status = '';
    if(!empty($statuses[$row['status_id']]['name'])) {
        $status = $statuses[$row['status_id']]['name'];
    }
    
    $colour_style = '';
    if(!empty($statuses[$row['status_id']]['colour'])) {
        $colour = $statuses[$row['status_id']]['colour'];
        $colour_style = "color: $colour";
    }
    ?>
<div style="padding: 10px;<?=$utilized_style ?>">
    <table style="margin-top: 25px; margin-bottom: 25px; font-size: 14px;">
        <tbody>
            <tr>
                <td style="text-align: right; padding-bottom: 10px; padding-right: 10px; width: 20%;"><input type="checkbox" /></td>
                <td style="padding-bottom: 10px; width: 30%;">Рулон <?=$row['ordinal'] ?></td>
                <td style="padding-bottom: 10px; width: 17%;"><a href="roll.php<?=$previous_params_string ?>id=<?=$row['id'] ?>"><i class="fas fa-ellipsis-h"></i></a></td>
                <td style="padding-bottom: 10px;"></td>
            </tr>
            <tr>
                <td style="padding-bottom: 10px; padding-right: 10px; white-space: nowrap;">Марка пленки</td>
                <td colspan="3" style="padding-bottom: 10px;"><?=$film ?></td>
            </tr>
            <tr>
                <td style="padding-bottom: 10px;">Ширина</td>
                <td style="padding-bottom: 10px;"><?=$row['width'] ?> мм</td>
                <td style="padding-bottom: 10px;">Толщина</td>
                <td style="padding-bottom: 10px;"><?=$thickness ?> мкм</td>
            </tr>
            <tr>
                <td style="padding-bottom: 10px;">Масса</td>
                <td style="padding-bottom: 10px;"><?=$row['weight'] ?> кг</td>
                <td style="padding-bottom: 10px;">Длина</td>
                <td style="padding-bottom: 10px;"><?=$row['length'] ?> м</td>
            </tr>
            <tr>
                <td style="padding-bottom: 10px;">ID</td>
                <td style="padding-bottom: 10px;"><?="П".$row['pallet_id']."Р".$row['ordinal'] ?></td>
                <td style="padding-bottom: 10px;">Статус</td>
                <td style="padding-bottom: 10px; font-size: 10px;<?=$colour_style ?>"><?=mb_strtoupper($status) ?></td>
            </tr>
            <tr>
                <td style="padding-bottom: 10px;">ID от поставщ.</td>
                <td colspan="3" style="padding-bottom: 10px;"><?=$row['id_from_supplier'] ?></td>
            </tr>
            <tr>
                <td style="padding-bottom: 10px; padding-right: 10px;">Комментарий</td>
                <td colspan="3" style="padding-bottom: 10px;"><?=$row['comment'] ?></td>
            </tr>
        </tbody>
    </table>
</div>
    <?php
    endwhile;
}
?>
<button type="button" class="close" data-dismiss='modal' style="position: absolute; right: 10px; top: 10px; z-index: 2000;"><img src="../images/icons/close_modal_red.svg" /></button>