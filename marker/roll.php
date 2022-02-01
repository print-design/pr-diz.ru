<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'marker'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// СТАТУС "СВОБОДНЫЙ"
const FREE_ROLL_STATUS_ID = 1;

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$supplier_id_valid = '';
$film_brand_id_valid = '';
$thickness_valid = '';
$width_valid = '';
$radius_valid = '';
$cell_valid = '';

if(null !== filter_input(INPUT_POST, 'create-submit')) {
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
    if(empty($width)) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    $radius = filter_input(INPUT_POST, 'radius');
    if(empty($radius)) {
        $radius_valid = ISINVALID;
        $form_valid = false;
    }
    
    $cell = addslashes(filter_input(INPUT_POST, 'cell'));
    if(empty($cell)) {
        $cell_valid = ISINVALID;
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
    $comment = '';
    $storekeeper_id = $user_id;
    $status_id = FREE_ROLL_STATUS_ID;
    
    if($form_valid) {    
        $sql = "insert into roll (supplier_id, id_from_supplier, film_brand_id, width, thickness, length, net_weight, cell, comment, storekeeper_id) "
                . "values ($supplier_id, '$id_from_supplier', $film_brand_id, $width, $thickness, $length, $net_weight, '$cell', '$comment', '$storekeeper_id')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $roll_id = $executer->insert_id;
        
        if(empty($error_message)) {
            $sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($roll_id, $status_id, $user_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            
            if(empty($error_message)) {
                header('Location: print.php');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include './_head.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-between">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="<?=APPLICATION ?>/marker/" class="btn btn-link nav-link"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" id="logout-submit" href="logout.php?link=<?= urlencode($_SERVER['REQUEST_URI']) ?>"><i class="fa fa-user-alt" aria-hidden="true"></i></a>
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
                        <input type="hidden" id="length" name="length" />
                        <input type="hidden" id="net_weight" name="net_weight" />
                        <div class="form-group">
                            <label for="supplier_id">Поставщик</label>
                            <select class="form-control<?=$supplier_id_valid ?>" id="supplier_id" name="supplier_id" required="required">
                                <option value="" hidden="hidden">Выберите поставщика</option>
                                <?php
                                $suppliers = (new Grabber("select id, name from supplier order by name"))->result;
                                foreach($suppliers as $supplier) {
                                    $id = $supplier['id'];
                                    $name = $supplier['name'];
                                    $selected = '';
                                    if(filter_input(INPUT_POST, 'supplier_id') == $supplier['id']) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$name</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Поставщик обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="film_brand_id">Марка плёнки</label>
                            <select class="form-control<?=$film_brand_id_valid ?>" id="film_brand_id" name="film_brand_id" required="required">
                                <option value="" hidden="hidden">Выберите марку</option>
                                <?php
                                if(!empty(filter_input(INPUT_POST, 'supplier_id'))) {
                                    $film_brands = (new Grabber("select id, name from film_brand where supplier_id = ".filter_input(INPUT_POST, 'supplier_id')))->result;
                                    foreach($film_brands as $film_brand) {
                                        $id = $film_brand['id'];
                                        $name = $film_brand['name'];
                                        $selected = '';
                                        if(filter_input(INPUT_POST, 'film_brand_id') == $film_brand['id']) $selected = " selected='selected'";
                                        echo "<option value='$id'$selected>$name</option>";
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Марка плёнки обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="thickness">Толщина, мкм</label>
                            <select class="form-control<?=$thickness_valid ?>" id="thickness" name="thickness" required="required">
                                <option value="" hidden="hidden">Выберите толщину</option>
                                <?php
                                if(!empty(filter_input(INPUT_POST, 'supplier_id')) && !empty(filter_input(INPUT_POST, 'film_brand_id'))) {
                                    $film_brand_variations = (new Grabber("select thickness, weight from film_brand_variation where film_brand_id = ".filter_input(INPUT_POST, 'film_brand_id')." order by thickness"))->result;
                                    foreach($film_brand_variations as $film_brand_variation) {
                                        $current_thickness = $film_brand_variation['thickness'];
                                        $current_weight = $film_brand_variation['weight'];
                                        $selected = '';
                                        if(filter_input(INPUT_POST, 'thickness') == $current_thickness) $selected = " selected='selected'";
                                        echo "<option value='$current_thickness'$selected>$current_thickness мкм $current_weight г/м<sup>2</sup></option>";
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Толщина обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="width">Ширина, мм</label>
                            <input type="text" id="width" name="width" value="<?= filter_input(INPUT_POST, 'width') ?>" class="form-control int-only<?=$width_valid ?>" data-max="1600" placeholder="Введите ширину" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Число, макс. 1600</div>
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
                            <input type="text" id="radius" name="radius" value="<?= filter_input(INPUT_POST, 'radius') ?>" class="form-control int-only<?=$radius_valid ?>" placeholder="Введите радиус от вала" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Радиус от вала обязательно</div>
                        </div>
                        <div class="form-group">
                            <label type="cell">Ячейка на складе</label>
                            <input type="text" id="cell" name="cell" value="<?= filter_input(INPUT_POST, 'cell') ?>" class="form-control<?=$cell_valid ?>" placeholder="Введите ячейку на складе" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Ячейка на складе обязательно</div>
                        </div>
                        <div class="form-group d-none d-lg-block">
                            <button type="submit" id="create-submit" name="create-submit" class="btn btn-dark form-control mt-4">Распечатать</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="d-block d-lg-none w-100 pb-4" id="bottom_buttons">
                <button type="button" class="btn btn-dark form-control" onclick="javascript: $('#create-submit').click();">Распечатать</button>
            </div>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Загрузка списка марок пленки
            $('#supplier_id').change(function(){
                if($(this).val() == "") {
                    $('#film_brand_id').html("<option value=''>Выберите марку</option>");
                }
                else {
                    $.ajax({ url: "../ajax/film_brand.php?supplier_id=" + $(this).val() })
                            .done(function(data) {
                                $('#film_brand_id').html(data);
                                $('#film_brand_id').change();
                            })
                            .fail(function() {
                                alert('Ошибка при выборе поставщика');
                            });
                }
            });
    
            // Загрузка списка толщин
            $('#film_brand_id').change(function(){
                if($(this).val() == "") {
                    $('#thickness').html("<option value=''>Выберите толщину</option>");
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?film_brand_id=" + $(this).val() })
                            .done(function(data) {
                                $('#thickness').html(data);
                            })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                            });
                }
            });
    
            // Позиционируем кнопку "Далее" относительно нижнего края экрана только если она не перекроет другие элементы
            function AdjustButtons() {
                if($('#cell').offset().top + $('#bottom_buttons').outerHeight() + 50 < $(window).height()) {
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
            
            $(document).ready(function() { 
                AdjustButtons();
                CalculateByRadius();
            });
            
            $(window).on('resize', AdjustButtons);
        </script>
    </body>
</html>