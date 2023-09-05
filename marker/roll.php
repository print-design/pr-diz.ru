<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MARKER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Валидация формы
$form_valid = true;
$error_message = '';

$film_id_valid = '';
$film_variation_id_valid = '';
$width_valid = '';
$radius_valid = '';
$cell_valid = '';

if(null !== filter_input(INPUT_POST, 'create-submit')) {
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
    
    $comment = '';
    $storekeeper_id = $user_id;
    $status_id = ROLL_STATUS_FREE;
    
    // Выбираем рандомного поставщика для данного типа плёнки
    $supplier_id = null;
    
    if(!empty($film_variation_id)) {
        $sql = "select supplier_id from supplier_film_variation where film_variation_id = $film_variation_id order by rand() limit 1";
        $fetcher = new Fetcher($sql);
        
        if($row = $fetcher->Fetch()) {
            $supplier_id = $row[0];
        }
    }
    
    if(empty($supplier_id)) {
        $error_message = "Ошибка при определении поставщика для плёнки";
        $form_valid = false;
    }
    
    if($form_valid) {    
        $sql = "insert into roll (supplier_id, film_variation_id, width, length, net_weight, cell, comment, storekeeper_id) "
                . "values ($supplier_id, $film_variation_id, $width, $length, $net_weight, '$cell', '$comment', '$storekeeper_id')";
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
                            <label for="film_id">Марка плёнки</label>
                            <select class="form-control<?=$film_id_valid ?>" id="film_id" name="film_id" required="required">
                                <option value="" hidden="hidden">Выберите марку</option>
                                <?php
                                $films = (new Grabber("select id, name from film where id in (select film_id from film_variation where id in (select film_variation_id from supplier_film_variation)) order by name"))->result;
                                foreach($films as $film) {
                                    $id = $film['id'];
                                    $name = $film['name'];
                                    $selected = '';
                                    if(filter_input(INPUT_POST, 'film_id') == $id) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$name</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Марка плёнки обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="film_variation_id">Толщина, мкм</label>
                            <select class="form-control<?=$film_variation_id_valid ?>" id="film_variation_id" name="film_variation_id" required="required">
                                <option value="" hidden="hidden">Выберите толщину</option>
                                <?php
                                if(!empty(filter_input(INPUT_POST, 'film_id'))) {
                                    $film_variations = (new Grabber("select id, thickness, weight from film_variation where film_id = ".filter_input(INPUT_POST, 'film_id')." and id in (select film_variation_id from supplier_film_variation) order by thickness"))->result;
                                    foreach($film_variations as $film_variation) {
                                        $id = $film_variation['id'];
                                        $thickness = $film_variation['thickness'];
                                        $weight = $film_variation['weight'];
                                        $selected = '';
                                        if(filter_input(INPUT_POST, 'film_variation_id') == $id) $selected = " selected='selected'";
                                        echo "<option value='$id'$selected>$thickness мкм $weight г/м<sup>2</sup></option>";
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
                                        <input type="radio" class="form-check-input spool" id="spool_76" name="spool" value="76"<?=$checked76 ?> />76 мм
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input spool" id="spool_152" name="spool" value="152"<?=$checked152 ?> />152 мм
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
                            <label for="cell">Ячейка на складе</label>
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
            // Загрузка списка толщин
            $('#film_id').change(function(){
                if($(this).val() == "") {
                    $('#film_variation_id').html("<option value=''>Выберите толщину</option>");
                }
                else {
                    $.ajax({ url: "_thickness.php?film_id=" + $(this).val() })
                            .done(function(data) {
                                $('#film_variation_id').html(data);
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
            $sql = "SELECT id, thickness, weight FROM film_variation";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()):
            ?>
            if(films.get(<?=$row['id'] ?>) == undefined) {
                films.set(<?=$row['id'] ?>, [<?=$row['thickness'] ?>, <?=$row['weight'] ?>]);
            }
            <?php endwhile; ?>
            
            // Расчёт длины и массы плёнки по шпуле, толщине, радиусу, ширине, удельному весу
            function CalculateByRadius() {
                film_variation_id = $('#film_variation_id').val();
                spool = $('.spool:checked').val();
                width = $('#width').val();
                radius = $('#radius').val();
                
                if(!isNaN(spool) && !isNaN(film_variation_id) && !isNaN(radius) && !isNaN(width) 
                        && spool != '' && film_variation_id != '' && radius != '' && width != '') {
                    thickness = films.get(parseInt(film_variation_id))[0];
                    density = films.get(parseInt(film_variation_id))[1];
                    
                    result = GetFilmLengthWeightBySpoolThicknessRadiusWidth(spool, thickness, radius, width, density);
                    
                    $('#length').val(result.length.toFixed(2));
                    $('#net_weight').val(result.weight.toFixed(2));
                }
            }
            
            // Рассчитываем ширину и массу плёнки при изменении значений каждого поля, участвующего в вычислении
            $('#spool_76').change(CalculateByRadius);
            
            $('#spool_152').change(CalculateByRadius);
            
            $('#radius').keypress(CalculateByRadius);
            
            $('#radius').keyup(CalculateByRadius);
            
            $('#radius').change(CalculateByRadius);
            
            $('#film_variation_id').change(CalculateByRadius);
            
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