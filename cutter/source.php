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
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$source_id_valid = '';
$source_id_valid_message = 'ID рулона обязательно';

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $cutting_id = filter_input(INPUT_POST, 'cutting_id');
    
    $source_id = filter_input(INPUT_POST, 'source_id');
    if(empty($source_id)) {
        $source_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    // Распознавание исходного ролика
    $source_id = trim($source_id);
    $is_from_pallet = null;
    $roll_id = null;
    
    // Если первый символ р или Р, ищем среди рулонов (временно убираем проверку по статусу).
    if((mb_substr($source_id, 0, 1) == "р" || mb_substr($source_id, 0, 1) == "Р") && is_numeric(mb_substr($source_id, 1))) {
        $source_roll_id = mb_substr($source_id, 1);
        $sql = "select r.id from roll r where r.id = '$source_roll_id' limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $is_from_pallet = 0;
            $roll_id = $row['id'];
        }
    }
    // Если первый символ п или П, ищем сначала среди свободных роликов в паллете,
    // если свободных нет, берём, какие есть.
    elseif((mb_substr($source_id, 0, 1) == "п" || mb_substr($source_id, 0, 1) == "П") && is_numeric(mb_substr($source_id, 1))) {
        $pallet_id = mb_substr($source_id, 1);
        $sql = "select pr.id id, ifnull(prsh.status_id, 0) status_id "
                . "from pallet_roll pr "
                . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                . "where pr.pallet_id = '$pallet_id' "
                . "order by status_id "
                . "limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $is_from_pallet = 1;
            $roll_id = $row['id'];
        }
    }
    
    // Если объект найден в базе, проверяем, соответствувет ли он нужным параметрам
    // марка и толщина
    $source_film_variation = null;
        
    if(!empty($roll_id) && $is_from_pallet !== null) {
        $sql = "";
        
        if($is_from_pallet == 0) {
            $sql = "select film_variation_id from roll where id=$roll_id";
        }
        else {
            $sql = "select film_variation_id from pallet where id in (select pallet_id from pallet_roll where id=$roll_id)";
        }
        
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $source_film_variation = $row['film_variation_id'];
        }
        else {
            $source_id_valid_message = "Параметры исходного ролика не найдены";
            $source_id_valid = ISINVALID;
            $form_valid = false;
        }
    }
    else {
        $source_id_valid_message = "Объект отсутствует в базе";
        $source_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $cutting_film_variation = null;
    
    if($form_valid) {
        $sql = "select film_variation_id from cutting where id=$cutting_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $cutting_film_variation = $row['film_variation_id'];
        }
        else {
            $source_id_valid_message = "Параметры нарезки не найдены";
            $source_id_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if($source_film_variation != $cutting_film_variation) {
        $source_id_valid_message = "Не совпадают характеристики";
        $source_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid && !empty($last_source)) {
        // Если исходный ролик тот же, что и предыдущий, запрещаем его использовать
        $last_is_from_pallet = null;
        $last_roll_id = null;
            
        $sql = "select is_from_pallet, roll_id from cutting_source where id = $last_source";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $last_is_from_pallet = $row['is_from_pallet'];
            $last_roll_id = $row['roll_id'];
        }
        
        if($last_is_from_pallet !== null && $last_roll_id !== null && $last_is_from_pallet == $is_from_pallet && $last_roll_id == $roll_id) {
            $source_id_valid_message = "Этот ролик уже использован";
            $source_id_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if($form_valid) {
        // Добавляем новый исходный ролик
        $sql = "insert into cutting_source (cutting_id, is_from_pallet, roll_id) values ($cutting_id, $is_from_pallet, $roll_id)";
        $executer = new Executer($sql);
        $error_message == $executer->error;
        
        // Меняем статусы всех исходных роликов (включая и новый) на "Раскроили" (если он ещё не установлен)
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
        
        if(empty($error_message)) {
            header("Location: wind.php");
        }
    }
}

// Получение объекта
$source_id = filter_input(INPUT_POST, 'source_id');
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
                        <?php if(empty($last_source)): ?>
                        <a class="nav-link" href="streams.php"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        <?php else: ?>
                        <a class="nav-link" href="wind.php"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        <?php endif; ?>
                    </li>
                </ul>
                <ul class="navbar-nav mr-4">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" href="javascript: void(0);" data-toggle="modal" data-target="#infoModal"><img src="<?=APPLICATION ?>/images/icons/info.svg" /></a>
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
            include '_info.php';
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            $next_submit_disabled = '';
            $create_submit_disabled = '';
            
            if(empty($source_id)) {
                $next_submit_disabled = " disabled";
            }
            else {
                $create_submit_disabled = " disabled";
            }
            ?>
            <h1>Исходный рулон</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <input type="hidden" name="cutting_id" value="<?=$cutting_id ?>" />
                        <div class="form-group">
                            <label for="source_id">ID рулона</label>
                            <input type="text" id="source_id" name="source_id" value="<?= $source_id ?>" class="form-control<?=$source_id_valid ?>" required="required" autocomplete="off" />
                            <div class="invalid-feedback order-last"><?=$source_id_valid_message ?></div>
                            <div style='position: absolute; top: 2.1rem; right: 1.2rem;'>
                                <button type='button' id="clear" class="d-none" style='background-color: white; border: 0;'><i class='fas fa-times'></i></button>
                            </div>
                        </div>
                        <div class="form-group d-none d-lg-block">
                            <div class="form-group">
                                <button type="submit" id="next-submit" name="next-submit" class="btn btn-dark form-control mt-4 next-submit<?=$next_submit_disabled ?>">Далее</button>
                            </div>
                            <div class="form-group">
                                <a href="create.php" class="btn btn-outline-dark form-control create-submit<?=$create_submit_disabled ?>">Добавить в базу</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="d-block d-lg-none w-100 pb-4" id="bottom_buttons">
                <div class="form-group">
                    <button type="button" class="btn btn-dark form-control next-submit<?=$next_submit_disabled ?>" onclick="javascript: $('#next-submit').click();">Далее</button>
                </div>
                <div class="form-group">
                    <a href="create.php" class="btn btn-outline-dark form-control create-submit<?=$create_submit_disabled ?>">Добавить в базу</a>
                </div>
            </div>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Очищаем поле по нажатию крестика
            function AddClearListener() {
                $('button#clear').click(function() {
                    $("input#source_id").val('');
                    $("input#source_id").change();
                    $("input#source_id").focus();
                });
            }
            
            // Показываем кнопку очистки поля
            // а также либо кнопку "Далее" либо кнопку "Добавить в базу"
            function SetClearVisibility(obj) {
                if(obj.val() == '') {
                    $('button#clear').addClass('d-none');
                    $('.next-submit').addClass('disabled');
                    $('.create-submit').removeClass('disabled');
                }
                else {
                    $('button#clear').removeClass('d-none');
                    $('.next-submit').removeClass('disabled');
                    $('.create-submit').addClass('disabled');
                }
            }
            
            // Позиционируем кнопку "Далее" относительно нижнего края экрана только если она не перекроет другие элементы
            function AdjustButtons() {
                if($('#source_id').offset().top + $('#bottom_buttons').outerHeight() + 80 < $(window).height()) {
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
                SetClearVisibility($('input#source_id'));
                AddClearListener();
                AdjustButtons();
            });
            
            $(window).on('resize', AdjustButtons);
            
            $('input#source_id').keyup(function(e) {
                $(e.target).removeClass('is-invalid');
                SetClearVisibility($(e.target));
            });
            
            $('input#source_id').keypress(function(e) {
                $(e.target).removeClass('is-invalid');
                SetClearVisibility($(e.target));
            });
            
            $('input#source_id').change(function(e) {
                $(e.target).removeClass('is-invalid');
                SetClearVisibility($(e.target));
            });    
        </script>
    </body>
</html>