<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

include '_check_rolls.php';
$opened_roll = CheckOpenedRolls($user_id);
$cutting_id = $opened_roll['id'];
$last_source = $opened_roll['last_source'];
$streams_count = $opened_roll['streams_count'];

// Если нет незакрытой нарезки, переходим на первую страницу
if(empty($cutting_id)) {
    header("Location: ",APPLICATION.'/cutter/');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$streams_count_valid = '';

for($i=1; $i<=19; $i++) {
    $stream_message = 'stream_'.$i.'_valid';
    $$stream_message = 'Ширина ручья обязательно';
    $stream_valid_name = 'stream_'.$i.'_valid';
    $$stream_valid_name = '';
    
    $comment_message = 'comment_'.$i.'_valid';
    $$comment_message = 'Комментарий к ручью обязательно';
    $comment_valid_name = 'comment_'.$i.'_valid';
    $$comment_valid_name = '';
}

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $streams_count = filter_input(INPUT_POST, 'streams_count');
    if(empty($streams_count) || intval($streams_count) > 19) {
        $streams_count_valid = ISINVALID;
        $form_valid = false;
    }
    
    for($i=1; $i<=$streams_count; $i++) {
        if(empty(filter_input(INPUT_POST, 'stream_'.$i))) {
            $stream_valid_name = 'stream_'.$i.'_valid';
            $$stream_valid_name = ISINVALID;
            $stream_message = 'stream_'.$i.'_message';
            $$stream_message = "Ручей $i обязательно, больше нуля";
            $form_valid = false;
        }
        
        if(empty(filter_input(INPUT_POST, 'comment_'.$i))) {
            $comment_valid_name = 'comment_'.$i.'_valid';
            $$comment_valid_name = ISINVALID;
            $comment_message = 'comment_'.$i.'_message';
            $$comment_message = "Комментарий к ручью $i обязательно";
            $form_valid = false;
        }
    }
    
    $cutting_id = filter_input(INPUT_POST, 'cutting_id');
    $width = 0;
    
    $sql = "select width from cutting where id=$cutting_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $width = $row['width'];
    }
    
    $streams_widths_sum = 0;
    
    for($i=1; $i<=19; $i++) {
        if(null !== filter_input(INPUT_POST, 'stream_'.$i)) {
            $stream = 'stream_'.$i;
            $$stream = filter_input(INPUT_POST, $stream);
            $comment = 'comment_'.$i;
            $$comment = filter_input(INPUT_POST, $comment);
            if(empty($$stream)) {
                $stream_valid = 'stream_'.$i.'_valid';
            }
            else {
                $streams_widths_sum += intval($$stream);
            }
        }
    }
    
    // Сумма ширин ручьёв должна быть равна ширине исходной плёнки
    if($streams_widths_sum != $width) {
        for($i=1; $i<19; $i++) {
            if(null !== filter_input(INPUT_POST, 'stream_'.$i)) {
                $stream_valid_name = 'stream_'.$i.'_valid';
                $$stream_valid_name = ISINVALID;
                $stream_message = 'stream_'.$i.'_message';
                $$stream_message = 'Сумма не равна общей ширине';
                $form_valid = false;
            }
        }
    }
    
    if($form_valid) {
        $sql = "delete from cutting_stream where cutting_id = $cutting_id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    
        if(empty($error_message)) {
            for($i=1; $i<=$streams_count; $i++) {
                if(!empty(filter_input(INPUT_POST, 'stream_'.$i)) && empty($error_message)) {
                    $width = filter_input(INPUT_POST, 'stream_'.$i);
                    $comment = addslashes(filter_input(INPUT_POST, 'comment_'.$i));
                    $sql = "insert into cutting_stream (cutting_id, width, comment) values ($cutting_id, $width, '$comment')";
                    $executer = new Executer($sql);
                    $error_message = $executer->error;
                    $insert_id = $executer->insert_id;
                }
            }
        }
        
        if(empty($error_message)) {
            header("Location: source.php");
        }
    }
}

