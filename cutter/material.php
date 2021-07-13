<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$supplier_id_valid = '';
$film_brand_id_valid = '';
$thickness_valid = '';
$width_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    if(empty($supplier_id)) {
        $supplier_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
    if(empty($film_brand_id)) {
        $film_brand_id = ISINVALID;
        $form_valid = false;
    }
    
    $thickness = filter_input(INPUT_POST, 'thickness');
    if(empty($thickness)) {
        $thickness_valid = ISINVALID;
        $form_valid = false;
    }
    
    $width = filter_input(INPUT_POST, 'width');
    if(empty($width)) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        header('Location: '.APPLICATION.'/cutter/cut.php?supplier_id='.$supplier_id.'&film_brand_id='.$film_brand_id.'&thickness='.$thickness.'&width='.$width);
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
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <?php if(empty(filter_input(INPUT_GET, 'link'))): ?>
                        <a class="nav-link" href="<?=APPLICATION ?>/cutter/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        <?php else: ?>
                        <a class="nav-link" href="<?= urldecode(filter_input(INPUT_GET, 'link')) ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1>Какой материал режем?</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <div class="form-group">
                            <label for="supplier_id">Поставщик</label>
                            <select class="form-control<?=$supplier_id_valid ?>" id="supplier_id" name="supplier_id" required="required">
                                <option value="" hidden="hidden">Выберите поставщика</option>
                                <?php
                                $suppliers = (new Grabber("select id, name from supplier order by name"))->result;
                                foreach ($suppliers as $supplier) {
                                    $id = $supplier['id'];
                                    $name = $supplier['name'];
                                    $selected = '';
                                    if(filter_input(INPUT_POST, 'supplier_id') == $supplier['id']) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$name</option>";
                                    }
                                ?>
                            </select>
                            <div class="invalid-feedback">Поставщик обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="film_brand_id">Марка пленки</label>
                            <select class="form-control<?=$film_brand_id_valid ?>" id="film_brand_id" name="film_brand_id" required="required">
                                <option value="" hidden="hidden">Выберите марку</option>
                                <?php
                                if(null !== filter_input(INPUT_POST, 'supplier_id')) {
                                    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
                                    $film_brands = (new Grabber("select id, name from film_brand where supplier_id = $supplier_id"))->result;
                                    foreach ($film_brands as $film_brand) {
                                        $id = $film_brand['id'];
                                        $name = $film_brand['name'];
                                        $selected = '';
                                        if(filter_input(INPUT_POST, 'film_brand_id') == $film_brand['id']) $selected = " selected='selected'";
                                        echo "<option value='$id'$selected>$name</option>";
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Марка пленки обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="thickness">Толщина, мкм</label>
                            <select class="form-control<?=$thickness_valid ?>" id="thickness" name="thickness" required="required">
                                <option value="" hidden="hidden">Выберите толщину</option>
                                <?php
                                if(null !== filter_input(INPUT_POST, 'film_brand_id')) {
                                    $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
                                    $film_brand_variations = (new Grabber("select thickness, weight from film_brand_variation where film_brand_id = $film_brand_id order by thickness"))->result;
                                    foreach ($film_brand_variations as $film_brand_variation) {
                                        $thickness = $film_brand_variation['thickness'];
                                        $weight = $film_brand_variation['weight'];
                                        $selected = '';
                                        if(filter_input(INPUT_POST, 'thickness') == $film_brand_variation['thickness']) $selected = " selected='selected'";
                                        echo "<option value='$thickness'$selected>$thickness мкм $weight г/м<sup>2</sup></option>";
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Толщина обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="width">Ширина, мм</label>
                            <input type="text" id="width" name="width" value="<?= filter_input(INPUT_POST, 'width') ?>" class="form-control<?=$width_valid ?>" placeholder="Введите ширину" required="required" />
                            <div class="invalid-feedback">Число, макс. 1600</div>
                        </div>
                        <div class="form-group">
                            <button type="submit" id="next-submit" name="next-submit" class="btn btn-dark form-control" style="margin-top: 100px;">Далее</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
        <script>
            // Загрузка списка марок пленки
            $('#supplier_id').change(function(){
                if($(this).val() == "") {
                    $('#film_brand_id').html("<option value=''>Выберите марку</option>");
                }
                else {
                    $.ajax({ url: "../ajax/film_brand.php?supplier_id=" + $(this).val() })
                            .done(function(data) {
                                $('#film_brand_id').html(data);
                                $('#film_brand_id').change();
                            })
                            .fail(function() {
                                alert('Ошибка при выборе поставщика');
                            });
                }
            });
            
            // Загрузка списка толщин
            $('#film_brand_id').change(function(){
                if($(this).val() == "") {
                    $('#thickness').html("<option value=''>Выберите толщину</option>");
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?film_brand_id=" + $(this).val() })
                            .done(function(data) {
                                $('#thickness').html(data);
                            })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                            });
                }
            });
            
            // В поле "Ширина" ограничиваем значения: целые числа от 1 до 50
            $('#width').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 1600)) {
                    $(this).addClass('is-invalid');
                    
                    return false;
                }
                else {
                    $(this).removeClass('is-invalid');
                }
            });
    
            $("#width").change(function(){
                if($(this).val() > 1600) {
                    $(this).addClass('is-invalid');
                }
                else {
                    $(this).removeClass('is-invalid');
                }
                
                ChangeLimitIntValue($(this), 1600);
            });
        </script>
    </body>
</html>