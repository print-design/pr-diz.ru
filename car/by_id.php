<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ELECTROCARIST], ROLE_NAMES[ROLE_AUDITOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/car/');
}
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
                    $sql = "select 'pallet' type, DATE_FORMAT(p.date, '%d.%m.%Y') date, p.id, 0 pallet_id, 0 ordinal, s.name supplier, f.name film, p.width, fv.thickness, p.cell, p.comment, "
                            . "(select sum(pr1.length) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
                            . (IsInRole(ROLE_NAMES[ROLE_AUDITOR]) ? '' : " and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")")
                            . ") length, "
                            . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
                            . (IsInRole(ROLE_NAMES[ROLE_AUDITOR]) ? '' : " and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")")
                            . ") weight, "
                            . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
                            . (IsInRole(ROLE_NAMES[ROLE_AUDITOR]) ? '' : " and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")")
                            . ") rolls_number "
                            . "from pallet p "
                            . "inner join supplier s on p.supplier_id=s.id "
                            . "inner join film_variation fv on p.film_variation_id=fv.id "
                            . "inner join film f on fv.film_id = f.id "
                            . "where p.id='$id' "
                            . "union "
                            . "select 'roll' type, DATE_FORMAT(r.date, '%d.%m.%Y') date, r.id, 0 pallet_id, 0 ordinal, s.name supplier, f.name film, r.width, fv.thickness, r.cell, r.comment, "
                            . "r.length length, "
                            . "r.net_weight weight, "
                            . "0 rolls_number "
                            . "from roll r "
                            . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                            . "inner join supplier s on r.supplier_id=s.id "
                            . "inner join film_variation fv on r.film_variation_id=fv.id "
                            . "inner join film f on fv.film_id = f.id "
                            . "where r.id='$id'"
                            . (IsInRole(ROLE_NAMES[ROLE_AUDITOR]) ? '' : " and (rsh.status_id is null or rsh.status_id = ".ROLL_STATUS_FREE.")")
                            . "order by id desc";
                    $fetcher = new Fetcher($sql);
                    while ($row = $fetcher->Fetch()):
                    $type = $row['type'];
                    $date = $row['date'];
                    $id = $row['id'];
                    $pallet_id = $row['pallet_id'];
                    $ordinal = $row['ordinal'];
                    $supplier = $row['supplier'];
                    $film = $row['film'];
                    $width = $row['width'];
                    $thickness = $row['thickness'];
                    $weight = $row['weight'];
                    $length = $row['length'];
                    $rolls_number = $row['rolls_number'];
                    $cell = $row['cell'];
                    $comment = htmlentities($row['comment'] ?? '');
                    if(($type == 'pallet' && $rolls_number > 0) || $type == 'roll' || $type == 'pallet_roll'):
                    ?>
                    <div class="object-card">
                        <h1>
                        <?php
                        switch ($type) {
                            case 'pallet':
                                echo "Паллет №П$id";
                                break;
                            case 'roll':
                                echo "Рулон №Р$id";
                                break;
                            case 'pallet_roll':
                                echo "Рулон паллета №П{$pallet_id}";
                                break;
                        }
                        ?>
                        </h1>
                        <p>от <?= $date ?></p>
                        <p><strong>Поставщик:</strong> <?=$supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки:</strong> <?=$film ?></p>
                        <p><strong>Ширина:</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина:</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто:</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина:</strong> <?=$length ?> м</p>
                        <?php if($type == 'pallet'): ?>
                        <p><strong>Количество рулонов:</strong> <?=$rolls_number ?></p>
                        <?php endif; ?>
                        <p><strong>Комментарий:</strong></p>
                        <div style="white-space: pre-wrap;"><?=$comment ?></div>
                        <p style="font-size: 32px; line-height: 48px;">Ячейка&nbsp;&nbsp;&nbsp;&nbsp;<?=$cell ?></p>
                        <a href="<?=$type ?>_edit.php?id=<?=$id ?>&link=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-outline-dark w-100 mt-4">
                            <?php if(IsInRole(ROLE_NAMES[ROLE_ELECTROCARIST])): ?>
                            Сменить ячейку
                            <?php elseif (IsInRole(ROLE_NAMES[ROLE_AUDITOR])): ?>
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