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
$quantity_valid = '';

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
    
    if(empty(filter_input(INPUT_POST, 'quantity'))) {
        $quantity_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $date = date('Y-m-d');
        $customer_id = filter_input(INPUT_POST, 'customer_id');
        $name = addslashes(filter_input(INPUT_POST, 'name'));
        $work_type_id = filter_input(INPUT_POST, 'work_type_id');
        $brand_name = addslashes(filter_input(INPUT_POST, 'brand_name'));
        $thickness = filter_input(INPUT_POST, 'thickness');
        
        $unit = filter_input(INPUT_POST, 'unit');
        $machine_type_id = filter_input(INPUT_POST, 'machine_type_id');
        if(empty($machine_type_id)) $machine_type_id = "NULL";
        
        $lamination1_brand_name = addslashes(filter_input(INPUT_POST, 'lamination1_brand_name'));
        $lamination1_thickness = filter_input(INPUT_POST, 'lamination1_thickness');
        if(empty($lamination1_thickness)) $lamination1_thickness = "NULL";
        $lamination2_brand_name = addslashes(filter_input(INPUT_POST, 'lamination2_brand_name'));
        $lamination2_thickness = filter_input(INPUT_POST, 'lamination2_thickness');
        if(empty($lamination2_thickness)) $lamination2_thickness = "NULL";
        
        $quantity = filter_input(INPUT_POST, 'quantity');
        $width = filter_input(INPUT_POST, 'width');
        if(empty($width)) $width = "NULL";
        $length = filter_input(INPUT_POST, 'length');
        if(empty($length)) $length = "NULL";
        $stream_width = filter_input(INPUT_POST, 'stream_width');
        if(empty($stream_width)) $stream_width = "NULL";
        $streams_count = filter_input(INPUT_POST, 'streams_count');
        if(empty($streams_count)) $streams_count = "NULL";
        $raport = filter_input(INPUT_POST, 'raport');
        if(empty($raport)) $raport = "NULL";
        $paints_count = filter_input(INPUT_POST, 'paints_count');
        if(empty($paints_count)) $paints_count = "NULL";
        
        $manager_id = GetUserId();
        $status_id = 1; // Статус "Расчёт"
        
        // Данные о цвете
        for($i=1; $i<=8; $i++) {
            $paint_var = "paint_$i";
            $$paint_var = filter_input(INPUT_POST, "paint_$i");
            
            $color_var = "color_$i";
            $$color_var = filter_input(INPUT_POST, "color_$i");
            
            $cmyk_var = "cmyk_$i";
            $$cmyk_var = filter_input(INPUT_POST, "cmyk_$i");
            
            $percent_var = "percent_$i";
            $$percent_var = filter_input(INPUT_POST, "percent_$i");
            if(empty($$percent_var)) $$percent_var = "NULL";
            
            $form_var = "form_$i";
            $$form_var = filter_input(INPUT_POST, "form_$i");
        }
        
        $sql = "insert into calculation (date, customer_id, name, work_type_id, brand_name, thickness, unit, machine_type_id, lamination1_brand_name, lamination1_thickness, lamination2_brand_name, lamination2_thickness, width, quantity, streams_count, length, stream_width, raport, paints_count, manager_id, status_id, "
                . "paint_1, paint_2, paint_3, paint_4, paint_5, paint_6, paint_7, paint_8, "
                . "color_1, color_2, color_3, color_4, color_5, color_6, color_7, color_8, "
                . "cmyk_1, cmyk_2, cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
                . "percent_1, percent_2, percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
                . "form_1, form_2, form_3, form_4, form_5, form_6, form_7, form_8) "
                . "values('$date', $customer_id, '$name', $work_type_id, '$brand_name', $thickness, '$unit', $machine_type_id, '$lamination1_brand_name', $lamination1_thickness, '$lamination2_brand_name', $lamination2_thickness, $width, $quantity, $streams_count, $length, $stream_width, $raport, $paints_count, $manager_id, $status_id, "
                . "'$paint_1', '$paint_2', '$paint_3', '$paint_4', '$paint_5', '$paint_6', '$paint_7', '$paint_8', "
                . "'$color_1', '$color_2', '$color_3', '$color_4', '$color_5', '$color_6', '$color_7', '$color_8', "
                . "'$cmyk_1', '$cmyk_2', '$cmyk_3', '$cmyk_4', '$cmyk_5', '$cmyk_6', '$cmyk_7', '$cmyk_8', "
                . "'$percent_1', '$percent_2', '$percent_3', '$percent_4', '$percent_5', '$percent_6', '$percent_7', '$percent_8', "
                . "'$form_1', '$form_2', '$form_3', '$form_4', '$form_5', '$form_6', '$form_7', '$form_8')";
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
    $extracharge = filter_input(INPUT_POST, 'extracharge');
    $sql = "update calculation set status_id=$status_id, extracharge=$extracharge where id=$id";
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
    $sql = "select date, customer_id, name, work_type_id, brand_name, thickness, unit, machine_type_id, lamination1_brand_name, lamination1_thickness, lamination2_brand_name, lamination2_thickness, "
            . "quantity, width, streams_count, length, stream_width, raport, paints_count, status_id, extracharge, "
            . "paint_1, paint_2, paint_3, paint_4, paint_5, paint_6, paint_7, paint_8, "
            . "color_1, color_2, color_3, color_4, color_5, color_6, color_7, color_8, "
            . "cmyk_1, cmyk_2, cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
            . "percent_1, percent_2, percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
            . "form_1, form_2, form_3, form_4, form_5, form_6, form_7, form_8 "
            . "from calculation where id=$id";
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

$unit = filter_input(INPUT_POST, "unit");
if(null === $unit) {
    if(isset($row['unit'])) $unit = $row['unit'];
    else $unit = null;
}

$machine_type_id = filter_input(INPUT_POST, 'machine_type_id');
if(null === $machine_type_id) {
    if(isset($row['machine_type_id'])) $machine_type_id = $row['machine_type_id'];
    else $machine_type_id = null;
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

$quantity = filter_input(INPUT_POST, 'quantity');
if(null === $quantity) {
    if(isset($row['quantity'])) $quantity = $row['quantity'];
    else $width = null;
}

$width = filter_input(INPUT_POST, 'width');
if(null === $width) {
    if(isset($row['width'])) $width = $row['width'];
    else $width = null;
}

$streams_count = filter_input(INPUT_POST, 'streams_count');
if(null === $streams_count) {
    if(isset($row['streams_count'])) $streams_count = $row['streams_count'];
    else $streams_count = null;
}

$length = filter_input(INPUT_POST, 'length');
if(null === $length) {
    if(isset($row['length'])) $length = $row['length'];
    else $length = null;
}

$stream_width = filter_input(INPUT_POST, 'stream_width');
if(null === $stream_width) {
    if(isset($row['stream_width'])) $stream_width = $row['stream_width'];
    else $stream_width = null;
}

$raport = filter_input(INPUT_POST, 'raport');
if(null === $raport) {
    if(isset($row['raport'])) $raport = $row['raport'];
    else $raport = null;
}

$paints_count = filter_input(INPUT_POST, 'paints_count');
if(null === $paints_count) {
    if(isset($row['paints_count'])) $paints_count = $row['paints_count'];
    else $paints_count = null;
}

if(isset($row['status_id'])) $status_id = $row['status_id'];
else $status_id = null;

if(isset($row['extracharge'])) $extracharge = $row['extracharge'];
else $extracharge = 0;

// Данные о цветах
for ($i=1; $i<=8; $i++) {
    $paint_var = "paint_$i";
    $$paint_var = filter_input(INPUT_POST, "paint_$i");
    if(null === $$paint_var) {
        if(isset($row["paint_$i"])) $$paint_var = $row["paint_$i"];
        else $$paint_var = null;
    }
    
    $color_var = "color_$i";
    $$color_var = filter_input(INPUT_POST, "color_$i");
    if(null === $$color_var) {
        if(isset($row["color_$i"])) $$color_var = $row["color_$i"];
        else $$color_var = null;
    }
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = filter_input(INPUT_POST, "cmyk_$i");
    if(null === $$cmyk_var) {
        if(isset($row["cmyk_$i"])) $$cmyk_var = $row["cmyk_$i"];
        else $$cmyk_var = null;
    }
    
    $percent_var = "percent_$i";
    $$percent_var = filter_input(INPUT_POST, "percent_$i");
    if(null === $$percent_var) {
        if(isset($row["percent_$i"])) $$percent_var = $row["percent_$i"];
        else $$percent_var = null;
    }
    
    $form_var = "form_$i";
    $$form_var = filter_input(INPUT_POST, "form_$i");
    if(null === $$form_var) {
        if(isset($row["form_$i"])) $$form_var = $row["form_$i"];
        else $$form_var = null;
    }
}
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
                <a href="<?=APPLICATION ?>/calculation/">Назад</a>
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
                        <!-- Единица заказа -->
                        <?php
                        $kg_checked = ($unit == "kg" || empty($unit)) ? " checked='checked'" : "";
                        $thing_checked = $unit == "thing" ? " checked='checked'" : "";
                        ?>
                        <div class="form-check-inline work-type-lam-only d-none">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="unit" value="kg"<?=$kg_checked ?> />Килограммы
                            </label>
                        </div>
                        <div class="form-check-inline work-type-lam-only d-none">
                            <label class="form-check-inline work-type-lam-only">
                                <input type="radio" class="form-check-input" name="unit" value="thing"<?=$thing_checked ?> />Штуки
                            </label>
                        </div>
                        <!-- Печатная машина -->
                        <div class="form-group work-type-lam-only d-none">
                            <select id="machine_type_id" name="machine_type_id" class="form-control work-type-lam-only d-none">
                                <option value="">Печатная машина...</option>
                                <?php
                                $sql = "select id, name from machine_type";
                                $fetcher = new Fetcher($sql);
                                
                                while ($row = $fetcher->Fetch()):
                                $selected = '';
                                if($row['id'] == $machine_type_id) {
                                    $selected = " selected='selected'";
                                }
                                ?>
                                <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                <?php
                                endwhile;
                                ?>
                            </select>
                        </div>
                        <!-- Объем заказа -->
                        <div class="row mt-3">
                            <!-- Объем заказа -->
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" id="quantity" name="quantity" class="form-control int-only" placeholder="Объем заказа, кг" value="<?=$quantity ?>" required="required" />
                                    <div class="invalid-feedback">Объем заказа обязательно</div>
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
                                        <button type="button" class="btn btn-light" onclick="javascript: HideLamination2();"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <!-- Обрезная ширина -->
                            <div class="col-6 lam-only lam-only-work-type-no-lam d-none">
                                <div class="form-group">
                                    <input type="text" id="width" name="width" class="form-control int-only lam-only lam-only-work-type-no-lam d-none" placeholder="Обрезная ширина, мм" value="<?=$width ?>" />
                                    <div class="invalid-feedback">Обрезная ширина обязательно</div>
                                </div>
                            </div>
                            <!-- Длина от метки до метки -->
                            <div class="col-6 work-type-lam-only d-none">
                                <div class="form-group">
                                    <input type="text" id="length" name="length" class="form-control float-only work-type-lam-only d-none" placeholder="Длина от метки до метки" value="<?=$length ?>" />
                                    <div class="invalid-feedback">Длина от метки до метки обязательно</div>
                                </div>
                            </div>
                            <!-- Ширина ручья -->
                            <div class="col-6 work-type-lam-only d-none">
                                <div class="form-group">
                                    <input type="text" id="stream_width" name="stream_width" class="form-control work-type-lam-only d-none" placeholder="Ширина ручья" value="<?=$stream_width ?>" />
                                    <div class="invalid-feedback">Ширина ручья обязательно</div>
                                </div>
                            </div>
                            <!-- Количество ручьёв -->
                            <div class="col-6 lam-only lam-only-work-type-no-lam work-type-lam-only d-none">
                                <div class="form-group">
                                    <input type="text" id="streams_count" name="streams_count" class="form-control int-only lam-only lam-only-work-type-no-lam work-type-lam-only d-none" placeholder="Количество ручьев" value="<?=$streams_count ?>" />
                                    <div class="invalid-feedback">Количество ручьев обязательно</div>
                                </div>
                            </div>
                            <!-- Рапорт -->
                            <div class="col-6 work-type-lam-only d-none">
                                <div class="form-group">
                                    <select id="raport" name="raport" class="form-control work-type-lam-only d-none">
                                        <option value="">Рапорт...</option>
                                        <?php
                                        $selected = "";
                                        if($raport == 1.111) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                        <option<?=$selected ?>>1.111</option>
                                        <?php
                                        $selected = "";
                                        if($raport == 2.222) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                        <option<?=$selected ?>>2.222</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Количество красок -->
                        <div class="form-group mt-3">
                            <select id="paints_count" name="paints_count" class="form-control work-type-lam-only d-none">
                                <option value="">Количество красок...</option>
                                <?php
                                for($i = 1; $i <= 8; $i++):
                                $selected = "";
                                if($paints_count == $i) {
                                    $selected = " selected='selected'";
                                }
                                ?>
                                <option<?=$selected ?>><?=$i ?></option>
                                <?php
                                endfor;
                                ?>
                            </select>
                        </div>
                        <!-- Каждая краска -->
                        <?php
                        for($i=1; $i<=8; $i++):
                        $block_class = " d-none";

                        if(!empty($paints_count) && is_numeric($paints_count) && $i <= $paints_count) {
                            $block_class = "";
                        }
                        ?>
                        <div class="row paint_block<?=$block_class ?>" id="paint_block_<?=$i ?>">
                            <?php
                            $paint_class = " col-12";
                            $cmyk_class = " d-none";
                            $color_class = " d-none";
                            $percent_class = " d-none";
                            $percent_cmyk_class = " d-none";
                            $form_class = " d-none";
                            $form_cmyk_class = " d-none";
                            
                            $paint_var_name = "paint_$i";
                            
                            if($$paint_var_name == "white" || $$paint_var_name == "lacquer") {
                                $paint_class = " col-6";
                                $form_class = " col-6";
                            }
                            else if($$paint_var_name == "panton") {
                                $paint_class = " col-3";
                                $color_class = " col-3";
                                $percent_class = " col-3";
                                $form_class = " col-3";
                            }
                            else if($$paint_var_name == "cmyk") {
                                $paint_class = " col-3";
                                $cmyk_class = " col-3";
                                $percent_cmyk_class = " col-3";
                                $form_cmyk_class = " col-3";
                            }
                            ?>
                            <div class="form-group<?=$paint_class ?>" id="paint_group_<?=$i ?>">
                                <select id="paint_<?=$i ?>" name="paint_<?=$i ?>" class="form-control paint" data-id="<?=$i ?>">
                                    <option value="">Цвет...</option>
                                    <?php
                                    $cmyk_selected = "";
                                    $panton_selected = "";
                                    $white_selected = "";
                                    $lacquer_selected = "";
                                    
                                    $selected_var_name = $$paint_var_name."_selected";
                                    $$selected_var_name = " selected='selected'";
                                    ?>
                                    <option value="cmyk"<?=$cmyk_selected ?>>CMYK</option>
                                    <option value="panton"<?=$panton_selected ?>>Пантон</option>
                                    <option value="white"<?=$white_selected ?>>Белый</option>
                                    <option value="lacquer"<?=$lacquer_selected ?>>Лак</option>
                                </select>
                            </div>
                            <div class="form-group<?=$color_class ?>" id="color_group_<?=$i ?>">
                                <?php $color_var = "color_$i"; ?>
                                <input type="text" id="color_<?=$i ?>" name="color_<?=$i ?>" class="form-control color" placeholder="Цвет..." value="<?=$$color_var ?>" />
                            </div>
                            <div class="form-group<?=$cmyk_class ?>" id="cmyk_group_<?=$i ?>">
                                <select id="cmyk_<?=$i ?>" name="cmyk_<?=$i ?>" class="form-control cmyk" data-id="<?=$i ?>">
                                    <?php
                                    $cyan_selected = "";
                                    $magenda_selected = "";
                                    $yellow_selected = "";
                                    $kontur_selected = "";
                                    
                                    $cmyk_var = "cmyk_$i";
                                    $cmyk_selected_var = $$cmyk_var."_selected";
                                    $$cmyk_selected_var = " selected='selected'";
                                    ?>
                                    <option value="cyan"<?=$cyan_selected ?>>Cyan</option>
                                    <option value="magenda"<?=$magenda_selected ?>>Magenda</option>
                                    <option value="yellow"<?=$yellow_selected ?>>Yellow</option>
                                    <option value="kontur"<?=$kontur_selected ?>>Kontur</option>
                                </select>
                            </div>
                            <div class="form-group<?=$percent_class ?>" id="percent_group_<?=$i ?>">
                                <?php
                                $percent_var = "percent_$i";
                                $percent_value = empty($$percent_var) ? "" : $$percent_var;
                                ?>
                                <input type="text" id="percent_<?=$i ?>" name="percent_<?=$i ?>" class="form-control percent" value="<?=$percent_value ?>" />
                            </div>
                            <div class="form-group<?=$percent_cmyk_class ?>" id="percent_group_cmyk_<?=$i ?>">
                                <input type="text" id="percent_cyan_<?=$i ?>" name="percent_cyan_<?=$i ?>" class="form-control percent percent_<?=$i ?>" />
                                <input type="hidden" id="percent_magenda_<?=$i ?>" name="percent_magenda_<?=$i ?>" class="form-control percent percent_<?=$i ?>" />
                                <input type="hidden" id="percent_yellow_<?=$i ?>" name="percent_yellow_<?=$i ?>" class="form-control percent percent_<?=$i ?>" />
                                <input type="hidden" id="percent_kontur_<?=$i ?>" name="percent_kontur_<?=$i ?>" class="form-control percent percent_<?=$i ?>" />
                            </div>
                            <div class="form-group<?=$form_class ?>" id="form_group_<?=$i ?>">
                                <select id="form_<?=$i ?>" name="form_<?=$i ?>" class="form-control form">
                                    <?php
                                    $flint_selected = "";
                                    $kodak_selected = "";
                                    
                                    $form_var = "form_$i";
                                    $form_selected_var = $$form_var."_selected";
                                    $$form_selected_var = " selected='selected'";
                                    ?>
                                    <option value="flint"<?=$flint_selected ?>>Флинт</option>
                                    <option value="kodak"<?=$kodak_selected ?>>Кодак</option>
                                </select>
                            </div>
                            <div class="form-group col-3<?=$form_cmyk_class ?>" id="form_group_cmyk_<?=$i ?>">
                                <select id="form_select_cyan_<?=$i ?>" name="form_cyan_<?=$i ?>" class="form-control form_select form_select_<?=$i ?>" data-cmyk="cyan" data-id="<?=$i ?>">
                                    <option value="flint">Флинт</option>
                                    <option value="kodak">Кодак</option>
                                </select>
                                <select id="form_select_magenda_<?=$i ?>" name="form_magenda_<?=$i ?>" class="form-control form_select form_select_<?=$i ?> d-none" data-cmyk="magenda" data-id="<?=$i ?>">
                                    <option value="flint">Флинт</option>
                                    <option value="kodak">Кодак</option>
                                </select>
                                <select id="form_select_yellow_<?=$i ?>" name="form_yellow_<?=$i ?>" class="form-control form_select form_select_<?=$i ?> d-none" data-cmyk="yellow" data-id="<?=$i ?>">
                                    <option value="flint">Флинт</option>
                                    <option value="kodak">Кодак</option>
                                </select>
                                <select id="form_select_kontur_<?=$i ?>" name="form_kontur_<?=$i ?>" class="form-control form_select form_select_<?=$i ?> d-none" data-cmyk="kontur" data-id="<?=$i ?>">
                                    <option value="flint">Флинт</option>
                                    <option value="kodak">Кодак</option>
                                </select>
                                <input type="hidden" id="form_cyan_<?=$i ?>" name="form_cyan_<?=$i ?>" value="flint" />
                                <input type="hidden" id="form_magenda_<?=$i ?>" name="form_magenda_<?=$i ?>" value="flint" />
                                <input type="hidden" id="form_yellow_<?=$i ?>" name="form_yellow_<?=$i ?>" value="flint" />
                                <input type="hidden" id="form_kontur_<?=$i ?>" name="form_kontur_<?=$i ?>" value="flint" />
                            </div>
                        </div>
                        <?php
                        endfor;
                        ?>
                        <button type="submit" id="create_calculation_submit" name="create_calculation_submit" class="btn btn-dark mt-3 d-none">Рассчитать</button>
                    </form>
                </div>
                <!-- Правая половина -->
                <div class="col-6 col-lg-3">
                    <!-- Расчёт -->
                    <?php
                    include './right_panel.php';
                    ?>
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
            
            // Автовыделение при щелчке для поля "наценка"
            $('#extracharge').click(function() {
                $(this).prop("selectionStart", 0);
                $(this).prop("selectionEnd", $(this).val().length);
            });
            
            // При смене типа работы: если тип работы "плёнка с печатью", показываем поля, предназначенные только для плёнки с печатью
            $('#work_type_id').change(function() {
                if($(this).val() == 2) {
                    WorkTypeFilmWithPrint();
                }
                else {
                    WorkTypeFilmWithoutPrint();
                }
            });
            
            // Если тип работы "Плёнка с печатью", то показываем поля, предназначенные только для пленки с печатью
            <?php if($work_type_id == 2): ?>
            WorkTypeFilmWithPrint();
            <?php endif; ?>
                
            function WorkTypeFilmWithPrint() {
                // Скрываем поля только с ламинацией
                $('.lam-only').addClass('d-none');
                $('input.lam-only').removeAttr('required');
                $('select.lam-only').removeAttr('required');
                
                // Показываем поля для плёнки с печатью
                $('.work-type-lam-only').removeClass('d-none');
                $('input.work-type-lam-only').attr('required', 'required');
                $('select.work-type-lam-only').attr('required', 'required');
                
                // Делаем поля, зависимые от ламинации, независимыми
                $('.lam-only-work-type-no-lam').removeClass('lam-only');
            }
            
            function WorkTypeFilmWithoutPrint() {
                // Скрываем поля для плёнки с печатью
                $('.work-type-lam-only').addClass('d-none');
                $('input.work-type-lam-only').removeAttr('required');
                $('select.work-type-lam-only').removeAttr('required');
                
                // Восстанавливаем зависимость полей от ламинации
                $('.lam-only-work-type-no-lam').addClass('lam-only');
                
                // Показываем поля только с ламинацией
                <?php if(!empty($lamination1_brand_name)): ?>
                $('.lam-only').removeClass('d-none');
                $('input.lam-only').attr('required', 'required');
                $('select.lam-only').attr('required', 'required');
                <?php endif; ?>
            }
            
            // Если единица объёма - кг, то в поле "Объём" пишем "Объём, кг", иначе "Объем, шт"
            if($('input[value=kg]').is(':checked')) {
                $('#quantity').attr('placeholder', 'Объем заказа, кг');
            }
            
            if($('input[value=thing]').is(':checked')) {
                $('#quantity').attr('placeholder', 'Объем заказа, шт');
            }
                
            $('input[name=unit]').click(function(){
                if($(this).val() == 'kg') {
                    $('#quantity').attr('placeholder', 'Объем заказа, кг');
                }
                else {
                    $('#quantity').attr('placeholder', 'Объем заказа, шт');
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
                $('.lam-only').removeClass('d-none');
                $('input.lam-only').attr('required', 'required');
                $('select.lam-only').attr('required', 'required');
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
                $('.lam-only').addClass('d-none');
                $('input.lam-only').removeAttr('required');
                $('select.lam-only').removeAttr('required');
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
            
            // Обработка выбора количества красок
            $('#paints_count').change(function(){
                var count = $(this).val();
                $('.paint_block').addClass('d-none');
                $('.paint').removeAttr('required');
                
                if(count != '') {
                    iCount = parseInt(count);
                    
                    for(var i=1; i<=iCount; i++) {
                        $('#paint_block_' + i).removeClass('d-none');
                        $('#paint_' + i).attr('required', 'required');
                    }
                }
            });
            
            // Обработка выбора краски
            $('.paint').change(function(){
                paint = $(this).val();
                var data_id = $(this).attr('data-id');
                
                // Устанавливаем видимость всех элементов по умолчанию, как если бы выбрали пустое значение
                $('#paint_group_' + data_id).removeClass('col-12');
                $('#paint_group_' + data_id).removeClass('col-6');
                $('#paint_group_' + data_id).removeClass('col-3');
                
                $('#color_group_' + data_id).addClass('d-none');
                $('.color').removeAttr('required');
                
                $('#percent_group_' + data_id).addClass('d-none');
                $('#percent_group_cmyk_' + data_id).addClass('d-none');
                $('.percent').removeAttr('required');
                
                $('#form_group_' + data_id).removeClass('col-6');
                $('#form_group_' + data_id).removeClass('col-3');
                $('#form_group_' + data_id).addClass('d-none');
                $('#form_group_cmyk_' + data_id).addClass('d-none');
                
                $('#cmyk_group_' + data_id).removeClass('col-3');
                $('#cmyk_group_' + data_id).addClass('d-none');
                
                // Затем, в зависимости от выбранного значения, устанавливаем видимость нужного элемента для этого значения
                if(paint == 'lacquer')  {
                    $('#paint_group_' + data_id).addClass('col-6');
                    $('#form_group_' + data_id).addClass('col-6');
                    $('#form_group_' + data_id).removeClass('d-none');
                }
                else if(paint == 'white') {
                    $('#paint_group_' + data_id).addClass('col-6');
                    $('#form_group_' + data_id).addClass('col-6');
                    $('#form_group_' + data_id).removeClass('d-none');
                }
                else if(paint == 'cmyk') {
                    $('#paint_group_' + data_id).addClass('col-3');
                    $('#cmyk_group_' + data_id).addClass('col-3');
                    $('#cmyk_group_' + data_id).removeClass('d-none');
                    $('#percent_group_cmyk_' + data_id).addClass('col-3');
                    $('#percent_group_cmyk_' + data_id).removeClass('d-none');
                    $('#form_group_cmyk_' + data_id).addClass('col-3');
                    $('#form_group_cmyk_' + data_id).removeClass('d-none');
                    
                    // Устанавливаем по умолчанию видимым значение CYAN, и делаем видимыми поля процента и формы для CYAN
                    $('#cmyk_' + data_id).val('cyan');
                    $(".percent_" + data_id).attr('type', 'hidden');
                    $('#percent_cyan_' + data_id).attr('type', 'text');
                    $(".form_select_" + data_id).addClass('d-none');
                    $('#form_select_cyan_' + data_id).removeClass('d-none');
                    
                    // Устанавливаем значения атрибутов "обязательное поле" для поля процента для значения CYAN
                    $('.percent_' + data_id).removeAttr('required');
                    $('#percent_cyan_' + data_id).attr('required', 'required');
                }
                else if(paint == 'panton') {
                    $('#paint_group_' + data_id).addClass('col-3');
                    $('#color_group_' + data_id).removeClass('d-none');
                    $('#color_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#form_group_' + data_id).removeClass('d-none');
                    $('#form_group_' + data_id).addClass('col-3');
                    
                    $('#color_' + data_id).attr('required', 'required');
                    $('#percent_' + data_id).attr('required', 'required');
                }
                else {
                    $('#paint_group_' + data_id).addClass('col-12');
                }
            });
            
            // Обработка выбора компонента цвета
            $(".cmyk").change(function(){
                cmyk = $(this).val();
                var data_id = $(this).attr('data-id');
                
                // Делаем видимой поле процента для этого значения CMYK
                $(".percent_" + data_id).attr('type', 'hidden');
                $("#percent_" + cmyk + "_" + data_id).attr('type', 'text');
                
                // Делаем видимым список форм для этого значения CMYK
                $(".form_select_" + data_id).addClass('d-none');
                $("#form_select_" + cmyk + "_" + data_id).removeClass('d-none');
                
                // Устанавливаем атрибут "обязательное поле" для этого значения CMYK
                $(".percent_" + data_id).removeAttr('required');
                $("#percent_" + cmyk + "_" + data_id).attr('required', 'required');
            });
            
            // Обработка выбора формы
            $(".form_select").change(function(){
                form = $(this).val();
                var data_cmyk = $(this).attr('data-cmyk');
                var data_id = $(this).attr('data-id');
                
                $("#form_" + data_cmyk + "_" + data_id).val(form);
            });
            
            // Маска % для поля "процент"
            $(".percent").mask("99%", { autoclear: false });
            
            // Фильтрация ввода в поле "наценка"
            $('.percent').keypress(function(e) {
                if(/\D/.test(String.fromCharCode(e.charCode))) {
                    return false;
                }
            });
            
            $('.percent').change(function(e) {
                var val = $(this).val();
                val = val.replace(/[^\d\%]/g, '');
                $(this).val(val);
            });
            
            $('.percent').change();
            
            // Автовыделение при щелчке для поля "наценка"
            $('.percent').click(function() {
                $(this).prop("selectionStart", 0);
                $(this).prop("selectionEnd", $(this).val().length);
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
            $("input[id!=extracharge]").change(function () {
                HideCalculation();
            });
            
            $('select').change(function () {
                HideCalculation();
            });
            
            $("input[id!=extracharge]").keydown(function () {
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