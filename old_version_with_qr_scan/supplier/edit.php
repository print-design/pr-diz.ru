<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на список
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION.'/supplier/');
}

// Обработка добавления вариации плёнки
$form_valid = true;

if(null !== filter_input(INPUT_POST, 'film_variation_submit')) {
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    $film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
    
    if(empty($film_variation_id)) {
        $error_message = "Не выбрана пленка";
        $form_valid = false;
    }
    
    if($form_valid) {
        $sql = "insert into supplier_film_variation(supplier_id, film_variation_id) values($supplier_id, $film_variation_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Обработка удаления вариации плёнки
if(null !== filter_input(INPUT_POST, 'delete_submit')) {
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    $film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
    $sql = "delete from supplier_film_variation where supplier_id = $supplier_id and film_variation_id = $film_variation_id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Получение объекта
$supplier_id = filter_input(INPUT_GET, 'id');

// Название поставщика
$name = '';
$sql = "select name from supplier where id = $supplier_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $name = $row['name'];
}

$films = array();

// Вариации, относящиеся к данному поставщику
$sql = "select f.id film_id, f.name film, fv.id film_variation_id, fv.thickness, fv.weight "
        . "from film f "
        . "inner join film_variation fv on fv.film_id = f.id "
        . "where fv.id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id) ";

if(null !== filter_input(INPUT_POST, 'create_film_submit')) {
    $post_film_id = filter_input(INPUT_POST, 'film_id');
    
    if(!empty($post_film_id)) {
        $sql .= "union "
                . "select f.id film_id, f.name film, null film_variation_id, null thickness, null weight "
                . "from film f "
                . "where f.id = $post_film_id ";
    }
}

$sql .= "order by film, thickness, weight";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    $film_id = $row['film_id'];
    if(!isset($films[$film_id])) {
        $films[$film_id] = array('name' => $row['film'], 'film_variations' => array(), 'my_film_variations' => array());
    }
    
    $film_variation_id = $row['film_variation_id'];
    if(!empty($film_variation_id) && !isset($films[$film_id]['my_film_variations'][$film_variation_id])) {
        $films[$film_id]['my_film_variations'][$film_variation_id] = array('thickness' => $row['thickness'], 'weight' => $row['weight']);
    }
}

// Вариации, не относящиеся к данному поставщику (для выбора из списка)
$sql = "select f.id film_id, f.name film, fv.id film_variation_id, fv.thickness, fv.weight "
        . "from film f "
        . "inner join film_variation fv on fv.film_id = f.id "
        . "where f.id in (select film_id from film_variation where id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id)) "
        . "and fv.id not in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id) ";

if(null !== filter_input(INPUT_POST, 'create_film_submit')) {
    $post_film_id = filter_input(INPUT_POST, 'film_id');
    
    if(!empty($post_film_id)) {
        $sql .= "union "
                . "select f.id film_id, f.name film, fv.id film_variation_id, fv.thickness, fv.weight "
                . "from film f "
                . "inner join film_variation fv on fv.film_id = f.id "
                . "where f.id = $post_film_id ";
    }
}

$sql .= "order by thickness, weight";

$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    $film_id = $row['film_id'];
    if(!isset($films[$film_id])) {
        $films[$film_id] = array('name' => $row['film'], 'film_variations' => array(), 'my_film_variations' => array());
    }
    
    $film_variation_id = $row['film_variation_id'];
    if(!isset($films[$film_id]['film_variations'][$film_variation_id])) {
        $films[$film_id]['film_variations'][$film_variation_id] = array('thickness' => $row['thickness'], 'weight' => $row['weight']);
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            .film_brand {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 30px;
                margin-top: 30px;
                margin-bottom: 40px;
            }
            
            .modal-content {
                border-radius: 20px;
            }
            
            .modal-header {
                border-bottom: 0;
                padding-bottom: 0;
            }
            
            .modal-footer {
                border-top: 0;
                padding-top: 0;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_admin.php';
        ?>
        <div id="create_film" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Выберите марку пленки</p>
                            <button type="button" class="close create_create_film_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <select class="form-control" id="film_id" name="film_id" required="required">
                                    <option value="" hidden="hidden">Новая пленка</option>
                                    <?php
                                    $sql = "select id, name "
                                            . "from film "
                                            . "where id not in (select film_id from film_variation where id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id)) "
                                            . "order by name";
                                    $fetcher = new Fetcher($sql);
                                    while($row = $fetcher->Fetch()):
                                    ?>
                                    <option value="<?=$row['id'] ?>"><?=$row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" id="create_film_submit" name="create_film_submit">Добавить</button>
                            <button type="button" class="btn btn-light create_create_film_dismiss" data-dismiss="modal">Отменить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-0">
                    <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/supplier/">Назад</a>
                </div>
                <div class="pt-1">
                    <button class="btn btn-dark" data-toggle="modal" data-target="#create_film">
                        <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить пленку
                    </button>
                </div>
            </div>
            <h1><?=$name ?></h1>
            <h2>Пленки</h2>
            <div class="row">
                <div class="col-12 col-md-8 col-lg-6">
                    <?php foreach($films as $f_key => $film): ?>
                    <div class="film_brand">
                        <h3 id="f_<?=$f_key ?>"><?=$film['name'] ?></h3>
                        <table class="table">
                            <tr style="border-top: 0;">
                                <th style="border-top: 0;">Толщина</th>
                                <th style="border-top: 0;">Удельный вес</th>
                                <th style="border-top: 0;"></th>
                            </tr>
                            <?php foreach($film['my_film_variations'] as $fv_key => $film_variation): ?>
                            <tr>
                                <td><?=$film_variation['thickness'] ?></td>
                                <td><?=$film_variation['weight'] ?></td>
                                <td class="text-right">
                                    <form method="post">
                                        <input type="hidden" name="supplier_id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                                        <input type="hidden" name="film_variation_id" value="<?=$fv_key ?>" />
                                        <input type="hidden" name="scroll" id="scroll" />
                                        <button type="submit" name="delete_submit" id="delete_submit" class="btn btn-link confirmable"><img src="../images/icons/trash2.svg" title="Удалить" /></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                        <form class="form-inline" method="post">
                            <input type="hidden" name="film_id" value="<?=$f_key ?>" />
                            <input type="hidden" name="supplier_id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                            <input type="hidden" id="scroll" name="scroll" />
                            <div class="d-flex justify-content-between mb-2 w-100">
                                <div class="form-group w-75">
                                    <select class="form-control w-100" name="film_variation_id" required="required">
                                        <option hidden="hidden" value="">Выберите пленку для добавления</option>
                                        <?php foreach($film['film_variations'] as $fv_key => $film_variation): ?>
                                        <option value="<?=$fv_key ?>">Толщина <?=$film_variation['thickness'] ?> мкм, Удельный вес <?=$film_variation['weight'] ?> г/м<sup>2</sup></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="film_variation_submit" id="film_variation_submit" class="btn btn-dark"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('#create_film').on('hidden.bs.modal', function() {
                $('select#film_id').val('');
            });
            
            <?php if(null !== filter_input(INPUT_POST, 'create_film_submit') && !empty(filter_input(INPUT_POST, 'film_id'))): ?>
            window.scrollTo(0, $('#f_<?= filter_input(INPUT_POST, 'film_id') ?>').offset().top - $('#topmost').height());
            <?php endif; ?>
        </script>
    </body>
</html>