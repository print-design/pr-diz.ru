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
        <?php
        include '../include/header_mobile.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            include '_find.php';
            
            $sql = "select s.name supplier, fb.name film_brand, r.id_from_supplier, r.width, r.thickness, r.net_weight, r.length, r.cell "
                    . "from roll r "
                    . "inner join supplier s on r.supplier_id=s.id "
                    . "inner join film_brand fb on r.film_brand_id=fb.id "
                    . "where r.id=$id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $supplier = $row['supplier'];
                $id_from_supplier = $row['id_from_supplier'];
                $film_brand = $row['film_brand'];
                $width = $row['width'];
                $thickness = $row['thickness'];
                $weight = $row['net_weight'];
                $length = $row['length'];
                $cell = $row['cell'];
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <h1>Рулон №Р<?= filter_input(INPUT_GET, 'id') ?></h1>
                    <table class="w-100 characteristics">
                        <tr>
                            <td class="font-weight-bold w-50">Поставщик</td>
                            <td><?=$supplier ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">ID поставщика</td>
                            <td><?=$id_from_supplier ?></td>
                        </tr>
                    </table>
                    <h2>Характеристики</h2>
                    <table class="w-100 characteristics">
                        <tr>
                            <td class="font-weight-bold w-50">Марка пленки</td>
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
                            <td class="font-weight-bold">Длина</td>
                            <td><?=$length ?></td>
                        </tr>
                    </table>
                    <p style="font-size: xx-large">Ячейка: <?=$cell ?></p>
                    <a href="roll_edit.php?id=<?=$id ?>" class="btn btn-outline-dark w-100">Сменить ячейку</a>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '_footer.php';
        ?>
    </body>
</html>