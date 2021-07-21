<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$sql = "update user set request_uri='$request_uri' where id=". GetUserId();
$error_message = (new Executer($sql))->error;
if(!empty($error_message)) {
    exit($error_message);
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-start">
        <ul class="navbar-nav">
            <li class="nav-item">
                <?php
                $data_sources = "";
                for($i=1; $i<=19; $i++) {
                    if(!empty(filter_input(INPUT_GET, 'stream_'.$i))) {
                        $data_sources .= " data-stream".$i."=".filter_input(INPUT_GET, 'stream_'.$i);
                    }
                }
                ?>
                <button type="button" class="nav-link btn btn-link goto_cut" data-supplier_id="<?= filter_input(INPUT_GET, 'supplier_id') ?>" data-film_brand_id="<?= filter_input(INPUT_GET, 'film_brand_id') ?>" data-thickness="<?= filter_input(INPUT_GET, 'thickness') ?>" data-width="<?= filter_input(INPUT_GET, 'width') ?>" data-streams-count="<?= filter_input(INPUT_GET, 'streams_count') ?>"<?=$data_sources ?>><i class="fas fa-chevron-left"></i>&nbsp;Назад</button>
            </li>
        </ul>
    </nav>
</div>
<div id="topmost"></div>
<div class="container-fluid">
    <h1>Нарезка / <?=date('d.m.Y') ?></h1>
    <p class="mb-3 mt-3" style="font-size: xx-large;">Намотка 1</p>
        <?php
        for($i=1; $i<=19; $i++):
        if(isset($_GET['stream_'.$i])):
        ?>
    <p>Ручей <?=$i ?> - <?=$_GET['stream_'.$i] ?> мм</p>
        <?php
        endif;
        endfor;
        ?>
    <form method="post" class="mt-3">
        <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$_GET['supplier_id'] ?>" />
        <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$_GET['film_brand_id'] ?>" />
        <input type="hidden" id="thickness" name="thickness" value="<?=$_GET['thickness'] ?>" />
        <input type="hidden" id="width" name="width" value="<?=$_GET['width'] ?>" />
        <input type="hidden" id="streams_count" name="streams_count" value="<?=$_GET['streams_count'] ?>" />
        <input type="hidden" id="spool" name="spool" value="76" />
        <input type="hidden" id="net_weight" name="net_weight" />
        <input type="hidden" id="normal_length" name="normal_length" />
            <?php
            for($i=1; $i<=19; $i++):
            if(key_exists('stream_'.$i, $_GET)):
            ?>
        <input type="hidden" id="stream_<?=$i ?>" name="stream_<?=$i ?>" value="<?=$_GET['stream_'.$i] ?>" />
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
            <?php
            $data_sources = "";
            for($i=1; $i<=19; $i++) {
                if(!empty(filter_input(INPUT_GET, 'stream_'.$i))) {
                    $data_sources .= " data-stream".$i."=".filter_input(INPUT_GET, 'stream_'.$i);
                }
            }
            ?>
            <button type="button" class="btn btn-outline-dark form-control mt-3" id="next-submit" data-supplier_id="<?= filter_input(INPUT_GET, 'supplier_id') ?>" data-film_brand_id="<?= filter_input(INPUT_GET, 'film_brand_id') ?>" data-thickness="<?= filter_input(INPUT_GET, 'thickness') ?>" data-width="<?= filter_input(INPUT_GET, 'width') ?>" data-streams-count="<?= filter_input(INPUT_GET, 'streams_count') ?>"<?=$data_sources ?>>След. намотка</button>
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
    
    // Создание нарезки и первой намотки
    $('#next-submit').click(function() {
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
        
        if(form_valid) {
            link = "_create_cut.php?supplier_id=" + $(this).attr('data-supplier_id') + "&film_brand_id=" + $(this).attr('data-film_brand_id') + "&thickness=" + $(this).attr('data-thickness') + "&width=" + $(this).attr('data-width') + "&length=" + $('#length').val().replaceAll(/\D/g, '') + "&radius=" + $('#radius').val() + "&net_weight=" + $('#net_weight').val();
            for(i=1; i<=19; i++) {
                for(i=1; i<=19; i++) {
                    if(!isNaN($(this).attr('data-stream' + i))) {
                        link += '&stream_' + i + "=" + $(this).attr('data-stream' + i);
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
                        }
                    })
                    .fail(function() {
                        alert('Ошибка при переходе на страницу.');
                    });
        }
    });
</script>