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
$last_wind = $opened_roll['last_wind'];

$streams_count = $opened_roll['streams_count'];

// Если нет незакрытой нарезки, переходим на первую страницу
if(empty($cutting_id)) {
    header("Location: ".APPLICATION.'/cutter/');
}
// Если нет исходного ролика, переходим на страницу создания исходного ролика
elseif(empty ($last_source)) {
    header("Location: source.php");
}
// Если нет ручьёв, переходим на страницу "Как режем"
elseif(empty ($streams_count)) {
    header("Location: streams.php");
}

// Валидация формы
$form_valid = true;
$error_message = '';

$length_valid = '';
$length_message = 'Обязательно, не более 30 000';
$radius_valid = '';
$radius_message = 'Обязательно, не более 999';

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $length = preg_replace("/\D/", "", filter_input(INPUT_POST, 'length') ?? '');
    if(empty($length) || intval($length) > 30000) {
        $length_valid = ISINVALID;
        $form_valid = false;
    }
    
    $radius = filter_input(INPUT_POST, 'radius');
    if(empty($radius) || intval($radius) > 999) {
        $radius_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Валидация длины
        $normal_length = filter_input(INPUT_POST, 'normal_length');
        $max_length = floatval($normal_length) * 1.2;
        $min_length = floatval($normal_length) * 0.8;
        $my_length = floatval($length);
        
        if($my_length > $max_length || $my_length < $min_length) {
            $length_valid = ISINVALID;
            $length_message = "Длина не соответствует радиусу";
            $radius_valid = ISINVALID;
            $radius_message = "Длина не соответствует радиусу";
            $form_valid = false;
        }
    }
    
    if($form_valid) {
        // Валидация, чтобы сумма длин всех намоток не превышала сумму длин исходных роликов (с запасом 1000 м для каждого исходного ролика)
        $source_length = 0;
        $source_count = 0;
        
        $sql = "select sum(r.length) length, count(r.id) count "
                . "from cutting_source cs "
                . "inner join roll r on cs.roll_id = r.id "
                . "where cs.cutting_id = $cutting_id and cs.is_from_pallet = 0 "
                . "union "
                . "select sum(pr.length) length, count(pr.id) count "
                . "from cutting_source cs "
                . "inner join pallet_roll pr on cs.roll_id = pr.id "
                . "where cs.cutting_id = $cutting_id and cs.is_from_pallet = 1";
        $fetcher = new Fetcher($sql);
        
        while($row = $fetcher->Fetch()) {
            $source_length += $row['length'];
            $source_count += $row['count'];
        }
        
        $wind_lengths = 0;
        
        $sql = "select sum(length) from cutting_wind where cutting_source_id in (select id from cutting_source where cutting_id = $cutting_id)";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $wind_lengths = $row[0];
        }
        
        $reserve_length = 1000 * $source_count;
        
        if($length + $wind_lengths > $source_length + $reserve_length) {
            $length_valid = ISINVALID;
            $length_message = "Превышенна сумма длин исходных роликов";
            $form_valid = false;
        }
    }
    
    if($form_valid) {
        // Создание намотки
        $net_weight = filter_input(INPUT_POST, 'net_weight');
        
        $sql = "insert into cutting_wind (cutting_source_id, length, radius) values ($last_source, $length, $radius)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $cutting_wind_id = $executer->insert_id;
        
        // Получение данных о материале
        $supplier_id = 0;
        $film_variation_id = 0;
        
        if(empty($error_message)) {
            $sql = "select supplier_id, film_variation_id from cutting where id=$cutting_id";
            $fetcher = new Fetcher($sql);
            $error_message = $fetcher->error;
            
            if($row = $fetcher->Fetch()) {
                $supplier_id = $row['supplier_id'];
                $film_variation_id = $row['film_variation_id'];
            }
        }
        
        // Создание рулона на каждый ручей
        if(empty($error_message)) {
            for($i = 1; $i <= 19; $i++) {
                if(key_exists('stream_'.$i, $_POST)) {
                    $width = filter_input(INPUT_POST, 'stream_'.$i);
                    $comment = addslashes(filter_input(INPUT_POST, 'comment_'.$i) ?? '');
                    $cell = addslashes(filter_input(INPUT_POST, 'cell_'.$i) ?? '');
                    $net_weight = filter_input(INPUT_POST, 'net_weight_'.$i);
        
                    $sql = "insert into roll (supplier_id, film_variation_id, width, length, net_weight, comment, storekeeper_id, cutting_wind_id) "
                            . "values ($supplier_id, $film_variation_id, $width, $length, $net_weight, '$comment', '$user_id', $cutting_wind_id)";
                    $executer = new Executer($sql);
                    $error_message = $executer->error;
                    $insert_id = $executer->insert_id;
                    
                    // Заполнение истории ячеек
                    if(empty($error_message)) {
                        $sql = "insert into roll_cell_history (roll_id, cell, user_id) values ($insert_id, '$cell', $user_id)";
                        $executer = new Executer($sql);
                        $error_message = $executer->error;
                    }
                    
                    // Заполнение истории статусов
                    if(empty($error_message)) {
                        $sql = "insert into roll_status_history (roll_id, status_id, user_id) values($insert_id, ".ROLL_STATUS_FREE.", $user_id)";
                        $executer = new Executer($sql);
                        $error_message = $executer->error;
                    }
                }
            }
        }
        
        // Переход на страницу печати рулонов
        if(empty($error_message)) {
            header("Location: print.php");
        }
    }
}

