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
$weight_valid = '';

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
    
    if(empty(filter_input(INPUT_POST, 'weight'))) {
        $weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $date = date('Y-m-d');
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
        
        $weight = filter_input(INPUT_POST, 'weight');
        $width = filter_input(INPUT_POST, 'width');
        if(empty($width)) $width = "NULL";
        $streamscount = filter_input(INPUT_POST, 'streamscount');
        if(empty($streamscount)) $streamscount = "NULL";
        
        $manager_id = GetUserId();
        $status_id = 1; // Статус "Расчёт"
        
        $sql = "insert into calculation (date, customer_id, name, work_type_id, brand_name, thickness, lamination1_brand_name, lamination1_thickness, lamination2_brand_name, lamination2_thickness, width, weight, streamscount, manager_id, status_id) values('$date', $customer_id, '$name', $work_type_id, '$brand_name', $thickness, '$lamination1_brand_name', $lamination1_thickness, '$lamination2_brand_name', $lamination2_thickness, $width, $weight, $streamscount, $manager_id, $status_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $insert_id = $executer->insert_id;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/calculation/create.php'.BuildQuery('id', $insert_id));
        }
    }
}

// Смена статуса
if(null !== filter_input(INPUT_POST, 'change_status_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $status_id = filter_input(INPUT_POST, 'status_id');
    $sql = "update calculation set status_id=$status_id where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        header('Location: '.APPLICATION.'/calculation/calculation.php'. BuildQuery('id', $id));
    }
}

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

if(!empty($id)) {
    $sql = "select date, customer_id, name, work_type_id, brand_name, thickness, lamination1_brand_name, lamination1_thickness, lamination2_brand_name, lamination2_thickness, weight, width, streamscount, status_id from calculation where id=$id";
    $row = (new Fetcher($sql))->Fetch();
}

if(isset($row['date'])) $date = $row['date'];
else $date = null;

$customer_id = filter_input(INPUT_POST, 'customer_id');
if(null === $customer_id) {
    if(isset($row['customer_id'])) $customer_id = $row['customer_id'];
    else $customer_id = null;
}

$name = filter_input(INPUT_POST, 'name');
if(null === $name) {
    if(isset($row['name'])) $name = $row['name'];
    else $name = null;
}

$work_type_id = filter_input(INPUT_POST, 'work_type_id');
if(null === $work_type_id) {
    if(isset($row['work_type_id'])) $work_type_id = $row['work_type_id'];
    else $work_type_id = null;
}

$brand_name = filter_input(INPUT_POST, 'brand_name');
if(null === $brand_name) {
    if(isset($row['brand_name'])) $brand_name = $row['brand_name'];
    else $brand_name = null;
}

$thickness = filter_input(INPUT_POST, 'thickness');
if(null === $thickness) {
    if(isset($row['thickness'])) $thickness = $row['thickness'];
    else $thickness = null;
}

$lamination1_brand_name = filter_input(INPUT_POST, 'lamination1_brand_name');
if(null === $lamination1_brand_name) {
    if(isset($row['lamination1_brand_name'])) $lamination1_brand_name = $row['lamination1_brand_name'];
    else $lamination1_brand_name = null;
}

$lamination1_thickness = filter_input(INPUT_POST, 'lamination1_thickness');
if(null === $lamination1_thickness) {
    if(isset($row['lamination1_thickness'])) $lamination1_thickness = $row['lamination1_thickness'];
    else $lamination1_thickness = null;
}

$lamination2_brand_name = filter_input(INPUT_POST, 'lamination2_brand_name');
if(null === $lamination2_brand_name) {
    if(isset($row['lamination2_brand_name'])) $lamination2_brand_name = $row['lamination2_brand_name'];
    else $lamination2_brand_name = null;
}

$lamination2_thickness = filter_input(INPUT_POST, 'lamination2_thickness');
if(null === $lamination2_thickness) {
    if(isset($row['lamination2_thickness'])) $lamination2_thickness = $row['lamination2_thickness'];
    else $lamination2_thickness = null;
}

