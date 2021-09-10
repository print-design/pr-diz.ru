<?php
include '../include/topscripts.php';

// Пекренаправление на страницу карщика или резчика при чтении QR-кода
if(IsInRole(array('electrocarist'))) {
    header('Location: '.APPLICATION.'/car/roll_edit.php?id='. filter_input(INPUT_GET, 'id'));
}

// Авторизация
elseif(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на список
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/roll/');
}

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

// СТАТУС "СРАБОТАННЫЙ"
$utilized_status_id = 2;

// СТАТУС "РАСКРОИЛИ"
$cut_status_id = 3;

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

$invalid_message = '';
$length_invalid_message = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'change-status-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    if(IsInRole(array('dev'))) {
        $supplier_id = filter_input(INPUT_POST, 'supplier_id');
        if(empty($supplier_id)) {
            $supplier_id_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if(IsInRole(array('dev'))) {
        $id_from_supplier = filter_input(INPUT_POST, 'id_from_supplier');
        if(empty($id_from_supplier)) {
            $id_from_supplier_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if(IsInRole(array('dev'))) {
        $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
        if(empty($film_brand_id)) {
            $film_brand_id_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if(IsInRole(array('dev'))) {
        $width = filter_input(INPUT_POST, 'width');
        if(empty($width)) {
            $width_valid = ISINVALID;
            $form_valid = false;
        }
        
        if(intval($width) < 50 || intval($width) > 1600) {
            $width_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if(IsInRole(array('dev'))) {
        $thickness = filter_input(INPUT_POST, 'thickness');
        if(empty($thickness)) {
            $thickness_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if(IsInRole(array('dev'))) {
        $length = filter_input(INPUT_POST, 'length');
        if(empty($length)) {
            $length_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if(IsInRole(array('dev'))) {
        $net_weight = filter_input(INPUT_POST, 'net_weight');
        if(empty($net_weight)) {
            $net_weight_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    // Проверяем правильность веса, для всех ролей
    // Определяем имеющуюся длину и ширину
    $sql = "select film_brand_id, thickness, length, width, net_weight from roll where id=$id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $old_film_brand_id = $row['film_brand_id'];
        $old_thickness = $row['thickness'];
        $old_length = $row['length'];
        $old_width = $row['width'];
        $old_net_weight = $row['net_weight'];
        
        $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
        if(empty($film_brand_id)) $film_brand_id = $old_film_brand_id;
        
        $thickness = filter_input(INPUT_POST, 'thickness');
        if(empty($thickness)) $thickness = $old_thickness;
        
        $length = filter_input(INPUT_POST, 'length');
        if(empty($length)) $length = $old_length;
        
        $width = filter_input(INPUT_POST, 'width');
        if(empty($width)) $width = $old_width;
        
        $net_weight = filter_input(INPUT_POST, 'net_weight');
        if(empty($net_weight)) $net_weight = $old_net_weight;
    }
        
    $weight_result = filter_input(INPUT_POST, 'net_weight_normal');
    $weight_result_high = $weight_result + ($weight_result * 15.0 / 100.0);
    $weight_result_low = $weight_result - ($weight_result * 15.0 / 100.0);
        
    if($net_weight < $weight_result_low || $net_weight > $weight_result_high) {
        $net_weight_valid = ISINVALID;
        $length_valid = ISINVALID;
        $form_valid = false;
        $invalid_message = "Неверное значение";
        $length_invalid_message = "Неверное значение";
    }
    
    if(IsInRole(array('dev', 'technologist', 'storekeeper'))) {
        $cell = filter_input(INPUT_POST, 'cell');
        if(empty($cell)) {
            $cell_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if(IsInRole(array('dev', 'technologist', 'storekeeper'))) {
        $status_id = filter_input(INPUT_POST, 'status_id');
        if(empty($status_id)) {
            if(empty($cell)) {
                $status_id_valid = ISINVALID;
                $form_valid = false;
            }
        }
    }
    
    $comment = addslashes(filter_input(INPUT_POST, 'comment'));
    $date = filter_input(INPUT_POST, 'date');
    $storekeeper_id = filter_input(INPUT_POST, 'storekeeper_id');
    
    if($form_valid) {
        // Получаем имеющийся статус и проверяем, совпадает ли он с новым статусом
        $sql = "select status_id from roll_status_history where roll_id=$id order by id desc limit 1";
        $row = (new Fetcher($sql))->Fetch();
        $status_id = filter_input(INPUT_POST, 'status_id');
        
        if((!$row || $row['status_id'] != $status_id) && !empty($status_id)) {
            $user_id = GetUserId();
            
            $sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($id, $status_id, $user_id)";
            $error_message = (new Executer($sql))->error;
        }
        
        if(empty($error_message)) {
            $sql = "update roll set ";
            if(IsInRole(array('dev'))) {
                $sql .= "supplier_id = $supplier_id, ";
            }
            
            if(IsInRole(array('dev'))) {
                $sql .= "id_from_supplier = '$id_from_supplier', ";
            }
            
            if(IsInRole(array('dev'))) {
                $sql .= "film_brand_id = $film_brand_id, ";
            }
            
            if(IsInRole(array('dev'))) {
                $sql .= "width = $width, ";
            }
            
            if(IsInRole(array('dev'))) {
                $sql .= "thickness = $thickness, ";
            }
            
            if(IsInRole(array('dev'))) {
                $sql .= "length = $length, ";
            }
            
            if(IsInRole(array('dev'))) {
                $sql .= "net_weight = $net_weight, ";
            }
            
            if(IsInRole(array('technologist', 'storekeeper'))) {
                $sql .= "cell = '$cell', ";
            }
            
            // Стирать старый комментарий может только технолог, остальные - только добавлять новый комментарий к старому
            if(IsInRole(array('dev', 'technologist', 'storekeeper'))) {
                $sql .= "comment = '$comment' where id=$id";
            }
            else {
                $sql .= "comment = concat(comment, ' ', '$comment') where id=$id";
            }
            
            $error_message = (new Executer($sql))->error;
        }
        
        if(empty($error_message)) {
            if(isset($status_id) && $status_id == $utilized_status_id) {
                header('Location: '.APPLICATION.'/utilized/');
            }
            elseif(isset($status_id) && $status_id == $cut_status_id) {
                header('Location: '.APPLICATION.'/cut_source/');
            }
            else {
                header('Location: '.APPLICATION.'/roll/');
            }
        }
    }
}

// Получение данных
$sql = "select DATE_FORMAT(r.date, '%d.%m.%Y') date, DATE_FORMAT(r.date, '%H:%i') time, r.storekeeper_id, u.last_name, u.first_name, r.supplier_id, r.id_from_supplier, r.film_brand_id, r.width, r.thickness, r.length, "
        . "r.net_weight, r.cell, "
        . "rsh.status_id status_id, DATE_FORMAT(rsh.date, '%d.%m.%Y') status_date, DATE_FORMAT(rsh.date, '%H.%i') status_time, "
        . "r.comment, r.is_unknown, r.cut_wind_id "
        . "from roll r "
        . "inner join user u on r.storekeeper_id = u.id "
        . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
        . "where r.id=$id";

$row = (new Fetcher($sql))->Fetch();
$date = $row['date'];
$time = $row['time'];
$storekeeper_id = $row['storekeeper_id'];
$storekeeper = $row['last_name'].' '.$row['first_name'];

$supplier_id = filter_input(INPUT_POST, 'supplier_id');
if(null === $supplier_id) $supplier_id = $row['supplier_id'];

$id_from_supplier = filter_input(INPUT_POST, 'id_from_supplier');
if(null === $id_from_supplier) $id_from_supplier = $row['id_from_supplier'];

$film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
if(null === $film_brand_id) $film_brand_id = $row['film_brand_id'];

$width = filter_input(INPUT_POST, 'width');
if(null === $width) $width = $row['width'];

$thickness = filter_input(INPUT_POST, 'thickness');
if(null === $thickness) $thickness = $row['thickness'];

$length = filter_input(INPUT_POST, 'length');
if(null === $length) $length = $row['length'];

$net_weight = filter_input(INPUT_POST, 'net_weight');
if(null === $net_weight) $net_weight = $row['net_weight'];

$cell = filter_input(INPUT_POST, 'cell');
if(null === $cell) $cell = $row['cell'];

$status_id = filter_input(INPUT_POST, 'status_id');
if(null === $status_id) $status_id = $row['status_id'];

$status_date = $row['status_date'];
$status_time = $row['status_time'];

$comment = filter_input(INPUT_POST, 'comment');
if(null === $comment) $comment = $row['comment'];

$is_unknown = $row['is_unknown'];

$cut_wind_id = $row['cut_wind_id'];
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
                echo "<div class='alert alert-danger>$error_message</div>";
            }
            
            // Если плёнка сработанная, то кнопка "Назад" переводит нас в раздел "Сработанная плёнка",
            // иначе - в раздел "Рулоны".
            if(isset($status_id) && $status_id == $utilized_status_id):
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/utilized/<?= BuildQueryRemove('id') ?>">Назад</a>
            <?php elseif(isset($status_id) && $status_id == $cut_status_id): ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/cut_source/<?= BuildQueryRemove('id') ?>">Назад</a>
            <?php else: ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/roll/<?= BuildQueryRemove('id') ?>">Назад</a>
            <?php endif; ?>
            <h1 style="font-size: 24px; font-weight: 600;">Информация о рулоне № <?="Р".$id ?> от <?= $date ?></h1>
            <?php if(!empty($time) && $time != '00:00'): ?>
            <div>Время добавления: <?=$time ?></div>
            <?php endif; ?>
            <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">ID <?=$id_from_supplier ?></h2>
            <form method="post">
                <div style="width: 423px;">
                    <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                    <input type="hidden" id="date" name="date" value="<?= $date ?>" />
                    <input type="hidden" id="storekeeper_id" name="storekeeper_id" value="<?= $storekeeper_id ?>" />
                    <input type="hidden" id="net_weight_normal" name="net_weight_normal" />
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
                        $id_from_supplier_disabled = " disabled='disabled'";
                        ?>
                        <label for="id_from_supplier">ID рулона от поставщика</label>
                        <input type="text" id="id_from_supplier" name="id_from_supplier" value="<?= $id_from_supplier ?>" class="form-control<?=$id_from_supplier_valid ?>" placeholder="Введите ID"<?=$id_from_supplier_disabled ?> />
                        <div class="invalid-feedback">ID паллета от поставщика обязательно</div>
                    </div>
                    <div class="form-group">
                        <?php
                        $film_brand_id_disabled = " disabled='disabled'";
                        ?>
                        <label for="film_brand_id">Марка пленки</label>
                        <select id="film_brand_id" name="film_brand_id" class="form-control<?=$film_brand_id_valid ?>"<?=$film_brand_id_disabled ?>>
                            <option value="">Выберите марку</option>
                            <?php
                            $film_brands = (new Grabber("select id, name from film_brand where supplier_id = $supplier_id"))->result;
                            foreach ($film_brands as $film_brand) {
                                $id = $film_brand['id'];
                                $name = $film_brand['name'];
                                $selected = '';
                                if($film_brand_id == $film_brand['id']) $selected = " selected='selected'";
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
                            $thickness_disabled = " disabled='disabled'";
                            ?>
                            <label for="thickness">Толщина, мкм</label>
                            <select id="thickness" name="thickness" class="form-control<?=$thickness_valid ?>"<?=$thickness_disabled ?>>
                                <option value="">Выберите толщину</option>
                                <?php
                                $film_brand_variations = (new Grabber("select thickness, weight from film_brand_variation where film_brand_id = $film_brand_id order by thickness"))->result;
                                foreach ($film_brand_variations as $film_brand_variation) {
                                    $weight = $film_brand_variation['weight'];
                                    $selected = '';
                                    if($thickness == $film_brand_variation['thickness']) $selected = " selected='selected'";
                                    echo "<option value='$thickness'$selected>$thickness мкм $weight г/м<sup>2</sup></option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Толщина обязательно</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <?php
                            $net_weight_disabled = '';
                            if(!IsInRole(array('dev'))) {
                                $net_weight_disabled = " disabled='disabled'";
                            }
                            ?>
                            <label for="net_weight">Масса нетто, кг</label>
                            <input type="text" id="net_weight" name="net_weight" value="<?= $net_weight ?>" class="form-control int-only<?=$net_weight_valid ?>" placeholder="Введите массу нетто"<?=$net_weight_disabled ?> />
                            <div class="invalid-feedback"><?= empty($invalid_message) ? "Масса нетто обязательно" : $invalid_message ?></div>
                        </div>
                        <div class="col-6 form-group">
                            <?php
                            $length_disabled = "";
                            if(!IsInRole(array('dev'))) {
                                $length_disabled = " disabled='disabled'";
                            }
                            ?>
                            <label for="length">Длина, м</label>
                            <input type="text" id="length" name="length" value="<?= $length ?>" class="form-control int-only<?=$length_valid ?>" placeholder="Введите длину"<?=$length_disabled ?>" />
                            <div class="invalid-feedback"><?= empty($length_invalid_message) ? "Длина обязательно" : $length_invalid_message ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <?php
                            $cell_disabled = "";
                            if(!IsInRole(array('technologist', 'storekeeper'))) {
                                $cell_disabled = " disabled='disabled'";
                            }
                            ?>
                            <label for="cell">Ячейка на складе</label>
                            <input type="text" id="cell" name="cell" value="<?= $cell ?>" class="form-control no-latin<?=$cell_valid ?>" placeholder="Введите ячейку"<?=$cell_disabled ?>" />
                            <div class="invalid-feedback">Ячейка на складе обязательно</div>
                        </div>
                        <div class="col-6 form-group"></div>
                    </div>
                    <div class="form-group d-none">
                        <?php
                        $manager_disabled = " disabled='disabled'";
                        ?>
                        <label for="manager_id">Менеджер</label>
                        <select id="manager_id" name="manager_id" class="form-control"<?=$manager_disabled ?>>
                            <option value="">Выберите менеджера</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <?php
                        $status_id_disabled = "";
                        if(!IsInRole(array('technologist', 'storekeeper'))) {
                            $status_id_disabled = " disabled='disabled'";
                        }
                        ?>
                        <label for="status_id">Статус</label>
                        <select id="status_id" name="status_id" class="form-control<?=$status_id_valid ?>" required="required"<?=$status_id_disabled ?>>
                            <?php
                            $statuses = (new Grabber("select s.id, s.name from roll_status s order by s.ordinal"))->result;
                            foreach ($statuses as $status) {
                                if(!(empty($status_id) && $status['id'] == $utilized_status_id)) { // Если статуса нет, то нельзя сразу поставить "Сработанный"
                                    $id = $status['id'];
                                    $name = $status['name'];
                                    $selected = '';
                                    if(empty($status_id)) $status_id = $free_status_id; // По умолчанию ставим статус "Свободный"
                                    if($status_id == $status['id']) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$name</option>";
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Статус обязательно</div>
                    </div>
                    <!-- Отображаем, в каких нарезках данный ролик участвовал -->
                    <?php
                    // Если этот рулон был раскроен
                    if($status_id == $cut_status_id):
                    ?>
                    <div class="form-group">
                        <label>Как резали:</label>
                        <br />
                        <div style="font-size: 1rem;">
                        <?=$status_date.' в '.$status_time ?><br />
                        <?php
                        $sql = "select cstr.width "
                                . "from cut_source cs "
                                . "inner join cut_stream cstr on cs.cut_id = cstr.cut_id "
                                . "where cs.roll_id = ". filter_input(INPUT_GET, 'id')." and is_from_pallet = 0";
                        $fetcher = new Fetcher($sql);
                        $result = "";
                        while ($row = $fetcher->Fetch()) {
                            if($result != "") {
                                $result .= " - ";
                            }
                            $result .= $row[0].' мм';
                        }
                        echo $result;
                        ?>
                        </div>
                    </div>
                    <?php
                    // Если этот рулон появился в результате нарезки
                    elseif(!empty($cut_wind_id)):
                    ?>
                    <div class="form-group">
                        <label>Получился из раскроя:</label>
                        <br />
                        <div style="font-size: 1rem;">
                        <?php
                        $sql = "select cstr.width, DATE_FORMAT(c.date, '%d.%m.%Y') date, DATE_FORMAT(c.date, '%H:%i') time "
                                . "from cut_wind cw "
                                . "inner join cut c on cw.cut_id = c.id "
                                . "inner join cut_stream cstr on cw.cut_id = cstr.cut_id "
                                . "where cw.id = $cut_wind_id";
                        $fetcher = new Fetcher($sql);
                        $result = "";
                        $date = "";
                        $time = "";
                        while ($row = $fetcher->Fetch()) {
                            if($result != "") {
                                $result .= " - ";
                            }
                            $result .= $row['width'].' мм';
                            $date = $row['date'];
                            $time = $row['time'];
                        }
                        echo "$date в $time<br />";
                        echo $result;
                        ?>
                        </div>
                    </div>
                    <?php
                    endif;
                    // Если этот рулон появился в результате нарезки
                    $sql = "select cs.width, DATE_FORMAT(c.date, '%d.%m.%Y') date, DATE_FORMAT(c.date, '%H:%i') time "
                            . "from cut c "
                            . "inner join cut_stream cs on cs.cut_id = c.id "
                            . "where c.remain = ". filter_input(INPUT_GET, 'id');
                    $grabber = new Grabber($sql);
                    if(count($grabber->result) > 0):
                    ?>
                    <div class="form-group">
                        <label>Остаток из раскроя:</label>
                        <br />
                        <div style="font-size: 1rem;">
                        <?php
                        $result = "";
                        $date = "";
                        $time = "";
                        foreach($grabber->result as $row) {
                            if($result != "") {
                                $result .= " - ";
                            }
                            $result .= $row['width'].' мм';
                            $date = $row['date'];
                            $time = $row['time'];
                        }
                        echo "$date в $time<br />";
                        echo $result;
                        ?>
                        </div>
                    </div>
                    <?php
                    endif;
                    ?>
                    <div class="form-group">
                        <?php
                        $comment_disabled = "";
                        if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
                            $comment_disabled = " disabled='disabled'";
                        }
                        
                        $comment_value = htmlentities($comment);
                        if(!IsInRole(array('technologist', 'dev', 'storekeeper'))) {
                            $comment_value = "";
                        }
                        ?>
                        <label for="comment">Комментарий</label>
                        <?php if(!IsInRole(array('technologist', 'dev', 'storekeeper'))): ?>
                        <p><?= htmlentities($comment) ?></p>
                        <?php endif; ?>
                        <textarea id="comment" name="comment" rows="4" class="form-control no-latin"<?=$comment_disabled ?>><?=$comment_value ?></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <?php
                    $is_unknown_checked = '';
                    if($is_unknown == 1) {
                        $is_unknown_checked = " checked='checked'";
                    }
                    ?>
                    <div class="form-group d-none">
                        <input type="checkbox" id="is_unknown" name="is_unknown"<?=$is_unknown_checked ?> disabled="disabled" />
                        <label class="form-check-label" for="is_unknown">Неизвестный</label>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <div class="p-0">
                            <button type="submit" id="change-status-submit" name="change-status-submit" class="btn btn-dark" style="width: 175px;">Сохранить</button>
                        </div>
                        <div class="p-0">
                            <?php if(IsInRole(array('technologist', 'dev', 'storekeeper'))): ?>
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
            
            // Все марки плёнки с их вариациями
            var films = new Map();
            
            <?php
            $sql = "SELECT fbv.film_brand_id, fbv.thickness, fbv.weight FROM film_brand_variation fbv";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()):
            ?>
            if(films.get(<?= $row['film_brand_id'] ?>) == undefined) {
                films.set(<?= $row['film_brand_id'] ?>, new Map());
            }
            films.get(<?= $row['film_brand_id'] ?>).set(<?= $row['thickness'] ?>, <?= $row['weight'] ?>);
            <?php        
            endwhile;
            ?>
                
            // Расчёт длины и массы плёнки по шпуле, толщине, радиусу, ширине, удельному весу
            function CalculateWeight() {
                film_brand_id = $('#film_brand_id').val();
                length = $('#length').val();
                width = $('#width').val();
                thickness = $('#thickness').val();
                
                if(!isNaN(length) && !isNaN(width) && !isNaN(thickness) && length != '' && width != '' && thickness != '') {
                    density = films.get(parseInt($('#film_brand_id').val())).get(parseInt(thickness));
                    weight = GetFilmWeightByLengthWidth(length, width, density);
                    $('#net_weight_normal').val(weight.toFixed(2));
                }
            }
            
            $(document).ready(CalculateWeight);
        </script>
    </body>
</html>