<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение cut_id, возвращаемся на первую страницу
$cut_id = $_REQUEST['cut_id'];
if(empty($cut_id)) {
    header('Location: '.APPLICATION.'/cutter/');
}

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

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$radius_valid = '';

if(null !== filter_input(INPUT_POST, 'close-submit')) {
    if(null == filter_input(INPUT_POST, 'remains')) {
        // Если не осталось исходного ролика, переходим на последнюю страницу
        header('Location: '.APPLICATION.'/cutter/finish.php');
    }
    else {
        // Если остался исходный ролик, создаём его, рассчитывая параметры по радиусу от вала и диаметру шпули
        $radius = filter_input(INPUT_POST, 'radius');
        if(empty($radius)) {
            $radius_valid = ISINVALID;
            $form_valid = false;
        }
        
        $spool = filter_input(INPUT_POST, 'spool');
        
        // Если пустые значения веса и длины, значит они почему-то не посчитались по радиусу от вала
        // Выдаём сообщение об этом в контроле "радиус от вала"
        $net_weight = filter_input(INPUT_POST, 'net_weight');
        if(empty($net_weight)) {
            $radius_valid = ISINVALID;
            $form_valid = false;
        }
        
        $length = filter_input(INPUT_POST, 'length');
        if(empty($length)) {
            $radius_valid = ISINVALID;
            $form_valid = false;
        }
        
        $id_from_supplier = "Из раскроя";
        $cell = "Цех";
        $comment = "";
        $user_id = GetUserId();
            
        if($form_valid) {
            $sql = "insert into roll (supplier_id, id_from_supplier, film_brand_id, width, thickness, length, net_weight, cell, comment, storekeeper_id) "
                . "values ($supplier_id, '$id_from_supplier', $film_brand_id, $width, $thickness, $length, $net_weight, '$cell', '$comment', '$user_id')";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            $roll_id = $executer->insert_id;
            
            if(empty($error_message)) {
                $sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($roll_id, $free_status_id, $user_id)";
                $executer = new Executer($sql);
                $error_message = $executer->error;
                
                if(empty($error_message)) {
                    header('Location: '.APPLICATION."/cutter/print_remain.php?id=$roll_id");
                }
            }
        }
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
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start"></nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1>Закрытие заявки</h1>
            <form method="post">
                <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand_id ?>" />
                <input type="hidden" id="thickness" name="thickness" value="<?=$thickness ?>" />
                <input type="hidden" id="width" name="width" value="<?=$width ?>" />
                <input type="hidden" id="net_weight" name="net_weight" />
                <input type="hidden" id="length" name="length" />
                <?php
                $checked = " checked='checked'";
                if(filter_input(INPUT_POST, 'close-submit') !== null && filter_input(INPUT_POST, 'remains') == null) {
                    $checked = "";
                }
                ?>
                <div class="form-group">
                    <input type="checkbox" id="remains" name="remains"<?=$checked ?> />
                    <label class="form-check-label" for="remains">Остался исходный ролик</label>
                </div>
                <?php
                $remainder_class = " d-none";
                $remainder_required = "";
                
                if(filter_input(INPUT_POST, 'close-submit') === null || filter_input(INPUT_POST, 'remains') == 'on') {
                    $remainder_class = "";
                    $remainder_required = " required='required'";
                }
                ?>
                <div class="form-group remainder-group<?=$remainder_class ?>">
                    <label for="radius">Введите радиус от вала исходного роля</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only<?=$radius_valid ?>" id="radius" name="radius" value="<?= filter_input(INPUT_POST, 'radius') ?>"<?=$remainder_required ?> />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback">Радиус от вала обязательно</div>
                    </div>
                </div>
                <div class="form-group remainder-group<?=$remainder_class ?>">
                    <label for="spool">Диаметр шпули</label>
                    <?php
                    $d76_checked = (filter_input(INPUT_POST, 'spool') == null || filter_input(INPUT_POST, 'spool') == 76) ? " checked='checked'" : "";
                    $d152_checked = filter_input(INPUT_POST, 'spool') == 152 ? " checked='checked'" : "";
                    ?>
                    <div class="d-block">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" id="spool" name="spool" value="76"<?=$d76_checked ?> />76 мм
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" id="spool" name="spool" value="152"<?=$d152_checked ?> />152 мм
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark form-control" style="height: 5rem;" id="close-submit" name="close-submit">Распечатать исходный роль<br /> и закрыть заявку</button>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
        <script src="<?=APPLICATION ?>/js/calculation.js"></script>
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
        </script>
    </body>
</html>