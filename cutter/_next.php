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
$supplier_id = null;
$film_brand_id = null;
$thickness = null;
$width = null;
$winds_count = 0;
$sql = "select DATE_FORMAT(c.date, '%d.%m.%Y') date, c.supplier_id, c.film_brand_id, c.thickness, c.width, (select count(id) from cut_wind where cut_id = c.id) winds_count from cut c where c.id=$cut_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $supplier_id = $row['supplier_id'];
    $film_brand_id = $row['film_brand_id'];
    $thickness = $row['thickness'];
    $width = $row['width'];
    $winds_count = $row['winds_count'];
}

$sql = "select width from cut_stream where cut_id=$cut_id order by id";
$fetcher = new Fetcher($sql);
$i = 0;
while ($row = $fetcher->Fetch()) {
    $stream = 'stream_'.++$i;
    $$stream = $row['width'];
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
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
    <p class="mb-3 mt-3" style="font-size: xx-large;">Намотка <?=($winds_count + 1) ?></p>
        <?php
        for($i=1; $i<=19; $i++):
            $stream = 'stream_'.$i;
        if(isset($$stream)):
            ?>
    <p>Ручей <?=$i ?> - <?=$$stream ?> мм</p>
        <?php
        endif;
        endfor;
        ?>
    <form method="post" class="mt-3">
        <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$supplier_id ?>" />
        <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand_id ?>" />
        <input type="hidden" id="thickness" name="thickness" value="<?=$thickness ?>" />
        <input type="hidden" id="width" name="width" value="<?=$width ?>" />
        <input type="hidden" id="spool" name="spool" value="76" />
        <input type="hidden" id="net_weight" name="net_weight" />
        <input type="hidden" id="normal_length" name="normal_length" />
        <input type="hidden" name="cut_id" value="<?=$cut_id ?>" />
            <?php
            for($i=1; $i<=19; $i++):
                $stream = 'stream_'.$i;
            if(isset($$stream)):
                ?>
        <input type="hidden" id="stream_<?=$i ?>" name="stream_<?=$i ?>" value="<?=$$stream ?>" />
        <input type="hidden" id="net_weight_<?=$i ?>" name="net_weight_<?=$i ?>" />
            <?php
            endif;
            endfor;
            ?>
        <div class="form-group">
            <label for="length">Длина, м</label>
            <div class="input-group">
                <input type="text" class="form-control int-only int-format" data-max="30000" id="length" name="length" required="required" autocomplete="off" />
                <div class="input-group-append"><span class="input-group-text">м</span></div>
                <div class="invalid-feedback invalid-length">Обязательно, не более 30 000</div>
            </div>
        </div>
        <div class="form-group">
            <label for="radius">Радиус от вала, мм</label>
            <div class="input-group">
                <input type="text" class="form-control int-only" data-max="999" id="radius" name="radius" required="required" autocomplete="off" />
                <div class="input-group-append"><span class="input-group-text">мм</span></div>
                <div class="invalid-feedback invalid-radius">Обязательно, не более 999</div>
            </div>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-outline-dark form-control mt-3" id="next-submit" data-cut-id="<?=$cut_id ?>">След. намотка</button>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-dark form-control mt-3" id="close-submit" data-cut-id="<?=$cut_id ?>">Заявка выполнена</button>
        </div>
    </form>
</div>
<script>
    // Все марки плёнки с их вариациями
    var films = new Map();
            
    <?php
    $sql = "SELECT fbv.film_brand_id, fbv.thickness, fbv.weight FROM film_brand_variation fbv";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        echo "if(films.get(".$row['film_brand_id'].") == undefined) {\n";
        echo "films.set(".$row['film_brand_id'].", new Map());\n";
        echo "}\n";
        echo "films.get(".$row['film_brand_id'].").set(".$row['thickness'].", ".$row['weight'].");\n";
    }
    ?>
                
    // Расчёт длины и массы плёнки по шпуле, толщине, радиусу, ширине, удельному весу
    function CalculateByRadius() {
        $('#normal_length').val('');
        $('#net_weight').val('');
                
        film_brand_id = $('#film_brand_id').val();
        spool = $('#spool').val();
        thickness = $('#thickness').val();
        radius = $('#radius').val();
        width = $('#width').val();
                
        if(!isNaN(spool) && !isNaN(thickness) && !isNaN(radius) && !isNaN(width) 
                && spool != '' && thickness != '' && radius != '' && width != '') {
            density = films.get(parseInt($('#film_brand_id').val())).get(parseInt(thickness));
                        
            result = GetFilmLengthWeightBySpoolThicknessRadiusWidth(spool, thickness, radius, width, density);
                        
            $('#normal_length').val(result.length.toFixed(2));
            $('#net_weight').val(result.weight.toFixed(2));
        }
        
        for(i=1; i<=19; i++) {
            if($('#stream_' + i).length > 0) {
                width = $('#stream_' + i).val();
                
                if(!isNaN(spool) && !isNaN(thickness) && !isNaN(radius) && !isNaN(width) 
                        && spool != '' && thickness != '' && radius != '' && width != '') {
                    density = films.get(parseInt($('#film_brand_id').val())).get(parseInt(thickness));
                    
                    result = GetFilmLengthWeightBySpoolThicknessRadiusWidth(spool, thickness, radius, width, density);
                    
                    $('#net_weight_' + i).val(result.weight.toFixed(2));
                }
            }
        }
    }
            
    $(document).ready(CalculateByRadius);
            
    // Рассчитываем ширину и массу плёнки при изменении значений радиуса
    $('#radius').keypress(CalculateByRadius);
            
    $('#radius').keyup(CalculateByRadius);
            
    $('#radius').change(CalculateByRadius);
    
    // Создание намотки
    submit = false;
    
    function Submit() {
        form_valid = true;
        
        if($('#length').val() == '') {
            $('#length').addClass('is-invalid');
            $('#length').focus();
            $('.invalid-length').text("Обязательно, не более 30 000");
            form_valid = false;
        }
        
        if($('#length').val() < 1) {
            $('#length').addClass('is-invalid');
            $('#length').focus();
            $('.invalid-length').text("Обязательно, не более 30 000");
            form_valid = false;
        }
        
        if($('#length').val() > 30000) {
            $('#length').addClass('is-invalid');
            $('#length').focus();
            $('.invalid-length').text("Обязательно, не более 30 000");
            form_valid = false;
        }
        
        if($('#radius').val() == '') {
            $('#radius').addClass('is-invalid');
            $('#radius').focus();
            form_valid = false;
        }
        
        if($('#radius').val() < 1) {
            $('#radius').addClass('is-invalid');
            $('#radius').focus();
            form_valid = false;
        }
        
        if($('#radius').val() > 999) {
            $('#radius').addClass('is-invalid');
            $('#radius').focus();
            form_valid = false;
        }
        
        if(form_valid) {
            // Валидация длины
            max_length = parseInt($('#normal_length').val()) * 1.2;
            min_length = parseInt($('#normal_length').val()) * 0.8;
            length = $('#length').val().replaceAll(/\D/g, '');
            
            if(length > max_length || length < min_length) {
                $('#length').addClass('is-invalid');
                $('.invalid-length').text("Длина не соответствует радиусу");
                form_valid = false;
            }
        }
        
        if(form_valid && !submit) {
            link = "_create_wind.php?cut_id=" + $('#next-submit').attr('data-cut-id') + "&length=" + $('#length').val().replaceAll(/\D/g, '') + "&radius=" + $('#radius').val() + "&net_weight=" + $('#net_weight').val();
            for(i=1; i<=19; i++) {
                for(i=1; i<=19; i++) {
                    if($('#stream_' + i).length && $('#net_weight_' + i).length) {
                        link += '&stream_' + i + "=" + $('#stream_' + i).val();
                        link += '&net_weight_' + i + "=" + $('#net_weight_' + i).val();
                    }
                }
            }
            
            $.ajax({ url: link })
                    .done(function(data) {
                        if(isNaN(data)) {
                            alert(data);
                        }
                        else {
                            OpenAjaxPage("_print.php?cut_wind_id=" + data);
                            submit = true;
                        }
                    })
                    .fail(function() {
                        alert('Ошибка при переходе на страницу.');
                    });
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
    
    // Закрытие заявки
    $('#close-submit').click(function() {
        OpenAjaxPage("_close.php?cut_id=" + $(this).attr('data-cut-id'));
    });
</script>