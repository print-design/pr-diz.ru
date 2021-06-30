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
        <style>
            body {
                padding-left: 0;
            }
            
            .container-fluid {
                padding-left: 15px;
            }
            
            @media (min-width: 768px) {
                body {
                    padding-left: 60px;
                }
            }
            
            td {
                height: 2.2rem;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?=APPLICATION ?>/car/pallet.php?id=<?=$id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            
            include '_find.php';
            
            $sql = "select s.name supplier, fb.name film_brand, p.width, p.thickness, p.cell, "
                    . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id <> $utilized_roll_status_id)) weight, "
                    . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id <> $utilized_roll_status_id)) rolls_number "
                    . "from pallet p "
                    . "inner join supplier s on p.supplier_id=s.id "
                    . "inner join film_brand fb on p.film_brand_id=fb.id "
                    . "where p.id=$id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $supplier = $row['supplier'];
                $film_brand = $row['film_brand'];
                $width = $row['width'];
                $thickness = $row['thickness'];
                $weight = $row['weight'];
                $rolls_number = $row['rolls_number'];
                $cell = $row['cell'];
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <h1>Паллет №П<?= filter_input(INPUT_GET, 'id') ?></h1>
                    <table class="w-100 characteristics">
                        <tr>
                            <td class="font-weight-bold">Поставщик</td>
                            <td><?=$supplier ?></td>
                        </tr>
                    </table>
                    <h2>Характеристики</h2>
                    <table class="w-100 characteristics">
                        <tr>
                            <td class="font-weight-bold">Марка пленки</td>
                            <td><?=$film_brand ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Ширина</td>
                            <td><?=$width ?> мм</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Толщина</td>
                            <td><?=$thickness ?> мкм</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Масса нетто</td>
                            <td><?=$weight ?> кг</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Количество рулонов</td>
                            <td><?=$rolls_number ?></td>
                        </tr>
                    </table>
                    <p style="font-size: xx-large">Ячейка: <?=$cell ?></p>
                    <a href="pallet_edit.php?id=<?=$id ?>" class="btn btn-outline-dark w-100">Сменить ячейку</a>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '_footer.php';
        ?>
    </body>
</html>