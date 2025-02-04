<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'electrocarist', 'auditor'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/car/');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$cell_valid = '';
$comment_valid = '';

// Обработка формы смены ячейки
if(null !== filter_input(INPUT_POST, 'cell-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $cell = addslashes(filter_input(INPUT_POST, 'cell'));
    
    if(empty($cell)) {
        $cell_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $sql = "update pallet set cell='$cell' where id=$id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            if(empty(filter_input(INPUT_GET, 'link'))) {
                header('Location: '.APPLICATION.'/car/pallet.php?id='.$id);
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
        $sql = "update pallet set comment='$comment' where id=$id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            if(empty(filter_input(INPUT_GET, 'link'))) {
                header('Location: '.APPLICATION.'/car/pallet.php?id='.$id);
            }
            else {
                header('Location: '.urldecode(filter_input(INPUT_GET, 'link')));
            }
        }
    }
}

// СТАТУС "СВОБОДНЫЙ"
$free_roll_status_id = 1;

// РОЛЬ "РЕВИЗОР"
const AUDITOR = 'auditor';
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
                        <a class="nav-link" href="<?=APPLICATION ?>/car/pallet.php?id=<?=$id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            
            $sql = "select DATE_FORMAT(p.date, '%d.%m.%Y') date, s.name supplier, f.name film, p.id_from_supplier, p.width, fv.thickness, p.cell, p.comment, "
                    . "(select sum(pr1.length) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
                    . (IsInRole(AUDITOR) ? '' : " and (prsh1.status_id is null or prsh1.status_id = $free_roll_status_id)")
                    . ") length, "
                    . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
                    . (IsInRole(AUDITOR) ? '' : " and (prsh1.status_id is null or prsh1.status_id = $free_roll_status_id)")
                    . ") weight, "
                    . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
                    . (IsInRole(AUDITOR) ? '' : " and (prsh1.status_id is null or prsh1.status_id = $free_roll_status_id)")
                    . ") rolls_number "
                    . "from pallet p "
                    . "inner join supplier s on p.supplier_id=s.id "
                    . "inner join film_variation fv on p.film_variation_id=fv.id "
                    . "inner join film f on fv.film_id = f.id "
                    . "where p.id=$id";
            $fetcher = new Fetcher($sql);
            $row = $fetcher->Fetch();
            
            if($row && $row['rolls_number']):
            $date = $row['date'];
            $supplier = $row['supplier'];
            $id_from_supplier = $row['id_from_supplier'];
            $film = $row['film'];
            $width = $row['width'];
            $thickness = $row['thickness'];
            $weight = $row['weight'];
            $length = $row['length'];
            $rolls_number = $row['rolls_number'];
            $cell = $row['cell'];
            $comment = htmlentities($row['comment']);
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1>Паллет №П<?= filter_input(INPUT_GET, 'id') ?></h1>
                        <p>от <?= $date ?></p>
                        <p><strong>Поставщик:</strong> <?=$supplier ?></p>
                        <p><strong>ID поставщика:</strong> <?=$id_from_supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки:</strong> <?=$film ?></p>
                        <p><strong>Ширина:</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина:</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто:</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина:</strong> <?=$length ?> м</p>
                        <p><strong>Количество рулонов:</strong> <?=$rolls_number ?></p>
                        <p><strong>Комментарий:</strong></p>
                        <div style="white-space: pre-wrap;"><?=$comment ?></div>
                        <?php if(IsInRole(array('electrocarist'))): ?>
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
                        <?php elseif(IsInRole(array('auditor'))): ?>
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
    </body>
</html>