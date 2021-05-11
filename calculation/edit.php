<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$customer_id_valid = '';
$name_valid = '';
$work_type_valid = '';
$brand_name_valid = '';
$thickness_valid = '';
$width_valid = '';
$weight_valid = '';
$diameter_valid = '';

// Сохранение в базу расчёта
if(null !== filter_input(INPUT_POST, 'create_calculation_submit')) {
    if(empty(filter_input(INPUT_POST, "customer_id"))) {
        $customer_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, "name"))) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'work_type_id'))) {
        $work_type_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'brand_name'))) {
        $brand_name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'thickness'))) {
        $thickness_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'width'))) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'weight'))) {
        $weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'diameter'))) {
        $diameter_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $id = filter_input(INPUT_POST, 'id');
        $customer_id = filter_input(INPUT_POST, 'customer_id');
        $name = addslashes(filter_input(INPUT_POST, 'name'));
        $work_type_id = filter_input(INPUT_POST, 'work_type_id');
        $brand_name = addslashes(filter_input(INPUT_POST, 'brand_name'));
        $thickness = filter_input(INPUT_POST, 'thickness');
        
        $lamination1_brand_name = addslashes(filter_input(INPUT_POST, 'lamination1_brand_name'));
        $lamination1_thickness = filter_input(INPUT_POST, 'lamination1_thickness');
        if(empty($lamination1_thickness)) $lamination1_thickness = "NULL";
        $lamination2_brand_name = addslashes(filter_input(INPUT_POST, 'lamination2_brand_name'));
        $lamination2_thickness = filter_input(INPUT_POST, 'lamination2_thickness');
        if(empty($lamination2_thickness)) $lamination2_thickness = "NULL";
        
        $width = filter_input(INPUT_POST, 'width');
        $weight = filter_input(INPUT_POST, 'weight');
        $diameter = filter_input(INPUT_POST, 'diameter');
        
        $sql = "update calculation set customer_id=$customer_id, name='$name', work_type_id=$work_type_id, brand_name='$brand_name', thickness=$thickness, lamination1_brand_name='$lamination1_brand_name', lamination1_thickness=$lamination1_thickness, lamination2_brand_name='$lamination2_brand_name', lamination2_thickness=$lamination2_thickness, weight=$weight, diameter=$diameter where id=$id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select date, customer_id, name, work_type_id, brand_name, thickness, lamination1_brand_name, lamination1_thickness, lamination2_brand_name, lamination2_thickness, width, weight, diameter, status_id from calculation where id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];

$customer_id = filter_input(INPUT_POST, 'customer_id');
if(null === $customer_id) {
    $customer_id = $row['customer_id'];
}

$name = filter_input(INPUT_POST, 'name');
if(null === $name) {
    $name = $row['name'];
}

$work_type_id = filter_input(INPUT_POST, 'work_type_id');
if(null === $work_type_id) {
    $work_type_id = $row['work_type_id'];
}

$brand_name = filter_input(INPUT_POST, 'brand_name');
if(null === $brand_name) {
    $brand_name = $row['brand_name'];
}

$thickness = filter_input(INPUT_POST, 'thickness');
if(null === $thickness) {
    $thickness = $row['thickness'];
}

$lamination1_brand_name = filter_input(INPUT_POST, 'lamination1_brand_name');
if(null === $lamination1_brand_name) {
    $lamination1_brand_name = $row['lamination1_brand_name'];
}

$lamination1_thickness = filter_input(INPUT_POST, 'lamination1_thickness');
if(null === $lamination1_thickness) {
    $lamination1_thickness = $row['lamination1_thickness'];
}

$lamination2_brand_name = filter_input(INPUT_POST, 'lamination2_brand_name');
if(null === $lamination2_brand_name) {
    $lamination2_brand_name = $row['lamination2_brand_name'];
}

$lamination2_thickness = filter_input(INPUT_POST, 'lamination2_thickness');
if(null === $lamination2_thickness) {
    $lamination2_thickness = $row['lamination2_thickness'];
}

$width = filter_input(INPUT_POST, 'width');
if(null === $width) {
    $width = $row['width'];
}

$weight = filter_input(INPUT_POST, 'weight');
if(null === $weight) {
    $weight = $row['weight'];
}

$diameter = filter_input(INPUT_POST, 'diameter');
if(null === $diameter) {
    $diameter = $row['diameter'];
}

$status_id = $row['status_id'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/jquery-ui.css" rel="stylesheet"/>
        <link href="<?=APPLICATION ?>/css/select2.min.css" rel="stylesheet"/>
    </head>
    <body>
        <?php
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="backlink">
                <a href="<?=APPLICATION ?>/calculation/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <div class="row">
                <!-- Левая половина -->
                <div class="col-6">
                    <form method="post">
                        <h1 style="font-size: 32px; line-height: 48px; font-weight: 600;"><?= htmlentities($name) ?></h1>
                        <h2 style="font-size: 26px;">№<?=$id ?> от <?= DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y') ?></h2>
                        <!-- Заказчик -->
                        <div class="row">
                            <div class="col-8">
                                <div class="form-group">
                                    <select id="customer_id" name="customer_id" class="form-control js-select2<?=$customer_id_valid ?>" required="required">
                                        <option value="">Заказчик...</option>
                                        <?php
                                        $sql = "select id, name from customer order by name";
                                        $fetcher = new Fetcher($sql);
                                        
                                        while ($row = $fetcher->Fetch()):
                                        $selected = '';
                                        if($row['id'] == $customer_id) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                        <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                        <?php
                                        endwhile;
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Заказчик обязательно</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-dark w-100" data-toggle="modal" data-target="#new_customer"><i class="fas fa-plus"></i>&nbsp;Создать нового</button>
                            </div>
                        </div>
                        <!-- Название заказа -->
                        <div class="form-group">
                            <input type="text" id="name" name="name" class="form-control<?=$name_valid ?>" placeholder="Название заказа" value="<?= htmlentities($name) ?>" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Название заказа обязательно</div>
                        </div>
                        <!-- Тип работы -->
                        <div class="form-group">
                            <select id="work_type_id" name="work_type_id" class="form-control" required="required">
                                <option value="">Тип работы...</option>
                                <?php
                                $sql = "select id, name from work_type";
                                $fetcher = new Fetcher($sql);
                                
                                while ($row = $fetcher->Fetch()):
                                $selected = '';
                                if($row['id'] == $work_type_id) {
                                    $selected = " selected='selected'";
                                }
                                ?>
                                <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                <?php
                                endwhile;
                                ?>
                            </select>
                        </div>
                        <!-- Основная плёнка -->
                        <div id="main_film_title" class="d-none">
                            <p class="font-weight-bold">Основная пленка</p>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <select id="brand_name" name="brand_name" class="form-control" required="required">
                                        <option value="">Марка пленки...</option>
                                        <?php
                                        $sql = "select distinct name from film_brand order by name";
                                        $brand_names = (new Grabber($sql))->result;
                                        
                                        foreach ($brand_names as $row):
                                        $selected = '';
                                        if($row['name'] == $brand_name) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                        <option value="<?=$row['name'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <select id="thickness" name="thickness" class="form-control" required="required">
                                        <option value="">Толщина...</option>
                                        <?php
                                        if(!empty($brand_name)) {
                                            $sql = "select distinct fbv.thickness, fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$brand_name' order by thickness";
                                            $thicknesses = (new Grabber($sql))->result;
                                            
                                            foreach ($thicknesses as $row):
                                            $selected = '';
                                            if($row['thickness'] == $thickness) {
                                                $selected = " selected='selected'";
                                            }
                                        ?>
                                        <option value="<?=$row['thickness'] ?>"<?=$selected ?>><?=$row['thickness'] ?> мкм <?=$row['weight'] ?> г/м<sup>2</sup></option>
                                        <?php
                                            endforeach;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="show_lamination_1">
                            <button type="button" class="btn btn-light" onclick="javascript: ShowLamination1(); $('#calculation').hide();"><i class="fas fa-plus"></i>&nbsp;Добавить ламинацию</button>
                        </div>
                        <!-- Ламинация 1 -->
                        <div id="form_lamination_1" class="d-none">
                            <p class="font-weight-bold">Ламинация 1</p>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <select id="lamination1_brand_name" name="lamination1_brand_name" class="form-control">
                                            <option value="">Марка пленки...</option>
                                                <?php
                                                foreach ($brand_names as $row):
                                                $selected = '';
                                                if($row['name'] == $lamination1_brand_name) {
                                                    $selected = " selected='selected'";
                                                }
                                                ?>
                                            <option value="<?=$row['name'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                                <?php
                                                endforeach;
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group">
                                        <select id="lamination1_thickness" name="lamination1_thickness" class="form-control">
                                            <option value="">Толщина...</option>
                                            <?php
                                            if(!empty($lamination1_brand_name)) {
                                                $sql = "select distinct fbv.thickness, fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$lamination1_brand_name' order by thickness";
                                                $thicknesses = (new Grabber($sql))->result;
                                                
                                                foreach ($thicknesses as $row):
                                                $selected = '';
                                                if($row['thickness'] == $lamination1_thickness) {
                                                    $selected = " selected='selected'";
                                                }
                                            ?>
                                            <option value="<?=$row['thickness'] ?>"<?=$selected ?>><?=$row['thickness'] ?> мкм <?=$row['weight'] ?> г/м<sup>2</sup></option>
                                            <?php
                                                endforeach;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-1" id="hide_lamination_1">
                                    <button type="button" class="btn btn-light" onclick="javascript: HideLamination1(); $('#calculation').hide();"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                            <div id="show_lamination_2">
                                <button type="button" class="btn btn-light" onclick="javascript: ShowLamination2(); $('#calculation').hide();"><i class="fas fa-plus"></i>&nbsp;Добавить ламинацию</button>
                            </div>
                            <!-- Ламинация 2 -->
                            <div id="form_lamination_2">
                                <p class="font-weight-bold">Ламинация 2</p>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select id="lamination2_brand_name" name="lamination2_brand_name" class="form-control">
                                                <option value="">Марка пленки...</option>
                                                    <?php
                                                    foreach ($brand_names as $row):
                                                    $selected = '';
                                                    if($row['name'] == $lamination2_brand_name) {
                                                        $selected = " selected='selected'";
                                                    }
                                                    ?>
                                                <option value="<?=$row['name'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                                    <?php
                                                    endforeach;
                                                    ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <select id="lamination2_thickness" name="lamination2_thickness" class="form-control">
                                                <option value="">Толщина...</option>
                                                <?php
                                                if(!empty($lamination2_brand_name)) {
                                                    $sql = "select distinct fbv.thickness, fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$lamination2_brand_name' order by thickness";
                                                    $thicknesses = (new Grabber($sql))->result;
                                                    
                                                    foreach ($thicknesses as $row):
                                                    $selected = "";
                                                    if($row['thickness'] == $lamination2_thickness) {
                                                        $selected = " selected='selected'";
                                                    }
                                                ?>
                                                <option value="<?=$row['thickness'] ?>"<?=$selected ?>><?=$row['thickness'] ?> мкм <?=$row['weight'] ?> г/м<sup>2</sup></option>
                                                <?php
                                                    endforeach;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-1" id="hide_lamination_2">
                                        <button type="button" class="btn" onclick="javascript: HideLamination2(); $('#calculation').hide();"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <!-- Ширина -->
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" id="width" class="form-control int-only" placeholder="Ширина, мм" value="<?=$width ?>" required="required" />
                                    <div class="invalid-feedback">Ширина обязательно</div>
                                </div>
                            </div>
                            <!-- Вес нетто -->
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" id="weight" name="weight" class="form-control float-only" placeholder="Вес нетто, кг" value="<?=$weight ?>" required="required" />
                                    <div class="invalid-feedback">Вес нетто обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Диаметр намотки -->
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" id="diameter" name="diameter" class="form-control int-only" placeholder="Диаметр намотки" value="<?=$diameter ?>" required="required" />
                                    <div class="invalid-feedback">Диаметр намотки обязательно</div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="edit_calculation_submit" name="edit_calculation_submit" class="btn btn-dark">Рассчитать</button>
                    </form>
                </div>
                <!-- Правая половина -->
                <div class="col-3">
                    <!-- Расчёт -->
                    <div id="calculation">
                        <h1>Расчет</h1>
                        <input type="text" id="extra_charge" name="extra_charge" class="form-control" placeholder="Наценка" />
                        <div class="mt-3 mb-1">Себестоимость</div>
                        <div class="font-weight-bold mt-1 mb-1" style="font-size: large;">1 200 000 руб.</div>
                        <div class="mt-3 mb-1">Отгрузочная стоимость</div>
                        <div class="font-weight-bold mt-1 mb-3" style="font-size: large;">800 000 руб.</div>
                        <button type="button" class="btn btn-light" id="show_costs" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать расходы</button>
                        <div id="costs" class="d-none">
                            <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть расходы</button>
                            <div class="mt-3 mb-1">Отходы</div>
                            <div class="font-weight-bold mt-1 mb-1" style="font-size: large;">200 280 руб.&nbsp;&nbsp;&nbsp;24,5 кг.</div>
                            <div class="mt-3 mb-1">Клей</div>
                            <div class="font-weight-bold mt-1 mb-3" style="font-size: large;">800 000 руб.</div>
                        </div>
                        <input type="hidden" id="create_calculation_submit1" name="create_calculation_submit1" />
                        <button type="submit" id="status_id" name="status_id" value="2" class="btn btn-outline-dark w-75 mt-3">Сделать КП</button>
                        <button type="submit" id="status_id" name="status_id" value="6" class="btn btn-dark w-75 mt-3">Отправить в работу</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script src="<?=APPLICATION ?>/js/select2.min.js"></script>
        <script src="<?=APPLICATION ?>/js/i18n/ru.js"></script>
        <script>
            // Список с  поиском
            $('.js-select2').select2({
                placeholder: "Заказчик...",
                maximumSelectionLength: 2,
                language: "ru"
            });
            
            // Скрытие расчёта при изменении значения полей
            $('input').change(function () {
                $('#calculation').hide();
            });
            
            $('select').change(function () {
                $('#calculation').hide();
            });
            
            $('input').keypress(function () {
                $('#calculation').hide();
            });
            
            // Если у объекта имеется ламинация 1, показываем ламинацию 1
            <?php if(!empty($lamination1_brand_name)): ?>
            ShowLamination1();
            <?php endif; ?>
                
            // Если у объекта имеется ламинация 2, показываем ламинацию 2
            <?php if(!empty($lamination2_brand_name)): ?>
            ShowLamination2();
            <?php endif; ?>
            
            // Маска % для поля "наценка"
            $("#extra_charge").mask("99%");
            
            // Фильтрация ввода в поле "наценка"
            $('#extra_charge').keypress(function(e) {
                if(/\D/.test(String.fromCharCode(e.charCode))) {
                    return false;
                }
            });
            
            $('#extra_charge').change(function(e) {
                var val = $(this).val();
                val = val.replace(/[^\d\%]/g, '');
                $(this).val(val);
            });
            
            // Обработка выбора типа плёнки основной плёнки: перерисовка списка толщин
            $('#brand_name').change(function(){
                if($(this).val() == "") {
                    $('#thickness').html("<option id=''>Толщина...</option>");
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?brand_name=" + $(this).val() })
                            .done(function(data) {
                                $('#thickness').html(data);
                            })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                            });
                }
            });
            
            // Обработка выбора типа плёнки ламинации1: перерисовка списка толщин
            $('#lamination1_brand_name').change(function(){
                if($(this).val() == "") {
                    $('#lamination1_thickness').html("<option id=''>Толщина...</option>");
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?brand_name=" + $(this).val() })
                            .done(function(data) {
                                $('#lamination1_thickness').html(data);
                            })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                            });
                }
            });
            
            // Обработка выбора типа плёнки ламинации2: перерисовка списка толщин
            $('#lamination2_brand_name').change(function(){
                if($(this).val() == "") {
                    $('#lamination2_thickness').html("<option id=''>Толщина...</option>");
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?brand_name=" + $(this).val() })
                            .done(function(data) {
                                $('#lamination2_thickness').html(data);
                            })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                            });
                }
            });
            
            // Показ марки плёнки и толщины для ламинации 1
            function ShowLamination1() {
                $('#form_lamination_1').removeClass('d-none');
                $('#show_lamination_1').addClass('d-none');
                $('#main_film_title').removeClass('d-none');
                $('#lamination1_brand_name').attr('required', 'required');
                $('#lamination1_thickness').attr('required', 'required');
                HideLamination2();
            }
            
            // Скрытие марки плёнки и толщины для ламинации 1
            function HideLamination1() {
                $('#form_lamination_1').addClass('d-none');
                $('#show_lamination_1').removeClass('d-none');
                $('#main_film_title').addClass('d-none');
                $('#lamination1_brand_name').removeAttr('required');
                $('#lamination1_thickness').removeAttr('required');
                HideLamination2();
            }
            
            // Показ марки плёнки и толщины для ламинации 2
            function ShowLamination2() {
                $('#form_lamination_2').removeClass('d-none');
                $('#show_lamination_2').addClass('d-none');
                $('#hide_lamination_1').addClass('d-none');
                $('#lamination2_brand_name').attr('required', 'required');
                $('#lamination2_thickness').attr('required', 'required');
            }
            
            // Скрытие марки плёнки и толщины для ламинации 2
            function HideLamination2() {
                $('#form_lamination_2').addClass('d-none');
                $('#show_lamination_2').removeClass('d-none');
                $('#hide_lamination_1').removeClass('d-none');
                $('#lamination2_brand_name').removeAttr('required');
                $('#lamination2_thickness').removeAttr('required');
            }
            
            // Показ расходов
            function ShowCosts() {
                $("#costs").removeClass("d-none");
                $("#show_costs").addClass("d-none");
            }
            
            // Скрытие расходов
            function HideCosts() {
                $("#costs").addClass("d-none");
                $("#show_costs").removeClass("d-none");
            }
        </script>
    </body>
</html>