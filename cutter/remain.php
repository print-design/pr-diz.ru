<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Проверяем, имеются ли незакрытые нарезки

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$radius_valid = '';

if(null !== filter_input(INPUT_POST, 'close-submit')) {
    header("Location: print_remain.php");
}

if(null !== filter_input(INPUT_POST, 'no-remain-submit')) {
    header("finish.php")
}

// Находим id раскроя
$cut_id = 4;
$sql = "select id from cut where cutter_id = $user_id and id in (select cut_id from cut_source) order by id desc limit 1";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $cut_id = $row[0];
}

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
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include '_head.php';
        include '_info.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-end">
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
            <h1>Закрытие заявки</h1>
            <form method="post">
                <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$supplier_id ?>" />
                <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand_id ?>" />
                <input type="hidden" id="thickness" name="thickness" value="<?=$thickness ?>" />
                <input type="hidden" id="width" name="width" value="<?=$width ?>" />
                <input type="hidden" id="net_weight" name="net_weight" />
                <input type="hidden" id="length" name="length" />
                <?php
                $remains_checked = " checked='checked'";
                $remainder_group_none = "";
                $radius_required = " required='required'";
                
                if(null !== filter_input(INPUT_POST, 'close-submit') && filter_input(INPUT_POST, 'remains') != 'on') {
                    $remains_checked = "";
                    $remainder_group_none = " d-none";
                    $radius_required = "";
                }
                ?>
                <div class="form-group">
                    <input type="checkbox" id="remains" name="remains"<?=$remains_checked ?> />
                    <label class="form-check-label" for="remains">Остался исходный ролик</label>
                </div>
                <div class="form-group remainder-group<?=$remainder_group_none ?>">
                    <label for="radius">Введите радиус от вала исходного роля</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only<?=$radius_valid ?>" data-max="999" id="radius" name="radius" value="<?= filter_input(INPUT_POST, 'radius') ?>" autocomplete="off"<?=$radius_required ?> />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback">Число, макс. 999</div>
                    </div>
                </div>
                <div class="form-group remainder-group<?=$remainder_group_none ?>">
                    <label for="spool">Диаметр шпули</label>
                    <div class="d-block">
                        <?php
                        $checked76 = " checked='checked'";
                        $checked152 = "";
                        
                        if(filter_input(INPUT_POST, 'spool') == 76) {
                            $checked76 = " checked='checked'";
                            $checked152 = "";
                        }
                        
                        if(filter_input(INPUT_POST, 'spool') == 152) {
                            $checked76 = "";
                            $checked152 = " checked='checked'";
                        }
                        ?>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" id="spool" name="spool" value="76"<?=$checked76 ?> />76 мм
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" id="spool" name="spool" value="152"<?=$checked152 ?> />152 мм
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group remainder-group">
                    <button type="submit" class="btn btn-dark form-control" style="height: 5rem;" id="close-submit" name="close-submit">Распечатать исходный роль<br /> и закрыть заявку</button>
                </div>
                <div class="form-group no-remainder-group d-none">
                    <button type="submit" class="btn btn-dark form-control" id="no-remain-submit" name="no-remain-submit">Закрыть заявку</button>
                </div>
            </form>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Скрытие/показ элементов формы в зависимости от того, остался ли исходный ролик
            $('#remains').change(function() {
                if($(this).is(':checked')) {
                    $('.remainder-group').removeClass('d-none');
                    $('input#radius').attr('required', 'required');
                    
                    $('.no-remainder-group').addClass('d-none');
                }
                else {
                    $('.remainder-group').addClass('d-none');
                    $('input#radius').removeAttr('required');
                    
                    $('.no-remainder-group').removeClass('d-none');
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
                spool = $('input[name="spool"]:checked').val();
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
            $('input[name="spool"]').click(CalculateByRadius);
            
            $('#radius').keypress(CalculateByRadius);
            
            $('#radius').keyup(CalculateByRadius);
            
            $('#radius').change(CalculateByRadius);
        </script>
    </body>
</html>