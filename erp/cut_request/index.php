<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Получение данных
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/jquery-ui.css" rel="stylesheet"/>
    </head>
    <body>
        <?php
        include '../include/header.php';
        include '../include/pager_top.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <h1>Заявки на раскрой</h1>
                </div>
                <div class="p-1">
                    <button class="btn btn-outline-dark disabled" data-toggle="modal" data-target="#filterModal" data-text="Фильтр" style="padding-left: 14px; padding-right: 42px; padding-bottom: 14px; padding-top: 14px;"><img src="../images/icons/filter.svg" style="margin-right: 20px;" />Фильтр</button>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr style="border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <th></th>
                        <th>Дата создания</th>
                        <th>Марка пленки</th>
                        <th>Толщина мкм</th>
                        <th>Ширина мкм</th>
                        <th>Длина раскроя</th>
                        <th>ID рулона</th>
                        <th>№ ячейки</th>
                        <th>Как режем</th>
                        <th>Менеджер</th>
                        <th>Для заказа</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $where = '';
                    
                    $sql = "select cr.date, fb.name film_brand, p.thickness, p.width, cr.length, p.inner_id, p.cell, "
                            . "(select group_concat(width separator '-') from stream where cut_request_id = cr.id) widths, "
                            . "(select group_concat(request separator '<br />') from stream where cut_request_id = cr.id) requests, "
                            . "psh.id status_id, ps.name status, ps.colour "
                            . "from cut_request cr "
                            . "inner join pallet p on cr.pallet_id = p.id "
                            . "inner join film_brand fb on p.film_brand_id = fb.id "
                            . "left join (select * from pallet_status_history where id in (select max(id) from pallet_status_history group by pallet_id)) psh on psh.pallet_id = p.id "
                            . "left join pallet_status ps on psh.status_id = ps.id "
                            . "$where "
                            . "order by cr.id desc limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    
                    while ($row = $fetcher->Fetch()):
                    $status = $row['status'];
                    $colour = '';
                    if(!empty($row['colour'])) {
                        $colour = " style='color: ".$row['colour']."'";
                    }
                    ?>
                    <tr style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <td></td>
                        <td><?= empty($row['date']) ? '' : date_create_from_format('Y-m-d', $row['date'])->format('d.m.Y') ?></td>
                        <td><?=$row['film_brand'] ?></td>
                        <td><?=$row['thickness'] ?></td>
                        <td><?=$row['width'] ?></td>
                        <td><?=$row['length'] ?></td>
                        <td><?=$row['inner_id'] ?></td>
                        <td><?=$row['cell'] ?></td>
                        <td><?=$row['widths'] ?></td>
                        <td></td>
                        <td><?=$row['requests'] ?></td>
                        <td<?=$colour ?>><?=$row['status'] ?></td>
                        <td></td>
                    </tr>
                    <?php
                    endwhile;
                    ?>
                </tbody>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>