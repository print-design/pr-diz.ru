<?php
include '../include/topscripts.php';

// Пекренаправление на страницу карщика при чтении QR-кода
if(IsInRole(ROLE_NAMES[ROLE_ELECTROCARIST])) {
    header('Location: '.APPLICATION.'/car/pallet_edit.php?id='. filter_input(INPUT_GET, 'id'));
}

// Авторизация
elseif(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на список
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/pallet/');
}

// Валидация формы
$form_valid = true;
$error_message = '';

$supplier_id_valid = '';
$film_id_valid = '';
$width_valid = '';
$film_variation_id_valid = '';
$length_valid = '';
$net_weight_valid = '';
$rolls_number_valid = '';
$cell_valid = '';

$invalid_message = '';
$length_invalid_message = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'change-status-submit')) {
    $id = filter_input(INPUT_POST, 'id');

    // Проверяем правильность веса, для всех ролей
    // Определяем имеющуюся длину и ширину
    $sql = "select p.film_variation_id, p.width, "
            . "(select sum(length) from pallet_roll where pallet_id = p.id) length, "
            . "(select sum(weight) from pallet_roll where pallet_id = p.id) net_weight "
            . "from pallet p where p.id=$id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $old_film_variation_id = $row['film_variation_id'];
        $old_length = $row['length'];
        $old_width = $row['width'];
        $old_net_weight = $row['net_weight'];
        
        $film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
        if(empty($film_variation_id)) $film_variation_id = $old_film_variation_id;
        
        $length = filter_input(INPUT_POST, 'length');
        if(empty($length)) $length = $old_length;
        
        $width = filter_input(INPUT_POST, 'width');
        if(empty($width)) $width = $old_width;
        
        $net_weight = filter_input(INPUT_POST, 'net_weight');
        if(empty($net_weight)) $net_weight = $old_net_weight;
    }
    
    // Определяем удельный вес
    $ud_ves = null;
    $sql = "select weight from film_variation where id=$film_variation_id";
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
    
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER]))) {
        $cell = filter_input(INPUT_POST, 'cell');
        if(empty($cell)) {
            $cell_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    $comment = addslashes(filter_input(INPUT_POST, 'comment'));
    $date = filter_input(INPUT_POST, 'date');
    $storekeeper_id = filter_input(INPUT_POST, 'storekeeper_id');
    
    if($form_valid) {
        if(empty($error_message)) {
            $sql = "";
            
            // Стирать старый комментарий может только технолог, остальные - только добавлять новый комментарий к старому
            if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER]))) {
                $sql = "update pallet set cell = '$cell', comment = '$comment' where id = $id";
            }
            else {
                $sql = "update pallet set comment = concat(comment, ' ', '$comment') where id = $id";
            }
            
            $error_message = (new Executer($sql))->error;
        }
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/pallet/'. BuildQueryRemove('id'));
        }
    }
}

// Получение данных
$sql = "select DATE_FORMAT(p.date, '%d.%m.%Y') date, DATE_FORMAT(p.date, '%H:%i') time, p.storekeeper_id, u.last_name, u.first_name, p.supplier_id, p.film_variation_id, p.width, "
        . "(select sum(pr1.length) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) length, "
        . "(select film_id from film_variation where id = p.film_variation_id) film_id, "
        . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) net_weight, "
        . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) rolls_number, "
        . "p.cell, "
        . "p.comment "
        . "from pallet p inner join user u on p.storekeeper_id = u.id "
        . "where p.id=$id";

$row = (new Fetcher($sql))->Fetch();
$date = $row['date'];
$time = $row['time'];
$storekeeper_id = $row['storekeeper_id'];
$storekeeper = $row['last_name'].' '.$row['first_name'];

$supplier_id = filter_input(INPUT_POST, 'supplier_id');
if(null === $supplier_id) $supplier_id = $row['supplier_id'];

$film_id = filter_input(INPUT_POST, 'film_id');
if(null === $film_id) $film_id = $row['film_id'];

$width = filter_input(INPUT_POST, 'width');
if(null === $width) $width = $row['width'];

$film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
if(null === $film_variation_id) $film_variation_id = $row['film_variation_id'];

$length = filter_input(INPUT_POST, 'length');
if(null === $length) $length = $row['length'];
if(null === $length) $length = 0;

