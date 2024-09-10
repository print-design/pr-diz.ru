<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_CUTTER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Проверяем, имеются ли незакрытые нарезки
include '_check_rolls.php';
$opened_roll = CheckOpenedRolls($user_id);
$cutting_id = $opened_roll['id'];
$last_source = $opened_roll['last_source'];
$last_wind = $opened_roll['last_wind'];

if(empty($cutting_id)) {
    header("Location: ".APPLICATION.'/cutter/');
}

// Валидация формы
$form_valid = true;
$error_message = '';

$radius_valid = '';

function CloseCutting($cutting_id, $last_source, $last_wind, $user_id) {
    // Закрываем нарезку
    $sql = "update cutting set date=now() where id=$cutting_id";
    $fetcher = new Fetcher($sql);
    $error = $fetcher->error;
    
    // Удаляем последний исходный ролик, если у него не было ни одной намотки.
    // (то есть если его ввели и сразу стали закрывать заявку)
    if(empty($error)) {
        if(!empty($last_source) && empty($last_wind)) {
            // Удаляем запись о статусе "Раскроили"
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
                    $error = $executer->error;
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
                    $error = $executer->error;
                }
            }
            
            // Удаляем запись об исходном ролике
            if(empty($error)) {
                $sql = "delete from cutting_source where id = $last_source";
                $executer = new Executer($sql);
                $error = $executer->error;
            }
        }
    }
    
    // Меняем статусы исходных роликов на "Раскроили" (если он ещё не установлен)
    $cut_sources = null;
    
    if(empty($error)) {
        $sql = "select is_from_pallet, roll_id from cutting_source where cutting_id=$cutting_id";
        $grabber = new Grabber($sql);
        $cut_sources = $grabber->result;
        $error = $grabber->error;
    }
    
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
                    $error = $executer->error;
                }
            }
            else {
                $sql = "select status_id from pallet_roll_status_history where pallet_roll_id = $source_roll_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                $row = $fetcher->Fetch();
                
                if(!$row || $row['status_id'] != ROLL_STATUS_CUT) {
                    $sql = "insert into pallet_roll_status_history (pallet_roll_id, status_id, user_id) values($source_roll_id, ".ROLL_STATUS_CUT.", $user_id)";
                    $executer = new Executer($sql);
                    $error = $executer->error;
                }
            }
        }
    }
    
    return $error;
}