// Получение объекта
$sql = "select width, comment from cutting_stream where cutting_id = $cutting_id";
$fetcher = new Fetcher($sql);
$i = 0;
while($row = $fetcher->Fetch()) {
    $stream_name = "stream_".(++$i);
    $$stream_name = $row['width'];
    $comment_name = "comment_$i";
    $$comment_name = $row['comment'];
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
                        <a class="nav-link" href="material.php"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            <h1>Нарезка / <?=date('d.m.Y') ?></h1>
            <p class="mb-3 mt-3" style="font-size: large;">Как режем?</p>
            <form method="post">
                <input type="hidden" id="cutting_id" name="cutting_id" value="<?=$cutting_id ?>" />
                <div class="form-group">
                    <label for="streams_count">Кол-во ручьев</label>
                    <input type="text" id="streams_count" name="streams_count" class="form-control int-only w-50<?=$streams_count_valid ?>" data-max="19" value="<?= empty($streams_count) ? '' : $streams_count ?>" required="required" autocomplete="off" />
                    <div class="invalid-feedback">Число, макс. 19</div>
                </div>
                    <?php
                    for($i=1; $i<=19; $i++):
                    $stream_name = "stream_$i";
                    $stream_valid_name = 'stream_'.$i.'_valid';
                    $stream_group_display_class = ' d-none';
                    $stream_message = 'stream_'.$i.'_message';
                    
                    $comment_name = "comment_$i";
                    $comment_valid_name = 'comment_'.$i.'_valid';
                    $comment_group_display_class = ' d-none';
                    $comment_message = 'comment_'.$i.'_message';
                
                    if(null !== $streams_count && intval($streams_count) >= intval($i)) {
                        $stream_group_display_class = '';
                        $comment_group_display_class = '';
                    }
                    ?>
                <div class="form-group stream_group<?=$stream_group_display_class ?>" id="stream_<?=$i ?>_group">
                    <label for="stream_<?=$i ?>">Ручей <?=$i ?></label>
                    <div class="input-group">
                        <input type="text" id="stream_<?=$i ?>" name="stream_<?=$i ?>" class="form-control int-only<?=$$stream_valid_name ?>" value="<?= isset($$stream_name) ? $$stream_name : '' ?>" placeholder="Ширина" autocomplete="off" />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback"><?=$$stream_message ?></div>                        
                    </div>
                </div>
                <div class="form-group comment_group<?=$comment_group_display_class ?>" id="comment_<?=$i ?>_group">
                    <input type="text" id="comment_<?=$i ?>" name="comment_<?=$i ?>" class="form-control<?=$$comment_valid_name ?>" value="<?= isset($$comment_name) ? urldecode(htmlentities($$comment_name)) : '' ?>" placeholder="Комментарий" autocomplete="off" />
                    <div class="invalid-feedback"><?=$$comment_message ?></div>
                </div>
                    <?php endfor; ?>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark form-control mt-4" id="next-submit" name="next-submit">Приступить к раскрою</button>
                </div>
            </form>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Показ или скрытие ручьёв в зависимости от введённого количества ручьёв
            $('#streams_count').keyup(function() {
                SetStreams($(this).val());
            });
            
            // Показ и заполнение каждого ручья
            function SetStreams(streams_count) {
                $('.stream_group').addClass('d-none');
                $('.stream_group .input-group input').removeAttr('required');
                $('.comment_group').addClass('d-none');
                $('.comment_group input').removeAttr('required');
                
                if(streams_count != '') {
                    iStreamsCount = parseInt(streams_count);
                    iMaxCount = parseInt($('#streams_count').attr('data-max'));
                    
                    if(!isNaN(iMaxCount) && iStreamsCount > iMaxCount) {
                        iStreamsCount = iMaxCount;
                    }
                    
                    if(!isNaN(iStreamsCount)) {
                        for(i=1; i<=iStreamsCount; i++) {
                            $('#stream_' + i + '_group').removeClass('d-none');
                            $('#comment_' + i + '_group').removeClass('d-none');
                            $('#stream_' + i + '_group .input-group input').attr('required', 'required');
                            $('#comment_' + i + '_group input').attr('required', 'required');
                        }
                    }
                }
            }
            
            SetStreams($('#streams_count').val());
        </script>
    </body>
</html>