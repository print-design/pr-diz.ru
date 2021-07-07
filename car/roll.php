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
$utilized_status_id = 2;
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
            $sql = "select DATE_FORMAT(r.date, '%d.%m.%Y') date, s.name supplier, fb.name film_brand, r.id_from_supplier, r.width, r.thickness, r.net_weight, r.length, r.cell, r.comment "
                    . "from roll r "
                    . "inner join supplier s on r.supplier_id=s.id "
                    . "inner join film_brand fb on r.film_brand_id=fb.id "
                    . "where r.id=$id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $date = $row['date'];
                $supplier = $row['supplier'];
                $id_from_supplier = $row['id_from_supplier'];
                $film_brand = $row['film_brand'];
                $width = $row['width'];
                $thickness = $row['thickness'];
                $weight = $row['net_weight'];
                $length = $row['length'];
                $cell = $row['cell'];
                $comment = htmlentities($row['comment']);
                $title = "Р".filter_input(INPUT_GET, 'id');
                
                include '_find.php';
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1>Рулон №<?=$title ?></h1>
                        <p>от <?= $date ?></p>
                        <p><strong>Поставщик</strong> <?=$supplier ?></p>
                        <p><strong>ID поставщика</strong> <?=$id_from_supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки</strong> <?=$film_brand ?></p>
                        <p><strong>Ширина</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина</strong> <?=$length ?> м</p>
                        <p class="mt-3"><strong>Комментарий</strong></p>
                        <p><?=$comment ?></p>
                        <p class="mt-1" style="font-size: 32px; line-height: 48px;">Ячейка&nbsp;&nbsp;&nbsp;&nbsp;<?=$cell ?></p>
                        <a href="roll_edit.php?id=<?=$id ?>&link=<?=$_SERVER['REQUEST_URI'] ?>" class="btn btn-outline-dark w-100 mt-4">Сменить ячейку</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '_footer.php';
        ?>
    </body>
</html>