<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// СТАТУС "СВОБОДНЫЙ"
const  FREE_ROLL_STATUS_ID = 1;

// Статус "РАСКРОИЛИ"
$cut_status_id = 3;

include '_check_rolls.php';
$opened_roll = CheckOpenedRolls($user_id);
$cutting_id = $opened_roll['id'];
$last_source = $opened_roll['last_source'];
$streams_count = $opened_roll['streams_count'];
$last_wind = $opened_roll['last_wind'];

// Если нет незакрытой нарезки, переходим на первую страницу
if(empty($cutting_id)) {
    header("Location: ".APPLICATION.'/cutter/');
}
// Если есть исходный ролик, но нет ручьёв, переходим на страницу "Как режем"
elseif(!empty ($last_source) && empty ($streams_count)) {
    header("Location: streams.php");
}
// Если есть исходный ролик, но нет нарезок, переходим на страницу создания нарезки
elseif(!empty ($last_source) && empty ($last_wind)) {
    header("Location: wind.php");
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$radius_valid = '';

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    if(empty($supplier_id)) {
        $error_message = "Не указан производитель плёнки";
        $form_valid = false;
    }
    
    $radius = filter_input(INPUT_POST, 'radius');
    if(empty($radius)) {
        $radius_valid = ISINVALID;
        $form_valid = false;
    }
    
    $length = filter_input(INPUT_POST, 'length');
    if(empty($length)) {
        $error_message = "Длина обязательно";
        $form_valid = false;
    }
    
    $net_weight = filter_input(INPUT_POST, 'net_weight');
    if(empty($net_weight)) {
        $error_message = "Масса нетто обязательно";
        $form_valid = false;
    }
    
    $id_from_supplier = rand(1000000, 9999999);
    $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
    $thickness = filter_input(INPUT_POST, 'thickness');
    $width = filter_input(INPUT_POST, 'width');
    $cell = '';
    $comment = addslashes('!');
    $storekeeper_id = $user_id;
    $status_id = FREE_ROLL_STATUS_ID;
    
    if($form_valid) {
        // Создаём новый рулон
        $sql = "insert into roll (supplier_id, id_from_supplier, film_brand_id, width, thickness, length, net_weight, cell, comment, storekeeper_id) "
                . "values ($supplier_id, '$id_from_supplier', $film_brand_id, $width, $thickness, $length, $net_weight, '$cell', '$comment', '$storekeeper_id')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $roll_id = $executer->insert_id;
        $is_from_pallet = 0;
        
        // Устанавливаем ему статус "Свободный"
        if(empty($error_message)) {
            $sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($roll_id, $status_id, $user_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        // Меняем статусы предыдущих исходных роликов на "Раскроили" (если он уже не установлен)
        if(empty($error_message)) {
            $cut_sources = null;
    
            $sql = "select is_from_pallet, roll_id from cutting_source where cutting_id=$cutting_id";
            $grabber = new Grabber($sql);
            $cut_sources = $grabber->result;
            $error_message = $grabber->error;
    
            if($cut_sources !== null) {
                foreach($cut_sources as $cut_source) {
                    $source_is_from_pallet = $cut_source['is_from_pallet'];
                    $source_roll_id = $cut_source['roll_id'];
        
                    if($source_is_from_pallet == 0) {
                        $sql = "select status_id from roll_status_history where roll_id = $source_roll_id order by id desc limit 1";
                        $fetcher = new Fetcher($sql);
                        $row = $fetcher->Fetch();
                
                        if(!$row || $row['status_id'] != $cut_status_id) {
                            $sql = "insert into roll_status_history (roll_id, status_id, user_id) values($source_roll_id, $cut_status_id, $user_id)";
                            $executer = new Executer($sql);
                            $error_message = $executer->error;
                        }
                    }
                    else {
                        $sql = "select status_id from pallet_roll_status_history where pallet_roll_id = $source_roll_id order by id desc limit 1";
                        $fetcher = new Fetcher($sql);
                        $row = $fetcher->Fetch();
                
                        if(!$row || $row['status_id'] != $cut_status_id) {
                            $sql = "insert into pallet_roll_status_history (pallet_roll_id, status_id, user_id) values($source_roll_id, $cut_status_id, $user_id)";
                            $executer = new Executer($sql);
                            $error_message = $executer->error;
                        }
                    }
                }
            }
        }
        
        // Добавляем новый исходный ролик
        if(empty($error_message)) {
            $sql = "insert into cutting_source (cutting_id, is_from_pallet, roll_id) values ($cutting_id, $is_from_pallet, $roll_id)";
            $executer = new Executer($sql);
            $error_message == $executer->error;
        }
        
        if(empty($error_message)) {
            header("Location: streams.php"); // А отсюда, если понадобится, будет перенаправление
        }
    }
}

// Получение объекта
$supplier_id = null;
$film_brand_id = null;
$thickness = null;
$width = null;

$sql = "select supplier_id, film_brand_id, thickness, width from cutting where id = $cutting_id";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $supplier_id = $row['supplier_id'];
    $film_brand_id = $row['film_brand_id'];
    $thickness = $row['thickness'];
    $width = $row['width'];
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
                        <a class="nav-link" href="source.php"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
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
            <h1>Новый рулон</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <input type="hidden" id="cutting_id" name="cutting_id" value="<?=$cutting_id ?>" />
                        <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$supplier_id ?>" />
                        <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand_id ?>" />
                        <input type="hidden" id="thickness" name="thickness" value="<?=$thickness ?>" />
                        <input type="hidden" id="width" name="width" value="<?=$width ?>" />
                        <input type="hidden" id="length" id="length" name="length" />
                        <input type="hidden" id="net_weight" id="net_weight" name="net_weight" />
                        <div class="form-group">
                            <label for="supplier_id">Поставщик</label>
                            <select class="form-control" disabled="disabled">
                                <option value="" hidden="hidden">Выберите поставщика</option>
                                <?php
                                $suppliers = (new Grabber("select id, name from supplier order by name"))->result;
                                foreach($suppliers as $supplier) {
                                    $id = $supplier['id'];
                                    $name = $supplier['name'];
                                    $selected = '';
                                    if($supplier_id == $supplier['id']) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="film_brand_id">Марка плёнки</label>
                            <select class="form-control" disabled="disabled">
                                <option value="" hidden="hidden">Выберите марку</option>
                                <?php
                                if(!empty($supplier_id)) {
                                    $film_brands = (new Grabber("select id, name from film_brand where supplier_id = $supplier_id"))->result;
                                    foreach($film_brands as $film_brand) {
                                        $id = $film_brand['id'];
                                        $name = $film_brand['name'];
                                        $selected = '';
                                        if($film_brand_id == $film_brand['id']) $selected = " selected='selected'";
                                        echo "<option value='$id'$selected>$name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="thickness">Толщина, мкм</label>
                            <select class="form-control" disabled="disabled">
                                <option value="" hidden="hidden">Выберите толщину</option>
                                <?php
                                if(!empty($supplier_id) && !empty($film_brand_id)) {
                                    $film_brand_variations = (new Grabber("select thickness, weight from film_brand_variation where film_brand_id = $film_brand_id order by thickness"))->result;
                                    foreach($film_brand_variations as $film_brand_variation) {
                                        $current_thickness = $film_brand_variation['thickness'];
                                        $current_weight = $film_brand_variation['weight'];
                                        $selected = '';
                                        if($thickness == $current_thickness) $selected = " selected='selected'";
                                        echo "<option value='$current_thickness'$selected>$current_thickness мкм $current_weight г/м<sup>2</sup></option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="width">Ширина, мм</label>
                            <input type="text" id="width" name="width" value="<?= $width ?>" class="form-control" disabled="disabled" />
                        </div>
                        <div class="form-group">
                            <label for="spool">Шпуля, мм</label>
                            <div class="d-block">
                                <?php
                                $checked76 = " checked='checked'";
                                $checked152 = "";
                                
                                if(filter_input(INPUT_POST, 'spool') == 76) {
                                    $checked76 = " checked='checked'";
                                    $checked152 = "";
                                }
                                
                                if(filter_input(INPUT_POST, 'spool') == 152) {
                                    $checked76 = "";
                                    $checked152 = " checked='checked'";
                                }
                                ?>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" id="spool" name="spool" value="76"<?=$checked76 ?> />76 мм
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" id="spool" name="spool" value="152"<?=$checked152 ?> />152 мм
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="radius">Радиус от вала, мм</label>
                            <input type="text" name="radius" id="radius" class="form-control int-only<?=$radius_valid ?>" value="<?= filter_input(INPUT_POST, 'radius') ?>" placeholder="Введите радиус от вала" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Радиус от вала обязательно</div>
                        </div>
                        <div class="form-group d-none d-lg-block">
                            <div class="form-group">
                                <button type="submit" id="next-submit" name="next-submit" class="btn btn-dark form-control mt-4">Далее</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="d-block d-lg-none w-100 pb-4" id="bottom_buttons">
                <div class="form-group">
                    <button type="button" class="btn btn-dark form-control" onclick="javascript: $('#next-submit').click();">Далее</button>
                </div>
            </div>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Позиционируем кнопку "Далее" относительно нижнего края экрана только если она не перекроет другие элементы
            function AdjustButtons() {
                if($('#radius').offset().top + $('#radius').outerHeight() + 80 < $(window).height()) {
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
            
            // Все марки плёнки с их вариациями
            var films = new Map();
            
            <?php
            $sql = "SELECT fbv.film_brand_id, fbv.thickness, fbv.weight FROM film_brand_variation fbv";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                echo "if(films.get(".$row['film_brand_id'].") == undefined) {\n";
                echo "films.set(".$row['film_brand_id'].", new Map());\n";
                echo "}\n";
                echo "films.get(".$row['film_brand_id'].").set(".$row['thickness'].", ".$row['weight'].");\n";
            }
            ?>
            
            // Расчёт длины и массы плёнки по шпуле, толщине, радиусу, ширине, удельному весу
            function CalculateByRadius() {
                film_brand_id = $('#film_brand_id').val();
                spool = $('#spool:checked').val();
                thickness = $('#thickness').val();
                width = $('#width').val();
                radius = $('#radius').val();
                
                if(!isNaN(spool) && !isNaN(thickness) && !isNaN(radius) && !isNaN(width) 
                        && spool != '' && thickness != '' && radius != '' && width != '') {
                    density = films.get(parseInt($('#film_brand_id').val())).get(parseInt(thickness));
                    
                    result = GetFilmLengthWeightBySpoolThicknessRadiusWidth(spool, thickness, radius, width, density);
                    
                    $('#length').val(result.length.toFixed(2));
                    $('#net_weight').val(result.weight.toFixed(2));
                }
            }
            
            // Рассчитываем ширину и массу плёнки при изменении значений каждого поля, участвующего в вычислении
            $('#spool').change(CalculateByRadius);
            
            $('#radius').keypress(CalculateByRadius);
            
            $('#radius').keyup(CalculateByRadius);
            
            $('#radius').change(CalculateByRadius);
            
            $('#thickness').change(CalculateByRadius);
            
            $('#width').keypress(CalculateByRadius);
            
            $('#width').keyup(CalculateByRadius);
            
            $('#width').change(CalculateByRadius);
            
            $(document).ready(function() {
                AdjustButtons();
                CalculateByRadius();
            });
            
            $(window).on('resize', AdjustButtons);
        </script>
    </body>
</html>