$net_weight = filter_input(INPUT_POST, 'net_weight');
if(null === $net_weight) $net_weight = $row['net_weight'];
if(null === $net_weight) $net_weight = 0;

$rolls_number = filter_input(INPUT_POST, 'rolls_number');
if(null === $rolls_number) $rolls_number = $row['rolls_number'];
if(null === $rolls_number) $rolls_number = 0;

$cell = filter_input(INPUT_POST, 'cell');
if(null === $cell) $cell = $row['cell'];

$status_id = ROLL_STATUS_FREE;
if($rolls_number == 0) $status_id = ROLL_STATUS_UTILIZED;

$comment = filter_input(INPUT_POST, 'comment');
if(null === $comment) $comment = $row['comment'];
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
                echo "<div class='alert alert-danger>$error_message</div>";
            }
            ?>
            <a class="btn btn-light backlink" href="<?=APPLICATION ?>/pallet/<?= BuildQueryRemove('id') ?>">Назад</a>
            <h1 style="font-size: 24px; font-weight: 600;">Информация о паллете № <?="П".$id ?> от <?= $date ?></h1>
            <?php if(!empty($time) && $time != '00:00'): ?>
            <div>Время добавления: <?=$time ?></div>
            <?php endif; ?>
            <form method="post">
                <div style="width: 423px;">
                    <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                    <input type="hidden" id="date" name="date" value="<?= $date ?>" />
                    <input type="hidden" id="storekeeper_id" name="storekeeper_id" value="<?= $storekeeper_id ?>" />
                    <input type="hidden" id="scroll" name="scroll" />
                    <div class="form-group">
                        <label for="storekeeper">Принят кладовщиком</label>
                        <p id="storekeeper"><?=$storekeeper ?></p>
                    </div>
                    <div class="form-group">
                        <?php
                        $supplier_id_disabled = " disabled='disabled'";
                        ?>
                        <label for="supplier_id">Поставщик</label>
                        <select id="supplier_id" name="supplier_id" class="form-control<?=$supplier_id_valid ?>"<?=$supplier_id_disabled ?>>
                            <option value="">Выберите поставщика</option>
                            <?php
                            $suppliers = (new Grabber("select id, name from supplier order by name"))->result;
                            foreach ($suppliers as $supplier) {
                                $id = $supplier['id'];
                                $name = $supplier['name'];
                                $selected = '';
                                if($supplier_id == $supplier['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$name</option>";
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Поставщик обязательно</div>
                    </div>
                    <div class="form-group">
                        <?php
                        $film_id_disabled = " disabled='disabled'";
                        ?>
                        <label for="film_id">Марка пленки</label>
                        <select id="film_id" name="film_id" class="form-control<?=$film_id_valid ?>"<?=$film_id_disabled ?>>
                            <option value="">Выберите марку</option>
                            <?php
                            $films = (new Grabber("select id, name from film where id in (select film_id from film_variation where id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id))"))->result;
                            foreach ($films as $film) {
                                $id = $film['id'];
                                $name = $film['name'];
                                $selected = '';
                                if($film_id == $film['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$name</option>";
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Марка пленки обязательно</div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <?php
                            $width_disabled = " disabled='disabled'";
                            ?>
                            <label for="width">Ширина, мм</label>
                            <input type="text" id="width" name="width" value="<?= $width ?>" class="form-control int-only<?=$width_valid ?>" placeholder="Введите ширину"<?=$width_disabled ?> />
                            <div class="invalid-feedback">От 50 до 1600</div>
                        </div>
                        <div class="col-6 form-group">
                            <?php
                            $film_variation_id_disabled = " disabled='disabled'";
                            ?>
                            <label for="film_variation_id">Толщина, мкм</label>
                            <select id="film_variation_id" name="film_variation_id" class="form-control<?=$film_variation_id_valid ?>"<?=$film_variation_id_disabled ?>>
                                <option value="">Выберите толщину</option>
                                <?php
                                $film_variations = (new Grabber("select id, thickness, weight from film_variation where film_id = $film_id and id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id) order by thickness"))->result;
                                foreach ($film_variations as $film_variation) {
                                    $_id = $film_variation['id'];
                                    $thickness = $film_variation['thickness'];
                                    $weight = $film_variation['weight'];
                                    $selected = '';
                                    if($film_variation_id == $_id) $selected = " selected='selected'";
                                    echo "<option value='$_id'$selected>$thickness мкм $weight г/м<sup>2</sup></option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Толщина обязательно</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <?php
                            $net_weight_disabled = " disabled='disabled'";
                            ?>
                            <label for="net_weight">Масса нетто, кг</label>
                            <input type="text" id="net_weight" name="net_weight" value="<?= $net_weight ?>" class="form-control int-only<?=$net_weight_valid ?>" placeholder="Введите массу нетто"<?=$net_weight_disabled ?> />
                            <div class="invalid-feedback"><?= empty($invalid_message) ? "Масса нетто обязательно" : $invalid_message ?></div>
                        </div>
                        <div class="col-6 form-group">
                            <?php
                            $length_disabled = " disabled='disabled'";
                            ?>
                            <label for="length">Длина, м</label>
                            <input type="text" id="length" name="length" value="<?= $length ?>" class="form-control int-only<?=$length_valid ?>" placeholder="Введите длину"<?=$length_disabled ?> />
                            <div class="invalid-feedback"><?= empty($length_invalid_message) ? "Длина обязательно" : $length_invalid_message ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <?php
                            $cell_disabled = "";
                            if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER]))) {
                                $cell_disabled = " disabled='disabled'";
                            }
                            ?>
                            <label for="cell">Ячейка на складе</label>
                            <input type="text" id="cell" name="cell" value="<?= $cell ?>" class="form-control no-latin<?=$cell_valid ?>" placeholder="Введите ячейку" autocomplete="off"<?=$cell_disabled ?> />
                            <div class="invalid-feedback">Ячейка на складе обязательно</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="rolls_number">Количество рулонов</label>
                            <?php
                            $rolls_number_disabled = " disabled='disabled'";
                            ?>
                            <select id="rolls_number" name="rolls_number" class="form-control<?=$rolls_number_valid ?>"<?=$rolls_number_disabled ?>>
                                <option value="">Выберите количество</option>
                                <?php if($rolls_number == 0): ?>
                                <option value="0" selected="selected">0</option>
                                <?php
                                endif;
                                for($i=1; $i<21; $i++) {
                                    $selected = '';
                                    if($rolls_number == $i) $selected = " selected='selected'";
                                    echo "<option value='$i'$selected>$i</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Количество рулонов обязательно</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status_id">Статус</label>
                        <select id="status_id" name="status_id" class="form-control" required="required" disabled='disabled'>
                            <?php
                            foreach(ROLL_STATUSES as $status):
                                if(!(empty($status_id) && $status == ROLL_STATUS_UTILIZED)) { // Если статуса нет, то нельзя сразу поставить "Сработанный".
                                    $selected = '';
                                    if(empty($status_id)) $status_id = ROLL_STATUS_FREE; // По умолчанию ставим статус "Свободный".
                                    if($status_id == $status) $selected = " selected = 'selected'";
                                }
                            ?>
                            <option value="<?=$status ?>"<?=$selected ?>><?=ROLL_STATUS_NAMES[$status] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Статус обязательно</div>
                    </div>
                    <div class="form-group">
                        <?php
                        $comment_disabled = "";
                        if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER]))) {
                            $comment_disabled = " disabled='disabled'";
                        }
                        
                        $comment_value = htmlentities($comment);
                        if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER]))) {
                            $comment_value = "";
                        }
                        ?>
                        <label for="comment">Комментарий</label>
                        <?php if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER]))): ?>
                        <p><?= htmlentities($comment) ?></p>
                        <?php endif; ?>
                        <textarea id="comment" name="comment" rows="4" class="form-control"<?=$comment_disabled ?>><?=$comment_value ?></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <div class="p-0">
                            <button type="submit" id="change-status-submit" name="change-status-submit" class="btn btn-dark" style="width: 175px;">Сохранить</button>
                        </div>
                        <div class="p-0">
                            <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER]))): ?>
                            <a href="print.php?id=<?= filter_input(INPUT_GET, 'id') ?>" class="btn btn-outline-dark" style="width: 175px;">Распечатать бирку</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>                
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            if($('.is-invalid').first() != null) {
                $('.is-invalid').first().focus();
            }
        </script>
    </body>
</html>