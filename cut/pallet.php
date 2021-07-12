<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
$roll_id = filter_input(INPUT_GET, 'roll_id');
if(empty($id) || empty($roll_id)) {
    header('Location: '.APPLICATION.'/cut/');
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

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

// СТАТУС "СРАБОТАННЫЙ"
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
        include '../include/style_mobile.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <?php if(empty(filter_input(INPUT_GET, 'link'))): ?>
                        <a class="nav-link" href="<?=APPLICATION ?>/cut/pallet_roll.php?id=<?=$roll_id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        <?php else: ?>
                        <a class="nav-link" href="<?= urldecode(filter_input(INPUT_GET, 'link')) ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <?php
            $sql = "select DATE_FORMAT(p.date, '%d.%m.%Y') date, s.name supplier, fb.name film_brand, p.id_from_supplier, p.width, p.thickness, p.cell, p.comment, "
                    . "(select sum(pr1.length) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id <> $utilized_status_id)) length, "
                    . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id <> $utilized_status_id)) weight, "
                    . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id <> $utilized_status_id)) rolls_number "
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
                $length = $row['length'];
                $rolls_number = $row['rolls_number'];
                $cell = $row['cell'];
                $comment = htmlentities($row['comment']);
                $title = "П".filter_input(INPUT_GET, 'id');
                
                $status = '';
                $colour_style = '';
                
                $status_id = 0;
                
                if($rolls_number == 0) {
                    $status_id = $utilized_status_id;
                }
                else {
                    $status_id = $free_status_id;
                }
                
                if(!empty($statuses[$status_id]['name'])) {
                    $status = $statuses[$status_id]['name'];
                }
                    
                if(!empty($statuses[$status_id]['colour'])) {
                    $colour = $statuses[$status_id]['colour'];
                    $colour_style = " color: $colour";
                }
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1 class="text-center">Паллет №<?=$title ?></h1>
                        <p><strong>Поставщик:</strong> <?=$supplier ?></p>
                        <p><strong>ID поставщика:</strong> <?=$id_from_supplier ?></p>
                        <p><strong>Дата поставки:</strong> <?= $date ?></p>
                        <p><strong>Статус:</strong> <span style="<?=$colour_style ?>"><?= mb_strtoupper($status) ?></span></p>
                        <p class="mt-3 mb-2 text-center" style="font-size: 1.3rem;"><stro1ng>Характеристики</stro1ng></p>
                        <p><strong>Марка пленки:</strong> <?=$film_brand ?></p>
                        <p><strong>Ширина:</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина:</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто:</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина:</strong> <?=$length ?> м</p>
                        <p><strong>Рулонов в паллете:</strong> <?=$rolls_number ?> шт</p>
                        <p><strong>Комментарий:</strong></p>
                        <p><?=$comment ?></p>
                        <a class="btn btn-dark w-100 mt-4" href="<?=APPLICATION ?>/cut/cut.php?id=<?=$roll_id ?>&pallet=1&link=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Приступить к раскрою</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
    </body>
</html>