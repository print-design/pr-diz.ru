<?php
include '../include/topscripts.php';

// Пекренаправление на страницу карщика или резчика при чтении QR-кода
if(IsInRole(ROLE_NAMES[ROLE_ELECTROCARIST])) {
    header('Location: '.APPLICATION.'/car/pallet_roll_edit.php?id='. filter_input(INPUT_GET, 'id'));
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

$cell_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'change-status-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    // Получаем имеющуюся ячейку и проверяем, совпадает ли она с новой ячейкой
    $sql = "select cell from pallet_cell_history where pallet_id = (select pallet_id from pallet_roll where id = $id) order by id desc limit 1";
    $row = (new Fetcher($sql))->Fetch();
    $cell = filter_input(INPUT_POST, 'cell');
            
    if((!$row || $row['cell'] != $cell) && !empty($cell)) {
        $user_id = GetUserId();
            
        $sql = "insert into pallet_cell_history (pallet_id, cell, user_id) values ((select pallet_id from pallet_roll where id = $id), '$cell', $user_id)";
        $error_message = (new Executer($sql))->error;
    }
    
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
        
        if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER]))) {
            $cell = filter_input(INPUT_POST, 'cell');
            if(empty($cell)) {
                $cell_valid = ISINVALID;
                $form_valid = false;
            }
        }
        
        $comment = addslashes(filter_input(INPUT_POST, 'comment'));
        
        if($form_valid) {
            $sql = "";
            
            if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER]))) {
                $sql .= "update pallet set comment = '$comment' where id = $pallet_id";
            }
            else {
                $sql .= "update pallet set comment = concat(comment, ' ', '$comment') where id = $pallet_id";
            }

            $executer = new Executer($sql);
            $error_message = $executer->error;
        
            if(empty($error_message)) {
                if($row['status_id'] == ROLL_STATUS_UTILIZED) {
                    header('Location: '.APPLICATION.'/utilized/'.BuildQueryRemove('id'));
                }
                elseif($row['status_id'] == ROLL_STATUS_CUT) {
                    header('Location: '.APPLICATION.'/cut_source/'.BuildQueryRemove('id'));
                }
                else {
                    header('Location: '.APPLICATION.'/pallet/'.BuildQueryRemove('id'));
                }
            }
        }
    }
}

// Получение данных
$sql = "select DATE_FORMAT(p.date, '%d.%m.%Y') date, DATE_FORMAT(p.date, '%H:%i') time, p.storekeeper_id, u.last_name, u.first_name, p.supplier_id, p.film_variation_id, p.width, pr.length, "
        . "(select film_id from film_variation where id = p.film_variation_id) film_id, "
        . "pr.weight, pr.pallet_id, pr.ordinal, (select cell from pallet_cell_history where pallet_id = p.id order by id desc limit 0, 1) cell, "
        . "prsh.status_id status_id, DATE_FORMAT(prsh.date, '%d.%m.%Y') status_date, DATE_FORMAT(prsh.date, '%H.%i') status_time, "
        . "p.comment "
        . "from pallet p "
        . "inner join user u on p.storekeeper_id = u.id "
        . "inner join pallet_roll pr on pr.pallet_id = p.id "
        . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
        . "where pr.id = $id";

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

