<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_AUDITOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/revision/');
}

// Валидация формы
$form_valid = true;
$error_message = '';

$rolls_valid = '';
$cell_valid = '';

// Обработка формы редактирования количества роликов и ячейки
if(null !== filter_input(INPUT_POST, 'save-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $rolls = filter_input(INPUT_POST, 'rolls');
    $cell = addslashes(filter_input(INPUT_POST, 'cell'));
    
    if(empty($rolls)) {
        $rolls_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty($cell)) {
        $cell_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $sql = "";
        $error_message = "";
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/revision/pallet.php?id='.$id);
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
                        <a class="nav-link" href="<?=APPLICATION ?>/revision/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            
            $sql = "select DATE_FORMAT(p.date, '%d.%m.%Y') date, s.name supplier, f.name film, p.width, fv.thickness, p.comment, "
                    . "(select sum(pr1.length) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) length, "
                    . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) weight "
                    . "from pallet p "
                    . "inner join supplier s on p.supplier_id=s.id "
                    . "inner join film_variation fv on p.film_variation_id=fv.id "
                    . "inner join film f on fv.film_id = f.id "
                    . "where p.id = $id";
            $fetcher = new Fetcher($sql);
            $row = $fetcher->Fetch();
            
            if($row):
            $date = $row['date'];
            $supplier = $row['supplier'];
            $film = $row['film'];
            $width = $row['width'];
            $thickness = $row['thickness'];
            $weight = $row['weight'];
            $length = $row['length'];
            $comment = htmlentities($row['comment']);
            
            $rolls = "";
            if(null !== filter_input(INPUT_POST, 'rolls')) {
                $rolls = filter_input(INPUT_POST, 'rolls');
            }
            
            $cell = "";
            if(null !== filter_input(INPUT_POST, 'cell')) {
                $cell = filter_input(INPUT_POST, 'cell');
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1>Паллет №П<?= filter_input(INPUT_GET, 'id') ?></h1>
                        <p>от <?=$date ?></p>
                        <p><strong>Поставщик:</strong> <?=$supplier ?></p>
                        <p class="mt-3"><strong>Характеристики</strong></p>
                        <p><strong>Марка пленки:</strong> <?=$film ?></p>
                        <p><strong>Ширина:</strong> <?=$width ?> мм</p>
                        <p><strong>Толщина:</strong> <?=$thickness ?> мкм</p>
                        <p><strong>Масса нетто:</strong> <?=$weight ?> кг</p>
                        <p><strong>Длина:</strong> <?=$length ?> м</p>
                        <p><strong>Комментарий:</strong></p>
                        <div style="white-space: pre-wrap;"><?=$comment ?></div>
                        <form method="post" class="mt-4">
                            <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                            <div class="row">
                            <div class="form-group col-6">
                                <label for="rolls">Рулонов</label>
                                <input type="text" id="rolls" name="rolls" value="<?=$rolls ?>" class="form-control int-only<?=$rolls_valid ?>" style="font-size: 32px;" required="required" autocomplete="off" />
                                <div class="invalid-feedback">Кол-во рулонов обязательно</div>
                            </div>
                            <div class="form-group col-6">
                                <label for="cell">Ячейка</label>
                                <input type="text" id="cell" name="cell" value="<?=$cell ?>" class="form-control no-latin<?=$cell_valid ?>" style="font-size: 32px;" required="required" autocomplete="off" />
                                <div class="invalid-feedback">Ячейка обязательно</div>
                            </div>
                                </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-dark form-control" id="save-submit" name="save-submit">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-danger">Объект не найден</div>
            <?php endif; ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
        <script>
            $('#rolls').focus();
        </script>
    </body>
</html>