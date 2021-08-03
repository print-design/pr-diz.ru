<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$user_id = GetUserId();
$sql = "update user set request_uri='$request_uri' where id=$user_id";
$error_message = (new Executer($sql))->error;

if(!empty($error_message)) {
    exit($error_message);
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-start">
        <ul class="navbar-nav">
            <li class="nav-item">
                <button class="btn btn-link nav-link" id="index-submit"><i class="fas fa-chevron-left"></i>&nbsp;Назад</button>
            </li>
        </ul>
    </nav>
</div>
<div id="topmost"></div>
<div class="container-fluid">
    <h1>Какой материал режем?</h1>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <form method="post">
                <div class="form-group">
                    <label for="supplier_id">Поставщик</label>
                    <select class="form-control" id="supplier_id" name="supplier_id" required="required">
                        <option value="" hidden="hidden">Выберите поставщика</option>
                            <?php
                            $suppliers = (new Grabber("select id, name from supplier order by name"))->result;
                            foreach ($suppliers as $supplier) {
                                $id = $supplier['id'];
                                $name = $supplier['name'];
                                $selected = '';
                                if(filter_input(INPUT_GET, 'supplier_id') == $supplier['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$name</option>";
                            }
                            ?>
                    </select>
                    <div class="invalid-feedback">Поставщик обязательно</div>
                </div>
                <div class="form-group">
                    <label for="film_brand_id">Марка пленки</label>
                    <select class="form-control" id="film_brand_id" name="film_brand_id" required="required">
                        <option value="" hidden="hidden">Выберите марку</option>
                            <?php
                            if(null !== filter_input(INPUT_GET, 'supplier_id')) {
                                $supplier_id = filter_input(INPUT_GET, 'supplier_id');
                                $film_brands = (new Grabber("select id, name from film_brand where supplier_id = $supplier_id"))->result;
                                foreach ($film_brands as $film_brand) {
                                    $id = $film_brand['id'];
                                    $name = $film_brand['name'];
                                    $selected = '';
                                    if(filter_input(INPUT_GET, 'film_brand_id') == $film_brand['id']) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$name</option>";
                                }
                            }
                            ?>
                    </select>
                    <div class="invalid-feedback">Марка пленки обязательно</div>
                </div>
                <div class="form-group">
                    <label for="thickness">Толщина, мкм</label>
                    <select class="form-control" id="thickness" name="thickness" required="required">
                        <option value="" hidden="hidden">Выберите толщину</option>
                            <?php
                            if(null !== filter_input(INPUT_GET, 'film_brand_id')) {
                                $film_brand_id = filter_input(INPUT_GET, 'film_brand_id');
                                $film_brand_variations = (new Grabber("select thickness, weight from film_brand_variation where film_brand_id = $film_brand_id order by thickness"))->result;
                                foreach ($film_brand_variations as $film_brand_variation) {
                                    $thickness = $film_brand_variation['thickness'];
                                    $weight = $film_brand_variation['weight'];
                                    $selected = '';
                                    if(filter_input(INPUT_GET, 'thickness') == $film_brand_variation['thickness']) $selected = " selected='selected'";
                                    echo "<option value='$thickness'$selected>$thickness мкм $weight г/м<sup>2</sup></option>";
                                }
                            }
                            ?>
                    </select>
                    <div class="invalid-feedback">Толщина обязательно</div>
                </div>
                <div class="form-group">
                    <label for="width">Ширина, мм</label>
                    <input type="text" id="width" name="width" value="<?= filter_input(INPUT_GET, 'width') ?>" class="form-control int-only" data-max="1600" placeholder="Введите ширину" required="required" autocomplete="off" />
                    <div class="invalid-feedback">Число, макс. 1600</div>
                </div>
                <div class="form-group">
                    <button type="button" id="next-submit" class="btn btn-dark form-control mt-4">Далее</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    submit = false;
    
    // Переход на страницу создания нарезки
    function Submit() {
        form_valid = true;
        
        if($('#supplier_id').val() == '') {
            $('#supplier_id').addClass('is-invalid');
            $('#supplier_id').focus();
            form_valid = false;
        }
        
        if($('#film_brand_id').val() == '') {
            $('#film_brand_id').addClass('is-invalid');
            $('#film_brand_id').focus();
            form_valid = false;
        }
        
        if($('#thickness').val() == '') {
            $('#thickness').addClass('is-invalid');
            $('#thickness').focus();
            form_valid = false;
        }
        
        if($('#width').val() == '') {
            $('#width').addClass('is-invalid');
            $('#width').focus();
            form_valid = false;
        }
        
        if($('#width').val() < 1) {
            $('#width').addClass('is-invalid');
            $('#width').focus();
            form_valid = false;
        }
        
        if($('#width').val() > 1600) {
            $('#width').addClass('is-invalid');
            $('#width').focus();
            form_valid = false;
        }
        
        if(form_valid && !submit) {
            link = "_cut.php?supplier_id=" + $('#supplier_id').val() + "&film_brand_id=" + $('#film_brand_id').val() + "&thickness=" + $('#thickness').val() + "&width=" + $('#width').val();
            OpenAjaxPage(link);
            submit = true;
        }
    }
    
    $('#next-submit').click(function() {
        $.ajax({ url: "_check_db_uri.php?uri=<?= urlencode($request_uri) ?>" })
                .done(function(data) {
                    if(data == "OK") {
                        Submit($(this));
                    }
                    else {
                        OpenAjaxPage(data);
                    }
                })
                .fail(function() {
                    alert('Ошибка при переходе на страницу.');
                });
    });
    
    // Возврат на главную страницу
    function Index() {
        OpenAjaxPage("_index.php");
    }
    
    $('#index-submit').click(function() {
        $.ajax({ url: "_check_db_uri.php?uri=<?= urlencode($request_uri) ?>" })
                .done(function(data) {
                    if(data == "OK") {
                        Index();
                    }
                    else {
                        OpenAjaxPage(data);
                    }
                })
                .fail(function() {
                    alert('Ошибка при переходе на страницу.');
                });
    });
</script>