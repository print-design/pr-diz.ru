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
            
            $sql = "select s.name supplier, fb.name film_brand, p.id_from_supplier, p.width, p.thickness, pr.weight, pr.length, p.cell, p.comment, "
                    . "p.id pallet_id, pr.ordinal "
                    . "from pallet_roll pr "
                    . "inner join pallet p on pr.pallet_id = p.id "
                    . "inner join supplier s on p.supplier_id=s.id "
                    . "inner join film_brand fb on p.film_brand_id=fb.id "
                    . "where pr.id=$id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $supplier = $row['supplier'];
                $id_from_supplier = $row['id_from_supplier'];
                $film_brand = $row['film_brand'];
                $width = $row['width'];
                $thickness = $row['thickness'];
                $weight = $row['weight'];
                $length = $row['length'];
                $cell = $row['cell'];
                $comment = htmlentities($row['comment']);
                $pallet_id = $row['pallet_id'];
                $ordinal = $row['ordinal'];
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <h1>Рулон №П<?=$pallet_id ?>Р<?=$ordinal ?></h1>
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
                        <tr>
                            <td class="font-weight-bold">Комментарий</td>
                            <td><?=$comment ?></td>
                        </tr>
                    </table>
                    <p style="font-size: xx-large">Ячейка: <?=$cell ?></p>
                    <a href="pallet_roll_edit.php?id=<?=$id ?>" class="btn btn-outline-dark w-100">Сменить ячейку</a>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '_footer.php';
        ?>
    </body>
</html>