<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ELECTROCARIST], ROLE_NAMES[ROLE_AUDITOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/car/');
}

// Валидация формы
$form_valid = true;
$error_message = '';

$cell_valid = '';
$comment_valid = '';

// Обработка формы смены ячейки
if(null !== filter_input(INPUT_POST, 'cell-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $cell = addslashes(filter_input(INPUT_POST, 'cell'));
    $user_id = GetUserId();
    
    if(empty($cell)) {
        $cell_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Проверяем, изменилось значение или нет.
        $old_cell = null;
        $sql = "select cell from roll_cell_history where roll_id = $id order by id desc";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        if($row = $fetcher->Fetch()) {
            $old_cell = $row['cell'];
        }
        
        if($cell != $old_cell) {
            $sql = "insert into roll_cell_history (roll_id, cell, user_id) values ($id, '$cell', $user_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message)) {
            if(empty(filter_input(INPUT_GET, 'link'))) {
                header('Location: '.APPLICATION.'/car/roll.php?id='.$id);
            }
            else {
                header('Location: '.urldecode(filter_input(INPUT_GET, 'link')));
            }
        }
    }
}

// Обработка формы добавления комментария
if(null !== filter_input(INPUT_POST, 'comment-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $old_comment = addslashes(filter_input(INPUT_POST, 'old_comment'));
    $comment = addslashes(filter_input(INPUT_POST, 'comment'));
    
    if(empty($comment)) {
        $comment_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(!empty($old_comment)) $comment = $old_comment.' '.$comment;
    
    if($form_valid) {
        $sql = "update roll set comment = '$comment' where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            if(empty(filter_input(INPUT_GET, 'link'))) {
                header('Location: '.APPLICATION.'/car/roll.php?id='.$id);
            }
            else {
                header('Location: '.urldecode(filter_input(INPUT_GET, 'link')));
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
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <?php if(empty(filter_input(INPUT_GET, 'link'))): ?>
                        <a class="nav-link" href="<?=APPLICATION ?>/car/roll.php?id=<?=$id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        <?php else: ?>
                        <a class="nav-link" href="<?= urldecode(filter_input(INPUT_GET, 'link')) ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            $sql = "select DATE_FORMAT(r.date, '%d.%m.%Y') date, s.name supplier, f.name film, r.width, fv.thickness, r.net_weight, r.length, rch.cell, r.comment "
                    . "from roll r "
                    . "inner join supplier s on r.supplier_id=s.id "
                    . "inner join film_variation fv on r.film_variation_id=fv.id "
                    . "inner join film f on fv.film_id = f.id "
                    . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                    . "left join (select * from roll_cell_history where id in (select max(id) from roll_cell_history group by roll_id)) rch on rch.roll_id = r.id "
                    . "where r.id=$id"
                    . (IsInRole(ROLE_NAMES[ROLE_AUDITOR]) ? '' : " and (rsh.status_id is null or rsh.status_id = ".ROLL_STATUS_FREE.")");
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()):
            $date = $row['date'];
            $supplier = $row['supplier'];
            $film = $row['film'];
            $width = $row['width'];
            $thickness = $row['thickness'];
            $weight = $row['net_weight'];
            $length = $row['length'];
            $cell = $row['cell'];
            $comment = htmlentities($row['comment']);
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1>Рулон №Р<?= filter_input(INPUT_GET, 'id') ?></h1>
                        <p>от <?= $date ?></p>
                        <p><strong>Поставщик:</strong> <?=$supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки:</strong> <?=$film ?></p>
                        <p><strong>Ширина:</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина:</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто:</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина:</strong> <?=$length ?> м</p>
                        <?php if(IsInRole(ROLE_NAMES[ROLE_AUDITOR])): ?>
                        <p><strong>Ячейка:</strong> <?= htmlentities($cell) ?></p>
                        <?php endif; ?>
                        <p><strong>Комментарий:</strong></p>
                        <div style="white-space: pre-wrap;"><?=$comment ?></div>
                        <?php if(IsInRole(ROLE_NAMES[ROLE_ELECTROCARIST])): ?>
                        <form method="post" class="mt-2">
                            <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                            <div class="form-group">
                                <label for="cell">Номер ячейки</label>
                                <input type="text" id="cell" name="cell" value="<?= htmlentities($cell) ?>" class="form-control no-latin" style="font-size: 32px;" required="required" autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-dark form-control" id="cell-submit" name="cell-submit">Сменить ячейку</button>
                            </div>
                        </form>
                        <?php elseif(IsInRole(ROLE_NAMES[ROLE_AUDITOR])): ?>
                        <form method="post" class="mt-2">
                            <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                            <input type="hidden" id="old_comment" name="old_comment" value="<?=$comment ?>" />
                            <div class="form-group">
                                <label for="comment"><strong>Новый комментарий:</strong></label>
                                <input type="text" id="comment" name="comment" class="form-control" style="font-size: 26px;" required="required" autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-dark form-control" id="comment-submit" name="comment-submit">Добавить комментарий</button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class='alert alert-danger'>Объект не найден</div>
            <?php endif; ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
        <script>
            <?php if(IsInRole(ROLE_NAMES[ROLE_ELECTROCARIST])): ?>
                $('#cell').prop('selectionStart', $('#cell').val().length);
                $('#cell').prop('selectionEnd', $('#cell').val().length);
                $('#cell').focus();
            <?php elseif(IsInRole(ROLE_NAMES[ROLE_AUDITOR])): ?>
                $('#comment').focus();
            <?php endif; ?>
        </script>
    </body>
</html>