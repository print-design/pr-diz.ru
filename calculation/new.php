<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
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
            <div class="row">
                <!-- Левая половина -->
                <div class="col-6">
                    <div class="backlink">
                        <a href="<?=APPLICATION ?>/calculation/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </div>
                    <h1 style="font-size: 32px; line-height: 48px; font-weight: 600;">Новый расчет</h1>
                    <form method="post">
                        <!-- Заказчик -->
                        <div class="row">
                            <div class="col-8">
                                <div class="form-group">
                                    <select id="customer_id" name="customer_id" class="form-control" required="required">
                                        <option value="">Заказчик...</option>
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
                            <input type="text" id="name" name="name" class="form-control" placeholder="Название заказа" required="required" />
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
                                ?>
                                <option value="<?=$row['id'] ?>"><?=$row['name'] ?></option>
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
                                        $thicknesses = (new Grabber($sql))->result;
                                        
                                        foreach ($thicknesses as $row):
                                        ?>
                                        <option value="<?=$row['name'] ?>"><?=$row['name'] ?></option>
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
                                        <select id="lamination1_brand_name" name="lamination1_brand_name" class="form-control" required="required">
                                            <option value="">Марка пленки...</option>
                                                <?php
                                                foreach ($thicknesses as $row):
                                                ?>
                                            <option value="<?=$row['name'] ?>"><?=$row['name'] ?></option>
                                                <?php
                                                endforeach;
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group">
                                        <select id="lamination1_thickness" name="lamination1_thickness" class="form-control" required="required">
                                            <option value="">Толщина...</option>
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
                                            <select id="lamination2_brand_name" name="lamination2_brand_name" class="form-control" required="required">
                                                <option value="">Марка пленки...</option>
                                                    <?php
                                                    foreach ($thicknesses as $row):
                                                    ?>
                                                <option value="<?=$row['name'] ?>"><?=$row['name'] ?></option>
                                                    <?php
                                                    endforeach;
                                                    ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <select id="lamination2_thickness" name="lamination2_thickness" class="form-control" required="required">
                                                <option value="">Толщина...</option>
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
                                    <input type="text" id="diameter" name="diameter" class="form-control int-only" placeholder="Диаметр намотки" required="required" />
                                    <div class="invalid-feedback">Диаметр намотки обязательно</div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="create_calculation_submit" name="create_calculation_submit" class="btn btn-dark" onclick="javascript: Calculate();">Рассчитать</button>
                    </form>
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
                        
                        <button type="button" class="btn btn-outline-dark w-75 mt-3">Сделать КП</button>
                        <button type="button" class="btn btn-dark w-75 mt-3">Отправить в работу</button>
                    </div>
                </div>
            </div>
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
                                <input type="text" id="name" name="name" class="form-control" placeholder="Название компании" required="required" />
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
                            <button type="submit" class="btn btn-dark mt-3">Complete</button>
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
                HideLamination2();
            }
            
            // Скрытие марки плёнки и толщины для ламинации 1
            function HideLamination1() {
                $('#form_lamination_1').addClass('d-none');
                $('#show_lamination_1').removeClass('d-none');
                $('#main_film_title').addClass('d-none');
                HideLamination2();
            }
            
            // Показ марки плёнки и толщины для ламинации 2
            function ShowLamination2() {
                $('#form_lamination_2').removeClass('d-none');
                $('#show_lamination_2').addClass('d-none');
                $('#hide_lamination_1').addClass('d-none');
            }
            
            // Скрытие марки плёнки и толщины для ламинации 2
            function HideLamination2() {
                $('#form_lamination_2').addClass('d-none');
                $('#show_lamination_2').removeClass('d-none');
                $('#hide_lamination_1').removeClass('d-none');
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