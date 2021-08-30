<?php
include '../include/topscripts.php';

// Получение объекта
$id = filter_input(INPUT_GET, 'id');
$cut_id = null;
$ordinal = null;

if(!empty($id)) {
    $sql = "select rational_cut_id from rational_cut_stream where id=$id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $cut_id = $row[0];
    }
}

if(empty($cut_id)) {
    $sql = "select id from rational_cut where id not in (select rational_cut_id from rational_cut_stage)";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $cut_id = $row[0];
    }
}

$brand_name = '';
$thickness = '';
$sql = "select brand_name, thickness from rational_cut where id = $cut_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $brand_name = $row['brand_name'];
    $thickness = $row['thickness'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include 'style.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_analytics.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/rational_cut/">К списку</a>
            <h1>Раскрой <?=$cut_id ?>, этап 1</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <div class="form-group">
                            <label for="brand_name">Марка плёнки</label>
                            <select id="brand_name_disabled" name="brand_name_disabled" class="form-control" disabled="disabled">
                                <option value=""><?=$brand_name ?></option>
                            </select>
                            <input type="hidden" id="brand_name" name="brand_name" value="<?=$brand_name ?>" />
                        </div>
                        <div class="form-group">
                            <label for="thickness">Толщина</label>
                            <select id="thickness_disabled" name="thickness_disabled" class="form-control" disabled="disabled">
                                <?php
                                $weight = '';
                                $brand_name = addslashes($brand_name);
                                $sql = "select fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$brand_name' and fbv.thickness=$thickness";
                                $fetcher = new Fetcher($sql);
                                if($row = $fetcher->Fetch()) {
                                    $weight = $row[0];
                                }
                                ?>
                                <option value=""><?=$thickness ?> мкм <?=$weight ?> г/м<sup>2</sup></option>
                            </select>
                            <input type="hidden" id="thickness" name="thickness" value="<?=$thickness ?>" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>