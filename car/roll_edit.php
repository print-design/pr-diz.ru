<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'electrocarist'))) {
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

// Обработка формы смены ячейки
if(null !== filter_input(INPUT_POST, 'cell-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $cell = addslashes(filter_input(INPUT_POST, 'cell'));
    
    if(empty($cell)) {
        $cell_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $sql = "update roll set cell='$cell' where id=$id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            if(empty(filter_input(INPUT_GET, 'link'))) {
                header('Location: '.APPLICATION.'/car/roll.php?id='.$id);
            }
            else {
                header('Location: '.filter_input(INPUT_GET, 'link'));
            }
        }
    }
}

// СТАТУС "СРАБОТАННЫЙ" ДЛЯ РУЛОНА
$utilized_status_id = 2;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '_style.php';
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
                        <a class="nav-link" href="<?= filter_input(INPUT_GET, 'link') ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            
            $sql = "select r.date, s.name supplier, fb.name film_brand, r.id_from_supplier, r.width, r.thickness, r.net_weight, r.length, r.cell, r.comment "
                    . "from roll r "
                    . "inner join supplier s on r.supplier_id=s.id "
                    . "inner join film_brand fb on r.film_brand_id=fb.id "
                    . "where r.id=$id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()):
            $date = $row['date'];
            $supplier = $row['supplier'];
            $id_from_supplier = $row['id_from_supplier'];
            $film_brand = $row['film_brand'];
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
                        <p>от <?= DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y') ?></p>
                        <p><strong>Поставщик</strong> <?=$supplier ?></p>
                        <p><strong>ID поставщика</strong> <?=$id_from_supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки</strong> <?=$film_brand ?></p>
                        <p><strong>Ширина</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина</strong> <?=$length ?> м</p>
                        <p class="mt-3"><strong>Комментарий</strong></p>
                        <p><?=$comment ?></p>
                        <form method="post" class="mt-3">
                            <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                            <div class="form-group">
                                <label for="cell">Номер ячейки</label>
                                <input type="text" 
                                       id="cell" 
                                       name="cell" 
                                       value="<?= htmlentities($cell) ?>" 
                                       class="form-control no-latin" 
                                       style="font-size: 32px;"
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                       onmouseup="javascript: $(this).attr('id', 'cell'); $(this).attr('name', 'cell');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'cell'); $(this).attr('name', 'cell');" 
                                       onfocusout="javascript: $(this).attr('id', 'cell'); $(this).attr('name', 'cell');" />
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-dark form-control" id="cell-submit" name="cell-submit">Сменить ячейку</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class='alert alert-danger'>Объект не найден</div>
            <?php endif; ?>
        </div>
        <?php
        include '../include/footer.php';
        include '_footer.php';
        ?>
    </body>
</html>