<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$user_id = GetUserId();
$sql = "update user set request_uri='$request_uri' where id=$user_id";
$error_message = (new Executer($sql))->error;

if(!empty($error_message)) {
    exit($error_message);
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-start">
        <ul class="navbar-nav">
            <li class="nav-item">
                <button type="button" class="nav-link btn btn-link goto_material" data-supplier_id="<?= filter_input(INPUT_GET, 'supplier_id') ?>" data-film_brand_id="<?= filter_input(INPUT_GET, 'film_brand_id') ?>" data-thickness="<?= filter_input(INPUT_GET, 'thickness') ?>" data-width="<?= filter_input(INPUT_GET, 'width') ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</button>
            </li>
        </ul>
    </nav>
</div>
<div id="topmost"></div>
<div class="container-fluid">
    <h1>Нарезка / <?=date('d.m.Y') ?></h1>
    <p class="mb-3 mt-3" style="font-size: large;">Как режем?</p>
    <form method="post">
        <div class="form-group">
            <label for="streams_count">Кол-во ручьев</label>
            <input type="text" id="streams_count" name="streams_count" class="form-control int-only w-50" data-max="19" value="<?= filter_input(INPUT_GET, 'streams-count') ?>" required="required" autocomplete="off" />
            <div class="invalid-feedback">Число, макс. 19</div>
        </div>
            <?php
            for($i=1; $i<=19; $i++):
                $stream_valid_name = 'stream_'.$i.'_valid';
                $stream_group_display_class = ' d-none';
                $stream_message = 'stream_'.$i.'_message';
                
                $streams_count = filter_input(INPUT_GET, 'streams-count');
                
                if(null !== $streams_count && intval($streams_count) >= intval($i)) {
                    $stream_group_display_class = '';
                }
                ?>
                <div class="form-group stream_group<?=$stream_group_display_class ?>" id="stream_<?=$i ?>_group">
                    <label for="stream_<?=$i ?>">Ручей <?=$i ?></label>
                    <div class="input-group w-75">
                        <input type="text" id="stream_<?=$i ?>" name="stream_<?=$i ?>" class="form-control int-only" value="<?= filter_input(INPUT_GET, 'stream_'.$i) ?>" autocomplete="off" />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback invalid-stream"></div>
                    </div>
                </div>
            <?php endfor; ?>
        <div class="form-group">
            <button type="button" class="btn btn-dark form-control mt-4" id="next-submit">Приступить к раскрою</button>
        </div>
    </form>
</div>
<script>
    $('#streams_count').keyup(function() {
        SetStreams($(this).val());
    });
    
    submit = false;
    
    function Submit() {
        form_valid = true;
        
        if($('#streams_count').val() == '') {
            $('#streams_count').addClass('is-invalid');
            $('#streams_count').focus();
            form_valid = false;
        }
        
        if($('#streams_count').val() < 1) {
            $('#streams_count').addClass('is-invalid');
            $('#streams_count').focus();
            form_valid = false;
        }
        
        if($('#streams_count').val() > 19) {
            $('#streams_count').addClass('is-invalid');
            $('#streams_count').focus();
            form_valid = false;
        }
        
        width_source = <?= filter_input(INPUT_GET, 'width') ?>;
        width_sum = 0;
        
        <?php for($i=1; $i<=19; $i++): ?>
            if(!$('#stream_' + <?=$i ?> + '_group').hasClass('d-none')) {
                if($('#stream_' + <?=$i ?>).val() == '') {
                    $('#stream_' + <?=$i ?>).addClass('is-invalid');
                    $('#stream_' + <?=$i ?>).focus();
                    $('.invalid-stream').text('Ширина ручья обязательно');
                    form_valid = false;
                }
                
                iWidth = parseInt($('#stream_' + <?=$i ?>).val());
                width_sum += iWidth;
            }
        <?php endfor; ?>

        if(form_valid) {
            <?php for($i=1; $i<=19; $i++): ?>
                if(!$('#stream_' + <?=$i ?> + '_group').hasClass('d-none')) {
                    if(width_source != width_sum) {
                        $('#stream_' + <?=$i ?>).addClass('is-invalid');
                        $('#stream_' + <?=$i ?>).focus();
                        $('.invalid-stream').text('Сумма не равна общей ширине');
                        form_valid = false;
                    }
                    else {
                        $('#stream_' + <?=$i ?>).removeClass('is-invalid');
                        $('.invalid-stream').text('');
                        form_valid = true;
                    }
                }
            <?php endfor; ?>
        }
        
        if(form_valid && !submit) {
            link = "_wind.php?supplier_id=<?= filter_input(INPUT_GET, 'supplier_id') ?>&film_brand_id=<?= filter_input(INPUT_GET, 'film_brand_id') ?>&thickness=<?= filter_input(INPUT_GET, 'thickness') ?>&width=<?= filter_input(INPUT_GET, 'width') ?>&streams_count=" + $('#streams_count').val();
            <?php for($i=1; $i<=19; $i++): ?>
                if(!$('#stream_' + <?=$i ?> + '_group').hasClass('d-none')) {
                    link += "&stream_" + <?=$i ?> + "=" + $('#stream_' + <?=$i ?>).val();
                }
            <?php endfor; ?>
            OpenAjaxPage(link);
            submit = true;
        }
    }
        
    $('#next-submit').click(function() {
        $.ajax({ url: "_check_db_uri.php?uri=<?= urlencode($request_uri) ?>" })
                .done(function(data) {
                    if(data == "OK") {
                        Submit();
                    }
                    else {
                        OpenAjaxPage(data);
                    }
                })
                .fail(function() {
                    alert('Ошибка при переходе на страницу.');
                });
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