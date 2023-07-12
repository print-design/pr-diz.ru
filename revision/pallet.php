<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_AUDITOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/revision/');
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
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <?php if(empty(filter_input(INPUT_GET, 'link'))): ?>
                        <a class="nav-link" href="<?=APPLICATION ?>/revision/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            $title = "П$id";
            include '../include/find_mobile.php';
            
            $sql = "select DATE_FORMAT(p.date, '%d.%m.%Y') date, s.name supplier, f.name film, p.width, fv.thickness, p.cell, p.comment, "
                    . "(select sum(pr1.length) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) length, "
                    . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) weight, "
                    . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) rolls "
                    . "from pallet p "
                    . "inner join supplier s on p.supplier_id=s.id "
                    . "inner join film_variation fv on p.film_variation_id=fv.id "
                    . "inner join film f on fv.film_id = f.id "
                    . "where p.id = $id";
            $fetcher = new Fetcher($sql);
            $row = $fetcher->Fetch();
            
            if($row):
            $date = $row['date'];
            $supplier = $row['supplier'];
            $film = $row['film'];
            $width = $row['width'];
            $thickness = $row['thickness'];
            $weight = $row['weight'];
            $length = $row['length'];
            $comment = htmlentities($row['comment']);
            
            $rolls = $row['rolls'];
            if(null !== filter_input(INPUT_POST, 'rolls')) {
                $rolls = filter_input(INPUT_POST, 'rolls');
            }
            
            $cell = $row['cell'];
            if(null !== filter_input(INPUT_POST, 'cell')) {
                $cell = filter_input(INPUT_POST, 'cell');
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1>Паллет №П<?= filter_input(INPUT_GET, 'id') ?></h1>
                        <p>от <?=$date ?></p>
                        <p><strong>Поставщик:</strong> <?=$supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки:</strong> <?=$film ?></p>
                        <p><strong>Ширина:</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина:</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто:</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина:</strong> <?=$length ?> м</p>
                        <p><strong>Комментарий:</strong></p>
                        <div style="white-space: pre-wrap;"><?=$comment ?></div>
                        <p style="font-size: 32px;" class="mt-3">Рулонов&nbsp;&nbsp;&nbsp;&nbsp;<?=$rolls ?></p>
                        <p style="font-size: 32px;" class="mt-3">Ячейка&nbsp;&nbsp;&nbsp;&nbsp;<?=$cell ?></p>
                        <a href="pallet_edit.php" class="btn btn-dark form-control mt-4">Редактировать</a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-danger">Объект не найден</div>
            <?php endif; ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
        <script>
            $('#rolls').focus();
        </script>
    </body>
</html>