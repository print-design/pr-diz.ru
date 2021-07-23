<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$user_id = GetUserId();
$sql = "update user set request_uri='$request_uri' where id=$user_id";
$error_message = (new Executer($sql))->error;
if(empty($error_message)) {
    $sql = "insert into history (user, request_uri) values($user_id, '$request_uri')";
    $error_message = (new Executer($sql))->error;    
}
if(!empty($error_message)) {
    exit($error_message);
}

include '_info.php';

// Получение объекта
$cut_id = filter_input(INPUT_GET, 'cut_id');
$date = '';
$sql = "select DATE_FORMAT(c.date, '%d.%m.%Y') date from cut c where c.id=$cut_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-between">
        <ul class="navbar-nav">
            <li class="nav-item">
                <button type="button" class="nav-link btn btn-link goto_next" data-cut-id="<?= $cut_id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</button>
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
    <h1>Нарезка <?=$cut_id ?> / <?=$date ?></h1>
    <p class="mb-3 mt-3" style="font-size: large;">Введите ID исходных ролей</p>
    <form method="post" id="sources_form">
        <div class="form-group" id="count-group">
            <label for="sources_count">Кол-во исходных ролей</label>
            <input type="text" id="sources_count" name="sources_count" class="form-control w-50 int-only" data-max="19" value="<?= filter_input(INPUT_GET, 'sources_count') ?>" required="required" autocomplete="off" />
            <div class="invalid-feedback">Число, макс. 19</div>
        </div>
            <?php
            for($i=1; $i<=19; $i++):
            ?>
        <div class="form-group source_group d-none" id="source_<?=$i ?>_group">
            <label for="source_<?=$i ?>">ID <?=$i ?>-го исходного роля</label>
            <input type="text" id="source_<?=$i ?>" name="source_<?=$i ?>" class="form-control no-latin" autocomplete="off" />
            <div class="invalid-feedback invalid-source" id="invalid-source-<?=$i ?>">Номер ролика обязательно</div>
        </div>
            <?php endfor; ?>
        <div class="form-group">
            <button type="button" class="btn btn-dark form-control mt-4" id="close-submit" data-cut-id="<?=$cut_id ?>">Закрыть заявку</button>
        </div>
    </form>
</div>
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
    
    // Закрытие заявки
    submit = false;
    
    function Submit() {
        form_valid = true;
        
        if($('#sources_count').val() == '') {
            $('#sources_count').addClass('is-invalid');
            $('#sources_count').focus();
            form_valid = false;
        }
        
        if($('#sources_count').val() < 1) {
            $('#sources_count').addClass('is-invalid');
            $('#sources_count').focus();
            form_valid = false;
        }
        
        if($('#sources_count').val() > 19) {
            $('#sources_count').addClass('is-invalid');
            $('#sources_count').focus();
            form_valid = false;
        }
        
        for(i=1; i<=19; i++) {
            if(!$('#source_' + i + '_group').hasClass('d-none')) {
                if($('#source_' + i).val() == '') {
                    $('#source_' + i).addClass('is-invalid');
                    $('#source_' + i).focus();
                    $('#invalid-source-' + i).text('Номер ролика обязательно');
                    form_valid = false;
                }
            }
        }
    
        if(form_valid && !submit) {
            link = "_create_sources.php?cut_id=" + $('#close-submit').attr('data-cut-id');
            for(i=1; i<=19; i++) {
                if(!$('#source_' + i + '_group').hasClass('d-none')) {
                    link += "&source_" + i + "=" + $('#source_' + i).val();
                }
            }
            
            $.ajax({ url: link })
                    .done(function(data) {
                        result = JSON.parse(data);
                
                        if(result.error != undefined && result.error != '') {
                            form_valid = false;
                            alert(result.error);
                        }
                        else {
                            for(i=1; i<=19; i++) {
                                if(result['source_' + i] != undefined && result['source_' + i] != '') {
                                    $('#source_' + i).addClass('is-invalid');
                                    $('#invalid-source-' + i).text(result['source_' + i]);
                                    form_valid = false;
                                }
                            }
                        }
                        
                        if(form_valid) {
                            OpenAjaxPage("_remain.php?cut_id=" + $('#close-submit').attr('data-cut-id'));
                            submit = true;
                        }
                    })
                    .fail(function() {
                        alert('Ошибка при переходе на страницу.');
                    });
        }
    }
    
    $('#close-submit').click(function() {
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
</script>