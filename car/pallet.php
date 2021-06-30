<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'electrocarist'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/car/');
}

// СТАТУС "СРАБОТАННЫЙ" ДЛЯ РУЛОНА
$utilized_roll_status_id = 2;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '_style.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_mobile.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            include '_find.php';
            
            $sql = "select p.date, s.name supplier, fb.name film_brand, p.id_from_supplier, p.width, p.thickness, p.cell, p.comment, "
                    . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id <> $utilized_roll_status_id)) weight, "
                    . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id <> $utilized_roll_status_id)) rolls_number "
                    . "from pallet p "
                    . "inner join supplier s on p.supplier_id=s.id "
                    . "inner join film_brand fb on p.film_brand_id=fb.id "
                    . "where p.id=$id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $date = $row['date'];
                $supplier = $row['supplier'];
                $id_from_supplier = $row['id_from_supplier'];
                $film_brand = $row['film_brand'];
                $width = $row['width'];
                $thickness = $row['thickness'];
                $weight = $row['weight'];
                $rolls_number = $row['rolls_number'];
                $cell = $row['cell'];
                $comment = htmlentities($row['comment']);
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <h1>Паллет №П<?= filter_input(INPUT_GET, 'id') ?></h1>
                    <p>от <?=$date ?></p>
                    <p><strong>Поставщик</strong> <?=$supplier ?></p>
                    <p><strong>ID поставщика</strong> <?=$id_from_supplier ?></p>
                    <p class="mt-3"><strong>Характеристики</strong></p>
                    <p><strong>Марка пленки</strong> <?=$film_brand ?></p>
                    <p><strong>Ширина</strong> <?=$width ?> мм</p>
                    <p><strong>Толщина</strong> <?=$thickness ?> мкм</p>
                    <p><strong>Масса нетто</strong> <?=$weight ?> кг</p>
                    <p><strong>Количество рулонов</strong> <?=$rolls_number ?></p>
                    <p class="mt-3"><strong>Комментарий</strong></p>
                    <p><?=$comment ?></p>
                    <p class="mt-1" style="font-size: 32px; line-height: 48px;">Ячейка&nbsp;&nbsp;&nbsp;&nbsp;<?=$cell ?></p>
                    <a href="pallet_edit.php?id=<?=$id ?>" class="btn btn-outline-dark w-100 mt-4">Сменить ячейку</a>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '_footer.php';
        ?>
    </body>
</html>