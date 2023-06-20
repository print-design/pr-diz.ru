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
$free_status_id = 1;

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
            $sql = "select DATE_FORMAT(p.date, '%d.%m.%Y') date, s.name supplier, f.name film, pr.id_from_supplier, p.width, fv.thickness, pr.weight, pr.length, p.cell, p.comment, "
                    . "p.id pallet_id, pr.ordinal "
                    . "from pallet_roll pr "
                    . "inner join pallet p on pr.pallet_id = p.id "
                    . "inner join supplier s on p.supplier_id=s.id "
                    . "inner join film_variation fv on p.film_variation_id=fv.id "
                    . "inner join film f on fv.film_id = f.id "
                    . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                    . "where pr.id=$id"
                    . (IsInRole(AUDITOR) ? '' : " and (prsh.status_id is null or prsh.status_id = $free_status_id)");
            $fetcher = new Fetcher($sql);
            
            if($row = $fetcher->Fetch()):
                $date = $row['date'];
                $supplier = $row['supplier'];
                $id_from_supplier = $row['id_from_supplier'];
                $film = $row['film'];
                $width = $row['width'];
                $thickness = $row['thickness'];
                $weight = $row['weight'];
                $length = $row['length'];
                $cell = $row['cell'];
                $comment = htmlentities($row['comment']);
                $pallet_id = $row['pallet_id'];
                $ordinal = $row['ordinal'];
                
                $title = "П".$pallet_id."Р".$ordinal;
                include '../include/find_mobile.php';
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1>Рулон №<?=$title ?></h1>
                        <p>от <?= $date ?></p>
                        <p><strong>Поставщик:</strong> <?=$supplier ?></p>
                        <p><strong>ID поставщика:</strong> <?=$id_from_supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки:</strong> <?=$film ?></p>
                        <p><strong>Ширина:</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина:</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто:</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина:</strong> <?=$length ?> м</p>
                        <p><strong>Комментарий:</strong></p>
                        <div style="white-space: pre-wrap;"><?=$comment ?></div>
                        <p style="font-size: 32px; line-height: 48px;">Ячейка&nbsp;&nbsp;&nbsp;&nbsp;<?=$cell ?></p>
                        <a href="pallet_roll_edit.php?id=<?=$id ?>&link=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-outline-dark w-100 mt-4">
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
            <?php
            else:
            $title = "П".$pallet_id."Р".$ordinal;
            include '../include/find_mobile.php';
            ?>
            <div class='alert alert-danger'>Объект не найден</div>
            <?php endif; ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
    </body>
</html>