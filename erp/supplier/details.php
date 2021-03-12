<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на список
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION.'/supplier/');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

// создание марки плёнки
$name_valid = '';

// создание вариации
$thickness_valid = '';
$weight_valid = '';

// Обработка отправки формы создания марки пленки
if(null !== filter_input(INPUT_POST, 'film_brand_create_submit')) {
    $name = filter_input(INPUT_POST, 'name');
    if(empty($name)) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $thickness = filter_input(INPUT_POST, 'thickness');
    if(empty($thickness)) {
        $thickness_valid = ISINVALID;
        $form_valid = false;
    }
    
    $weight = filter_input(INPUT_POST, 'weight');
    if(empty($weight)) {
        $weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $name = addslashes($name);
        $supplier_id = filter_input(INPUT_POST, 'supplier_id');
        $executer = new Executer("insert into film_brand (name, supplier_id) values ('$name', $supplier_id)");
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            $insert_id = $executer->insert_id;
            $variation_executer = new Executer("insert into film_brand_variation (film_brand_id, thickness, weight) values ($insert_id, $thickness, $weight)");
            $error_message = $variation_executer->error;
        }
    }
}

// Обработка отправки формы создания вариации марки пленки
if(null !== filter_input(INPUT_POST, 'film_brand_variation_create_submit')) {
    $thickness = filter_input(INPUT_POST, 'thickness');
    $thickness = preg_replace('/[^0-9]/i', '', $thickness);
    if(empty($thickness)) {
        $thickness_valid = ISINVALID;
        $form_valid = false;
    }
    
    $weight = filter_input(INPUT_POST, 'weight');
    $weight = str_replace(',', '.', $weight);
    $weight = preg_replace('/[^.0-9]/i', '', $weight);
    if(empty($weight)) {
        $weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $supplier_id = filter_input(INPUT_POST, 'supplier_id');
        $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
        $executer = new Executer("insert into film_brand_variation (film_brand_id, thickness, weight) values ($film_brand_id, $thickness, $weight)");
        $error_message = $executer->error;
    }
}