$weight = filter_input(INPUT_POST, 'weight');
if(null === $weight) {
    if(isset($row['weight'])) $weight = $row['weight'];
    else $width = null;
}

$width = filter_input(INPUT_POST, 'width');
if(null === $width) {
    if(isset($row['width'])) $width = $row['width'];
    else $width = null;
}

$streamscount = filter_input(INPUT_POST, 'streamscount');
if(null === $streamscount) {
    if(isset($row['streamscount'])) $streamscount = $row['streamscount'];
    else $streamscount = null;
}

if(isset($row['status_id'])) $status_id = $row['status_id'];
else $status_id = null;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
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
                <div class="col-6" id="left_side">
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <?php if(null === filter_input(INPUT_GET, 'id')): ?>
                        <h1 style="font-size: 32px; font-weight: 600;">Новый расчет</h1>
                        <?php else: ?>
                        <h1 style="font-size: 32px; font-weight: 600;"><?= htmlentities($name) ?></h1>
                        <h2 style="font-size: 26px;">№<?=$id ?> от <?= DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y') ?></h2>
                        <?php endif; ?>
                        <!-- Заказчик -->
                        <div class="row">
                            <div class="col-8">
                                <div class="form-group">
                                    <select id="customer_id" name="customer_id" class="form-control<?=$customer_id_valid ?>" multiple="multiple" required="required">
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
                        <!-- Вес нетто -->
                        <div class="row mt-3">
                            <!-- Вес нетто -->
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" id="weight" name="weight" class="form-control float-only" placeholder="Вес нетто, кг" value="<?=$weight ?>" required="required" />
                                    <div class="invalid-feedback">Вес нетто обязательно</div>
                                </div>
                            </div>
                        </div>
                        <!-- Основная плёнка -->
                        <div id="film_title">
                            <p><span class="font-weight-bold">Пленка</span>&nbsp;&nbsp;<span style="color: gray;">(325 руб) <i class="fas fa-info-circle" title="325 руб" data-placement="top"></i></span></p>
                        </div>
                        <div id="main_film_title" class="d-none">
                            <p><span class="font-weight-bold">Основная пленка</span>&nbsp;&nbsp;<span style="color: gray;">(325 руб   34кг   600мм) <i class="fas fa-info-circle" title="325 руб   34кг   600мм" data-placement="top"></i></span></p>
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
                            <button type="button" class="btn btn-light" onclick="javascript: ShowLamination1();"><i class="fas fa-plus"></i>&nbsp;Добавить ламинацию</button>
                        </div>
                        <!-- Ламинация 1 -->
                        <div id="form_lamination_1" class="d-none">
                            <p><span class="font-weight-bold">Ламинация 1</span>&nbsp;&nbsp;<span style="color: gray;">(325 руб   34кг   600мм) <i class="fas fa-info-circle" title="325 руб   34кг   600мм" data-placement="top"></i></span></p>
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
                                    <button type="button" class="btn btn-light" onclick="javascript: HideLamination1();"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                            <div id="show_lamination_2">
                                <button type="button" class="btn btn-light" onclick="javascript: ShowLamination2();"><i class="fas fa-plus"></i>&nbsp;Добавить ламинацию</button>
                            </div>
                            <!-- Ламинация 2 -->
                            <div id="form_lamination_2" class="d-none">
                                <p><span class="font-weight-bold">Ламинация 2</span>&nbsp;&nbsp;<span style="color: gray;">(325 руб   34кг   600мм) <i class="fas fa-info-circle" title="325 руб   34кг   600мм" data-placement="top"></i></span></p>
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
                                        <button type="button" class="btn" onclick="javascript: HideLamination2();"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3 d-none" id="lam-only">
                            <!-- Обрезная ширина -->
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" id="width" name="width" class="form-control int-only" placeholder="Обрезная ширина, мм" value="<?=$width ?>" />
                                    <div class="invalid-feedback">Обрезная ширина обязательно</div>
                                </div>
                            </div>
                            <!-- Количество ручьёв -->
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" id="streamscount" name="streamscount" class="form-control int-only" placeholder="Количество ручьев" value="<?=$streamscount ?>" />
                                    <div class="invalid-feedback">Количество ручьев обязательно</div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="create_calculation_submit" name="create_calculation_submit" class="btn btn-dark mt-3 d-none">Рассчитать</button>
                    </form>
                </div>
                <!-- Правая половина -->
                <div class="col-6 col-lg-3">
                    <!-- Расчёт -->
                    <div id="calculation">
                        <h1>Расчет</h1>
                        <div class="d-table w-100">
                            <div class="d-table-row">
                                <div class="d-table-cell pb-2 pt-2">
                                    <div style="font-size: x-small;">Наценка</div>
                                    10%
                                </div>
                                <div class="d-table-cell pb-2 pt-2 pl-3" style="color: gray; border: solid 1px gray; border-radius: 10px;">
                                    <div style="font-size: x-small;">Курс евро</div>
                                    93
                                </div>
                            </div>
                            <div class="d-table-row">
                                <div class="d-table-cell pb-2 pt-2">
                                    Себестоимость
                                    <div class="font-weight-bold" style="font-size: large;">1 200 000 руб.</div>
                                </div>
                                <div class="d-table-cell pb-2 pt-2 pl-3">
                                    За 1 кг
                                    <div class="font-weight-bold" style="font-size: large;">765 руб.</div>
                                </div>
                            </div>
                            <div class="d-table-row">
                                <div class="d-table-cell pb-2 pt-2">
                                    Отгрузочная стоимость
                                    <div class="font-weight-bold" style="font-size: large;">800 000 руб.</div>
                                </div>
                                <div class="d-table-cell pb-2 pt-2 pl-3">
                                    За 1 кг
                                    <div class="font-weight-bold" style="font-size: large;">978 руб.</div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-light" id="show_costs" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать расходы</button>
                        <div id="costs" class="d-none">
                            <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть расходы</button>
                            <div class="d-table w-100">
                                <div class="d-table-row">
                                    <div class="d-table-cell pb-2 pt-2">
                                        Отходы
                                        <div class="font-weight-bold" style="font-size: large;">1 280 руб.</div>
                                    </div>
                                    <div class="d-table-cell pb-2 pt-2 pl-3">
                                        <br />
                                        <div class="font-weight-bold" style="font-size: large;">4,5 кг.</div>
                                    </div>
                                </div>
                                <div class="d-table-row">
                                    <div class="d-table-cell pb-2 pt-2">
                                        Клей
                                        <div class="font-weight-bold" style="font-size: large;">800 000 руб.</div>
                                    </div>
                                    <div class="d-table-cell pb-2 pt-2 pl-3">
                                        <br />
                                        <div class="font-weight-bold" style="font-size: large;">1,0 кг.</div>
                                    </div>
                                </div>
                                <div class="d-table-row">
                                    <div class="d-table-cell pb-2 pt-2">
                                        Работа ламинатора
                                        <div class="font-weight-bold">230 руб.</div>
                                    </div>
                                    <div class="d-table-cell pb-2 pt-2 pl-3">
                                        <br />
                                        <div class="font-weight-bold" style="font-size: large;">1,5 ч.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form method="post">
                            <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                            <input type="hidden" id="change_status_submit" name="change_status_submit" />
                            <button type="submit" id="status_id" name="status_id" value="2" class="btn btn-outline-dark mt-3">Сделать КП</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/select2.min.js"></script>
        <script src="<?=APPLICATION ?>/js/i18n/ru.js"></script>
        <script>
            // Список с  поиском
            $('#customer_id').select2({
                placeholder: "Заказчик...",
                maximumSelectionLength: 1,
                language: "ru"
            });
            
            // Всплывающая подсказка
            $(function() {
                $("i.fa-info-circle").tooltip({
                    position: {
                        my: "left center",
                        at: "right+10 center"
                    }
                });
            });
    
            // Маска телефона заказчика
            $.mask.definitions['~'] = "[+-]";
            $("#customer_phone").mask("+7 (999) 999-99-99");
    
            // При щелчке в поле телефона, устанавливаем курсор в самое начало ввода телефонного номера.
            $("#customer_phone").click(function(){
                var maskposition = $(this).val().indexOf("_");
                if(Number.isInteger(maskposition)) {
                    $(this).prop("selectionStart", maskposition);
                    $(this).prop("selectionEnd", maskposition);
                }
            });
    
            // Если у объекта имеется ламинация 1, показываем ламинацию 1
            <?php if(!empty($lamination1_brand_name)): ?>
            ShowLamination1();
            <?php endif; ?>
                
            // Если у объекта имеется ламинация 2, показываем ламинацию 2
            <?php if(!empty($lamination2_brand_name)): ?>
            ShowLamination2();
            <?php endif; ?>
                
            // Показ марки плёнки и толщины для ламинации 1
            function ShowLamination1() {
                $('#form_lamination_1').removeClass('d-none');
                $('#show_lamination_1').addClass('d-none');
                $('#main_film_title').removeClass('d-none');
                $('#film_title').addClass('d-none');
                $('#lamination1_brand_name').attr('required', 'required');
                $('#lamination1_thickness').attr('required', 'required');
                $('#lam-only').removeClass('d-none');
                $('#width').attr('required', 'required');
                $('#streamscount').attr('required', 'required');
            }
            
            // Скрытие марки плёнки и толщины для ламинации 1
            function HideLamination1() {
                $('#lamination1_brand_name').val('');
                $('#lamination1_brand_name').change();
                
                $('#form_lamination_1').addClass('d-none');
                $('#show_lamination_1').removeClass('d-none');
                $('#main_film_title').addClass('d-none');
                $('#film_title').removeClass('d-none');
                $('#lamination1_brand_name').removeAttr('required');
                $('#lamination1_thickness').removeAttr('required');
                $('#lam-only').addClass('d-none');
                $('#width').removeAttr('required');
                $('#streamscount').removeAttr('required');
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
                $('#lamination2_brand_name').val('');
                $('#lamination2_brand_name').change();
                
                $('#form_lamination_2').addClass('d-none');
                $('#show_lamination_2').removeClass('d-none');
                $('#hide_lamination_1').removeClass('d-none');
                $('#lamination2_brand_name').removeAttr('required');
                $('#lamination2_thickness').removeAttr('required');
            }
            
            // Обработка выбора типа плёнки основной плёнки: перерисовка списка толщин
            $('#brand_name').change(function(){
                if($(this).val() == "") {
                    $('#thickness').html("<option value=''>Толщина...</option>");
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
                    $('#lamination1_thickness').html("<option value=''>Толщина...</option>");
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
                    $('#lamination2_thickness').html("<option value=''>Толщина...</option>");
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
            
            // Скрытие расчёта
            function HideCalculation() {
                $("#calculation").hide();
                $("#create_calculation_submit").removeClass("d-none");
            }
            
            // Скрытие расчёта при изменении значения полей
            $("input[id!='extra_charge']").change(function () {
                HideCalculation();
            });
            
            $('select').change(function () {
                HideCalculation();
            });
            
            $("input[id!='extra_charge']").keydown(function () {
                HideCalculation();
            });
            
            // Скрытие расчёта при создании нового заказчика
            <?php if(null !== filter_input(INPUT_POST, 'create_customer_submit')): ?>
                HideCalculation();
            <?php endif; ?>
                
            // Скрытие расчёта при создании нового расчёта
            <?php if(null === filter_input(INPUT_GET, 'id')): ?>
                HideCalculation();
            <?php endif; ?>
        </script>
    </body>
</html>