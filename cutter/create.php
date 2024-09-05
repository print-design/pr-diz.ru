<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_CUTTER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

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
// Если нет ручьёв, переходим на страницу "Как режем"
elseif(empty ($streams_count)) {
    header("Location: streams.php");
}

// Валидация формы
$form_valid = true;
$error_message = '';

$width_valid = '';
$width_message = "Ширина обязательно";
$radius_valid = '';

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    if(empty($supplier_id)) {
        $error_message = "Не указан производитель плёнки";
        $form_valid = false;
    }
    
    $width = filter_input(INPUT_POST, 'width');
    if(empty($width)) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    elseif($width < 50 || $width > 1600) {
        $width_valid = ISINVALID;
        $width_message = "От 50 до 1600";
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
    
    $film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
    $width = filter_input(INPUT_POST, 'width');
    $cell = 'Цех';
    $comment = addslashes('!');
    $storekeeper_id = $user_id;
    
    if($form_valid) {
        // Создаём новый рулон
        $sql = "insert into roll (supplier_id, film_variation_id, width, length, net_weight, cell, comment, storekeeper_id) "
                . "values ($supplier_id, $film_variation_id, $width, $length, $net_weight, '$cell', '$comment', '$storekeeper_id')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $roll_id = $executer->insert_id;
        $is_from_pallet = 0;
        
        // Устанавливаем ему статус "Свободный"
        if(empty($error_message)) {
            $sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($roll_id, ".ROLL_STATUS_FREE.", $user_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        // Устанавливаем ему статус "Раскроили"
        if(empty($error_message)) {
            $sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($roll_id, ".ROLL_STATUS_CUT.", $user_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        // Меняем статусы предыдущих исходных роликов на "Раскроили" (если он ещё не установлен)
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
                
                        if(!$row || $row['status_id'] != ROLL_STATUS_CUT) {
                            $sql = "insert into roll_status_history (roll_id, status_id, user_id) values($source_roll_id, ".ROLL_STATUS_CUT.", $user_id)";
                            $executer = new Executer($sql);
                            $error_message = $executer->error;
                        }
                    }
                    else {
                        $sql = "select status_id from pallet_roll_status_history where pallet_roll_id = $source_roll_id order by id desc limit 1";
                        $fetcher = new Fetcher($sql);
                        $row = $fetcher->Fetch();
                
                        if(!$row || $row['status_id'] != ROLL_STATUS_CUT) {
                            $sql = "insert into pallet_roll_status_history (pallet_roll_id, status_id, user_id) values($source_roll_id, ".ROLL_STATUS_CUT.", $user_id)";
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
            header("Location: wind.php");
        }
    }
}

// Получение объекта
$supplier_id = null;
$film_id = null;
$film_variation_id = null;
$width = null;

$sql = "select c.supplier_id, fv.film_id, c.film_variation_id, c.width from cutting c inner join film_variation fv on c.film_variation_id = fv.id where c.id = $cutting_id";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $supplier_id = $row['supplier_id'];
    $film_id = $row['film_id'];
    $film_variation_id = $row['film_variation_id'];
    
    if(null !== filter_input(INPUT_POST, 'width')) {
        $width = filter_input(INPUT_POST, 'width');
    }
    else {
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
                        <input type="hidden" id="film_variation_id" name="film_variation_id" value="<?=$film_variation_id ?>" />
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
                            <label for="film_id">Марка плёнки</label>
                            <select class="form-control" disabled="disabled">
                                <option value="" hidden="hidden">Выберите марку</option>
                                <?php
                                if(!empty($supplier_id)) {
                                    $films = (new Grabber("select id, name from film where id in (select film_id from film_variation where id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id))"))->result;
                                    foreach($films as $film) {
                                        $id = $film['id'];
                                        $name = $film['name'];
                                        $selected = '';
                                        if($film_id == $film['id']) $selected = " selected='selected'";
                                        echo "<option value='$id'$selected>$name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="film_variation_id">Толщина, мкм</label>
                            <select class="form-control" disabled="disabled">
                                <option value="" hidden="hidden">Выберите толщину</option>
                                <?php
                                if(!empty($supplier_id) && !empty($film_id)) {
                                    $film_variations = (new Grabber("select id, thickness, weight from film_variation where film_id = $film_id and id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id) order by thickness"))->result;
                                    foreach($film_variations as $film_variation) {
                                        $id = $film_variation['id'];
                                        $thickness = $film_variation['thickness'];
                                        $weight = $film_variation['weight'];
                                        $selected = '';
                                        if($film_variation_id == $id) $selected = " selected='selected'";
                                        echo "<option value='$id'$selected>$thickness мкм $weight г/м<sup>2</sup></option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="width">Ширина, мм</label>
                            <input type="text" id="width" name="width" value="<?= filter_input(INPUT_POST, 'width') ?>" class="form-control int-only<?=$width_valid ?>" required="required" autocomplete="off" />
                            <div class="invalid-feedback"><?=$width_message ?></div>
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
            $sql = "select id, thickness, weight from film_variation";
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
                spool = $('#spool:checked').val();
                width = $('#width').val();
                radius = $('#radius').val();
                
                if(!isNaN(spool) && !isNaN(film_variation_id) && !isNaN(radius) && !isNaN(width) 
                        && spool !== '' && film_variation_id !== '' && radius !== '' && width !== '') {
                    thickness = films.get(parseInt(film_variation_id))[0];
                    density = films.get(parseInt(film_variation_id))[1];
                    
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
            
            // Установление фокуса
            $('#width').focus();
        </script>
    </body>
</html>