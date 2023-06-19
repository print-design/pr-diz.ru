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
    
    // Если первый символ р или Р, ищем среди рулонов
    if(mb_substr($source_id, 0, 1) == "р" || mb_substr($source_id, 0, 1) == "Р") {
        $roll_id = mb_substr($source_id, 1);
        $sql = "select r.id from roll r where r.id = '$roll_id' limit 1";
                //. "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                //. "where r.id='$roll_id' and (rsh.status_id is null or rsh.status_id = ".FREE_ROLL_STATUS_ID.") limit 1";
                // Временно убираем проверку по статусу.
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $is_from_pallet = 0;
            $roll_id = $row['id'];
        }
    }
    // Если первый символ п или П
    elseif(mb_substr($source_id, 0, 1) == "п" || mb_substr ($source_id, 0, 1) == "П") {
        $pallet_trim = mb_substr($source_id, 1);
        $substrings = mb_split("[Рр]", $pallet_trim);
        
        // Если внутри имеется буква, ищем среди рулонов, которые в паллетах
        if(count($substrings) == 2 && mb_strlen($substrings[0]) > 0 && mb_strlen($substrings[1]) > 0) {
            $pallet_id = $substrings[0];
            $ordinal = $substrings[1];
            $sql = "select pr.id "
                    . "from pallet_roll pr "
                    . "where pr.pallet_id = $pallet_id and pr.ordinal = $ordinal";
                    //. "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                    //. "where pr.pallet_id=$pallet_id and pr.ordinal=$ordinal "
                    //. "and (prsh.status_id is null or prsh.status_id = ".FREE_ROLL_STATUS_ID.")";
                    // Временно убираем проверку по статусу
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $is_from_pallet = 1;
                $roll_id = $row['id'];
            }
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
            <div id="codeReaderWrapper" class="modal fade show">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            ID рулона (внутренний или поставщика)
                            <button type="button" class="close" data-dismiss="modal" id="close_video"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div id="waiting2" style="position: absolute; top: 20px; left: 20px;">
                                <img src="<?=APPLICATION ?>/images/waiting2.gif" />
                            </div>
                            <video id="video" class="w-100"></video>
                        </div>
                    </div>
                </div>
            </div>
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
                            <!--div class="input-group find-group"-->
                                <input type="text" id="source_id" name="source_id" value="<?= $source_id ?>" class="form-control<?=$source_id_valid ?>" required="required" autocomplete="off" />
                                <div class="invalid-feedback order-last"><?=$source_id_valid_message ?></div>
                                <!--div class='input-group-append'>
                                    <?php /*if(empty($source_id)): ?>
                                    <button type='button' class='btn find-btn'><i class='fas fa-camera'></i></button>
                                    <?php else: ?>
                                    <button type="button" class="btn clear-btn"><i class="fas fa-times"></i></button>
                                    <?php endif;*/ ?>
                                </div-->
                            <!--/div-->
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
            source_input_id = '';
            
            // Очищаем поле по нажатию крестика
            function AddFindClearListener() {
                $('button.clear-btn').click(function() {
                    source_input_id = $(this).parent('.input-group-append').prev().prev('input').attr('id');
                    $("input#" + source_input_id).val('');
                    $("input#" + source_input_id).change();
                    $("input#" + source_input_id).focus();
                });
            }
            
            // Открываем форму чтения штрих коду по нажатию кнопки с камерой
            function AddFindCameraListener() {
                $('button.find-btn').click(function() {
                    source_input_id = $(this).parent('.input-group-append').prev().prev('input').attr('id');
                    $('#codeReaderWrapper').modal('show');
                });
            }
            
            // Показываем либо кнопку открытия сканера либо кнопку очистки поля
            // ... прибавление 16.02.2022 ...
            // а также либо кнопку "Далее" либо кнопку "Добавить в базу"
            function SetFindClearVisibility(obj) {
                obj.removeClass('is-invalid');
                
                if(obj.val() == '' && obj.parent().children('.input-group-append').children('.find-btn').length == 0) {
                    obj.parent().children('.input-group-append').html('');
                    var btn = $("<button type='button' class='btn find-btn'><i class='fas fa-camera'></i></button>");
                    obj.parent().children('.input-group-append').append(btn);
                    AddFindCameraListener();
                    
                    // ... прибавление 16.02.2022 ...
                    $('.next-submit').addClass('disabled');
                    $('.create-submit').removeClass('disabled');
                }
                else if(obj.val() != '' && obj.parent().children('.input-group-append').children('.clear-btn').length == 0) {
                    obj.parent().children('.input-group-append').html('');
                    var btn = $("<button type='button' class='btn clear-btn'><i class='fas fa-times'></i></button>");
                    obj.parent().children('.input-group-append').append(btn);
                    AddFindClearListener();
                    
                    // ... прибавление 16.02.2022 ...
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
                // Открываем форму чтения штрих коду по нажатию кнопки с камерой
                AddFindCameraListener();
                
                // Очищаем поле по нажатию крестика
                AddFindClearListener();
                
                // При показе формы посылаем сигнал "Сканируй"
                $('#codeReaderWrapper').on('shown.bs.modal', function() {
                    document.dispatchEvent(new Event('scan'));
                });
                
                // При скрытии формы делаем видимыми песочные часы (чтобы при следующем открытии они были видны)
                $('#codeReaderWrapper').on('hidden.bs.modal', function() {
                    $('#waiting2').removeClass('d-none');
                });
                
                // При вводе текста, отображаем крестик "стереть". Если поле пустое, отображаем кнопку с камерой.
                $('input#source_id').keyup(function(e) {
                    SetFindClearVisibility($(e.target));
                });
    
                $('input#source_id').keypress(function(e) {
                    SetFindClearVisibility($(e.target));
                });
    
                $('input#source_id').change(function(e) {
                    SetFindClearVisibility($(e.target));
                });
                
                AdjustButtons();
            });
            
            $(window).on('resize', AdjustButtons);
            
            $(document).on("play", function() {
                // При появлении картинки делаем невидимыми песочные часы
                $('#waiting2').addClass('d-none');
        
                // При закрытии формы посылаем сигнал "Останови поток видео"
                $('#close_video').click(function() {
                    document.dispatchEvent(new Event('stop'));
                });
            });
            
            $(document).on("decode", function(e) {
                if(e.detail.type == 'ZBAR_QRCODE') {
                    substrings = e.detail.value.split("?id=");
                    
                    if(substrings.length != 2 && isNaN(substrings[1])) {
                        $('input#' + source_input_id).val("Неправильный код");
                        $('input#' + source_input_id).change();
                        $('#close_video').click();
                    }
                    else if(e.detail.value.includes('pallet/pallet.php?id=')) {
                        $('input#' + source_input_id).val("П" + substrings[1]);
                        $('input#' + source_input_id).change();
                        $('#close_video').click();
                    }
                    else if(e.detail.value.includes('roll/roll.php?id=')) {
                        $('input#' + source_input_id).val("Р" + substrings[1]);
                        $('input#' + source_input_id).change();
                        $('#close_video').click();
                    }
                    else if(e.detail.value.includes('pallet/roll.php?id=')) {
                        $.ajax({ url: "../ajax/roll_id_to_number.php?id=" + substrings[1] })
                                .done(function(data) {
                                    $('input#' + source_input_id).val(data);
                                    $('input#' + source_input_id).change();
                                    $('#close_video').click();
                                })
                                .fail(function() {
                                    $('input#' + source_input_id).val("Ошибка");
                                    $('input#' + source_input_id).change();
                                    $('#close_video').click();
                                });
                    }
                    else {
                        $('input#' + source_input_id).val("Неправильный код");
                        $('input#' + source_input_id).change();
                        $('#close_video').click();
                    }
                }
                else {
                    $('input#' + source_input_id).val(e.detail.value);
                    $('input#' + source_input_id).change();
                    $('#close_video').click();
                }
            });
        </script>
        <script>!function(e){function r(r){for(var n,l,f=r[0],i=r[1],a=r[2],p=0,s=[];p<f.length;p++)l=f[p],Object.prototype.hasOwnProperty.call(o,l)&&o[l]&&s.push(o[l][0]),o[l]=0;for(n in i)Object.prototype.hasOwnProperty.call(i,n)&&(e[n]=i[n]);for(c&&c(r);s.length;)s.shift()();return u.push.apply(u,a||[]),t()}function t(){for(var e,r=0;r<u.length;r++){for(var t=u[r],n=!0,f=1;f<t.length;f++){var i=t[f];0!==o[i]&&(n=!1)}n&&(u.splice(r--,1),e=l(l.s=t[0]))}return e}var n={},o={1:0},u=[];function l(r){if(n[r])return n[r].exports;var t=n[r]={i:r,l:!1,exports:{}};return e[r].call(t.exports,t,t.exports,l),t.l=!0,t.exports}l.m=e,l.c=n,l.d=function(e,r,t){l.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,r){if(1&r&&(e=l(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(l.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var n in e)l.d(t,n,function(r){return e[r]}.bind(null,n));return t},l.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(r,"a",r),r},l.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},l.p="<?=APPLICATION ?>/zbar/";var f=this.webpackJsonpsrc=this.webpackJsonpsrc||[],i=f.push.bind(f);f.push=r,f=f.slice();for(var a=0;a<f.length;a++)r(f[a]);var c=i;t()}([])</script>
        <script src="<?=APPLICATION ?>/zbar/js/2.8358c4d7.chunk.js"></script>
        <script src="<?=APPLICATION ?>/zbar/js/main.73d75875.chunk.js"></script>
    </body>
</html>