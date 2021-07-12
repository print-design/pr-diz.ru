<?php
include '../include/topscripts.php';

// СТАТУС "СВОБОДНЫЙ" ДЛЯ РУЛОНА
$free_status_id = 1;

// СТАТУС "СРАБОТАННЫЙ" ДЛЯ РУЛОНА
$utilized_status_id = 2;

// Пекренаправление на страницу карщика или резчика при чтении QR-кода
if(IsInRole(array('electrocarist'))) {
    header('Location: '.APPLICATION.'/car/pallet_roll_edit.php?id='. filter_input(INPUT_GET, 'id'));
}

if(IsInRole(array('cutter'))) {
    header('Location: '.APPLICATION.'/cut/pallet_roll.php?id='. filter_input(INPUT_GET, 'id'));
}

// Авторизация
elseif(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на список
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/pallet/');
}

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'change-status-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    // Получаем имеющийся статус и проверяем, совпадает ли он с новым статусом
    $sql = "select status_id from pallet_roll_status_history where pallet_roll_id=$id order by id desc limit 1";
    $row = (new Fetcher($sql))->Fetch();
    $status_id = filter_input(INPUT_POST, 'status_id');
    
    if((!$row || $row['status_id'] != $status_id) && !empty($status_id)) {
        $user_id = GetUserId();
        
        $sql = "insert into pallet_roll_status_history (pallet_roll_id, status_id, user_id) values ($id, $status_id, $user_id)";
        $error_message = (new Executer($sql))->error;
    }
    
    if(empty($error_message)) {
        // Редактирование данных паллета
        $pallet_id = filter_input(INPUT_POST, 'pallet_id');
        $sql = "";
        
        if(!empty(filter_input(INPUT_POST, 'cell'))) {
            $cell = filter_input(INPUT_POST, 'cell');
            
            if(!empty($sql)) {
                $sql .= ", ";
            }
            
            $sql .= "cell='$cell'";
        }
        if(!empty(filter_input(INPUT_POST, 'comment'))) {
            $comment = addslashes(filter_input(INPUT_POST, 'comment'));
            
            if(!empty($sql)) {
                $sql .= ", ";
            }
            
            if(IsInRole(array('dev', 'technologist', 'storekeeper'))) {
                $sql .= "comment='$comment'";
            }
            else {
                $sql .= "comment=concat(comment, ' ', '$comment')";
            }
        }
        
        $sql = "update pallet set $sql where id=$pallet_id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/pallet/'. BuildQueryRemove('id'));
        }
    }
}

// Получение данных
$sql = "select DATE_FORMAT(p.date, '%d.%m.%Y') date, DATE_FORMAT(p.date, '%H:%i') time, p.storekeeper_id, u.last_name, u.first_name, p.supplier_id, p.id_from_supplier, p.film_brand_id, p.width, p.thickness, pr.length, "
        . "pr.weight, pr.pallet_id, pr.ordinal, p.cell, "
        . "(select ifnull(prsh.status_id, $free_status_id) from pallet_roll_status_history prsh where prsh.pallet_roll_id = pr.id order by prsh.id desc limit 0, 1) status_id, "
        . "p.comment "
        . "from pallet p "
        . "inner join user u on p.storekeeper_id = u.id "
        . "inner join pallet_roll pr on pr.pallet_id = p.id "
        . "where pr.id=$id";

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

$weight = filter_input(INPUT_POST, 'weight');
if(null === $weight) $net_weight = $row['weight'];

$pallet_id = filter_input(INPUT_POST, 'pallet_id');
if(null === $pallet_id) $pallet_id = $row['pallet_id'];

$ordinal = filter_input(INPUT_POST, 'ordinal');
if(null === $ordinal) $ordinal = $row['ordinal'];

$cell = filter_input(INPUT_POST, 'cell');
if(null === $cell) $cell = $row['cell'];

