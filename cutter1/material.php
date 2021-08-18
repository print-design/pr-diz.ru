<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

include_once '_redirects.php';

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$supplier_id_valid = '';
$film_brand_id_valid = '';
$thickness_valid = '';
$width_valid = '';

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    if(empty($supplier_id)) {
        $supplier_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
    if(empty($film_brand_id)) {
        $film_brand_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $thickness = filter_input(INPUT_POST, 'thickness');
    if(empty($thickness)) {
        $thickness_valid = ISINVALID;
        $form_valid = false;
    }
    
    $width = filter_input(INPUT_POST, 'width');
    if(empty($width) || intval($width) > 1600) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        header("Location: cut.php?supplier_id=$supplier_id&film_brand_id=$film_brand_id&thickness=$thickness&width=$width");
    }
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
        <script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
        <script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script src="<?=APPLICATION ?>/js/popper.min.js"></script>
        <script src="<?=APPLICATION ?>/js/jquery.maskedinput.js"></script>
        <script src="<?=APPLICATION ?>/js/calculation.js?version=100"></script>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1>Какой материал режем?</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <div class="form-group">
                            <label for="supplier_id">Поставщик</label>
                            <select class="form-control<?=$supplier_id_valid ?>" id="supplier_id" name="supplier_id" required="required">
                                <option value="" hidden="hidden">Выберите поставщика</option>
                                    <?php
                                    $suppliers = (new Grabber("select id, name from supplier order by name"))->result;
                                    foreach ($suppliers as $supplier) {
                                        $id = $supplier['id'];
                                        $name = $supplier['name'];
                                        $selected = '';
                                        if(isset($_REQUEST['supplier_id']) && $_REQUEST['supplier_id'] == $supplier['id']) $selected = " selected='selected'";
                                        echo "<option value='$id'$selected>$name</option>";
                                    }
                                    ?>
                            </select>
                            <div class="invalid-feedback">Поставщик обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="film_brand_id">Марка пленки</label>
                            <select class="form-control<?=$film_brand_id_valid ?>" id="film_brand_id" name="film_brand_id" required="required">
                                <option value="" hidden="hidden">Выберите марку</option>
                                    <?php
                                    if(isset($_REQUEST['supplier_id'])) {
                                        $supplier_id = $_REQUEST['supplier_id'];
                                        $film_brands = (new Grabber("select id, name from film_brand where supplier_id = $supplier_id"))->result;
                                        foreach ($film_brands as $film_brand) {
                                            $id = $film_brand['id'];
                                            $name = $film_brand['name'];
                                            $selected = '';
                                            if($_REQUEST['film_brand_id'] == $film_brand['id']) $selected = " selected='selected'";
                                            echo "<option value='$id'$selected>$name</option>";
                                        }
                                    }
                                    ?>
                            </select>
                            <div class="invalid-feedback">Марка пленки обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="thickness">Толщина, мкм</label>
                            <select class="form-control<?=$thickness_valid ?>" id="thickness" name="thickness" required="required">
                                <option value="" hidden="hidden">Выберите толщину</option>
                                    <?php
                                    if(isset($_REQUEST['film_brand_id'])) {
                                        $film_brand_id = $_REQUEST['film_brand_id'];
                                        $film_brand_variations = (new Grabber("select thickness, weight from film_brand_variation where film_brand_id = $film_brand_id order by thickness"))->result;
                                        foreach ($film_brand_variations as $film_brand_variation) {
                                            $thickness = $film_brand_variation['thickness'];
                                            $weight = $film_brand_variation['weight'];
                                            $selected = '';
                                            if($_REQUEST['thickness'] == $film_brand_variation['thickness']) $selected = " selected='selected'";
                                            echo "<option value='$thickness'$selected>$thickness мкм $weight г/м<sup>2</sup></option>";
                                        }
                                    }
                                    ?>
                            </select>
                            <div class="invalid-feedback">Толщина обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="width">Ширина, мм</label>
                            <input type="text" id="width" name="width" value="<?= $_REQUEST['width'] ?? '' ?>" class="form-control int-only<?=$width_valid ?>" data-max="1600" placeholder="Введите ширину" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Число, макс. 1600</div>
                        </div>
                        <div class="form-group">
                            <button type="submit" id="next-submit" name="next-submit" class="btn btn-dark form-control mt-4">Далее</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '_footer.php';
        ?>
    </body>
</html>