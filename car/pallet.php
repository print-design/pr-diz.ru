<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'electrocarist', 'auditor'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/car/');
}

// СТАТУС "СВОБОДНЫЙ"
$free_roll_status_id = 1;

// РОЛЬ "РЕВИЗОР"
const AUDITOR = 'auditor';
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
            $title = "П".filter_input(INPUT_GET, 'id');
            include '../include/find_mobile.php';
            
            $sql = "select DATE_FORMAT(p.date, '%d.%m.%Y') date, s.name supplier, fb.name film_brand, p.id_from_supplier, p.width, p.thickness, p.cell, p.comment, "
                    . "(select sum(pr1.length) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
                    . (IsInRole(AUDITOR) ? '' : " and (prsh1.status_id is null or prsh1.status_id = $free_roll_status_id)")
                    . ") length, "
                    . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
                    . (IsInRole(AUDITOR) ? '' : " and (prsh1.status_id is null or prsh1.status_id = $free_roll_status_id)")
                    . ") weight, "
                    . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
                    . (IsInRole(AUDITOR) ? '' : " and (prsh1.status_id is null or prsh1.status_id = $free_roll_status_id)")
                    . ") rolls_number "
                    . "from pallet p "
                    . "inner join supplier s on p.supplier_id=s.id "
                    . "inner join film_brand fb on p.film_brand_id=fb.id "
                    . "where p.id=$id";
            $fetcher = new Fetcher($sql);
            $row = $fetcher->Fetch();
            
            if($row && $row['rolls_number'] != 0):
                $date = $row['date'];
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
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1>Паллет №<?=$title ?></h1>
                        <p>от <?= $date ?></p>
                        <p><strong>Поставщик:</strong> <?=$supplier ?></p>
                        <p><strong>ID поставщика:</strong> <?=$id_from_supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки:</strong> <?=$film_brand ?></p>
                        <p><strong>Ширина:</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина:</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто:</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина:</strong> <?=$length ?> м</p>
                        <p><strong>Количество рулонов:</strong> <?=$rolls_number ?></p>
                        <p><strong>Комментарий:</strong></p>
                        <div style="white-space: pre-wrap;"><?= $comment ?></div>
                        <p style="font-size: 32px; line-height: 48px;">Ячейка&nbsp;&nbsp;&nbsp;&nbsp;<?=$cell ?></p>
                        <a href="pallet_edit.php?id=<?=$id ?>&link=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-outline-dark w-100 mt-4">
                            <?php if(IsInRole(array('electrocarist'))): ?>
                            Сменить ячейку
                            <?php elseif (IsInRole(array('auditor'))): ?>
                            Оставить комментарий
                            <?php else: ?>
                            Редактировать
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class='alert alert-danger'>Объект не найден</div>
            <?php endif; ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
    </body>
</html>