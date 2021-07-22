<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$sql = "update user set request_uri='$request_uri' where id=". GetUserId();
$error_message = (new Executer($sql))->error;
if(!empty($error_message)) {
    exit($error_message);
}

$cut_id = filter_input(INPUT_GET, 'cut_id');

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

// Получение объекта
$supplier_id = null;
$film_brand_id = null;
$thickness = null;
$width = null;

$sql = "select supplier_id, film_brand_id, thickness, width from cut where id = $cut_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $supplier_id = $row['supplier_id'];
    $film_brand_id = $row['film_brand_id'];
    $thickness = $row['thickness'];
    $width = $row['width'];
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-start"></nav>
</div>
<div id="topmost"></div>
<div class="container-fluid">
    <h1>Закрытие заявки</h1>
    <form method="post">
        <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$supplier_id ?>" />
        <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand_id ?>" />
        <input type="hidden" id="thickness" name="thickness" value="<?=$thickness ?>" />
        <input type="hidden" id="width" name="width" value="<?=$width ?>" />
        <input type="hidden" id="net_weight" name="net_weight" />
        <input type="hidden" id="length" name="length" />
        <div class="form-group">
            <input type="checkbox" id="remains" name="remains" checked="checked" />
            <label class="form-check-label" for="remains">Остался исходный ролик</label>
        </div>
        <div class="form-group remainder-group">
            <label for="radius">Введите радиус от вала исходного роля</label>
            <div class="input-group">
                <input type="text" class="form-control int-only" data-max="999" id="radius" name="radius" autocomplete="off" />
                <div class="input-group-append"><span class="input-group-text">мм</span></div>
                <div class="invalid-feedback">Число, макс. 999</div>
            </div>
        </div>
        <div class="form-group remainder-group">
            <label for="spool">Диаметр шпули</label>
            <div class="d-block">
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" id="spool" name="spool" value="76" checked="checked" />76 мм
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" id="spool" name="spool" value="152" />152 мм
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-dark form-control" style="height: 5rem;" id="close-submit">Распечатать исходный роль<br /> и закрыть заявку</button>
        </div>
    </form>
</div>
<script>
    // Скрытие/показ элементов формы в зависимости от того, остался ли исходный ролик
    $('#remains').change(function() {
        if($(this).is(':checked')) {
            $('.remainder-group').removeClass('d-none');
            $('input#radius').attr('required', 'required');
        }
        else {
            $('.remainder-group').addClass('d-none');
            $('input#radius').removeAttr('required');
        }
    });
    
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
        $('#length').val('');
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
                    
            $('#length').val(result.length.toFixed(2));
            $('#net_weight').val(result.weight.toFixed(2));
        }
    }
            
    $(document).ready(CalculateByRadius);
            
    // Рассчитываем ширину и массу плёнки при изменении значений каждого поля, участвующего в вычислении
    $('#spool').change(CalculateByRadius);
            
    $('#radius').keypress(CalculateByRadius);
            
    $('#radius').keyup(CalculateByRadius);
            
    $('#radius').change(CalculateByRadius);
    
    // Создание оставшегося ролика
    submit = false;
    
    $('#close-submit').click(function() {
        form_valid = true;
        
        if(!$('#remains').is(':checked')) {
            OpenAjaxPage("_finish.php");
        }
        else {
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
            
            if(form_valid && !submit) {
                link = "_create_remain.php?supplier_id=" + $('#supplier_id').val() + "&film_brand_id=" + $('#film_brand_id').val() + "&width=" + $('#width').val() + "&thickness=" + $('#thickness').val() + "&radius=" + $('#radius').val() + "&spool=" + $('#spool').val() + "&net_weight=" + $('#net_weight').val() + "&length=" + $('#length').val();
            
                $.ajax({ url: link })
                        .done(function(data) {
                            if(isNaN(data)) {
                                alert(data);
                            }
                            else {
                                OpenAjaxPage("_print_remain.php?id=" + data);
                                submit = true;
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при переходе на страницу.');
                        });
            }
        }
    });
</script>