$status_id = filter_input(INPUT_POST, 'status_id');
if(null === $status_id) $status_id = $row['status_id'];

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
        <div class="container-fluid" style="padding-left: 40px;">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger>$error_message</div>";
            }
            
            // Если плёнка сработанная, то кнопка "Назад" переводит нас в раздел "Сработанная плёнка",
            // иначе - в раздел "Паллеты".
            if(isset($status_id) && $status_id == $utilized_status_id):
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/utilized/<?= BuildQueryRemove('id') ?>">Назад</a>
            <?php else: ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/pallet/<?= BuildQueryRemove('id') ?>">Назад</a>
            <?php endif; ?>
            <h1 style="font-size: 24px; font-weight: 600;">Информация о рулоне № <?="П".$pallet_id."Р".$ordinal ?> от <?= $date ?></h1>
            <?php if(!empty($time) && $time != '00:00'): ?>
            <div>Время добавления: <?=$time ?></div>
            <?php endif; ?>
            <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">ID <?=$id_from_supplier ?></h2>
            <form method="post">
                <div style="width: 423px;">
                    <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                    <input type="hidden" id="date" name="date" value="<?= $date ?>" />
                    <input type="hidden" id="storekeeper_id" name="storekeeper_id" value="<?= $storekeeper_id ?>" />
                    <input type="hidden" id="pallet_id" name="pallet_id" value="<?= $pallet_id ?>" />
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
                        <select id="supplier_id" name="supplier_id" class="form-control"<?=$supplier_id_disabled ?>>
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
                        <input type="text" id="id_from_supplier" name="id_from_supplier" value="<?= $id_from_supplier ?>" class="form-control" placeholder="Введите ID"<?=$id_from_supplier_disabled ?> />
                        <div class="invalid-feedback">ID паллета от поставщика обязательно</div>
                    </div>
                    <div class="form-group">
                        <?php
                        $film_brand_id_disabled = " disabled='disabled'";
                        ?>
                        <label for="film_brand_id">Марка пленки</label>
                        <select id="film_brand_id" name="film_brand_id" class="form-control"<?=$film_brand_id_disabled ?>>
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
                            <input type="text" id="width" name="width" value="<?= $width ?>" class="form-control int-only" placeholder="Введите ширину"<?=$width_disabled ?> />
                            <div class="invalid-feedback">От 50 до 1600</div>
                        </div>
                        <div class="col-6 form-group">
                            <?php
                            $thickness_disabled = " disabled='disabled'";
                            ?>
                            <label for="thickness">Толщина, мкм</label>
                            <select id="thickness" name="thickness" class="form-control"<?=$thickness_disabled ?>>
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
                            $net_weight_disabled = " disabled='disabled'";
                            ?>
                            <label for="net_weight">Масса нетто, кг</label>
                            <input type="text" id="net_weight" name="net_weight" value="<?= $net_weight ?>" class="form-control int-only" placeholder="Введите массу нетто"<?=$net_weight_disabled ?> />
                            <div class="invalid-feedback"><?= empty($invalid_message) ? "Масса нетто обязательно" : $invalid_message ?></div>
                        </div>
                        <div class="col-6 form-group">
                            <?php
                            $length_disabled = " disabled='disabled'";
                            ?>
                            <label for="length">Длина, м</label>
                            <input type="text" id="length" name="length" value="<?= $length ?>" class="form-control int-only" placeholder="Введите длину"<?=$length_disabled ?>" />
                            <div class="invalid-feedback">Длина обязательно</div>
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
                            <input type="text" id="cell" name="cell" value="<?= $cell ?>" class="form-control no-latin" placeholder="Введите ячейку"<?=$cell_disabled ?>" />
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
                        <select id="status_id" name="status_id" class="form-control" required="required"<?=$status_id_disabled ?>>
                            <?php
                            $statuses = (new Grabber("select s.id, s.name from roll_status s order by s.name"))->result;
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
                        <textarea id="comment" name="comment" rows="4" class="form-control no-latin"<?=$comment_disabled ?>><?= $comment_value ?></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <div class="p-0">
                            <button type="submit" id="change-status-submit" name="change-status-submit" class="btn btn-dark" style="width: 175px;">Сохранить</button>
                        </div>
                        <div class="p-0">
                            <?php if(IsInRole(array('technologist', 'dev', 'storekeeper'))): ?>
                            <a href="roll_print.php?id=<?= filter_input(INPUT_GET, 'id') ?>" class="btn btn-outline-dark" style="width: 175px;">Распечатать бирку</a>
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