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

$cutting_id = null;
$no_streams_source = null;

include '_check_rolls.php';
$opened_roll = CheckOpenedRolls($user_id);

$cutting_id = $opened_roll['id'];
$no_streams_source = $opened_roll['no_streams_source'];

if(empty($cutting_id)) {
    header("Location: ".APPLICATION.'/cutter/');
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
    
    if($form_valid) {
        // Распознавание исходного ролика
        $source_id = trim($source_id);
        $is_from_pallet = null;
        $id = null;
    
        // Если первый символ р или Р, ищем среди рулонов
        if(mb_substr($source_id, 0, 1) == "р" || mb_substr($source_id, 0, 1) == "Р") {
            $roll_id = mb_substr($source_id, 1);
            $sql = "select r.id "
                    . "from roll r "
                    . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                    . "where r.id='$roll_id' and (rsh.status_id is null or rsh.status_id = ".FREE_ROLL_STATUS_ID.") limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $is_from_pallet = 0;
                $id = $row['id'];
            }
        }
        // Если первый символ п или П
        elseif(mb_substr($source_id, 0, 1) == "п" || mb_substr ($source_id, 0, 1) == "П") {
            $pallet_trim = mb_substr($source_id, 1);
            $substrings = mb_split("\D", $pallet_trim);
        
            // Если внутри имеется буква, ищем среди рулонов, которые в паллетах
            if(count($substrings) == 2 && mb_strlen($substrings[0]) > 0 && mb_strlen($substrings[1]) > 0) {
                $pallet_id = $substrings[0];
                $ordinal = $substrings[1];
                $sql = "select pr.id "
                        . "from pallet_roll pr "
                        . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                        . "where pr.pallet_id=$pallet_id and pr.ordinal=$ordinal "
                        . "and (prsh.status_id is null or prsh.status_id = ".FREE_ROLL_STATUS_ID.")";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    $is_from_pallet = 1;
                    $id = $row['id'];
                }
            }
        }
        // Если букв нет, ищем среди ID от поставщика
        else {
            $sql = "select r.id, 0 is_from_pallet "
                    . "from roll r "
                    . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                    . "where r.id_from_supplier='$source_id' and (rsh.status_id is null or rsh.status_id = ".FREE_ROLL_STATUS_ID.") "
                    . "union "
                    . "select pr.id, 1 is_from_pallet "
                    . "from pallet_roll pr "
                    . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                    . "where pr.id_from_supplier='$source_id' and (prsh.status_id is null or prsh.status_id = ".FREE_ROLL_STATUS_ID.")";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $is_from_pallet = $row['is_from_pallet'];
                $id = $row['id'];
            }
        }
        
        if(!empty($id) && $is_from_pallet !== null) {
            if(empty($no_streams_source)) {
                $sql = "insert into cutting_source (cutting_id, is_from_pallet, roll_id) values($cutting_id, $is_from_pallet, $id)";
                $executer = new Executer($sql);
                $error_message = $executer->error;
                $insert_id = $executer->insert_id;
            }
            else {
                $sql = "update cutting_source set is_from_pallet = $is_from_pallet, roll_id = $id where id = $no_streams_source";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
            
            if(empty($error_message)) {
                header("Location: streams.php");
            }
        }
        else {
            $source_id_valid_message = "Объект отсутствует в базе";
            $source_id_valid = ISINVALID;
            $form_valid = false;
        }
    }
}

// Получение объекта
$source_id = filter_input(INPUT_POST, 'source_id');

if(empty($source_id) && !empty($no_streams_source)) {
    $sql = "select cs.is_from_pallet, r.id roll_id, pr.id pallet_roll_id, pr.pallet_id, pr.ordinal "
            . "from cutting_source cs "
            . "left join roll r on r.id = cs.roll_id "
            . "left join pallet_roll pr on pr.id = cs.roll_id "
            . "where cs.id = $no_streams_source";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        if($row['is_from_pallet'] == 1) {
            $source_id = "П".$row['pallet_id'].'Р'.$row['ordinal'];
        }
        else {
            $source_id = 'Р'.$row['roll_id'];
        }
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
                        <?php if(empty($no_streams_source)): ?>
                        <a class="nav-link" href="<?=APPLICATION."/cutter/material.php" ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        <?php endif; ?>
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
            <div id="codeReaderWrapper" class="modal fade show">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            ID наш или от поставщика
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
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Исходный рулон</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <input type="hidden" name="cutting_id" value="<?=$cutting_id ?>" />
                        <div class="form-group">
                            <label for="source_id">ID рулона</label>
                            <div class="input-group find-group">
                                <input type="text" id="source_id" name="source_id" value="<?= $source_id ?>" class="form-control<?=$source_id_valid ?>" required="required" autocomplete="off" />
                                <div class="invalid-feedback order-last"><?=$source_id_valid_message ?></div>
                                <div class='input-group-append'>
                                    <?php if(empty($source_id)): ?>
                                    <button type='button' class='btn find-btn'><i class='fas fa-camera'></i></button>
                                    <?php else: ?>
                                    <button type="button" class="btn clear-btn"><i class="fas fa-times"></i></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-none d-lg-block">
                            <button type="submit" id="next-submit" name="next-submit" class="btn btn-dark form-control mt-4">Далее</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="d-block d-lg-none w-100 pl-4 pr-4 pb-4" style="position: absolute; bottom: 0; left: 0;">
                <button type="button" class="btn btn-dark form-control" onclick="javascript: $('#next-submit').click();">Далее</button>
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
            
            function SetFindClearVisibility(obj) {
                obj.removeClass('is-invalid');
                
                if(obj.val() == '' && obj.parent().children('.input-group-append').children('.find-btn').length == 0) {
                    obj.parent().children('.input-group-append').html('');
                    var btn = $("<button type='button' class='btn find-btn'><i class='fas fa-camera'></i></button>");
                    obj.parent().children('.input-group-append').append(btn);
                    AddFindCameraListener();
                }
                else if(obj.val() != '' && obj.parent().children('.input-group-append').children('.clear-btn').length == 0) {
                    obj.parent().children('.input-group-append').html('');
                    var btn = $("<button type='button' class='btn clear-btn'><i class='fas fa-times'></i></button>");
                    obj.parent().children('.input-group-append').append(btn);
                    AddFindClearListener();
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