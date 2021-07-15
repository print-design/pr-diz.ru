<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение supplier_id, film_brand_id, thickness, width, перенаправляем на Главную
$supplier_id = $_REQUEST['supplier_id'];
$film_brand_id = $_REQUEST['film_brand_id'];
$thickness = $_REQUEST['thickness'];
$width = $_REQUEST['width'];
if(empty($supplier_id) || empty($film_brand_id) || empty($thickness) || empty($width)) {
    header('Location: '.APPLICATION.'/cutter/');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$streams_count_valid = '';
for($i=1; $i<=19; $i++) {
    $stream_valid = 'stream_'.$i.'_valid';
    $$stream_valid = '';
    
    $stream_message = 'stream_'.$i.'_message';
    $$stream_message = 'Ширина ручья обязательно';
}

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $streams_count = filter_input(INPUT_POST, 'streams_count');
    if(empty($streams_count)) {
        $streams_count_valid = ISINVALID;
        $form_valid = false;
    }
    
    $temp_width = 0;
    
    for($i=1; $i<=$streams_count; $i++) {
        $stream = filter_input(INPUT_POST, 'stream_'.$i);
        if(empty($stream)) {
            $stream_valid = 'stream_'.$i.'_valid';
            $$stream_valid = ISINVALID;
            $stream_message = 'stream_'.$i.'_message';
            $$stream_message = 'Ширина ручья обязательно';
            $form_valid = false;
        }
        else {
            $temp_width += intval($stream);
        }
    }
    
    if($form_valid) {
        if($width != $temp_width) {
            for($i=1; $i<=$streams_count; $i++) {
                $stream_valid = 'stream_'.$i.'_valid';
                $$stream_valid = ISINVALID;
                $stream_message = 'stream_'.$i.'_message';
                $$stream_message = 'Сумма не равна общей ширине';
                $form_valid = false;
            }
        }
    }
    
    if($form_valid) {
        $streams = '';
        
        for($i=1; $i<=$streams_count; $i++) {
            $streams .= '&stream_'.$i.'='. filter_input(INPUT_POST, 'stream_'.$i);
        }
        
        $link = APPLICATION.'/cutter/wind.php?supplier_id='.$supplier_id.'&film_brand_id='.$film_brand_id.'&thickness='.$thickness.'&width='.$width.'&streams_count='.$streams_count.$streams;
        header('Location: '.$link);
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '../include/style_mobile.php';
        ?>
    </head>
    <body>
        <form method="post" action="material.php" id="back_form">
            <?php foreach ($_REQUEST as $key=>$value): ?>
            <input type="hidden" name="<?=$key ?>" value="<?=$value ?>" />
            <?php endforeach; ?>
            <div class="container-fluid header">
                <nav class="navbar navbar-expand-sm justify-content-start">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="javascript: $('form#back_form').submit();"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </form>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1>Нарезка / <?=date('d.m.Y') ?></h1>
            <p class="mb-3 mt-3" style="font-size: large;">Как режем?</p>
            <form method="post">
                <?php foreach ($_REQUEST as $key=>$value): ?>
                <input type="hidden" name="<?=$key ?>" value="<?=$value ?>" />
                <?php endforeach; ?>
                <div class="form-group">
                    <label for="streams_count">Кол-во ручьев</label>
                    <input type="text" id="streams_count" name="streams_count" class="form-control w-50<?=$streams_count_valid ?>" value="<?= filter_input(INPUT_POST, 'streams_count') ?>" required="required" />
                    <div class="invalid-feedback">Число, макс. 19</div>
                </div>
                <?php
                for($i=1; $i<=19; $i++):
                $stream_valid_name = 'stream_'.$i.'_valid';
                $stream_group_display_class = ' d-none';
                $stream_message = 'stream_'.$i.'_message';
                
                $streams_count = filter_input(INPUT_POST, 'streams_count');
                
                if(null !== $streams_count && intval($streams_count) >= intval($i)) {
                    $stream_group_display_class = '';
                }
                ?>
                <div class="form-group stream_group<?=$stream_group_display_class ?>" id="stream_<?=$i ?>_group">
                    <label for="stream_<?=$i ?>">Ручей <?=$i ?></label>
                    <div class="input-group w-75">
                        <input type="text" id="stream_<?=$i ?>" name="stream_<?=$i ?>" class="form-control int-only<?=$$stream_valid_name ?>" value="<?= filter_input(INPUT_POST, 'stream_'.$i) ?>" />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback"><?=$$stream_message ?></div>
                    </div>
                </div>
                <?php endfor; ?>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark form-control mt-4" id="next-submit" name="next-submit">Приступить к раскрою</button>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
        <script>
            // В поле "Кол-во ручьев" ограничиваем значения: целые числа от 1 до 19
            $('#streams_count').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 19)) {
                    $(this).addClass('is-invalid');
                    
                    return false;
                }
                else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            $('#streams_count').keyup(function() {
                SetStreams($(this).val());
            });
    
            $("#streams_count").change(function(){
                if($(this).val() > 19) {
                    $(this).addClass('is-invalid');
                }
                else {
                    $(this).removeClass('is-invalid');
                }
                
                ChangeLimitIntValue($(this), 19);
                SetStreams($(this).val());
            });
            
            // Показ и заполнение каждого ручья
            function SetStreams(streams_count) {
                $('.stream_group').addClass('d-none');
                $('.stream_group .input-group input').removeAttr('required');
                
                if(streams_count != '') {
                    iStreamsCount = parseInt(streams_count);
                    if(!isNaN(iStreamsCount)) {
                        for(i=1; i<=iStreamsCount; i++) {
                            $('#stream_' + i + '_group').removeClass('d-none');
                            $('#stream_' + i + '_group .input-group input').attr('required', 'required');
                        }
                    }
                }
            }
        </script>
    </body>
</html>