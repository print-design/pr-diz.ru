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

// Получение объекта
$supplier_id = filter_input(INPUT_GET, 'id');
$name = '';
$film_brands = array();
$sql = "select s.name, fb.id film_brand_id, fb.name film_brand, fbv.id film_brand_variation_id, fbv.thickness, fbv.weight "
        . "from supplier s "
        . "inner join film_brand fb on fb.supplier_id = s.id "
        . "inner join film_brand_variation fbv on fbv.film_brand_id = fb.id "
        . "where s.id = $supplier_id";
$fetcher = new Fetcher($sql);
while ($row = $fetcher->Fetch()) {
    $name = $row['name'];
    
    $film_brand_id = $row['film_brand_id'];
    if(!isset($film_brands[$film_brand_id])) {
        $film_brands[$film_brand_id] = array('name' => $row['film_brand'], 'film_brand_variations' => array());
    }
    
    $film_brand_variation_id = $row['film_brand_variation_id'];
    if(!isset($film_brands[$film_brand_id]['film_brand_variations'][$film_brand_variation_id])) {
        $film_brands[$film_brand_id]['film_brand_variations'][$film_brand_variation_id] = array('thickness' => $row['thickness'], 'weight' => $row['weight']);
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
        <div id="create_film_brand" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Выберите марку пленки</p>
                            <button type="button" class="close create_create_film_brand_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <select class="form-control" id="film_brand_id" name="film_brand_id">
                                    <option value="" hidden="hidden">Новая пленка</option>
                                    <?php
                                    $sql = "select id, name from film_brand where supplier_id = $supplier_id order by name";
                                    $fetcher = new Fetcher($sql);
                                    while($row = $fetcher->Fetch()):
                                    ?>
                                    <option value="<?=$row['id'] ?>"><?=$row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" id="create_create_film_brand" name="create_supplier">Добавить</button>
                            <button type="button" class="btn btn-light create_create_film_brand_dismiss" data-dismiss="modal">Отменить</button>
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
                <div class="p-0">
                    <button class="btn btn-dark" data-toggle="modal" data-target="#create_film_brand">
                        <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить пленку
                    </button>
                </div>
            </div>
            <h1><?=$name ?></h1>
            <h2>Пленки</h2>
            <div class="row">
                <div class="col-12 col-md-8 col-lg-6">
                    <?php foreach($film_brands as $fb_key => $film_brand): ?>
                    <div class="film_brand">
                        <h3><?=$film_brand['name'] ?></h3>
                        <table class="table table-hover">
                            <tr style="border-top: 0;">
                                <th style="border-top: 0;">Толщина</th>
                                <th style="border-top: 0;">Удельный вес</th>
                                <th style="border-top: 0;"></th>
                            </tr>
                            <?php foreach($film_brand['film_brand_variations'] as $fbv_key => $film_brand_variation): ?>
                            <tr>
                                <td><?=$film_brand_variation['thickness'] ?></td>
                                <td><?=$film_brand_variation['weight'] ?></td>
                                <td class="text-right"><img src="../images/icons/trash2.svg" title="Удалить" /></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                        <form class="form-inline">
                            <input type="hidden" name="film_brand_id" value="<?=$fb_key ?>" />
                            <input type="hidden" id="scroll" name="scroll" />
                            <div class="d-flex justify-content-between mb-2 w-100">
                                <div class="form-group w-75">
                                    <select class="form-control w-100" name="film_brand_variation_id">
                                        <option hidden="hidden" value="">Выберите пленку для добавления</option>
                                        <?php foreach($film_brand['film_brand_variations'] as $fbv_key => $film_brand_variation): ?>
                                        <option value="<?=$fbv_key ?>">Толщина <?=$film_brand_variation['thickness'] ?> мкм, Удельный вес <?=$film_brand_variation['weight'] ?> г/м<sup>2</sup></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="film_brand_variation_submit" id="film_brand_variation_submit" class="btn btn-dark"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить</button>
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
    </body>
</html>