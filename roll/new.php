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
$film_id_valid = '';
$film_variation_id_valid = '';
$width_valid = '';
$length_valid = '';
$net_weight_valid = '';
$cell_valid = '';
$status_id_valid = '';
$radius_valid = '';

$invalid_radius_message = '';
$net_weight_invalid_message = '';
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
    
    $film_id = filter_input(INPUT_POST, 'film_id');
    if(empty($film_id)) {
        $film_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
    if(empty($film_variation_id)) {
        $film_variation_id_valid = ISINVALID;
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
    if(filter_input(INPUT_POST, 'caclulate_by_radius') != 'on' && empty($length)) {
        $length_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'caclulate_by_radius') == 'on') {
        $length = filter_input(INPUT_POST, 'length_hidden');
        if(empty($length)) {
            $length_valid = false;
            $form_valid = false;
        }
    }
    
    $net_weight = filter_input(INPUT_POST, 'net_weight');
    if(filter_input(INPUT_POST, 'caclulate_by_radius') != 'on' && empty($net_weight)) {
        $net_weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'caclulate_by_radius') == 'on') {
        $net_weight = filter_input(INPUT_POST, 'net_weight_hidden');
        if(empty($net_weight)) {
            $net_weight_valid = false;
            $form_valid = false;
        }
    }
    
    if(filter_input(INPUT_POST, 'caclulate_by_radius') == 'on') {
        // Валидация диаметра
        $radius = filter_input(INPUT_POST, 'radius');
        if(empty($radius)) {
            $radius_valid = ISINVALID;
            $form_valid = false;
        }
        else if(intval($radius) < 20 || intval($radius) > 500) {
            $radius_valid = ISINVALID;
            $form_valid = false;
            $invalid_radius_message = "От 20 до 500";
        }
    }
    
    // Определяем толщину и удельный вес
    $thickness = null;
    $ud_ves = null;
    $sql = "select thickness, weight from film_variation where id = $film_variation_id";
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
        $sql = "insert into roll (supplier_id, id_from_supplier, film_variation_id, width, length, net_weight, cell, comment, storekeeper_id) "
                . "values ($supplier_id, '$id_from_supplier', $film_variation_id, $width, $length, $net_weight, '$cell', '$comment', '$storekeeper_id')";
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
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            include '../include/find_camera.php';
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
                            <label for="width" id="label_width">Ширина, мм</label>
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
                                    $film_variations = (new Grabber("select id, thickness, weight from film_variation where film_id = $film_id and id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id) order by thickness"))->result;
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
                    <?php
                    $checked = '';
                    if(filter_input(INPUT_POST, 'caclulate_by_radius') == 'on') {
                        $checked = " checked='checked'";
                    }
                    ?>
                    <div class="form-group">
                        <input type="checkbox" id="caclulate_by_radius" name="caclulate_by_radius"<?=$checked ?> />
                        <label class="form-check-label" for="caclulate_by_radius">Рассчитать по радиусу</label>
                    </div>
                    <div class="row" id="controls-for-calculation">
                        <div class="col-6 form-group">
                            <label for="spool">Шпуля</label>
                            <select id="spool" name="spool" class="form-control">
                                <?php
                                $spool_selected_76 = '';
                                $spool_selected_152 = '';
                                $spool = filter_input(INPUT_POST, 'spool');
                                if($spool == 76) $spool_selected_76 = " selected='selected'";
                                if($spool == 152) $spool_selected_152 = " selected='selected'";
                                ?>
                                <option value="" hidden="hidden">Выберите шпулю</option>
                                <option value="76"<?=$spool_selected_76 ?>">76</option>
                                <option value="152"<?=$spool_selected_152 ?>">152</option>
                            </select>
                        </div>
                        <div class="col-6 form-group">
                            <label for="radius">Расчет по радиусу (от вала), мм</label>
                            <input type="text" id="radius" name="radius" class="form-control int-only<?=$radius_valid ?>" value="<?= filter_input(INPUT_POST, 'radius') ?>" autocomplete="off" />
                            <div class="invalid-feedback"><?= empty($invalid_radius_message) ? "Радиус обязательно" : $invalid_radius_message ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="net_weight">Масса нетто, кг</label>
                            <input type="text" id="net_weight" name="net_weight" value="<?= filter_input(INPUT_POST, 'net_weight') ?>" class="form-control int-only<?=$net_weight_valid ?>" placeholder="Введите массу нетто" required="required" autocomplete="off" />
                            <input type="hidden" id="net_weight_hidden" name="net_weight_hidden" />
                            <div class="invalid-feedback"><?= empty($net_weight_invalid_message) ? "Масса нетто обязательно" : $net_weight_invalid_message ?></div>
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
                        <textarea id="comment" name="comment" rows="4" class="form-control"><?= htmlentities(filter_input(INPUT_POST, 'comment')) ?></textarea>
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
            
            if($('#caclulate_by_radius').prop('checked') == true) {
                $('#controls-for-calculation').show();
                $('#length').prop('disabled', true);
                $('#net_weight').prop('disabled', true);
            }
            else {
                $('#controls-for-calculation').hide();
                $('#length').prop('disabled', false);
                $('#net_weight').prop('disabled', false);
            }
            
            $('#caclulate_by_radius').change(function(e){
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
            $sql = "SELECT fv.film_id, fv.id, fv.thickness, fv.weight FROM film_variation fv";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()):
            ?>
                if(films.get(<?=$row['film_id'] ?>) == undefined) {
                    films.set(<?=$row['film_id'] ?>, new Map());
                }
                
                films.get(<?=$row['film_id'] ?>).set(<?=$row['id'] ?>, [<?=$row['thickness'] ?>, <?=$row['weight'] ?>]);
            <?php endwhile; ?>
            
            // Расчёт длины и массы плёнки по шпуле, толщине, радиусу, ширине, удельному весу
            function CalculateByRadius() {
                $('#length').removeClass('is-invalid');
                $('#net_weight').removeClass('is-invalid');
                
                $('#length').val('');
                $('#net_weight').val('');
                
                film_id = $('#film_id').val();
                spool = $('#spool').val();
                film_variation_id = $('#film_variation_id').val();
                radius = $('#radius').val();
                width = $('#width').val();
                
                if(!isNaN(spool) && !isNaN(film_variation_id) && !isNaN(radius) && !isNaN(width) 
                        && spool != '' && film_variation_id != '' && radius != '' && width != '') {
                    thickness = films.get(parseInt($('#film_id').val())).get(parseInt(film_variation_id))[0];
                    density = films.get(parseInt($('#film_id').val())).get(parseInt(film_variation_id))[1];
                    
                    result = GetFilmLengthWeightBySpoolThicknessRadiusWidth(spool, thickness, radius, width, density);
                    
                    $('#length').val(result.length.toFixed(2));
                    $('#length_hidden').val(result.length.toFixed(2));
                    $('#net_weight').val(result.weight.toFixed(2));
                    $('#net_weight_hidden').val(result.weight.toFixed(2));
                }
            }
    
            // Рассчитываем ширину и массу плёнки при изменении значений каждого поля, участвующего в вычислении
            $('#spool').change(CalculateByRadius);
            
            $('#radius').keypress(CalculateByRadius);
            
            $('#radius').keyup(CalculateByRadius);
            
            $('#radius').change(CalculateByRadius);
            
            $('#film_variation_id').change(CalculateByRadius);
            
            $('#width').keypress(CalculateByRadius);
            
            $('#width').keyup(CalculateByRadius);
            
            $('#width').change(CalculateByRadius);
            
            <?php
            if(filter_input(INPUT_POST, 'caclulate_by_radius') == 'on'):
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