if(null !== filter_input(INPUT_POST, 'close-submit')) {
    // Создаём остаточный ролик
    $cutting_id = filter_input(INPUT_POST, 'cutting_id');
    $last_source = filter_input(INPUT_POST, 'last_source');
    $last_wind = filter_input(INPUT_POST, 'last_wind');
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    $film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
    $width = filter_input(INPUT_POST, 'width');
    $net_weight = filter_input(INPUT_POST, 'net_weight');
    $length = filter_input(INPUT_POST, 'length');
    $spool = filter_input(INPUT_POST, 'spool');
    $cell = "Цех";
    $comment = addslashes(filter_input(INPUT_POST, 'comment'));
            
    $sql = "insert into roll (supplier_id, film_variation_id, width, length, net_weight, comment, storekeeper_id) "
            . "values ($supplier_id, $film_variation_id, $width, $length, $net_weight, '$comment', '$user_id')";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    $roll_id = $executer->insert_id;
    
    // Устанавливаем этому ролику ячейку "Цех"
    if(empty($error_message)) {
        $sql = "insert into roll_cell_history (roll_id, cell, user_id) values ($roll_id, '$cell', $user_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
            
    // Устанавливаем этому ролику статус "Свободный"
    if(empty($error_message)) {
        $sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($roll_id, ".ROLL_STATUS_FREE.", $user_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
            
    // Добавляем остаточный ролик к последней закрытой нарезке данного пользователя
    if(empty($error_message)) {
        $sql = "update cutting set remain = $roll_id where id = $cutting_id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    // Закрываем нарезку
    if(empty($error_message)) {
        $error_message = CloseCutting($cutting_id, $last_source, $last_wind, $user_id);
    }
    
    if(empty($error_message)) {
        header("Location: print_remain.php");
    }
}

if(null !== filter_input(INPUT_POST, 'no-remain-submit')) {
    $cutting_id = filter_input(INPUT_POST, 'cutting_id');
    $error_message = CloseCutting($cutting_id, $last_source, $last_wind, $user_id);
    
    if(empty($error_message)) {
        header("Location: finish.php?id=$cutting_id");
    }
}

// Получение объекта
$supplier_id = null;
$film_variation_id = null;
$width = null;

$sql = "select supplier_id, film_variation_id, width from cutting where id = $cutting_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $supplier_id = $row['supplier_id'];
    $film_variation_id = $row['film_variation_id'];
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
                <ul class="navbar-nav w-75">
                    <li class="nav-item">
                        <a href="wind.php" class="nav-link"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            <h1>Закрытие заявки</h1>
            <form method="post">
                <input type="hidden" id="cutting_id" name="cutting_id" value="<?=$cutting_id ?>" />
                <input type="hidden" id="last_source" name="last_source" value="<?=$last_source ?>" />
                <input type="hidden" id="last_wind" name="last_wind" value="<?=$last_wind ?>" />
                <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$supplier_id ?>" />
                <input type="hidden" id="film_variation_id" name="film_variation_id" value="<?=$film_variation_id ?>" />
                <input type="hidden" id="width" name="width" value="<?=$width ?>" />
                <input type="hidden" id="net_weight" name="net_weight" />
                <input type="hidden" id="length" name="length" />
                <?php
                $remains_checked = " checked='checked'";
                $remainder_group_none = "";
                $radius_required = " required='required'";
                
                if(null !== filter_input(INPUT_POST, 'close-submit') && filter_input(INPUT_POST, 'remains') != 'on') {
                    $remains_checked = "";
                    $remainder_group_none = " d-none";
                    $radius_required = "";
                }
                ?>
                <div class="form-group">
                    <input type="checkbox" id="remains" name="remains"<?=$remains_checked ?> />
                    <label class="form-check-label" for="remains">Остался исходный ролик</label>
                </div>
                <div class="form-group remainder-group<?=$remainder_group_none ?>">
                    <label for="radius">Введите радиус от вала исходного роля</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only<?=$radius_valid ?>" data-max="999" id="radius" name="radius" value="<?= filter_input(INPUT_POST, 'radius') ?>" autocomplete="off"<?=$radius_required ?> />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback">Число, макс. 999</div>
                    </div>
                </div>
                <div class="form-group remainder-group<?=$remainder_group_none ?>">
                    <label for="spool">Диаметр шпули</label>
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
                <div class="form-group remainder-group<?=$remainder_group_none ?>">
                    <label for="comment">Комментарий</label>
                    <input type="text" class="form-control" name="comment" id="comment" value="<?= filter_input(INPUT_POST, 'comment') ?>" autocomplete="off" />
                </div>
                <div class="form-group remainder-group">
                    <button type="submit" class="btn btn-dark form-control" style="height: 5rem;" id="close-submit" name="close-submit">Распечатать исходный роль<br /> и закрыть заявку</button>
                </div>
                <div class="form-group no-remainder-group d-none">
                    <button type="submit" class="btn btn-dark form-control" id="no-remain-submit" name="no-remain-submit">Закрыть заявку</button>
                </div>
            </form>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Скрытие/показ элементов формы в зависимости от того, остался ли исходный ролик
            $('#remains').change(function() {
                if($(this).is(':checked')) {
                    $('.remainder-group').removeClass('d-none');
                    $('input#radius').attr('required', 'required');
                    $('input#radius').focus();
                    
                    $('.no-remainder-group').addClass('d-none');
                }
                else {
                    $('.remainder-group').addClass('d-none');
                    $('input#radius').removeAttr('required');
                    
                    $('.no-remainder-group').removeClass('d-none');
                }
            });
    
            // Все марки плёнки с их вариациями
            var films = new Map();
            
            <?php
            $sql = "SELECT id, thickness, weight from film_variation";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()):
            ?>
                if(films.get(<?=$row['id'] ?>) == undefined) {
                    films.set(<?=$row['id'] ?>, [<?=$row['thickness'] ?>, <?=$row['weight'] ?>]);
                }
            <?php endwhile; ?>
            
            // Расчёт длины и массы плёнки по шпуле, толщине, радиусу, ширине, удельному весу
            function CalculateByRadius() {
                $('#length').val('');
                $('#net_weight').val('');
                
                film_variation_id = $('#film_variation_id').val();
                spool = $('input[name="spool"]:checked').val();
                radius = $('#radius').val();
                width = $('#width').val();
                
                if(!isNaN(spool) && !isNaN(film_variation_id) && !isNaN(radius) && !isNaN(width) 
                        && spool !== '' && film_variation_id !== '' && radius !== '' && width !== '') {
                    thickness = films.get(parseInt(film_variation_id))[0];
                    density = films.get(parseInt(film_variation_id))[1];
                    
                    result = GetFilmLengthWeightBySpoolThicknessRadiusWidth(spool, thickness, radius, width, density);
                    
                    $('#length').val(result.length.toFixed(2));
                    $('#net_weight').val(result.weight.toFixed(2));
                }
            }
            
            $(document).ready(CalculateByRadius);
            
            // Рассчитываем ширину и массу плёнки при изменении значений каждого поля, участвующего в вычислении
            $('input[name="spool"]').click(CalculateByRadius);
            
            $('#radius').keypress(CalculateByRadius);
            
            $('#radius').keyup(CalculateByRadius);
            
            $('#radius').change(CalculateByRadius);
            
            // Установка фокуса
            $('input#radius').focus();
        </script>
    </body>
</html>