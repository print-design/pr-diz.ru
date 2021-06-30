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
            header('Location: '.APPLICATION.'/car/roll.php?id='.$id);
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
        <style>
            body {
                padding-left: 0;
            }
            
            .container-fluid {
                padding-left: 15px;
            }
            
            @media (min-width: 768px) {
                body {
                    padding-left: 60px;
                }
            }
            
            td {
                height: 1.8rem;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?=APPLICATION ?>/car/roll.php?id=<?=$id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            
            include '_find.php';
            
            $sql = "select s.name supplier, fb.name film_brand, r.width, r.thickness, r.net_weight, r.length, r.cell, r.comment "
                    . "from roll r "
                    . "inner join supplier s on r.supplier_id=s.id "
                    . "inner join film_brand fb on r.film_brand_id=fb.id "
                    . "where r.id=$id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $supplier = $row['supplier'];
                $film_brand = $row['film_brand'];
                $width = $row['width'];
                $thickness = $row['thickness'];
                $weight = $row['net_weight'];
                $length = $row['length'];
                $cell = $row['cell'];
                $comment = htmlentities($row['comment']);
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <h1>Рулон №Р<?= filter_input(INPUT_GET, 'id') ?></h1>
                    <table class="w-100 characteristics">
                        <tr>
                            <td class="font-weight-bold">Поставщик</td>
                            <td><?=$supplier ?></td>
                        </tr>
                    </table>
                    <h2>Характеристики</h2>
                    <table class="w-100 characteristics">
                        <tr>
                            <td class="font-weight-bold">Марка пленки</td>
                            <td><?=$film_brand ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Ширина</td>
                            <td><?=$width ?> мм</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Толщина</td>
                            <td><?=$thickness ?> мкм</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Масса нетто</td>
                            <td><?=$weight ?> кг</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Длина</td>
                            <td><?=$length ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Комментарий</td>
                            <td><?=$comment ?></td>
                        </tr>
                    </table>
                    <form method="post" class="mt-3">
                        <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                        <div class="form-group">
                            <label for="cell">Номер ячейки</label>
                            <input type="text" 
                                   id="cell" 
                                   name="cell" 
                                   value="<?= htmlentities($cell) ?>" 
                                   class="form-control"  
                                   style="font-size: x-large;"
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                   onmouseup="javascript: $(this).attr('id', 'cell'); $(this).attr('name', 'cell');" 
                                   onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
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
        <?php
        include '../include/footer.php';
        include '_footer.php';
        ?>
    </body>
</html>