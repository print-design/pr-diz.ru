<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

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
}

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    header("Location: wind.php");
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
                        <a class="nav-link" href="<?=APPLICATION."/cutter/source.php" ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Нарезка / <?=date('d.m.Y') ?></h1>
            <p class="mb-3 mt-3" style="font-size: large;">Как режем?</p>
            <form method="post">
                <div class="form-group">
                    <label for="streams_count">Кол-во ручьев</label>
                    <input type="text" id="streams_count" name="streams_count" class="form-control int-only w-50<?=$streams_count_valid ?>" data-max="19" value="<?= isset($_REQUEST['streams_count']) ? $_REQUEST['streams_count'] : '' ?>" required="required" autocomplete="off" />
                    <div class="invalid-feedback">Число, макс. 19</div>
                </div>
                    <?php
                    for($i=1; $i<=19; $i++):
                    $stream_valid_name = 'stream_'.$i.'_valid';
                    $stream_group_display_class = ' d-none';
                    $stream_message = 'stream_'.$i.'_message';
                
                    $streams_count = isset($_REQUEST['streams_count']) ? $_REQUEST['streams_count'] : null;
                
                    if(null !== $streams_count && intval($streams_count) >= intval($i)) {
                        $stream_group_display_class = '';
                    }
                    ?>
                <div class="form-group stream_group<?=$stream_group_display_class ?>" id="stream_<?=$i ?>_group">
                    <label for="stream_<?=$i ?>">Ручей <?=$i ?></label>
                    <div class="input-group">
                        <input type="text" id="stream_<?=$i ?>" name="stream_<?=$i ?>" class="form-control int-only<?=$$stream_valid_name ?>" value="<?= isset($_REQUEST['stream_'.$i]) ? $_REQUEST['stream_'.$i] : '' ?>" placeholder="Ширина" autocomplete="off" />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback invalid-stream"><?=$$stream_message ?></div>                        
                    </div>
                </div>
                <div class="form-group stream_group<?=$stream_group_display_class ?>" id="comment_<?=$i ?>_group">
                    <input type="text" id="comment_<?=$i ?>" name="comment_<?=$i ?>" class="form-control" value="<?= isset($_REQUEST['comment_'.$i]) ? $_REQUEST['comment_'.$i] : '' ?>" placeholder="Комментарий" autocomplete="off" />
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
                        }
                    }
                }
            }
        </script>
    </body>
</html>