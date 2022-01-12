<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Проверяем, имеются ли незакрытые нарезки
include '_check_cuts.php';
CheckCuts($user_id);

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$supplier_id_valid = '';
$film_brand_id_valid = '';
$thickness_valid = '';
$width_valid = '';

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $cutting_id = filter_input(INPUT_POST, 'cutting_id');
    
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
        if(empty($cutting_id)) {
            $sql = "insert into cutting (supplier_id, film_brand_id, thickness, width, cutter_id) values ($supplier_id, $film_brand_id, $thickness, $width, $user_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            $cutting_id = $executer->insert_id;
        }
        else {
            $sql = "update cutting set supplier_id = $supplier_id, film_brand_id = $film_brand_id, thickness = $thickness, width = $width where id = $cutting_id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message) && !empty($cutting_id)) {
            header("Location: source.php?cutting_id=$cutting_id");
        }
    }
}

// Получение объекта
$cutting_id = filter_input(INPUT_GET, 'cutting_id');

$supplier_id = null;
$film_brand_id = null;
$thickness = null;
$width = null;

if(!empty($cutting_id)) {
    $sql = "select supplier_id, film_brand_id, thickness, width from cutting where id = $cutting_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $supplier_id = $row['supplier_id'];
        $film_brand_id = $row['film_brand_id'];
        $thickness = $row['thickness'];
        $width = $row['width'];
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include '_head.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-between">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <?php if(empty(filter_input(INPUT_GET, 'cutting_id'))): ?>
                        <a class="nav-link" href="<?=APPLICATION."/cutter/" ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        <?php endif; ?>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" id="logout-submit" href="logout.php?link=<?= urlencode($_SERVER['REQUEST_URI']) ?>"><i class="fa fa-user-alt" aria-hidden="true""></i></a>
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
            ?>
            <h1>Какой материал режем?</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <input type="hidden" id="cutting_id" name="cutting_id" value="<?=$cutting_id ?>" />
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
                                        if($supplier_id == $supplier['id']) $selected = " selected='selected'";
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
                                    if(!empty($supplier_id)) {
                                        $film_brands = (new Grabber("select id, name from film_brand where supplier_id = $supplier_id"))->result;
                                        foreach ($film_brands as $film_brand) {
                                            $id = $film_brand['id'];
                                            $name = $film_brand['name'];
                                            $selected = '';
                                            if($film_brand_id == $film_brand['id']) $selected = " selected='selected'";
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
                                    if(!empty($supplier_id) && !empty($film_brand_id)) {
                                        $film_brand_variations = (new Grabber("select thickness, weight from film_brand_variation where film_brand_id = $film_brand_id order by thickness"))->result;
                                        foreach ($film_brand_variations as $film_brand_variation) {
                                            $current_thickness = $film_brand_variation['thickness'];
                                            $current_weight = $film_brand_variation['weight'];
                                            $selected = '';
                                            if($thickness == $film_brand_variation['thickness']) $selected = " selected='selected'";
                                            echo "<option value='$current_thickness'$selected>$current_thickness мкм $current_weight г/м<sup>2</sup></option>";
                                        }
                                    }
                                    ?>
                            </select>
                            <div class="invalid-feedback">Толщина обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="width">Ширина, мм</label>
                            <input type="text" id="width" name="width" value="<?= $width ?>" class="form-control int-only<?=$width_valid ?>" data-max="1600" placeholder="Введите ширину" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Число, макс. 1600</div>
                        </div>
                        <div class="form-group d-none d-lg-block">
                            <button type="submit" id="next-submit" name="next-submit" class="btn btn-dark form-control mt-4">Далее</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="d-block d-lg-none w-100 pl-4 pr-4 pb-4" style="position: absolute; bottom: 0; left: 0;">
                <button type="button" class="btn btn-dark form-control" onclick="javascript: $('#next-submit').click();">Далее</button>
            </div>
        </div>
        <?php
        include '_footer.php';
        ?>
    </body>
</html>