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
            $title = "Р".filter_input(INPUT_GET, 'id');    
            include '../include/find_mobile.php';
            
            $sql = "select DATE_FORMAT(r.date, '%d.%m.%Y') date, s.name supplier, f.name film, r.width, fv.thickness, r.net_weight, r.length, rch.cell, r.comment "
                    . "from roll r "
                    . "inner join supplier s on r.supplier_id=s.id "
                    . "inner join film_variation fv on r.film_variation_id=fv.id "
                    . "inner join film f on fv.film_id = f.id "
                    . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                    . "left join (select * from roll_cell_history where id in (select max(id) from roll_cell_history group by roll_id)) rch on rch.roll_id = r.id "
                    . "where r.id=$id"
                    . (IsInRole(ROLE_NAMES[ROLE_AUDITOR]) ? '' : " and (rsh.status_id is null or rsh.status_id = ".ROLL_STATUS_FREE.")");
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()):
                $date = $row['date'];
                $supplier = $row['supplier'];
                $film = $row['film'];
                $width = $row['width'];
                $thickness = $row['thickness'];
                $weight = $row['net_weight'];
                $length = $row['length'];
                $cell = $row['cell'];
                $comment = htmlentities($row['comment'] ?? '');
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1>Рулон №<?=$title ?></h1>
                        <p>от <?= $date ?></p>
                        <p><strong>Поставщик:</strong> <?=$supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки:</strong> <?=$film ?></p>
                        <p><strong>Ширина:</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина:</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто:</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина:</strong> <?=$length ?> м</p>
                        <p><strong>Комментарий:</strong></p>
                        <div style="white-space: pre-wrap;"><?=$comment ?></div>
                        <p style="font-size: 32px; line-height: 48px;">Ячейка&nbsp;&nbsp;&nbsp;&nbsp;<?=$cell ?></p>
                        <a href="roll_edit.php?id=<?=$id ?>&link=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-outline-dark w-100 mt-4">
                            <?php if(IsInRole(ROLE_NAMES[ROLE_ELECTROCARIST])): ?>
                            Сменить ячейку
                            <?php elseif (IsInRole(ROLE_NAMES[ROLE_AUDITOR])): ?>
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