if(null !== filter_input(INPUT_POST, 'previous-submit')) {
    // Удаляем запись о статусе "Рескроили" последнего исходного ролика
    $last_source_roll_id = null;
    $last_source_is_from_pallet = null;
    $last_source_history_id = null;
    $last_source_status_id = null;
    
    $sql = "select roll_id, is_from_pallet from cutting_source where id = $last_source";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $last_source_roll_id = $row['roll_id'];
        $last_source_is_from_pallet = $row['is_from_pallet'];
    }
            
    if(!empty($last_source_roll_id) && $last_source_is_from_pallet == 0) {
        $sql = "select id, status_id from roll_status_history where roll_id = $last_source_roll_id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $last_source_history_id = $row['id'];
            $last_source_status_id = $row['status_id'];
        }
                
        if(!empty($last_source_history_id) && !empty($last_source_status_id) && $last_source_status_id == ROLL_STATUS_CUT) {
            $sql = "delete from roll_status_history where id = $last_source_history_id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
    }
    elseif(!empty ($last_source_roll_id) && $last_source_is_from_pallet == 1) {
        $sql = "select id, status_id from pallet_roll_status_history where pallet_roll_id = $last_source_roll_id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $last_source_history_id = $row['id'];
            $last_source_status_id = $row['status_id'];
        }
                
        if(!empty($last_source_history_id) && !empty($last_source_status_id) && $last_source_status_id == ROLL_STATUS_CUT) {
            $sql = "delete from pallet_roll_status_history where id = $last_source_history_id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
    }
    
    // Удаляем запись о последнем исходном ролике
    if(empty($error_message)) {
        $last_source = filter_input(INPUT_POST, 'last_source');
        $sql = "delete from cutting_source where id = $last_source";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        header("Location: source.php");
    }
}

// Получение объекта
$supplier_id = null;
$film_variation_id = null;
$width = null;
$winds_count = 0;

$sql = "select c.supplier_id, c.film_variation_id, c.width, (select count(id) from cutting_wind where cutting_source_id in (select id from cutting_source where cutting_id=c.id)) winds_count "
        . "from cutting c where c.id=$cutting_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $supplier_id = $row['supplier_id'];
    $film_variation_id = $row['film_variation_id'];
    $width = $row['width'];
    $winds_count = $row['winds_count'];
}

