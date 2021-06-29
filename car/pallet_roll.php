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
            
            $sql = "select s.name supplier, fb.name film_brand, p.width, p.thickness, pr.weight, p.cell, "
                    . "p.id pallet_id, pr.ordinal "
                    . "from pallet_roll pr "
                    . "inner join pallet p on pr.pallet_id = p.id "
                    . "inner join supplier s on p.supplier_id=s.id "
                    . "inner join film_brand fb on p.film_brand_id=fb.id "
                    . "where pr.id=$id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $supplier = $row['supplier'];
                $film_brand = $row['film_brand'];
                $width = $row['width'];
                $thickness = $row['thickness'];
                $weight = $row['weight'];
                $cell = $row['cell'];
                $pallet_id = $row['pallet_id'];
                $ordinal = $row['ordinal'];
            }
            ?>
            <h1>Рулон №П<?=$pallet_id ?>Р<?=$ordinal ?></h1>
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
            </table>
            <p style="font-size: xx-large">Ячейка: <?=$cell ?></p>
            <a href="pallet_edit.php?id=<?=$id ?>" class="btn btn-outline-dark w-100">Сменить ячейку</a>
        </div>
        <?php
        include '../include/footer.php';
        include '_footer.php';
        ?>
    </body>
</html>