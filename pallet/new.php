<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$supplier_id_valid = '';
$id_from_supplier_valid = '';
$film_variation_id_valid = '';
$width_valid = '';
$length_valid = '';
$net_weight_valid = '';
$rolls_number_valid = '';
$cell_valid = '';
$status_id_valid = '';

$length_invalid_message = '';
$net_weight_invalid_message = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'create-pallet-submit')) {
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
    
    $film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
    if(empty($film_variation_id)) {
        $film_variation_id = ISINVALID;
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
        
    $length = filter_input(INPUT_POST, 'length');
    if(empty($length)) {
        $length_valid = ISINVALID;
        $form_valid = false;
    }
    
    $net_weight = filter_input(INPUT_POST, 'net_weight');
    if(empty($net_weight)) {
        $net_weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    // Определяем толщину и удельный вес
    $thickness = null;
    $ud_ves = null;
    $sql = "select thickness, weight from film_variation where id=$film_variation_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $thickness = $row['thickness'];
        $ud_ves = $row['weight'];
    }
    
    $weight_result = floatval($ud_ves) * floatval($length) * floatval($width) / 1000.0 / 1000.0;
    $weight_result_high = $weight_result + ($weight_result * 15.0 / 100.0);
    $weight_result_low = $weight_result - ($weight_result * 15.0 / 100.0);
        
    if($net_weight < $weight_result_low || $net_weight > $weight_result_high) {
        $net_weight_valid = ISINVALID;
        $length_valid = ISINVALID;
        $form_valid = false;
        $net_weight_invalid_message = "Неверное значение";
        $length_invalid_message = "Неверное значение";
    }
    
    $rolls_number = filter_input(INPUT_POST, 'rolls_number');
    if(empty($rolls_number)) {
        $rolls_number_valid = ISINVALID;
        $form_valid = false;
    }
    
    $cell = filter_input(INPUT_POST, 'cell');
    if(empty($cell)) {
        $cell_valid = ISINVALID;
        $form_valid = false;
    }
    
    // Валидация роликов
    $rolls_valid_data = array();
    $roll_number = 1;
    $rolls_weight = 0;
    while (filter_input(INPUT_POST, "id_from_supplier_roll$roll_number") !== null && filter_input(INPUT_POST, "weight_roll$roll_number") !== null && filter_input(INPUT_POST, "length_roll$roll_number") !== null) {
        $roll_valid_data = array();
        $roll_valid_data['id_from_supplier_valid'] = '';
        $roll_valid_data['id_from_supplier_message'] = 'ID от поставщика обязательно';
        $roll_valid_data['length_valid'] = '';
        $roll_valid_data['length_message'] = 'Длина обязательно';
        $roll_valid_data['weight_valid'] = '';
        $roll_valid_data['weight_message'] = 'Масса нетто обязательно';
        
        $roll_id_from_supplier = filter_input(INPUT_POST, "id_from_supplier_roll$roll_number");
        if(empty($roll_id_from_supplier)) {
            $roll_valid_data['id_from_supplier_valid'] = ISINVALID;
            $form_valid = false;
        }
        
        $roll_length = filter_input(INPUT_POST, "length_roll$roll_number");
        if(empty($roll_length)) {
            $roll_valid_data['length_valid'] = ISINVALID;
            $form_valid = false;
        }
        
        $roll_weight = filter_input(INPUT_POST, "weight_roll$roll_number");
        if(empty($roll_weight)) {
            $roll_valid_data['weight_valid'] = ISINVALID;
            $form_valid = false;
        }
        
        if(empty($roll_valid_data['length_valid']) && empty($roll_valid_data['weight_valid'])) {
            $roll_weight_result = floatval($ud_ves) * floatval($roll_length) * floatval($width) / 1000.0 / 1000.0;
            $roll_weight_result_high = $roll_weight_result + ($roll_weight_result * 15.0 / 100.0);
            $roll_weight_result_low = $roll_weight_result - ($roll_weight_result * 15.0 / 100.0);
            
            if($roll_weight < $roll_weight_result_low || $roll_weight > $roll_weight_result_high) {
                $roll_valid_data['weight_valid'] = ISINVALID;
                $roll_valid_data['length_valid'] = ISINVALID;
                $form_valid = false;
                $roll_valid_data['weight_message'] = "Неверное значение";
                $roll_valid_data['length_message'] = "Неверное значение";
            }
        }
        
        // Прибавляем вес ролика к сумме весов роликов
        $rolls_weight += intval($roll_weight);

        // Добавляем данные о валидации ролика в массив данных о валидации роликов
        $rolls_valid_data[$roll_number] = $roll_valid_data;
        $roll_number++;
    }
    
    // Масса паллета должна быть равна сумме масс роликов
    $rolls_weight_high = $rolls_weight + ($rolls_weight * 5.0 / 100.0);
    $rolls_weight_low = $rolls_weight - ($rolls_weight * 5.0 / 100.0);
    if($net_weight > $rolls_weight_high || $net_weight < $rolls_weight_low) {
        $net_weight_valid = ISINVALID;
        $form_valid = false;
        $net_weight_invalid_message = "Не равно сумме роликов";
    }
    
    // Выбор менеджера пока не обязательный.
    $manager_id = filter_input(INPUT_POST, 'manager_id');
    if(empty($manager_id)) {
        $manager_id = "NULL";
    }

    $comment = addslashes(filter_input(INPUT_POST, 'comment'));
    $date = filter_input(INPUT_POST, 'date');
    $storekeeper_id = filter_input(INPUT_POST, 'storekeeper_id');
    
    if($form_valid) {
        $sql = "insert into pallet (supplier_id, id_from_supplier, film_variation_id, width, cell, comment, storekeeper_id) "
                . "values ($supplier_id, '$id_from_supplier', $film_variation_id, $width, '$cell', '$comment', '$storekeeper_id')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $pallet_id = $executer->insert_id;
        $user_id = GetUserId();
        
        if(empty($error_message)) {
            // Заполнение роликов этого паллета
            $roll_number = 1;
            
            while (filter_input(INPUT_POST, "id_from_supplier_roll$roll_number") !== null && filter_input(INPUT_POST, "weight_roll$roll_number") !== null && filter_input(INPUT_POST, "length_roll$roll_number") !== null && filter_input(INPUT_POST, "ordinal_roll$roll_number") != null) {
                $id_from_supplier = addslashes(filter_input(INPUT_POST, "id_from_supplier_roll$roll_number"));
                $weight = filter_input(INPUT_POST, "weight_roll$roll_number");
                $length = filter_input(INPUT_POST, "length_roll$roll_number");
                $ordinal = filter_input(INPUT_POST, "ordinal_roll$roll_number");
                $sql = "insert into pallet_roll (pallet_id, weight, length, ordinal, id_from_supplier) values ($pallet_id, $weight, $length, $ordinal, '$id_from_supplier')";
                $executer = new Executer($sql);
                $error_message = $executer->error;
                $roll_number++;
            }
            
            if(empty($error_message)) {
                header('Location: '.APPLICATION."/pallet/print.php?id=$pallet_id");
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
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            include '../include/find_camera.php';
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/pallet/">Назад</a>
            <h1 style="font-size: 32px; font-weight: 600; margin-bottom: 20px;">Новый паллет</h1>
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
                        <label for="id_from_supplier">ID паллета от поставщика</label>
                        <input type="text" id="id_from_supplier" name="id_from_supplier" value="<?= filter_input(INPUT_POST, 'id_from_supplier') ?>" class="form-control" placeholder="Введите ID" required="required" autocomplete="off" />
                        <div class="invalid-feedback">ID паллета от поставщика обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="film_id">Марка пленки</label>
                        <select id="film_id" name="film_id" class="form-control" required="required">
                            <option value="">Выберите марку</option>
                            <?php
                            if(null !== filter_input(INPUT_POST, 'supplier_id')) {
                                $supplier_id = filter_input(INPUT_POST, 'supplier_id');
                                $films = (new Grabber("select id, name from film where id in (select film_id from film_variation where id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id))"))->result;
                                foreach ($films as $film) {
                                    $film_id = $film['id'];
                                    $name = $film['name'];
                                    $selected = '';
                                    if(filter_input(INPUT_POST, 'film_id') == $film_id) $selected = " selected='selected'";
                                    echo "<option value='$film_id'$selected>$name</option>";
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Марка пленки обязательно</div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="width">Ширина, мм</label>
                            <input type="text" id="width" name="width" value="<?= filter_input(INPUT_POST, 'width') ?>" class="form-control int-only<?=$width_valid ?>" placeholder="Введите ширину" required="required" autocomplete="off" />
                            <div class="invalid-feedback">От 50 до 1600</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="film_variation_id">Толщина, мкм</label>
                            <select id="film_variation_id" name="film_variation_id" class="form-control" required="required">
                                <option value="">Выберите толщину</option>
                                <?php
                                if(null !== filter_input(INPUT_POST, 'film_id')) {
                                    $film_id = filter_input(INPUT_POST, 'film_id');
                                    $film_variations = (new Grabber("select id, thickness, weight from film_variation where film_id = $film_id order by thickness"))->result;
                                    foreach ($film_variations as $film_variation) {
                                        $film_variation_id = $film_variation['id'];
                                        $thickness = $film_variation['thickness'];
                                        $weight = $film_variation['weight'];
                                        $selected = '';
                                        if(filter_input(INPUT_POST, 'film_variation_id') == $film_variation_id) $selected = " selected='selected'";
                                        echo "<option value='$film_variation_id'$selected>$thickness мкм $weight г/м<sup>2</sup></option>";
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Толщина обязательно</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="net_weight">Масса нетто, кг</label>
                            <input type="text" id="net_weight" name="net_weight" value="<?= filter_input(INPUT_POST, 'net_weight') ?>" class="form-control int-only<?=$net_weight_valid ?>" placeholder="Введите массу нетто" required="required" autocomplete="off" />
                            <div class="invalid-feedback"><?= empty($net_weight_invalid_message) ? "Масса нетто обязательно" : $net_weight_invalid_message ?></div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="length">Длина, м</label>
                            <input type="text" id="length" name="length" value="<?= filter_input(INPUT_POST, 'length') ?>" class="form-control int-only<?=$length_valid ?>" placeholder="Введите длину" required="required" autocomplete="off" />
                            <div class="invalid-feedback"><?= empty($length_invalid_message) ? "Длина обязательно" : $length_invalid_message ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="cell">Ячейка на складе</label>
                            <input type="text" id="cell" name="cell" value="<?= filter_input(INPUT_POST, 'cell') ?>" class="form-control no-latin<?=$cell_valid ?>" placeholder="Введите ячейку" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Ячейка на складе обязательно</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="rolls_number">Количество рулонов</label>
                            <select id="rolls_number" name="rolls_number" class="form-control" required="required">
                                <option value="">Выберите количество</option>
                                <?php
                                for($i=1; $i<11; $i++) {
                                    $selected = '';
                                    if(filter_input(INPUT_POST, 'rolls_number') == $i) $selected = " selected='selected'";
                                    echo "<option value='$i'$selected>$i</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Количество рулонов обязательно</div>
                        </div>
                    </div>
                    <?php
                    $checked = '';
                        
                    if(filter_input(INPUT_POST, 'equal_rolls') == 'on') {
                        $checked = " checked='checked'";
                    }
                    ?>
                    <div class="row">
                        <div class="col-6"></div>
                        <div class="col-6">
                            <div class="form-group">
                                <input type="checkbox" id="equal_rolls" name="equal_rolls"<?=$checked ?> />
                                <label class="form-check-label" for="equal_rolls">Все примерно одинаковые</label>
                            </div>
                        </div>
                    </div>
                    <div id="rolls_info">
                        <?php
                        $roll_number = 1;
                        while (filter_input(INPUT_POST, "weight_roll$roll_number") !== null && filter_input(INPUT_POST, "length_roll$roll_number") !== null && key_exists($roll_number, $rolls_valid_data)):
                        ?>
                        <div class='mt-1'><?=$roll_number ?> рулон</div>
                        <input type='hidden' id='ordinal_roll<?=$roll_number ?>' name='ordinal_roll<?=$roll_number ?>' value='<?=$roll_number ?>' />
                        <div class="row">
                            <div class="col-12 form-group">
                                <label for="id_from_supplier_roll<?=$roll_number ?>">ID от поставщика</label>
                                <input type="text" id="id_from_supplier_roll<?=$roll_number ?>" name="id_from_supplier_roll<?=$roll_number ?>" class="form-control<?=$rolls_valid_data[$roll_number]['id_from_supplier_valid'] ?>" placeholder="ID от поставщика" value="<?= filter_input(INPUT_POST, "id_from_supplier_roll$roll_number") ?>" required="required" autocomplete="off" />
                                <div class="invalid-feedback"><?=$roll_valid_data[$roll_number]['id_from_supplier_message'] ?></div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-6 form-group'>
                                <label for='weight_roll<?=$roll_number ?>'>Масса нетто, кг</label>
                                <input type='text' id='weight_roll<?=$roll_number ?>' name='weight_roll<?=$roll_number ?>' class='form-control int-only<?=$rolls_valid_data[$roll_number]['weight_valid'] ?>' placeholder='Масса нетто рулона' value="<?= filter_input(INPUT_POST, "weight_roll$roll_number") ?>" required='required' autocomplete="off" />
                                <div class="invalid-feedback"><?=$rolls_valid_data[$roll_number]['weight_message'] ?></div>
                            </div>
                            <div class='col-6 form-group'>
                                <label for='length_roll<?=$roll_number ?>'>Длина, м</label>
                                <input type='text' id='length_roll<?=$roll_number ?>' name='length_roll<?=$roll_number ?>' class='form-control int-only<?=$rolls_valid_data[$roll_number]['length_valid'] ?>' placeholder='Длина рулона' value="<?= filter_input(INPUT_POST, "length_roll$roll_number") ?>" required='required' autocomplete="off" />
                                <div class="invalid-feedback"><?=$rolls_valid_data[$roll_number]['length_message'] ?></div>
                            </div>
                        </div>
                        <?php
                        $roll_number++;
                        endwhile;
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="comment">Комментарий</label>
                        <textarea id="comment" name="comment" rows="4" class="form-control no-latin"><?= htmlentities(filter_input(INPUT_POST, 'comment')) ?></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-flex justify-content-start mt-4">
                        <div class="p-0">
                            <button type="submit" id="create-pallet-submit" name="create-pallet-submit" class="btn btn-dark" style="width: 175px;">Распечатать бирку</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_find.php';
        ?>
        <script>
            $('#supplier_id').change(function(){
                if($(this).val() == "") {
                    $('#film_id').html("<option value=''>Выберите марку</option>");
                }
                else {
                    $.ajax({ url: "../ajax/film.php?supplier_id=" + $(this).val() })
                            .done(function(data) {
                                $('#film_id').html(data);
                                $('#film_id').change();
                            })
                            .fail(function() {
                                alert('Ошибка при выборе поставщика');
                            });
                    }
                });
                
            $('#film_id').change(function(){
                if($(this).val() == "") {
                    $('#film_variation_id').html("<option value=''>Выберите толщину</option>");
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?film_id=" + $(this).val() + "&supplier_id=" + $('#supplier_id').val() })
                            .done(function(data) {
                                $('#film_variation_id').html(data);
                            })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                            });
                }
            });
            
            if($('.is-invalid').first() != null) {
                $('.is-invalid').first().focus();
            }
            
            // Открытие информация о роликах
            $('#rolls_number').change(function(e) {
                var val = $(e.target).val();
                if(!Number.isNaN(val)) {
                    var num_val = parseInt(val);
                    $('#rolls_info').html("");
                    
                    for(var i=1; i<=num_val; i++) {
                        var form_row = "<div class='mt-1'>" + i + " рулон</div>";
                        form_row += "<input type='hidden' id='ordinal_roll" + i + "' name='ordinal_roll" + i + "' value='" + i + "' />";
                        form_row += "<div class='row'>";
                        form_row += "<div class='col-12 form-group'>";
                        form_row += "<label for='id_from_supplier_roll" + i + "'>ID от поставщика</label>";
                        form_row += "<input type='text' id='id_from_supplier_roll" + i + "' name='id_from_supplier_roll" + i + "' class='form-control' placeholder='ID от поставщика' required='required' autocomplete='off' />";
                        form_row += "</div>";
                        form_row += "</div>";
                        form_row += "<div class='row'>";
                        form_row += "<div class='col-6 form-group'>";
                        form_row += "<label for='weight_roll" + i + "'>Масса нетто, кг</label>";
                        form_row += "<input type='text' id='weight_roll" + i + "' name='weight_roll" + i + "' class='form-control int-only' placeholder='Масса Нетто рулона' required='required' />";
                        form_row += "</div>";
                        form_row += "<div class='col-6 form-group'>";
                        form_row += "<label for='length_roll" + i + "'>Длина, м</label>";
                        form_row += "<input type='text' id='length_roll" + i + "' name='length_roll" + i + "' class='form-control int-only' placeholder='Длина рулона' required='required' />";
                        form_row += "</div>";
                        form_row += "</div>";
                        
                        $('#rolls_info').append(form_row);
                    }
                    
                    $('.int-only').keypress(function(e) {
                        if(/\D/.test(String.fromCharCode(e.charCode))) {
                            return false;
                        }
                    });
                    
                    $('.int-only').change(function(e) {
                        var val = $(this).val();
                        val = val.replace(/[^\d]/g, '');
                        $(this).val(val);
                    });
                    
                    $('#equal_rolls').change();
                }
            });
            
            // При установки флажка "Все ролики примерно одинаковые" автоматически устанавливается масса и длина всех роликов.
            // При снятии этого флажка эти поля очищаются и все массы и длины надо вводить вручную
            $('#equal_rolls').change(function(e) {
                if(!e.target.checked) {
                    var rolls_number = $('#rolls_number').val();
                    if(rolls_number != '') {
                        var num_val = parseInt(rolls_number);
                        
                        for(var i=1; i<=num_val; i++) {
                            $('#weight_roll' + i).val('');
                            $('#length_roll' + i).val('');
                        }
                    }
                }
                else {
                    var net_weight = $('#net_weight').val();
                    var length = $('#length').val();
                    var rolls_number = $('#rolls_number').val();
                    
                    if(rolls_number != '') {
                        var num_val = parseInt(rolls_number);
                        
                        if(net_weight != ''&& !isNaN(net_weight)) {
                            var num_weight = parseFloat(net_weight);
                                
                            for(var i=1; i<=num_val; i++) {
                                var result_weight = Math.round(num_weight / num_val);
                                $('#weight_roll' + i).val(result_weight);
                            }
                        }
                        else {
                            for(var i=1; i<=num_val; i++) {
                                $('#weight_roll' + i).val('');
                            }
                        }
                        
                        if(length != '' && !isNaN(length)) {
                            var num_length = parseFloat(length);
                            
                            for(var i=1; i<=num_val; i++) {
                                var result_length = Math.round(num_length / num_val);
                                $('#length_roll' + i).val(result_length);
                            }
                        }
                        else {
                            for(var i=1; i<=num_val; i++) {
                                $('#length_roll' + i).val('');
                            }
                        }
                    }
                }
            });
            
            // При изменении значений веса и длины, эти значения автоматически пересчитываются для всех роликов
            $('#net_weight').keypress(function() { $('#equal_rolls').change(); });
            
            $('#net_weight').keyup(function() { $('#equal_rolls').change(); });
            
            $('#net_weight').change(function() { $('#equal_rolls').change(); });
            
            $('#length').keypress(function() { $('#equal_rolls').change(); });
            
            $('#length').keyup(function() { $('#equal_rolls').change(); });
            
            $('#length').change(function() { $('#equal_rolls').change(); });
        </script>
    </body>
</html>