$sql = "select width, comment, cell from cutting_stream where cutting_id=$cutting_id order by id";
$fetcher = new Fetcher($sql);
$i = 0;
while ($row = $fetcher->Fetch()) {
    $stream = 'stream_'.++$i;
    $$stream = $row['width'];
    $comment = 'comment_'.$i;
    $$comment = $row['comment'];
    $cell = 'cell_'.$i;
    $$cell = $row['cell'];
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
                <ul class="navbar-nav w-75">
                    <li class="nav-item">
                        <?php if(empty($last_wind)): ?>
                        <form method="post">
                            <input type="hidden" name="last_source" value="<?=$last_source ?>" />
                            <button class="btn btn-link nav-link" type="submit" name="previous-submit"><i class="fas fa-chevron-left"></i>&nbsp;Назад</button>
                        </form>
                        <?php endif; ?>
                    </li>
                </ul>
                <ul class="navbar-nav mr-4">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" href="javascript: void(0);" data-toggle="modal" data-target="#infoModal"><img src="<?=APPLICATION ?>/images/icons/info.svg" /></a>
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
            include '_info.php';
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Нарезка <?=$cutting_id ?> / <?=date('d.m.Y') ?></h1>
            <p class="mb-3 mt-3" style="font-size: xx-large;">Намотка <?=($winds_count + 1) ?></p>
                <?php
                for($i=1; $i<=19; $i++):
                    $stream = 'stream_'.$i;
                if(isset($$stream)):
                ?>
            <p>Ручей <?=$i ?> - <?=$$stream ?> мм</p>
                <?php
                endif;
                endfor;
                ?>
            <form method="post" class="mt-3">
                <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$supplier_id ?>" />
                <input type="hidden" id="film_variation_id" name="film_variation_id" value="<?=$film_variation_id ?>" />
                <input type="hidden" id="width" name="width" value="<?=$width ?>" />
                <input type="hidden" id="spool" name="spool" value="76" />
                <input type="hidden" id="net_weight" name="net_weight" />
                <input type="hidden" id="normal_length" name="normal_length" />
                <input type="hidden" name="cutting_id" value="<?=$cutting_id ?>" />
                <input type="hidden" name="last_source" value="<?=$last_source ?>" />
                    <?php
                    for($i=1; $i<=19; $i++):
                    $stream = 'stream_'.$i;
                    $comment = 'comment_'.$i;
                    $cell = 'cell_'.$i;
                    if(isset($$stream)):
                    ?>
                <input type="hidden" id="stream_<?=$i ?>" name="stream_<?=$i ?>" value="<?=$$stream ?>" />
                <input type="hidden" id="comment_<?=$i ?>" name="comment_<?=$i ?>" value="<?= htmlspecialchars($$comment) ?>" />
                <input type="hidden" id="cell_<?=$i ?>" name="cell_<?=$i ?>" value="<?= htmlspecialchars($$cell) ?>" />
                <input type="hidden" id="net_weight_<?=$i ?>" name="net_weight_<?=$i ?>" />
                    <?php
                    endif;
                    endfor;
                    ?>
                <div class="form-group">
                    <label for="length">Длина, м</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only int-format<?=$length_valid ?>" data-max="30000" id="length" name="length" value="<?= filter_input(INPUT_POST, 'length') ?>" required="required" autocomplete="off" />
                        <div class="input-group-append"><span class="input-group-text">м</span></div>
                        <div class="invalid-feedback invalid-length"><?=$length_message ?></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="radius">Радиус от вала, мм</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only<?=$radius_valid ?>" data-max="999" id="radius" name="radius" value="<?= filter_input(INPUT_POST, 'radius') ?>" required="required" autocomplete="off" />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback invalid-radius"><?=$radius_message ?></div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-outline-dark form-control mt-3 mb-5 next_wind" id="next-submit" name="next-submit">Следующая намотка</button>
                </div>
            </form>
            <div class="w-100 pb-4" id="bottom_buttons">
                <!--?php if(!empty($last_wind)): ?-->
                <div class="form-group">
                    <a href="source.php" class="btn btn-outline-dark form-control next_source mt-3">Новый исходный рулон</a>
                </div>
                <!--?php endif; ?-->
                <?php if($winds_count > 0): ?>
                <div class="form-group">
                    <a href="remain.php" class="btn btn-dark form-control mt-3">Заявка выполнена</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Все марки плёнки с их вариациями
            var films = new Map();
            
            <?php
            $sql = "SELECT id, thickness, weight FROM film_variation";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()):
            ?>
                if(films.get(<?=$row['id'] ?>) === undefined) {
                    films.set(<?=$row['id'] ?>, [<?=$row['thickness'] ?>, <?=$row['weight'] ?>]);
                }
            <?php endwhile; ?>
                
            // Расчёт длины и массы плёнки по шпуле, толщине, радиусу, ширине, удельному весу
            function CalculateByRadius() {
                $('#normal_length').val('');
                $('#net_weight').val('');
                
                spool = $('#spool').val();
                film_variation_id = $('#film_variation_id').val();
                radius = $('#radius').val();
                width = $('#width').val();
                length = $('#length').val().replaceAll(/\D/g, '');
                
                if(!isNaN(spool) && !isNaN(film_variation_id) && !isNaN(radius) && !isNaN(width) 
                        && spool !== '' && film_variation_id !== '' && radius !== '' && width !== '') {
                    thickness = films.get(parseInt(film_variation_id))[0];
                    density = films.get(parseInt(film_variation_id))[1];
                    
                    result = GetFilmLengthWeightBySpoolThicknessRadiusWidth(spool, thickness, radius, width, density);
                    
                    $('#normal_length').val(result.length.toFixed(2));
                    $('#net_weight').val(result.weight.toFixed(2));
                }
        
                for(i=1; i<=19; i++) {
                    if($('#stream_' + i).length > 0) {
                        width = $('#stream_' + i).val();
                
                        if(!isNaN(length) && !isNaN(film_variation_id) && !isNaN(width) 
                                && length !== '' && film_variation_id !== '' && width !== '') {
                            density = films.get(parseInt(film_variation_id))[1];
                            weight = GetFilmWeightByLengthWidth(length, width, density);
                            $('#net_weight_' + i).val(weight.toFixed(2));
                        }
                    }
                }
                
                // Меняем видимость кнопок "Следующий исх. рулон" и "След. намотка"
                if(length === '' && radius === '') {
                    $('.next_source').removeClass('disabled');
                    $('.next_wind').addClass('disabled');
                }
                else {
                    $('.next_source').addClass('disabled');
                    $('.next_wind').removeClass('disabled');
                }
            }
            
            // Позиционируем кнопки "Следующий исходный ролик" и "Закрытие заявки" только если они не перекроют другие элементы
            function AdjustButtons() {
                if($('#next-submit').offset().top + $('#bottom_buttons').outerHeight() + 100 < $(window).height()) {
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
            
            $(document).ready(function() {
                CalculateByRadius();
                AdjustButtons();
            });
            
            $(window).on('resize', function() {
                AdjustButtons();
            });
            
            // Рассчитываем ширину и массу плёнки при изменении значений радиуса
            $('#radius').keypress(CalculateByRadius);
            
            $('#radius').keyup(CalculateByRadius);
            
            $('#radius').change(CalculateByRadius);
    
            $('#length').keypress(CalculateByRadius);
            
            $('#length').keyup(CalculateByRadius);
            
            $('#length').change(CalculateByRadius);
            
            // Установка фокуса
            $('#length').focus();
        </script>
    </body>
</html>