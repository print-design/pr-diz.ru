<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'electrocarist', 'auditor'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$cell = filter_input(INPUT_GET, 'cell');
if(empty($cell)) {
    header('Location: '.APPLICATION.'/car/');
}

// СТАТУС "СВОБОДНЫЙ"
$free_roll_status_id = 1;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '../include/style_mobile.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_mobile.php';
        ?>
        <div class="container-fluid">
            <?php
            include '../include/find_mobile.php';
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <?php
                    $sql = "select 'pallet' type, DATE_FORMAT(p.date, '%d.%m.%Y') date, p.id, s.name supplier, fb.name film_brand, p.id_from_supplier, p.width, p.thickness, p.cell, p.comment, "
                            . "(select sum(pr1.length) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = $free_roll_status_id)) length, "
                            . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = $free_roll_status_id)) weight, "
                            . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = $free_roll_status_id)) rolls_number "
                            . "from pallet p "
                            . "inner join supplier s on p.supplier_id=s.id "
                            . "inner join film_brand fb on p.film_brand_id=fb.id "
                            . "where p.cell='$cell' "
                            . "union "
                            . "select 'roll' type, DATE_FORMAT(r.date, '%d.%m.%Y') date, r.id, s.name supplier, fb.name film_brand, r.id_from_supplier, r.width, r.thickness, r.cell, r.comment, "
                            . "r.length length, "
                            . "r.net_weight weight, "
                            . "0 rolls_number "
                            . "from roll r "
                            . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                            . "inner join supplier s on r.supplier_id=s.id "
                            . "inner join film_brand fb on r.film_brand_id=fb.id "
                            . "where r.cell='$cell' and (rsh.status_id is null or rsh.status_id = $free_roll_status_id) order by id desc";
                    $fetcher = new Fetcher($sql);
                    while ($row = $fetcher->Fetch()):
                    $type = $row['type'];
                    $date = $row['date'];
                    $id = $row['id'];
                    $supplier = $row['supplier'];
                    $id_from_supplier = $row['id_from_supplier'];
                    $film_brand = $row['film_brand'];
                    $width = $row['width'];
                    $thickness = $row['thickness'];
                    $weight = $row['weight'];
                    $length = $row['length'];
                    $rolls_number = $row['rolls_number'];
                    $cell = $row['cell'];
                    $comment = htmlentities($row['comment']);
                    if(($type == 'pallet' && $rolls_number > 0) || $type == 'roll'):
                    ?>
                    <div class="object-card">
                        <h1><?=$type == 'pallet' ? "Паллет №П$id" : "Рулон №Р$id" ?></h1>
                        <p>от <?= $date ?></p>
                        <p><strong>Поставщик:</strong> <?=$supplier ?></p>
                        <p><strong>ID поставщика:</strong> <?=$id_from_supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки:</strong> <?=$film_brand ?></p>
                        <p><strong>Ширина:</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина:</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто:</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина:</strong> <?=$length ?> м</p>
                        <?php if($type == 'pallet'): ?>
                        <p><strong>Количество рулонов:</strong> <?=$rolls_number ?></p>
                        <?php endif; ?>
                        <p><strong>Комментарий:</strong></p>
                        <div><?=$comment ?></div>
                        <p style="font-size: 32px; line-height: 48px;">Ячейка&nbsp;&nbsp;&nbsp;&nbsp;<?=$cell ?></p>
                        <a href="<?=$type ?>_edit.php?id=<?=$id ?>&link=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-outline-dark w-100 mt-4">
                            <?php if(IsInRole(array('electrocarist'))): ?>
                            Сменить ячейку
                            <?php elseif (IsInRole(array('auditor'))): ?>
                            Оставить комментарий
                            <?php else: ?>
                            Редактировать
                            <?php endif; ?>
                        </a>
                    </div>
                    <?php endif; endwhile; ?>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
    </body>
</html>