// Обработка отправки формы удаления марки
if(null !== filter_input(INPUT_POST, 'delete_brand_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from film_brand where id=$id"))->error;
}

// Обработка отправки формы удаления вариации
if(null !== filter_input(INPUT_POST, 'delete_variation_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    $error_message = (new Executer("delete from film_brand_variation where id=$id"))->error;
}

// Обработка отправки формы удаления поставщика
if(null !== filter_input(INPUT_POST, 'delete-brand-button')) {
    $id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from film_brand_variation where film_brand_id in (select id from film_brand where supplier_id = $id)"))->error;
    
    if(empty($error_message)) {
        $error_message = (new Executer("delete from film_brand where supplier_id = $id"))->error;
        
        if(empty($error_message)) {
            $error_message = (new Executer("delete from supplier where id = $id"))->error;
            
            if(empty($error_message)) {
                header('Location: '.APPLICATION.'/supplier/');
            }
        }
    }
}

// Получение объекта
$row = (new Fetcher("select name from supplier where id=". filter_input(INPUT_GET, 'id')))->Fetch();
$name = htmlentities($row['name']);
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
        include '../include/header.php';
        ?>
        <div class="container-fluid form-page">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-2 nav2">
                <div class="p-1 row">
                    <div class="col-6">
                        <a href="<?=APPLICATION ?>/user/">Сотрудники</a>
                    </div>
                    <div class="col-6">
                        <a class="active" href="<?=APPLICATION ?>/supplier/">Поставщики</a>    
                    </div>
                </div>
                <div class="p-1"></div>
            </div>
            <div class="supplier-page">
                <div class="backlink">
                    <a href="<?=APPLICATION ?>/supplier/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                </div>
                <h1 style="font-size: 24px; line-height: 32px; font-weight: 600;"><?=$name ?></h1>
                <h2 style="font-size: 18px; line-height: 24px; font-weight: 600;">Пленки поставщика</h2>
                <div style="margin-top: 10px; margin-bottom: 30px;">
                    <form method="post" class="form-inline" id="add-brand-form">
                        <input type="hidden" id="supplier_id" name="supplier_id" value="<?= filter_input(INPUT_GET, 'id') ?>"/>
                        <input type="hidden" id="scroll" name="scroll" />
                        <div class="form-group">
                            <input type="text" class="form-control mr-2" id="name" name="name" required="required" placeholder="Марка пленки"/>
                            <div class="invalid-feedback">Марка пленки обязательно</div>
                        </div>
                        <div class="form-group">
                            <input type="text" id="thickness" name="thickness" class="form-control int-only" placeholder="Толщина" style="width: 100px; margin-left: 12px;" required="required" />
                            <div class="invalid-feedback">Толщина обязательно</div>
                        </div>
                        <div class="form-group">
                            <input type="text" id="weight" name="weight" class="form-control float-only" placeholder="Удельный вес" style="width: 120px; margin-left: 12px;" required="required" />
                            <div class="invalid-feedback">Удельный вес обязательно</div>
                        </div>
                        <button type="submit" class="btn btn-link" id="film_brand_create_submit" name="film_brand_create_submit" style="padding-left: 10px; padding-right: 0;"><i class="fas fa-plus" style="font-size: 10px;"></i>&nbsp;Добавить</button>
                        <button class="btn btn-link" id="add-brand-cancel" style="padding-left: 10px; padding-right: 0;"><i class="fas fa-undo" style="font-size: 10px;"></i>&nbsp;Отмена</button>
                    </form>
                    <button class="btn btn-outline-dark" id="add-brand-button" style="padding-left: 32px; padding-right: 68px; padding-top: 8px; padding-bottom: 9px;">
                        <div style="float:left; padding-top: 6px; padding-right: 30px; font-size: 12px;"><i class="fas fa-plus"></i></div>
                        &nbsp;Добавить марку<br/>пленки
                    </button>
                </div>
                <?php
                $film_brands = (new Grabber("select id, name from film_brand where supplier_id=". filter_input(INPUT_GET, 'id')." order by name"))->result;
                $film_brand_variations = (new Grabber("select v.id, v.film_brand_id, v.thickness, v.weight from film_brand_variation v inner join film_brand b on v.film_brand_id=b.id where b.supplier_id=". filter_input(INPUT_GET, 'id')." order by thickness, weight"))->result;

                foreach ($film_brands as $film_brand):
                    $current_film_brand_variations = array_filter($film_brand_variations, function($param) use($film_brand) { return $param['film_brand_id'] == $film_brand['id']; });
                ?>
                <table>
                    <tr>
                        <td>
                            <h2 style="font-size: 18px; line-height: 24px; font-weight: 600;"><?=$film_brand['name'] ?></h2>
                        </td>
                        <td>
                            <form method="post" class="form-inline add-variation-form" style="margin-left: 30px;">
                                <input type="hidden" id="supplier_id" name="supplier_id" value="<?= filter_input(INPUT_GET, 'id') ?>"/>
                                <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand['id'] ?>"/>
                                <input type="hidden" id="scroll" name="scroll" />
                                <div class="form-group">
                                    <input type="text" class="form-control int-only mr-2" id="thickness" name="thickness" placeholder="Толщина" required="required" style="width:100px;"/>
                                    <div class="invalid-feedback">Толщина обязательно</div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control float-only" id="weight" name="weight" placeholder="Удельный вес" required="required" style="width:120px;"/>
                                    <div class="invalid-feedback">Удельный вес обязательно</div>
                                </div>
                                <button type="submit" class="btn btn-link ml-2" id="film_brand_variation_create_submit" name="film_brand_variation_create_submit" style="padding-left: 0; padding-right: 0;"><i class="fas fa-plus" style="font-size: 8px; vertical-align: top; padding-top: 8px;"></i>&nbsp;Добавить</button>
                                <button class="btn btn-link ml-2 add-variation-cancel" style="padding-left: 0; padding-right: 0;"><i class="fas fa-undo" style="font-size: 10px; vertical-align: top; padding-top: 8px;"></i>&nbsp;Отмена</button>
                            </form>
                            <button class="btn btn-link add-variation-button"><i class="fas fa-plus" style="font-size: 8px; vertical-align: top; padding-top: 8px;"></i>&nbsp;Добавить</button>
                        </td>
                    </tr>
                </table>
                <table class="table" style="width: auto; border-bottom: 0; margin-bottom: 30px;">
                    <tr>
                        <th>Толщина</th>
                        <?php
                        foreach ($current_film_brand_variations as $current_film_brand_variation) {
                            echo "<td>".$current_film_brand_variation['thickness']." мкм</td>";
                        }
                        ?>
                    </tr>
                    <tr>
                        <th>Удельный вес</th>
                        <?php
                        foreach ($current_film_brand_variations as $current_film_brand_variation) {
                            echo "<td>".$current_film_brand_variation['weight']." г/м<sup>2</sup></td>";
                        }
                        ?>
                    </tr>
                    <tr>
                        <th></th>
                        <?php
                        foreach ($current_film_brand_variations as $current_film_brand_variation):
                        ?>
                        <td>
                            <form method="post">
                                <input type="hidden" id="id" name="id" value="<?=$current_film_brand_variation['id'] ?>"/>
                                <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand['id'] ?>"/>
                                <input type="hidden" id="supplier_id" name="supplier_id" value="<?= filter_input(INPUT_GET, 'id') ?>"/>
                                <input type="hidden" id="scroll" name="scroll" />
                                <button type="submit" class="btn btn-link confirmable" id="delete_variation_submit" name="delete_variation_submit"><img src="<?=APPLICATION ?>/images/icons/trash1.svg" /></button>
                            </form>  
                        </td>
                        <?php
                        endforeach;
                        ?>
                    </tr>
                </table>
                <?php endforeach; ?>                
            </div>
            <form method="post">
                <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                <button class="btn btn-outline-danger confirmable" id="delete-brand-button" name="delete-brand-button" style="padding-left: 45px; padding-right: 45px; padding-top: 14px; padding-bottom: 14px;"><img src="<?=APPLICATION ?>/images/icons/trash-red.svg" style="vertical-align: top;" />&nbsp;&nbsp;&nbsp;Удалить поставщика</button>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('#add-brand-form').hide();
            
            $('#add-brand-button').click(function(){
                $(this).hide();
                $('#add-brand-form').show();
                $('#add-brand-form').find('input[id="name"]').focus();
            });
            
            $('#add-brand-cancel').click(function(){
                $('#add-brand-form').hide();
                $('#add-brand-button').show();
            });
            
            $('.add-variation-form').hide();
            
            $('.add-variation-button').click(function(){
                $(this).hide();
                var frm = $(this).prev('.add-variation-form');
                frm.show();
                frm.find('input[id="thickness"]').focus();
            });
            
            $('.add-variation-cancel').click(function(){
                var frm = $(this).parent();
                frm.hide();
                frm.next('.add-variation-button').show();
            });
        </script>
    </body>
</html>