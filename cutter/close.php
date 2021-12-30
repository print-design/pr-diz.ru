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

// Статус "СВОБОДНЫЙ"
$free_status_id = 1;

// Статус "РАСКРОИЛИ"
$cut_status_id = 3;

// Список исходных роликов
$cut_sources = array();

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$sources_count_valid = '';

for($i=1; $i<=19; $i++) {
    $source_valid = 'source_'.$i.'_valid';
    $$source_valid = '';
    
    $source_message = 'source_'.$i.'_message';
    $$source_message = 'Номер ролика обязательно';
}

if(null !== filter_input(INPUT_POST, 'close-submit')) {
    $sources_count = filter_input(INPUT_POST, 'sources_count');
    if(empty($sources_count) || intval($sources_count) > 19) {
        $sources_count_valid = ISINVALID;
        $form_valid = false;
    }
    
    for($i=1; $i<=19; $i++) {
        $source = 'source_'.$i;
        $$source = filter_input(INPUT_POST, $source);
        if($i <= $sources_count && empty($$source)) {
            $source_valid = 'source_'.$i.'_valid';
            $$source_valid = ISINVALID;
            
            $source_message = 'source_'.$i.'_message';
            $$source_message = "Номер ролика обязательно";
            
            $form_valid = false;
        }
    }
    
    if($form_valid) {
        // Получаем параметры раскроя
        $cut_id = filter_input(INPUT_POST, 'cut_id');
        
        $sql = " select fb.name film_brand, c.thickness, c.width "
                . "from cut c inner join film_brand fb on c.film_brand_id = fb.id "
                . "where c.id=$cut_id";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        $row = $fetcher->Fetch();
        $film_brand = $row['film_brand'];
        $thickness = $row['thickness'];
        $width = $row['width'];
        
        if(empty($error_message)) {
            for($i=1; $i<=19; $i++) {
                $source = filter_input(INPUT_POST, 'source_'.$i);
    
                if(!empty($source)) {
                    $message = "";
        
                    // Проверяем, чтобы номер рулона соответствовал реальному рулону и имел такие же параметры
                    if(mb_substr($source, 0, 1) == "р" || mb_substr($source, 0, 1) == "Р") {
                        // Ищем такой среди свободных роликов
                        $roll_id = mb_substr($source, 1);
            
                        $sql = "select fb.name film_brand, r.thickness, r.width, r.length, rsh.status_id "
                                . "from roll r "
                                . "inner join film_brand fb on r.film_brand_id = fb.id "
                                . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                                . "where r.id = $roll_id"; // проверку статусов временно отключаем  // and (rsh.status_id is null or rsh.status_id = $free_status_id)";
                        $fetcher = new Fetcher($sql);
                        $error_message = $fetcher->error;
            
                        if($row = $fetcher->Fetch()) {
                            if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness && $row['width'] == $width) {
                            // Валидацию по ширине временно отключаем
                            // if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness) {
                                $cut_source = array();
                                $cut_source['cut_id'] = $cut_id;
                                $cut_source['is_from_pallet'] = 0;
                                $cut_source['roll_id'] = $roll_id;
                                $cut_source['length'] = $row['length'];
                                $cut_source['status_id'] = $row['status_id'];
                                array_push($cut_sources, $cut_source);
                            }
                            else {
                                $message = "Марка/толщина/ширина не совпадают";
                                // Валидацию по ширине временно отключаем
                                // $message = "Марка/толщина не совпадают";
                            }
                        }
                        else {
                            $message = "Нет ролика с таким номером";
                        }
                    }
                    elseif(mb_substr($source, 0, 1) == "п" || mb_substr ($source, 0, 1) == "П") {
                        // Ищем среди роликов в паллетах
                        $pallet_trim = mb_substr($source, 1);
                        $substrings = mb_split("\D", $pallet_trim);
            
                        if(count($substrings) == 2 && !empty($substrings[0]) && !empty($substrings[1])) {
                            $pallet_id = $substrings[0];
                            $ordinal = $substrings[1];
                
                            $sql = "select pr.id roll_id, fb.name film_brand, p.thickness, p.width, pr.length, prsh.status_id "
                                    . "from pallet p "
                                    . "inner join pallet_roll pr on pr.pallet_id = p.id "
                                    . "inner join film_brand fb on p.film_brand_id = fb.id "
                                    . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                                    . "where p.id = $pallet_id and pr.ordinal = $ordinal"; // проверку статусов временно отключаем // and (prsh.status_id is null or prsh.status_id = $free_status_id)";
                            $fetcher = new Fetcher($sql);
                            $error_message = $fetcher->error;
                
                            if($row = $fetcher->Fetch()) {
                                if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness && $row['width'] == $width) {
                                    // Валидацию по ширине временно отключаем
                                    // if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness) {
                                    $cut_source = array();
                                    $cut_source['cut_id'] = $cut_id;
                                    $cut_source['is_from_pallet'] = 1;
                                    $cut_source['roll_id'] = $row['roll_id'];
                                    $cut_source['length'] = $row['length'];
                                    $cut_source['status_id'] = $row['status_id'];
                                    array_push($cut_sources, $cut_source);
                                }
                                else {
                                    $message = "Марка/толщина/ширина не совпадают";
                                    // Валидацию по ширине временно отключаем
                                    // $message = "Марка/толщина не совпадают";
                                }
                            }
                            else {
                                $message = "Нет ролика с таким номером";
                            }
                        }
                        else {
                            $message = "Нет ролика с таким номером";
                        }
                    }
                    else {
                        // Если нет букв ни "П", ни "Р", то ищем по ID от поставщика
                        $sql = "select 0 is_from_pallet, r.id roll_id, fb.name film_brand, r.thickness, r.width, r.length, rsh.status_id "
                                . "from roll r "
                                . "inner join film_brand fb on r.film_brand_id = fb.id "
                                . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                                . "where r.id_from_supplier = '$source' " // проверку статусов временно отключаем // and (rsh.status_id is null or rsh.status_id = $free_status_id) "
                                . "union "
                                . "select 1 is_from_pallet, pr.id roll_id, fb.name film_brand, p.thickness, p.width, pr.length, prsh.status_id "
                                . "from pallet p "
                                . "inner join pallet_roll pr on pr.pallet_id = p.id "
                                . "inner join film_brand fb on p.film_brand_id = fb.id "
                                . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                                . "where pr.id_from_supplier = '$source'"; // проверку статусов временно отключаем // and (prsh.status_id is null or prsh.status_id = $free_status_id)";
                        $fetcher = new Fetcher($sql);
                        $error_message = $fetcher->error;
                        
                        if($row = $fetcher->Fetch()) {
                            if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness && $row['width'] == $width) {
                                // Валидацию по ширине временно отключаем
                                // if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness) {
                                $cut_source = array();
                                $cut_source['cut_id'] = $cut_id;
                                $cut_source['is_from_pallet'] = $row['is_from_pallet'];
                                $cut_source['roll_id'] = $row['roll_id'];
                                $cut_source['length'] = $row['length'];
                                $cut_source['status_id'] = $row['status_id'];
                                array_push($cut_sources, $cut_source);
                            }
                            else {
                                $message = "Марка/толщина/ширина не совпадают";
                                // Валидацию по ширине временно отключаем
                                // $message = "Марка/толщина не совпадают";
                            }
                        }
                        else {
                            $message = "Нет ролика с таким номером";
                        }
                    }
        
                    if(!empty($message)) {
                        $source_message = 'source_'.$i.'_message';
                        $$source_message = $message;
                        
                        $source_valid = 'source_'.$i.'_valid';
                        $$source_valid = ISINVALID;
                        
                        $form_valid = false;
                    }
                }
            }
        }
    }
    
    // Проверка сумм длин исходных роликов и намоток
    // (Временно отключаем)
    /*if($form_valid) {
        // Общая длина исходных роллей
        $source_sum = 0;
    
        foreach ($cut_sources as $cut_source) {
            $source_sum += $cut_source['length'];
        }
    
        // Общая длина намоток
        $wind_sum = 0;
        $sql = "select sum(length) sum from cut_wind where cut_id = $cut_id";
        $fetcher = new Fetcher($sql);
    
        if($row = $fetcher->Fetch()) {
            $wind_sum = $row['sum'];
        }
    
        if($wind_sum > $source_sum) {
            for($i=1; $i<=19; $i++) {
                $source_message = 'source_'.$i.'_message';
                $$source_message = "Сумма длин намоток больше суммы длин исходных роликов";
                
                $source_valid = 'source_'.$i.'_valid';
                $$source_valid = ISINVALID;
                
                $form_valid = false;
            }
        }
    }*/
    
    // Проверяем, чтобы не ввели два одинаковых ролика
    if($form_valid) {
        $existing_rolls = array();
    
        for($i=1; $i<=19; $i++) {
            $source = filter_input(INPUT_POST, 'source_'.$i);
        
            if(!empty($source)) {
                if(in_array($source, $existing_rolls)) {
                    $source_message = 'source_'.$i.'_message';
                    $$source_message = "Ролик введён два раза";
                    
                    $source_valid = 'source_'.$i.'_valid';
                    $$source_valid = ISINVALID;
                    
                    $form_valid = false;
                }
            
                array_push($existing_rolls, $source);
            }
        }
    }
    
    // Если у исходных роликов были неправильные статусы, записываем это в таблицу error
    if($form_valid) {
        foreach ($cut_sources as $cut_source) {
            if($cut_source['status_id'] != $free_status_id) {
                $state_id = $cut_source['status_id'];
                $roll_id = $cut_source['roll_id'];
                $is_from_pallet = $cut_source['is_from_pallet'] == 1 ? "из паллета" : "НЕ из паллета";
                $message = addslashes("Неправильный статус исходного ролика: статус $state_id, ID $roll_id, $is_from_pallet");
                $sql = "insert into error (user_id, message) values ($user_id, '$message')";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
    }
    
    // Устанавливаем исходные ролики для этой нарезки, меняем статусы исходных роликов и переходим к странице остаточного ролика
    if($form_valid) {
        foreach ($cut_sources as $cut_source) {
            $cut_id = $cut_source['cut_id'];
            $is_from_pallet = $cut_source['is_from_pallet'];
            $roll_id = $cut_source['roll_id'];
            $sql = "insert into cut_source (cut_id, is_from_pallet, roll_id) values ($cut_id, $is_from_pallet, $roll_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            
            if(empty($error_message)) {
                if($is_from_pallet == 0) {
                    $sql = "insert into roll_status_history (roll_id, status_id, user_id) values($roll_id, $cut_status_id, $user_id)";
                    $executer = new Executer($sql);
                    $error_message = $executer->error;
                }
                else {
                    $sql = "insert into pallet_roll_status_history (pallet_roll_id, status_id, user_id) values($roll_id, $cut_status_id, $user_id)";
                    $executer = new Executer($sql);
                    $error_message = $executer->error;
                }         
            }
        }
        
        if(empty($error_message)) {
            header("Location: remain.php");
        }
    }
}

// Получение объекта
$cut_id = null;
$date = '';
$sql = "select id, DATE_FORMAT(c.date, '%d.%m.%Y') date from cut c where cutter_id = $user_id and id not in (select cut_id from cut_source)";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $cut_id = $row['id'];
    $date = $row['date'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include '_head.php';
        include '_info.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-between">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="next.php"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" href="javascript: void(0);" data-toggle="modal" data-target="#infoModal"><img src="<?=APPLICATION ?>/images/icons/info.svg" /></a>
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
                            ID от поставщика
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
            <h1>Нарезка <?=$cut_id ?> / <?=$date ?></h1>
            <p class="mb-3 mt-3" style="font-size: large;">Введите ID исходных ролей</p>
            <form method="post" id="sources_form">
                <input type="hidden" id="cut_id" name="cut_id" value="<?=$cut_id ?>" />
                <div class="form-group" id="count-group">
                    <label for="sources_count">Кол-во исходных ролей</label>
                    <input type="text" class="form-control w-50 int-only<?=$sources_count_valid ?>" data-max="19" id="sources_count" name="sources_count" value="<?= filter_input(INPUT_POST, 'sources_count') ?>" required="required" autocomplete="off" />
                    <div class="invalid-feedback">Число, макс. 19</div>
                </div>
                    <?php
                    for($i=1; $i<=19; $i++):
                    $source_d_none = 'source_'.$i.'_d_none';
                    $$source_d_none = " d-none";
                    
                    $source_required = 'source_'.$i.'_required';
                    $$source_required = "";
                    
                    $sources_count = filter_input(INPUT_POST, 'sources_count');
                    
                    if(!empty($sources_count) && $i <= intval($sources_count)) {
                        $$source_d_none = "";
                        $$source_required = " required='required'";
                    }
                    
                    $source_valid = 'source_'.$i.'_valid';
                    $source_message = 'source_'.$i.'_message';
                    ?>
                <div class="form-group source_group<?=$$source_d_none ?>" id="source_<?=$i ?>_group">
                    <label for="source_<?=$i ?>">ID <?=$i ?>-го исходного роля</label>
                    <div class="input-group find-group" id="source_<?=$i ?>_group">
                        <input type="text" class="form-control<?=$$source_valid ?>" id="source_<?=$i ?>" name="source_<?=$i ?>" value="<?= filter_input(INPUT_POST, "source_$i") ?>" autocomplete="off"<?=$$source_required ?> />
                        <div class="invalid-feedback invalid-source order-last" id="invalid-source-<?=$i ?>"><?=$$source_message ?></div>
                        <div class='input-group-append'><button type='button' class='btn find-btn' id='find_camera_<?=$i ?>'><i class='fas fa-camera'></i></button></div>
                    </div>
                </div>
                    <?php endfor; ?>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark form-control mt-4" id="close-submit" name="close-submit">Закрыть заявку</button>
                </div>
            </form>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // В поле "Кол-во исходных ролей" ограничиваем значения: целые числа от 1 до 19
            $('#sources_count').keyup(function() {
                SetSources($(this).val());
            });
            
            // Показ каждого источника
            function SetSources(sources_count) {
                $('.source_group').addClass('d-none');
                $('.source_group input').removeAttr('required');
                
                if(sources_count != '') {
                    iSourcesCount = parseInt(sources_count);
                    if(!isNaN(iSourcesCount)) {
                        for(i=1; i<=iSourcesCount; i++) {
                            $('#source_' + i + '_group').removeClass('d-none');
                            $('#source_' + i + '_group input').attr('required', 'required');
                        }
                    }
                }
            }
            
            source_input_id = '';
     
            $(document).ready(function() {
                // Открываем форму чтения штрих коду по нажатию кнопки с камерой
                $('button.find-btn').click(function() {
                    source_input_id = $(this).parent('.input-group-append').prev().prev('input').attr('id');
                    $('#codeReaderWrapper').modal('show');
                });
                
                // При показе формы посылаем сигнал "Сканируй"
                $('#codeReaderWrapper').on('shown.bs.modal', function() {
                    document.dispatchEvent(new Event('scan'));
                });
                
                // При скрытии формы делаем видимыми песочные часы (чтобы при следующем открытии они были видны)
                $('#codeReaderWrapper').on('hidden.bs.modal', function() {
                    $('#waiting2').removeClass('d-none');
                });
            });
            
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
                        $('input#' + source_input_id).removeClass('is-invalid');
                        $('#close_video').click();
                    }
                    else if(e.detail.value.includes('pallet/pallet.php?id=')) {
                        $('input#' + source_input_id).val("П" + substrings[1]);
                        $('input#' + source_input_id).removeClass('is-invalid');
                        $('#close_video').click();
                    }
                    else if(e.detail.value.includes('roll/roll.php?id=')) {
                        $('input#' + source_input_id).val("Р" + substrings[1]);
                        $('input#' + source_input_id).removeClass('is-invalid');
                        $('#close_video').click();
                    }
                    else if(e.detail.value.includes('pallet/roll.php?id=')) {
                        $.ajax({ url: "../ajax/roll_id_to_number.php?id=" + substrings[1] })
                                .done(function(data) {
                                    $('input#' + source_input_id).val(data);
                                    $('input#' + source_input_id).removeClass('is-invalid');
                                    $('#close_video').click();
                                })
                                .fail(function() {
                                    $('input#' + source_input_id).val("Ошибка");
                                    $('input#' + source_input_id).removeClass('is-invalid');
                                    $('#close_video').click();
                                });
                    }
                    else {
                        $('input#' + source_input_id).val("Неправильный код");
                        $('input#' + source_input_id).removeClass('is-invalid');
                        $('#close_video').click();
                    }
                }
                else {
                    $('input#' + source_input_id).val(e.detail.value);
                    $('input#' + source_input_id).removeClass('is-invalid');
                    $('#close_video').click();
                }
            });
        </script>
        <script>!function(e){function r(r){for(var n,l,f=r[0],i=r[1],a=r[2],p=0,s=[];p<f.length;p++)l=f[p],Object.prototype.hasOwnProperty.call(o,l)&&o[l]&&s.push(o[l][0]),o[l]=0;for(n in i)Object.prototype.hasOwnProperty.call(i,n)&&(e[n]=i[n]);for(c&&c(r);s.length;)s.shift()();return u.push.apply(u,a||[]),t()}function t(){for(var e,r=0;r<u.length;r++){for(var t=u[r],n=!0,f=1;f<t.length;f++){var i=t[f];0!==o[i]&&(n=!1)}n&&(u.splice(r--,1),e=l(l.s=t[0]))}return e}var n={},o={1:0},u=[];function l(r){if(n[r])return n[r].exports;var t=n[r]={i:r,l:!1,exports:{}};return e[r].call(t.exports,t,t.exports,l),t.l=!0,t.exports}l.m=e,l.c=n,l.d=function(e,r,t){l.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,r){if(1&r&&(e=l(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(l.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var n in e)l.d(t,n,function(r){return e[r]}.bind(null,n));return t},l.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(r,"a",r),r},l.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},l.p="<?=APPLICATION ?>/zbar/";var f=this.webpackJsonpsrc=this.webpackJsonpsrc||[],i=f.push.bind(f);f.push=r,f=f.slice();for(var a=0;a<f.length;a++)r(f[a]);var c=i;t()}([])</script>
        <script src="<?=APPLICATION ?>/zbar/js/2.8358c4d7.chunk.js"></script>
        <script src="<?=APPLICATION ?>/zbar/js/main.73d75875.chunk.js"></script>
    </body>
</html>