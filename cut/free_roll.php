<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
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
                        <a class="nav-link" href="<?=APPLICATION ?>/cut/roll.php?id=<?=$id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            $sql = "select DATE_FORMAT(r.date, '%d.%m.%Y') date, s.name supplier, fb.name film_brand, r.id_from_supplier, r.width, r.thickness, r.net_weight, r.length, r.cell, r.comment, "
                    . "(select rsh.status_id from roll_status_history rsh where rsh.roll_id = r.id order by rsh.id desc limit 0, 1) status_id "
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
                $status_id = $row['status_id'];
                
                $status = '';
                $colour_style = '';
                
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
                        <h1 class="text-center">Рулон №<?=$title ?></h1>
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
                        <p><strong>Комментарий:</strong></p>
                        <p><?=$comment ?></p>
                        <a class="btn btn-dark w-100 mt-4" href="<?=APPLICATION ?>/cut/cut.php?id=<?=$id ?>&link=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Приступить к раскрою</a>
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