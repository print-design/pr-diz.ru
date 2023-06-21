<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_CUTTER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Проверяем, есть ли незакрытые нарезки
include '_check_rolls.php';
$opened_roll = CheckOpenedRolls($user_id);
$cutting_id = $opened_roll['id'];
$last_source = $opened_roll['last_source'];
$streams_count = $opened_roll['streams_count'];

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$supplier_id_valid = '';
$film_id_valid = '';
$film_variation_id_valid = '';
$width_valid = '';

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $cutting_id = filter_input(INPUT_POST, 'cutting_id');
    
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    if(empty($supplier_id)) {
        $supplier_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $film_id = filter_input(INPUT_POST, 'film_id');
    if(empty($film_id)) {
        $film_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
    if(empty($film_variation_id)) {
        $film_variation_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $width = filter_input(INPUT_POST, 'width');
    if(empty($width) || intval($width) > 1600) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        if(empty($cutting_id)) {
            $sql = "insert into cutting (supplier_id, film_variation_id, width, cutter_id) values ($supplier_id, $film_variation_id, $width, $user_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            $cutting_id = $executer->insert_id;
        }
        else {
            $sql = "update cutting set supplier_id = $supplier_id, film_variation_id = $film_variation_id, width = $width where id = $cutting_id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message) && !empty($cutting_id)) {
            header("Location: streams.php");
        }
    }
}

if(null !== filter_input(INPUT_POST, 'previous-submit')) {
    $cutting_id = filter_input(INPUT_POST, 'cutting_id');
    
    if(!empty($cutting_id)) {
        $sql = "delete from cutting where id = $cutting_id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        header("Location: ".APPLICATION.'/cutter/');
    }
}

// Получение объекта
$supplier_id = null;
$film_id = null;
$film_variation_id = null;
$width = null;

if(!empty($cutting_id)) {
    $sql = "select c.supplier_id, fv.film_id, c.film_variation_id, c.width "
            . "from cutting c "
            . "inner join film_variation fv on c.film_variation_id=fv.id "
            . "where c.id=$cutting_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $supplier_id = $row['supplier_id'];
        $film_id = $row['film_id'];
        $film_variation_id = $row['film_variation_id'];
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
                        <form method="post">
                            <?php if(!empty($cutting_id)): ?>
                            <input type="hidden" id="cutting_id" name="cutting_id" value="<?=$cutting_id ?>" />
                            <?php endif; ?>
                            <button type="submit" id="previous-submit" name="previous-submit" class="btn btn-link nav-link"><i class="fas fa-chevron-left"></i>&nbsp;Назад</button>
                        </form>
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
                            <label for="film_id">Марка пленки</label>
                            <select class="form-control<?=$film_id_valid ?>" id="film_id" name="film_id" required="required">
                                <option value="" hidden="hidden">Выберите марку</option>
                                    <?php
                                    if(!empty($supplier_id)) {
                                        $films = (new Grabber("select id, name from film where id in (select film_id from film_variation where id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id))"))->result;
                                        foreach ($films as $film) {
                                            $id = $film['id'];
                                            $name = $film['name'];
                                            $selected = '';
                                            if($film_id == $film['id']) $selected = " selected='selected'";
                                            echo "<option value='$id'$selected>$name</option>";
                                        }
                                    }
                                    ?>
                            </select>
                            <div class="invalid-feedback">Марка пленки обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="film_variation_id">Толщина, мкм</label>
                            <select class="form-control<?=$film_variation_id_valid ?>" id="film_variation_id" name="film_variation_id" required="required">
                                <option value="" hidden="hidden">Выберите толщину</option>
                                    <?php
                                    if(!empty($supplier_id) && !empty($film_id)) {
                                        $film_variations = (new Grabber("select id, thickness, weight from film_variation where film_id = $film_id and id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id) order by thickness"))->result;
                                        foreach ($film_variations as $film_variation) {
                                            $id = $film_variation['id'];
                                            $thickness = $film_variation['thickness'];
                                            $weight = $film_variation['weight'];
                                            $selected = '';
                                            if($film_variation_id == $film_variation['id']) $selected = " selected='selected'";
                                            echo "<option value='$id'$selected>$thickness мкм $weight г/м<sup>2</sup></option>";
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
            <div class="d-block d-lg-none w-100 pb-4" id="bottom_buttons">
                <button type="button" class="btn btn-dark form-control" onclick="javascript: $('#next-submit').click();">Далее</button>
            </div>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Позиционируем кнопку "Далее" относительно нижнего края экрана только если она не перекроет другие элементы
            function AdjustButtons() {
                if($('#width').offset().top + $('#bottom_buttons').outerHeight() + 50 < $(window).height()) {
                    $('#bottom_buttons').removeClass('sticky-top');
                    $('#bottom_buttons').addClass('fixed-bottom');
                    $('#bottom_buttons').addClass('container-fluid');
                }
                else {
                    $('#bottom_buttons').addClass('sticky-top');
                    $('#bottom_buttons').removeClass('fixed-bottom');
                    $('#bottom_buttons').removeClass('container-fluid');
                }
            }
            
            $(document).ready(AdjustButtons);
            
            $(window).on('resize', AdjustButtons);
        </script>
    </body>
</html>