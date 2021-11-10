<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'top_manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$supplier_id_valid = '';
$id_from_supplier_valid = '';
$film_brand_id_valid = '';
$width_valid = '';
$thickness_valid = '';
$length_valid = '';
$net_weight_valid = '';
$cell_valid = '';
$status_id_valid = '';
$diameter_valid = '';

$invalid_diameter_message = '';
$invalid_message = '';
$length_invalid_message = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'create-roll-submit')) {
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    if(empty($supplier_id)) {
        $supplier_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $id_from_supplier = filter_input(INPUT_POST, 'id_from_supplier');
    if(empty($id_from_supplier)) {
        $id_from_supplier_valid = ISINVALID;
        $form_valid = false;
    }
    
    $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
    if(empty($film_brand_id)) {
        $film_brand_id = ISINVALID;
        $form_valid = false;
    }
    
    $width = filter_input(INPUT_POST, 'width');
    if(empty($width)) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(intval($width) < 50 || intval($width) > 1600) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    $thickness = filter_input(INPUT_POST, 'thickness');
    if(empty($thickness)) {
        $thickness_valid = ISINVALID;
        $form_valid = false;
    }
    
    $length = filter_input(INPUT_POST, 'length');
    if(filter_input(INPUT_POST, 'caclulate_by_diameter') != 'on' && empty($length)) {
        $length_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'caclulate_by_diameter') == 'on') {
        $length = filter_input(INPUT_POST, 'length_hidden');
        if(empty($length)) {
            $length_valid = false;
            $form_valid = false;
        }
    }
    
    $net_weight = filter_input(INPUT_POST, 'net_weight');
    if(filter_input(INPUT_POST, 'caclulate_by_diameter') != 'on' && empty($net_weight)) {
        $net_weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'caclulate_by_diameter') == 'on') {
        $net_weight = filter_input(INPUT_POST, 'net_weight_hidden');
        if(empty($net_weight)) {
            $net_weight_valid = false;
            $form_valid = false;
        }
    }
    
    if(filter_input(INPUT_POST, 'caclulate_by_diameter') == 'on') {
        // Валидация диаметра
        $diameter = filter_input(INPUT_POST, 'diameter');
        if(empty($diameter)) {
            $diameter_valid = ISINVALID;
            $form_valid = false;
        }
        else if(intval($diameter) < 20 || intval($diameter) > 500) {
            $diameter_valid = ISINVALID;
            $form_valid = false;
            $invalid_diameter_message = "От 20 до 500";
        }
    }
    
    // Определяем удельный вес
    $ud_ves = null;
    $sql = "select weight from film_brand_variation where film_brand_id=$film_brand_id and thickness=$thickness";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $ud_ves = $row[0];
    }
    
    $weight_result = floatval($ud_ves) * floatval($length) * floatval($width) / 1000.0 / 1000.0;
    $weight_result_high = $weight_result + ($weight_result * 15.0 / 100.0);
    $weight_result_low = $weight_result - ($weight_result * 15.0 / 100.0);
    
    if($net_weight < $weight_result_low || $net_weight > $weight_result_high) {
        $net_weight_valid = ISINVALID;
        $length_valid = ISINVALID;
        $form_valid = false;
        $invalid_message = "Неверное значение";
        $length_invalid_message = "Неверное значение";
    }
    
    $cell = filter_input(INPUT_POST, 'cell');
    if(empty($cell)) {
        $cell_valid = ISINVALID;
        $form_valid = false;
    }
    
    // Выбор менеджера пока необязательный.
    $manager_id = filter_input(INPUT_POST, 'manager_id');
    if(empty($manager_id)) {
        $manager_id = "NULL";
    }

    // Статус пока не обязательно.
    $status_id = filter_input(INPUT_POST, 'status_id');
    if(empty($status_id)) {
        $status_id = "NULL";
    }
    
    $comment = addslashes(filter_input(INPUT_POST, 'comment'));
    $date = filter_input(INPUT_POST, 'date');
    $storekeeper_id = filter_input(INPUT_POST, 'storekeeper_id');
    
    if($form_valid) {
        $sql = "insert into roll (supplier_id, id_from_supplier, film_brand_id, width, thickness, length, net_weight, cell, comment, storekeeper_id) "
                . "values ($supplier_id, '$id_from_supplier', $film_brand_id, $width, $thickness, $length, $net_weight, '$cell', '$comment', '$storekeeper_id')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $roll_id = $executer->insert_id;
        $user_id = GetUserId();
        
        if(empty($error_message)) {
            $sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($roll_id, $status_id, $user_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            
            if(empty($error_message)) {
                header('Location: '.APPLICATION."/roll/print.php?id=$roll_id");
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
    </head>
    <body>
        <?php
        include '../include/header_sklad.php';
        ?>
        <div class="container-fluid" style="padding-left: 40px;">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/roll/">Назад</a>
            <h1 style="font-size: 32px; font-weight: 600; margin-bottom: 20px;">Новый рулон</h1>
            <form method="post">
                <div style="width: 423px;">
                    <input type="hidden" id="date" name="date" value="<?= date("Y-m-d") ?>" />
                    <input type="hidden" id="storekeeper_id" name="storekeeper_id" value="<?= GetUserId() ?>" />
                    <input type="hidden" id="scroll" name="scroll" />
                    <div class="form-group">
                        <label for="supplier_id">Поставщик</label>
                        <select id="supplier_id" name="supplier_id" class="form-control" required="required">
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
                        <label for="id_from_supplier">ID рулона от поставщика</label>
                        <input type="text" id="id_from_supplier" name="id_from_supplier" value="<?= filter_input(INPUT_POST, 'id_from_supplier') ?>" class="form-control" placeholder="Введите ID" required="required" autocomplete="off" />
                        <div class="invalid-feedback">ID рулона от поставщика обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="film_brand_id">Марка пленки</label>
                        <select id="film_brand_id" name="film_brand_id" class="form-control" required="required">
                            <option value="">Выберите марку</option>
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
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="width" id="label_width">Ширина, мм</label>
                            <input type="text" id="width" name="width" value="<?= filter_input(INPUT_POST, 'width') ?>" class="form-control int-only<?=$width_valid ?>" placeholder="Введите ширину" required="required" autocomplete="off" />
                            <div class="invalid-feedback">От 50 до 1600</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="thickness" id="label_thickness">Толщина, мкм</label>
                            <select id="thickness" name="thickness" class="form-control" required="required">
                                <option value="">Выберите толщину</option>
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
                    </div>
                    <?php
                    $checked = '';
                    if(filter_input(INPUT_POST, 'caclulate_by_diameter') == 'on') {
                        $checked = " checked='checked'";
                    }
                    ?>
                    <div class="form-group">
                        <input type="checkbox" id="caclulate_by_diameter" name="caclulate_by_diameter"<?=$checked ?> />
                        <label class="form-check-label" for="caclulate_by_diameter">Рассчитать по радиусу</label>
                    </div>
                    <div class="row" id="controls-for-calculation">
                        <div class="col-6 form-group">
                            <label for="shpulya">Шпуля</label>
                            <select id="shpulya" name="shpulya" class="form-control">
                                <?php
                                $shpulya_selected_76 = '';
                                $shpulya_selected_152 = '';
                                $shpulya = filter_input(INPUT_POST, 'shpulya');
                                if($shpulya == 76) $shpulya_selected_76 = " selected='selected'";
                                if($shpulya == 152) $shpulya_selected_152 = " selected='selected'";
                                ?>
                                <option value="" hidden="hidden">Выберите шпулю</option>
                                <option value="76"<?=$shpulya_selected_76 ?>">76</option>
                                <option value="152"<?=$shpulya_selected_152 ?>">152</option>
                            </select>
                        </div>
                        <div class="col-6 form-group">
                            <label for="diameter">Расчет по радиусу (от вала), мм</label>
                            <input type="text" id="diameter" name="diameter" class="form-control int-only<?=$diameter_valid ?>" value="<?= filter_input(INPUT_POST, 'diameter') ?>" autocomplete="off" />
                            <div class="invalid-feedback"><?= empty($invalid_diameter_message) ? "Радиус обязательно" : $invalid_diameter_message ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="net_weight">Масса нетто, кг</label>
                            <input type="text" id="net_weight" name="net_weight" value="<?= filter_input(INPUT_POST, 'net_weight') ?>" class="form-control int-only<?=$net_weight_valid ?>" placeholder="Введите массу нетто" required="required" autocomplete="off" />
                            <input type="hidden" id="net_weight_hidden" name="net_weight_hidden" />
                            <div class="invalid-feedback"><?= empty($invalid_message) ? "Масса нетто обязательно" : $invalid_message ?></div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="length">Длина, м</label>
                            <input type="text" id="length" name="length" value="<?= filter_input(INPUT_POST, 'length') ?>" class="form-control int-only<?=$length_valid ?>" placeholder="Введите длину" required="required" autocomplete="off" />
                            <input type="hidden" id="length_hidden" name="length_hidden" />
                            <div class="invalid-feedback"><?= empty($length_invalid_message) ? "Длина обязательно" : $length_invalid_message ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="cell">Ячейка на складе</label>
                            <input type="text" id="cell" name="cell" value="<?= filter_input(INPUT_POST, 'cell') ?>" class="form-control no-latin<?=$cell_valid ?>" placeholder="Введите ячейку" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Ячейка на складе обязательно</div>
                        </div>
                        <div class="col-6 form-group"></div>
                    </div>
                    <div class="form-group d-none">
                        <label for="manager_id">Менеджер</label>
                        <select id="manager_id" name="manager_id" class="form-control" disabled="disabled">
                            <option value="">Выберите менеджера</option>
                            <?php
                            $managers = (new Grabber("select u.id, u.first_name, u.last_name from user u inner join role r on u.role_id = r.id where r.name in ('manager', 'seniormanager') order by u.last_name"))->result;
                            foreach ($managers as $manager) {
                                $id = $manager['id'];
                                $first_name = $manager['first_name'];
                                $last_name = $manager['last_name'];
                                $selected = '';
                                if(filter_input(INPUT_POST, 'manager_id') == $manager['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$last_name $first_name</option>";
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Менеджер обязательно</div>
                    </div>
                    <input type="hidden" id="status_id" name="status_id" value="1" />
                    <div class="form-group d-none">
                        <label for="status_id_">Статус</label>
                        <select id="status_id_" name="status_id_" class="form-control" disabled="disabled">
                            <?php
                            $statuses = (new Grabber("select s.id, s.name from roll_status s order by s.name"))->result;
                            foreach ($statuses as $status) {
                                $id = $status['id'];
                                $name = $status['name'];
                                $selected = '';
                                if($status['id'] == 1) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$name</option>";
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Статус обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Комментарий</label>
                        <textarea id="comment" name="comment" rows="4" class="form-control no-latin"><?= htmlentities(filter_input(INPUT_POST, 'comment')) ?></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-flex justify-content-start mt-4">
                        <div class="p-0">
                            <button type="submit" id="create-roll-submit" name="create-roll-submit" class="btn btn-dark">Распечатать бирку</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
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
            
            if($('#caclulate_by_diameter').prop('checked') == true) {
                $('#controls-for-calculation').show();
                $('#length').prop('disabled', true);
                $('#net_weight').prop('disabled', true);
            }
            else {
                $('#controls-for-calculation').hide();
                $('#length').prop('disabled', false);
                $('#net_weight').prop('disabled', false);
            }
            
            $('#caclulate_by_diameter').change(function(e){
                if(e.target.checked) {
                    $('#controls-for-calculation').show();
                    $('#length').prop('disabled', true);
                    $('#net_weight').prop('disabled', true);
                }
                else {
                    $('#controls-for-calculation').hide();
                    $('#length').prop('disabled', false);
                    $('#net_weight').prop('disabled', false);
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
                $('#length').removeClass('is-invalid');
                $('#net_weight').removeClass('is-invalid');
                
                $('#length').val('');
                $('#net_weight').val('');
                
                film_brand_id = $('#film_brand_id').val();
                spool = $('#shpulya').val();
                thickness = $('#thickness').val();
                radius = $('#diameter').val();
                width = $('#width').val();
                
                if(!isNaN(spool) && !isNaN(thickness) && !isNaN(radius) && !isNaN(width) 
                        && spool != '' && thickness != '' && radius != '' && width != '') {
                    density = films.get(parseInt($('#film_brand_id').val())).get(parseInt(thickness));
                    
                    result = GetFilmLengthWeightBySpoolThicknessRadiusWidth(spool, thickness, radius, width, density);
                    
                    $('#length').val(result.length.toFixed(2));
                    $('#length_hidden').val(result.length.toFixed(2));
                    $('#net_weight').val(result.weight.toFixed(2));
                    $('#net_weight_hidden').val(result.weight.toFixed(2));
                }
            }
    
            // Рассчитываем ширину и массу плёнки при изменении значений каждого поля, участвующего в вычислении
            $('#shpulya').change(CalculateByRadius);
            
            $('#diameter').keypress(CalculateByRadius);
            
            $('#diameter').keyup(CalculateByRadius);
            
            $('#diameter').change(CalculateByRadius);
            
            $('#thickness').change(CalculateByRadius);
            
            $('#width').keypress(CalculateByRadius);
            
            $('#width').keyup(CalculateByRadius);
            
            $('#width').change(CalculateByRadius);
            
            <?php
            if(filter_input(INPUT_POST, 'caclulate_by_diameter') == 'on'):
            ?>
            $(document).ready(CalculateByRadius);
            <?php
            endif;
            ?>
                
            if($('.is-invalid').first() != null) {
                $('.is-invalid').first().focus();
            }
        </script>
   </body>
</html>