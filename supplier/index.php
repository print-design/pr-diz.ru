<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Получение объекта
$sql = "select s.id supplier_id, s.name supplier, fb.id film_brand_id, fb.name film_brand, fbv.id film_brand_variation_id, fbv.thickness, fbv.weight "
        . "from supplier s "
        . "inner join film_brand fb on fb.supplier_id = s.id "
        . "inner join film_brand_variation fbv on fbv.film_brand_id = fb.id "
        . "order by s.name, fb.name, fbv.thickness, fbv.weight";
$fetcher = new Fetcher($sql);
$suppliers = array();
while($row = $fetcher->Fetch()) {
    $supplier_id = $row['supplier_id'];
    if(!isset($suppliers[$supplier_id])) {
        $suppliers[$supplier_id] = array('name' => $row['supplier'], 'film_brands' => array());
    }
    
    $film_brand_id = $row['film_brand_id'];
    if(!isset($suppliers[$supplier_id]['film_brands'][$film_brand_id])) {
        $suppliers[$supplier_id]['film_brands'][$film_brand_id] = array('name' => $row['film_brand'], 'film_brand_variations' => array());
    }
    
    $film_brand_variation_id = $row['film_brand_variation_id'];
    if(!isset($suppliers[$supplier_id]['film_brands'][$film_brand_id]['film_brand_variations'][$film_brand_variation_id])) {
        $suppliers[$supplier_id]['film_brands'][$film_brand_id]['film_brand_variations'][$film_brand_variation_id] = array('thickness' => $row['thickness'], 'weight' => $row['weight']);
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_admin.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-0">
                    <h1>Поставщики</h1>
                </div>
                <div class="p-0">
                    <a href="create.php" title="Добавить поставщика" class="btn btn-dark">
                        <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить поставщика
                    </a>
                </div>
            </div>
            <?php foreach(array_keys($suppliers) as $key): ?>
            <h2><?=$suppliers[$key]['name'] ?></h2>
            <?php foreach ($suppliers[$key]['film_brands'] as $film_brand): ?>
            <h3><?=$film_brand['name'] ?></h3>
            <?php foreach(array_keys($film_brand['film_brand_variations']) as $fbv_key): ?>
            <p><?=$film_brand['film_brand_variations'][$fbv_key]['thickness'] ?> &ndash; <?=$film_brand['film_brand_variations'][$fbv_key]['weight'] ?></p>
            <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>