$status_date = $row['status_date'];
$status_time = $row['status_time'];

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
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            // Если плёнка сработанная, то кнопка "Назад" переводит нас в раздел "Сработанная плёнка",
            // если плёнка раскроенная, то кнопка "Назад" переводит нас в раздел "Раскроили"
            // иначе - в раздел "Паллеты".
            if(isset($status_id) && $status_id == ROLL_STATUS_UTILIZED):
            ?>
            <a class="btn btn-light backlink" href="<?=APPLICATION ?>/utilized/<?= BuildQueryRemove('id') ?>">Назад</a>
            <?php elseif (isset($status_id) && $status_id == ROLL_STATUS_CUT): ?>
            <a class="btn btn-light backlink" href="<?=APPLICATION ?>/cut_source/<?= BuildQueryRemove('id') ?>">Назад</a>
            <?php else: ?>
            <a class="btn btn-light backlink" href="<?=APPLICATION ?>/pallet/<?= BuildQueryRemove('id') ?>">Назад</a>
            <?php endif; ?>
            <button class="btn btn-light ml-4 mb-2 mt-1" data-toggle="modal" data-target="#history"><i class="fas fa-history"></i>&nbsp;&nbsp;&nbsp;История</button>
            <h1 style="font-size: 24px; font-weight: 600;">Информация о рулоне из паллета № <?="П".$pallet_id."Р".$ordinal ?> от <?= $date ?></h1>
            <?php if(!empty($time) && $time != '00:00'): ?>
            <div>Время добавления: <?=$time ?></div>
            <?php endif; ?>
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
                        $film_id_disabled = " disabled='disabled'";
                        ?>
                        <label for="film_id">Марка пленки</label>
                        <select id="film_id" name="film_id" class="form-control"<?=$film_id_disabled ?>>
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
                            <input type="text" id="width" name="width" value="<?= $width ?>" class="form-control int-only" placeholder="Введите ширину"<?=$width_disabled ?> />
                            <div class="invalid-feedback">От 50 до 1600</div>
                        </div>
                        <div class="col-6 form-group">
                            <?php
                            $film_variation_id_disabled = " disabled='disabled'";
                            ?>
                            <label for="film_variation_id">Толщина, мкм</label>
                            <select id="film_variation_id" name="film_variation_id" class="form-control"<?=$film_variation_id_disabled ?>>
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
                            <input type="text" id="net_weight" name="net_weight" value="<?= $net_weight ?>" class="form-control int-only" placeholder="Введите массу нетто"<?=$net_weight_disabled ?> />
                            <div class="invalid-feedback"><?= empty($invalid_message) ? "Масса нетто обязательно" : $invalid_message ?></div>
                        </div>
                        <div class="col-6 form-group">
                            <?php
                            $length_disabled = " disabled='disabled'";
                            ?>
                            <label for="length">Длина, м</label>
                            <input type="text" id="length" name="length" value="<?= $length ?>" class="form-control int-only" placeholder="Введите длину"<?=$length_disabled ?> />
                            <div class="invalid-feedback">Длина обязательно</div>
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
                        <div class="col-6 form-group"></div>
                    </div>
                    <div class="form-group">
                        <?php
                        $status_id_disabled = "";
                        if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER]))) {
                            $status_id_disabled = " disabled='disabled'";
                        }
                        ?>
                        <label for="status_id">Статус</label>
                        <select id="status_id" name="status_id" class="form-control" required="required"<?=$status_id_disabled ?>>
                            <?php
                            foreach(ROLL_STATUSES as $status):
                                if(!(empty($status_id) && $status == ROLL_STATUS_UTILIZED)) { // Если статуса нет, то нельзя сразу поставить "Сработано".
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
                    <!-- Отображаем, в каких нарезках данный ролик участвовал -->
                    <?php
                    // Если этот рулон был раскроен
                    if($status_id == ROLL_STATUS_CUT):
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
                                . "where cs.roll_id = ". filter_input(INPUT_GET, 'id')." and cs.is_from_pallet = 1";
                        $fetcher = new Fetcher($sql);
                        $result = "";
                        while ($row = $fetcher->Fetch()) {
                            if($result != "") {
                                $result .= " - ";
                            }
                            $result .= $row[0].' мм';
                        }
                        echo $result;
                        
                        $sql = "select cstr.width "
                                . "from cutting_source cs "
                                . "inner join cutting_stream cstr on cs.cutting_id = cstr.cutting_id "
                                . "where cs.roll_id = ". filter_input(INPUT_GET, 'id')." and cs.is_from_pallet = 1";
                        $fetcher = new Fetcher($sql);
                        $result = "";
                        while($row = $fetcher->Fetch()) {
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
                    endif;
                    ?>
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
                        <textarea id="comment" name="comment" rows="4" class="form-control"<?=$comment_disabled ?>><?= $comment_value ?></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <div class="p-0">
                            <button type="submit" id="change-status-submit" name="change-status-submit" class="btn btn-dark" style="width: 175px;">Сохранить</button>
                        </div>
                        <div class="p-0">
                            <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER]))): ?>
                            <a href="roll_print.php?id=<?= filter_input(INPUT_GET, 'id') ?>" class="btn btn-outline-dark" style="width: 175px;">Распечатать бирку</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- История -->
        <div class="modal fixed-left fade" id="history" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-aside" role="document">
                <div class="modal-content" style="padding-left: 25px; padding-right: 25px; padding-top: 25px; width: 521px; overflow-y: auto;">
                    <h2>История перемещения</h2>
                    <button type="button" class="close" title="Закрыть" data-dismiss='modal' style="position: absolute; right: 10px; top: 10px; z-index: 2000;"><img src="../images/icons/close_modal_red.svg" /></button>
                    <table class="table">
                        <?php
                        $sql = "select u.last_name, left(u.first_name, 1) first_name, date_format(pch.date, '%d.%m.%Y %H:%i') date, pch.cell "
                                . "from pallet_cell_history pch "
                                . "inner join user u on pch.user_id = u.id "
                                . "where pch.pallet_id = $pallet_id";
                        $fetcher = new Fetcher($sql);
                        while($row = $fetcher->Fetch()):
                        ?>
                        <tr><td><?=$row['last_name'].' '.$row['first_name'].'.' ?></td><td><?=$row['date'] ?></td><td>Ячейка: <?=$row['cell'] ?></td></tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            if($('.is-invalid').first() !== null) {
                $('.is-invalid').first().focus();
            }
        </script>
    </body>
</html>