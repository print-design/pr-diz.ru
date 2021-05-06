<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Создание заказчика
$customer_id = null;

if(null !== filter_input(INPUT_POST, 'create_customer_submit')) {
    if(!empty(filter_input(INPUT_POST, 'customer_name'))) {
        $name = addslashes(filter_input(INPUT_POST, 'customer_name'));
        $person = addslashes(filter_input(INPUT_POST, 'person'));
        $phone = filter_input(INPUT_POST, 'phone');
        $email = filter_input(INPUT_POST, 'email');
        
        $sql = "insert into customer (name, person, phone, email) values ('$name', '$person', '$phone', '$email')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $customer_id = $executer->insert_id;
    }
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$customer_id_valid = '';
$name_valid = '';
$work_type_valid = '';

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
    
    if($form_valid) {
        $date = date('Y-m-d');
        $customer_id = filter_input(INPUT_POST, 'customer_id');
        $name = addslashes(filter_input(INPUT_POST, 'name'));
        $weight = 24.5; // ВРЕМЕННОЕ ЗНАЧЕНИЕ
        $work_type_id = filter_input(INPUT_POST, 'work_type_id');
        $manager_id = GetUserId();
        $status_id = filter_input(INPUT_POST, 'status_id');
        
        $sql = "insert into calculation (date, customer_id, name, weight, work_type_id, manager_id, status_id) values('$date', $customer_id, '$name', $weight, $work_type_id, $manager_id, $status_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/calculation/');
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
        <link href="<?=APPLICATION ?>/css/jquery-ui.css" rel="stylesheet"/>
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
            <form method="post">
                <div class="row">
                    <!-- Левая половина -->
                    <div class="col-6">
                        <h1 style="font-size: 32px; line-height: 48px; font-weight: 600;">Новый расчет</h1>
                        <!-- Заказчик -->
                        <div class="row">
                            <div class="col-8">
                                <div class="form-group">
                                    <select id="customer_id" name="customer_id" class="form-control<?=$customer_id_valid ?>" required="required">
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
                            <input type="text" id="name" name="name" class="form-control<?=$name_valid ?>" placeholder="Название заказа" value="<?= filter_input(INPUT_POST, 'name') ?>" required="required" />
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
                                $work_type_id = filter_input(INPUT_POST, 'work_type_id');
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
                                        $brand_name = filter_input(INPUT_POST, 'brand_name');
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
                                        if(null !== filter_input(INPUT_POST, 'brand_name')) {
                                            $brand_name = filter_input(INPUT_POST, 'brand_name');
                                            $sql = "select distinct fbv.thickness, fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$brand_name' order by thickness";
                                            $thicknesses = (new Grabber($sql))->result;
                                            
                                            foreach ($thicknesses as $row):
                                            $selected = '';
                                            $thickness = filter_input(INPUT_POST, 'thickness');
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
                            <p class="font-weight-bold">Ламинация 1</p>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <select id="lamination1_brand_name" name="lamination1_brand_name" class="form-control">
                                            <option value="">Марка пленки...</option>
                                                <?php
                                                foreach ($brand_names as $row):
                                                $selected = '';
                                                $lamination1_brand_name = filter_input(INPUT_POST, 'lamination1_brand_name');
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
                                            if(null !== filter_input(INPUT_POST, 'lamination1_brand_name')) {
                                                $lamination1_brand_name = filter_input(INPUT_POST, 'lamination1_brand_name');
                                                $sql = "select distinct fbv.thickness, fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$lamination1_brand_name' order by thickness";
                                                $thicknesses = (new Grabber($sql))->result;
                                                
                                                foreach ($thicknesses as $row):
                                                $selected = '';
                                                $thickness = filter_input(INPUT_POST, 'lamination1_thickness');
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
                                <div class="col-1" id="hide_lamination_1">
                                    <button type="button" class="btn btn-light" onclick="javascript: HideLamination1();"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                            <div id="show_lamination_2">
                                <button type="button" class="btn btn-light" onclick="javascript: ShowLamination2();"><i class="fas fa-plus"></i>&nbsp;Добавить ламинацию</button>
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
                                                    $lamination2_brand_name = filter_input(INPUT_POST, 'lamination2_brand_name');
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
                                                if(null !== filter_input(INPUT_POST, 'lamination2_brand_name')) {
                                                    $lamination2_brand_name = filter_input(INPUT_POST, 'lamination2_brand_name');
                                                    $sql = "select distinct fbv.thickness, fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$lamination2_brand_name' order by thickness";
                                                    $thicknesses = (new Grabber($sql))->result;
                                                    
                                                    foreach ($thicknesses as $row):
                                                    $selected = "";
                                                    $thickness = filter_input(INPUT_POST, 'lamination2_thickness');
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
                                    <div class="col-1" id="hide_lamination_2">
                                        <button type="button" class="btn" onclick="javascript: HideLamination2();"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-8">
                                <div class="form-group">
                                    <input type="text" id="diameter" name="diameter" class="form-control int-only" placeholder="Диаметр намотки" value="<?= filter_input(INPUT_POST, 'diameter') ?>" required="required" />
                                    <div class="invalid-feedback">Диаметр намотки обязательно</div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="create_calculation_submit" name="create_calculation_submit" class="btn btn-dark" onclick="javascript: Calculate();">Рассчитать</button>
                    </div>
                    <!-- Правая половина -->
                    <div class="col-3">
                        <!-- Расчёт -->
                        <div id="calculation" class="d-none">
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
                            <input type="hidden" id="create_calculation_submit" name="create_calculation_submit" />
                            <button type="submit" id="status_id" name="status_id" value="2" class="btn btn-outline-dark w-75 mt-3">Сделать КП</button>
                            <button type="submit" id="status_id" name="status_id" value="6" class="btn btn-dark w-75 mt-3">Отправить в работу</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Форма добавления заказчика -->
        <div id="new_customer" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <i class="fas fa-user"></i>&nbsp;&nbsp;Новый заказчик
                            <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Название компании" required="required" />
                                <div class="invalid-feedback">Название компании обязательно</div>
                            </div>
                            <div class="form-group">
                                <input type="text" id="person" name="person" class="form-control" placeholder="Имя представителя" required="required" />
                                <div class="invalid-feedback">Имя представителя обязательно</div>
                            </div>
                            <div class="form-group">
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="Номер телефона" required="required" />
                                <div class="invalid-feedback">Номер телефона обязательно</div>
                            </div>
                            <div class="form-group">
                                <input type="email" id="email" name="email" class="form-control" placeholder="E-Mail" required="required" />
                                <div class="invalid-feedback">E-Mail обязательно</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-dark mt-3" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="create_customer_submit" name="create_customer_submit" class="btn btn-dark mt-3">Complete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script>
            // Если форма возвращается назад, как не прошедшая валидацию, и в ней была ламинация 1, показываем ламинацию 1
            <?php if(null !== filter_input(INPUT_POST, 'lamination1_brand_name')): ?>
            ShowLamination1();
            <?php endif; ?>
                
            // Если форма возвращается назад, как не прошедшая валидацию, и в ней была ламинация 2, показываем ламинацию 2
            <?php if(null !== filter_input(INPUT_POST, 'lamination2_brand_name')): ?>
            ShowLamination2();
            <?php endif; ?>
                
            // Если форма возвращается назад, как не прошедшая валидацию, показываем расчёт
            <?php if(null !== filter_input(INPUT_POST, 'create_calculation_submit')): ?>
            Calculate();
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
            
            // Расчёт
            function Calculate() {
                // Проверка полей формы
                $("#calculation").removeClass("d-none");
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