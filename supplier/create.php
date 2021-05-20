<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$name_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'supplier_create_submit')) {
    $name = filter_input(INPUT_POST, 'name');
    if(empty($name)) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        // Сохранение поставщика
        $name = addslashes($name);
        
        $executer = new Executer("insert into supplier (name) values ('$name')");
        $error_message = $executer->error;
        $id = $executer->insert_id;
        
        // Получение данных о марках и вариациях
        $post_keys = array_keys($_POST);
        $film_brands = array();
        
        foreach ($post_keys as $post_key) {
            if(strpos($post_key, "film_brand_") === 0) {
                $reminder = substr($post_key, strlen("film_brand_"));
                $film_brands[$reminder]["film_brand"] = $_POST[$post_key];
            }
            
            if(strpos($post_key, "thickness_") === 0) {
                $reminder = substr($post_key, strlen("thickness_"));
                $film_brands[$reminder]["thickness"] = $_POST[$post_key];
            }
            
            if(strpos($post_key, "weight_") === 0) {
                $reminder = substr($post_key, strlen("weight_"));
                $film_brands[$reminder]["weight"] = $_POST[$post_key];
            }
        }
        
        $film_brand_variations = array();
        
        foreach ($film_brands as $film_brand) {
            if(!isset($film_brand_variations[$film_brand['film_brand']]) || !is_array($film_brand_variations[$film_brand['film_brand']])) {
                $film_brand_variations[$film_brand['film_brand']] = array();
            }
            
            $variation = array();
            $variation['thickness'] = $film_brand['thickness'];
            $variation['weight'] = $film_brand['weight'];
            array_push($film_brand_variations[$film_brand['film_brand']], $variation);
        }
        
        // Сохранение марок
        $film_brand_variations_keys = array_keys($film_brand_variations);
        
        foreach ($film_brand_variations_keys as $film_brand_variations_key) {
            $film_brand_variations_key = addslashes($film_brand_variations_key);
            $executer = new Executer("insert into film_brand (name, supplier_id) values ('$film_brand_variations_key', $id)");
            $error_message = $executer->error;
            $film_brand_id = $executer->insert_id;
            
            // Сохранение вариаций
            foreach ($film_brand_variations[$film_brand_variations_key] as $variation) {
                $thickness = $variation['thickness'];
                $thickness = preg_replace('/[^0-9]/i', '', $thickness);
                
                $weight = $variation['weight'];
                $weight = str_replace(',', '.', $weight);
                $weight = preg_replace('/[^.0-9]/i', '', $weight);
                
                $executer = new Executer("insert into film_brand_variation (film_brand_id, thickness, weight) values ($film_brand_id, $thickness, $weight)");
                $error_message = $executer->error;
            }
        }

        if(empty($error_message)) {
            header('Location: '.APPLICATION."/supplier/details.php?id=$id");
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
            <div class="d-flex justify-content-start">
                <div class="p-1">
                    <?php
                    include '../include/subheader_admin.php';
                    ?>
                </div>
            </div>
            <hr />
            <div class="backlink">
                <a href="<?=APPLICATION ?>/supplier/">Назад</a>
            </div>
            <h1>Добавление поставщика</h1>
            <form method="post">
                <div class="form-group" style="padding-bottom: 6px;">
                    <div style="width:319px;">
                        <label for="name">Название поставщика</label>
                        <input type="text" id="name" name="name" class="form-control<?=$name_valid ?>" value="<?= filter_input(INPUT_POST, 'name') ?>" required="required"/>
                        <div class="invalid-feedback">Название поставщика обязательно</div>                            
                    </div>
                </div>
                <div class="form-group" style="padding-bottom: 14px;">
                    <table class="table film-table" id="variations-table" style="width: 472px;"></table>
                    <div class="form-inline" id="add-brand-form">
                        <input type="text" id="film_brand" name="film_brand" class="form-control" placeholder="Название" style="width:215px; margin-right: 13px;" />
                        <input type="text" id="thickness" name="thickness" class="form-control int-only" placeholder="Толщина" style="width: 100px; margin-right: 13px;" />
                        <input type="text" id="weight" name="weight" class="form-control float-only" placeholder="Удельный вес" style="width: 129px; margin-right: 13px;" />
                        <button type="button" class="btn btn-link" id="add-brand-link">Добавить</button>
                    </div>
                    <button type="button" class="btn btn-outline-dark" id="add-brand-button">
                        <i class="fas fa-plus"></i>&nbsp;Добавить марку пленки
                    </button>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark" id="supplier_create_submit" name="supplier_create_submit">Создать поставщика</button>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('#add-brand-form').hide();
            $('#variations-table').hide();
            
            $('#add-brand-button').click(function(){
                $(this).hide();
                $('#add-brand-form').show();
                $('#add-brand-form').find('input[id="film_brand"]').focus();
            });
            
            $('#add-brand-cancel').click(function(){
                $('#add-brand-form').find('input').val('');
                $('#add-brand-form').hide();
                $('#add-brand-button').show();
            });
            
            $('#add-brand-table-link').click(function(){
                $('#add-brand-button').click();
            });
            
            function RemoveVariation(param) {
                if(confirm("Действительно удалить?")) {
                    param.closest("tr").remove();
                    
                    var tblrows = $('#variations-table').find("tr");
                    var tblrnumber = tblrows.length;
                    
                    if(tblrnumber == 1) {
                        $('#variations-table').hide();
                    }
                }
            }
            
            $('#add-brand-link').click(function(){
                var empties = $('#add-brand-form input').filter(function(){return $(this).val() == ''});
                if(empties.length > 0) {
                    $('#add-brand-form').find('input').filter(function(){return $(this).val() == ''}).first().focus();
                }
                else {
                    // Показ таблицы
                    if($('#variations-table').is(':hidden')) {
                        $('#variations-table').show();
                    }
                    
                    // Добавление строки в таблицу
                    var film_brand = $('#add-brand-form').find('input[id="film_brand"]').val();
                    var thickness = $('#add-brand-form').find('input[id="thickness"]').val();
                    var weight = $('#add-brand-form').find('input[id="weight"]').val();
                    var rowscount = $('#variations-table').find("tr").length;
                    
                    var tblrow = '<tr>' + 
                            '<td>' + film_brand + '<input type="hidden" id="film_brand_' + rowscount + '" name="film_brand_' + rowscount + '" value="' + film_brand + '" /></td>' + 
                            '<td class="text-right">' + thickness + '<input type="hidden" id="thickness_' + rowscount + '" name="thickness_' + rowscount + '" value="' + thickness + '" /></td>' + 
                            '<td class="text-right">' + weight + '<input type="hidden" id="weight_' + rowscount + '" name="weight_' + rowscount + '" value="' + weight + '" /></td>'
                            '</tr>';
                    $('#variations-table').append(tblrow);
                    
                    // Скрытие формы и показ кнопки
                    $('#add-brand-form').find('input').val('');
                    $('#add-brand-form').hide();
                    $('#add-brand-button').show();
                }
            });
        </script>
    </body>
</html>