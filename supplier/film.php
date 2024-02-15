<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка создания марки плёнки
$form_valid = true;
$film_insert_id = null;

if(null !== filter_input(INPUT_POST, 'create_film_submit')) {
    $name = filter_input(INPUT_POST, 'name');
    
    if(empty($name)) {
        $error_message = "Не указано название марки пленки";
        $form_valid = false;
    }
    
    $name = addslashes($name);
    $sql = "select count(id) from film where name = '$name'";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        if($row[0] != 0) {
            $error_message = "Такая марка пленки уже есть";
            $form_valid = false;
        }
    }
    
    if($form_valid) {
        $sql = "insert into film(name) values('$name')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $film_insert_id = $executer->insert_id;
    }
}

// Обработка создания вариации плёнки
if(null !== filter_input(INPUT_POST, 'create_film_variation_submit')) {
    $film_id = filter_input(INPUT_POST, 'film_id');
    if(empty($film_id)) {
        $error_message = "Не указана марка пленки";
        $form_valid = false;
    }
    
    $thickness = filter_input(INPUT_POST, 'thickness');
    if(empty($thickness)) {
        $error_message = "Не указана толщина";
        $form_valid = false;
    }
    
    $weight = filter_input(INPUT_POST, 'weight');
    if(empty($weight)) {
        $error_message = "Не указан удельный вес";
        $form_valid = false;
    }
    
    $sql = "select count(id) from film_variation where film_id = $film_id and thickness = $thickness and round(weight, 4) = $weight";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        if($row[0] != 0) {
            $error_message = "Для этой марки пленки уже есть такие параметры";
            $form_valid = false;
        }
    }
    
    if($form_valid) {
        $sql = "insert into film_variation(film_id, thickness, weight) values($film_id, $thickness, $weight)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Редактирование цены за материал
if(null !== filter_input(INPUT_POST, 'price_submit')) {
    $film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
    $price = filter_input(INPUT_POST, 'price');
    $currency = filter_input(INPUT_POST, 'currency');
    
    if(!empty($film_variation_id) && !empty($price) && !empty($currency)) {
        $eco_price = null;
        $eco_currency = '';
    
        $sql = "select eco_price, eco_currency from film_price where film_variation_id = $film_variation_id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $eco_price = $row['eco_price'];
            $eco_currency = $row['eco_currency'];
        }
    
        if(empty($eco_price)) {
            $eco_price = "NULL";
        }
    
        $sql = "insert into film_price (film_variation_id, price, currency, eco_price, eco_currency) values ($film_variation_id, $price, '$currency', $eco_price, '$eco_currency')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Редактирование экосбора
if(null !== filter_input(INPUT_POST, 'eco_price_submit')) {
    $film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
    $eco_price = filter_input(INPUT_POST, 'eco_price');
    $eco_currency = filter_input(INPUT_POST, 'eco_currency');
    
    if(!empty($film_variation_id) && !empty($eco_price) && !empty($eco_currency)) {
        $price = "NULL";
        $currency = '';
        
        $sql = "select price, currency from film_price where film_variation_id = $film_variation_id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $price = $row['price'];
            $currency = $row['currency'];
        }
        
        $sql = "insert into film_price (film_variation_id, price, currency, eco_price, eco_currency) values ($film_variation_id, $price, '$currency', $eco_price, '$eco_currency')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Редактирование экосбора с другого материала
if(null !== filter_input(INPUT_POST, 'other_price_submit')) {
    $price_type = filter_input(INPUT_POST, 'price_type');
    $price = filter_input(INPUT_POST, 'price');
    $currency = filter_input(INPUT_POST, 'currency');
    
    if(!empty($price_type) && !empty($price) && !empty($currency)) {
        $sql = "insert into other_price (price_type, price, currency) values ($price_type, $price, '$currency')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Получение объекта
$sql = "select f.id film_id, f.name film, fv.id film_variation_id, fv.thickness, fv.weight, fp.price, fp.currency, fp.eco_price, fp.eco_currency "
        . "from film f "
        . "left join film_variation fv on fv.film_id = f.id "
        . "left join (select film_variation_id, price, currency, eco_price, eco_currency from film_price where id in (select max(id) from film_price group by film_variation_id)) fp on fp.film_variation_id = fv.id "
        . "order by f.name, fv.thickness, fv.weight";
$fetcher = new Fetcher($sql);
$films = array();
while($row = $fetcher->Fetch()) {
    $film_id = $row['film_id'];
    if(!isset($films[$film_id])) {
        $films[$film_id] = array('name' => $row['film'], 'film_variations' => array());
    }
    
    $film_variation_id = $row['film_variation_id'];
    if(!isset($films[$film_id]['film_variations'][$film_variation_id]) && !empty($row['thickness']) && !empty($row['weight'])) {
        $films[$film_id]['film_variations'][$film_variation_id] = array('thickness' => $row['thickness'], 'weight' => $row['weight'], 'price' => $row['price'], 'currency' => $row['currency'], 'eco_price' => $row['eco_price'], 'eco_currency' => $row['eco_currency']);
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
            h2 {
                margin-top: 20px;
            }
            
            table.table tr th, table.table tr td {
                height: 55px;
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
        <div id="create_film_variation" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Добавить пленку</p>
                            <button type="button" class="close create_film_variation_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <select class="form-control" name="film_id" id="film_id" required="required">
                                    <option value="" hidden="hidden">Марка пленки</option>
                                    <?php foreach($films as $f_key => $film): ?>
                                    <option value="<?=$f_key ?>"><?=$film['name'] ?></option>
                                    <?php endforeach; ?>
                                    <option disabled="disabled">  </option>
                                    <option value="+">+&nbsp;Новая марка</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" id="thickness" name="thickness" class="form-control int-only" placeholder="Толщина" required="required" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" id="weight" name="weight" class="form-control float-only" placeholder="Удельный вес" required="required" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" id="create_film_variation_submit" name="create_film_variation_submit">Добавить</button>
                            <button type="button" class="btn btn-light create_film_variation_dismiss" data-dismiss="modal">Отменить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="create_film" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Введите марку пленки</p>
                            <button type="button" class="close create_film_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="name" id="name" class="form-control" placeholder="Марка пленки" required="required" autocomplete="off" />
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" id="create_film_submit" name="create_film_submit">Добавить</button>
                            <button type="button" class="btn btn-light create_film_dismiss" data-dismiss="modal">Отменить</button>
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
                    <h1>Пленка</h1>
                </div>
                <div class="pt-1">
                    <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))): ?>
                    <button class="btn btn-dark" data-toggle="modal" data-target="#create_film_variation">
                        <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить тип пленки
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $show_table_header = true;
            foreach($films as $f_key => $film):
            ?>
            <h2 id="f_<?=$f_key ?>"><?=$film['name'] ?></h2>
            <table class="table">
                <?php if($show_table_header): ?>
                <tr>
                    <th style="width: 50%; border-top: 0;">Название пленки</th>
                    <th style="width: 10%; border-top: 0;">Толщина</th>
                    <th style="width: 10%; border-top: 0;">Удельный вес</th>
                    <th style="width: 15%; border-top: 0;">Цена</th>
                    <th style="border-top: 0;">Экосбор</th>
                </tr>
                <?php
                endif;
                $no_border_top = $show_table_header ? '' : " border-top: 0;";
                foreach($film['film_variations'] as $fv_key => $film_variation):
                ?>
                <tr>
                    <td style="width: 50%;<?=$no_border_top ?>"><?=$film['name'] ?></td>
                    <td style="width: 10%;<?=$no_border_top ?>"><?=$film_variation['thickness'] ?> мкм</td>
                    <td style="width: 10%;<?=$no_border_top ?>"><?=$film_variation['weight'] ?> г/м<sup>2</sup></td>
                    <td style="width: 15%;<?=$no_border_top ?>">
                        <form class="form-inline" method="post">
                            <input type="hidden" name="scroll" />
                            <input type="hidden" name="film_variation_id" value="<?=$fv_key ?>" />
                            <div class="input-group">
                                <label for="price" style="font-size: 14px;">от&nbsp;</label>
                                <input type="text" name="price" class="form-control float-only film-price" placeholder="Цена" style="width: 80px;" value="<?=$film_variation['price'] ?>" data-film-variation-id="<?=$fv_key ?>" required="required" autocomplete="off" />
                                <div class="input-group-append">
                                    <select name="currency" class="film-currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="<?=CURRENCY_RUB ?>"<?=$film_variation['currency'] == CURRENCY_RUB ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="<?=CURRENCY_USD ?>"<?=$film_variation['currency'] == CURRENCY_USD ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="<?=CURRENCY_EURO ?>"<?=$film_variation['currency'] == CURRENCY_EURO ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-outline-dark d-none" name="price_submit">OK</button>
                        </form>
                    </td>
                    <td style="width: auto;<?=$no_border_top ?>">
                        <form class="form-inline" method="post">
                            <input type="hidden" name="scroll" />
                            <input type="hidden" name="film_variation_id" value="<?=$fv_key ?>" />
                            <div class="input-group">
                                <input type="text" name="eco_price" class="form-control float-only film-price" placeholder="Экосбор" style="width: 80px;" value="<?=$film_variation['eco_price'] ?>" data-film-variation-id="<?=$fv_key ?>" required="required" autocomplete="off" />
                                <div class="input-group-append">
                                    <select name="eco_currency" class="film-currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="<?=CURRENCY_RUB ?>"<?=$film_variation['eco_currency'] == CURRENCY_RUB ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="<?=CURRENCY_USD ?>"<?=$film_variation['eco_currency'] == CURRENCY_USD ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="<?=CURRENCY_EURO ?>"<?=$film_variation['eco_currency'] == CURRENCY_EURO ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-outline-dark d-none" name="eco_price_submit">OK</button>
                        </form>
                    </td>
                </tr>
                <?php
                $no_border_top = '';
                $show_table_header = false;
                endforeach;
                ?>
            </table>
            <?php
            endforeach;
            ?>
            <h2>Другое</h2>
            <table class="table">
                <?php
                $customers_eco_price = null;
                $customers_eco_currency = '';
                
                $sql = "select price, currency from other_price where price_type = ".PRICE_ECO_CUSTOMERS_MATERIAL." order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                
                if($row = $fetcher->Fetch()) {
                    $customers_eco_price = $row['price'];
                    $customers_eco_currency = $row['currency'];
                }
                ?>
                <tr>
                    <td style="width: 50%; border-top: 0;">Сырьё заказчика</td>
                    <td style="width: 10%; border-top: 0;"></td>
                    <td style="width: 10%; border-top: 0;"></td>
                    <td style="width: 15%; border-top: 0;"></td>
                    <td style="border-top: 0;">
                        <form class="form-inline" method="post">
                            <input type="hidden" name="scroll" />
                            <input type="hidden" name="price_type" value="<?=PRICE_ECO_CUSTOMERS_MATERIAL ?>" />
                            <div class="input-group">
                                <input type="text" name="price" class="form-control float-only film-price" placeholder="Экосбор" style="width: 80px;" value="<?=$customers_eco_price ?>" required="required" autocomplete="off" />
                                <div class="input-group-append">
                                    <select name="currency" class="film-currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="<?=CURRENCY_RUB ?>"<?=$customers_eco_currency == CURRENCY_RUB ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="<?=CURRENCY_USD ?>"<?=$customers_eco_currency == CURRENCY_USD ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="<?=CURRENCY_EURO ?>"<?=$customers_eco_currency == CURRENCY_EURO ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-outline-dark d-none" name="other_price_submit">OK</button>
                        </form>
                    </td>
                </tr>
                <?php
                $other_eco_price = null;
                $other_eco_currency = '';
                
                $sql = "select price, currency from other_price where price_type = ".PRICE_ECO_OTHER_MATERIAL." order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                
                if($row = $fetcher->Fetch()) {
                    $other_eco_price = $row['price'];
                    $other_eco_currency = $row['currency'];
                }
                ?>
                <tr>
                    <td style="width: 50%;">Сырьё не из базы данных</td>
                    <td style="width: 10%;"></td>
                    <td style="width: 10%;"></td>
                    <td style="width: 15%;"></td>
                    <td>
                        <form class="form-inline" method="post">
                            <input type="hidden" name="scroll" />
                            <input type="hidden" name="price_type" value="<?=PRICE_ECO_OTHER_MATERIAL ?>" />
                            <div class="input-group">
                                <input type="text" name="price" class="form-control float-only film-price" placeholder="Экосбор" style="width: 80px;" value="<?=$other_eco_price ?>" required="required" autocomplete="off" />
                                <div class="input-group-append">
                                    <select name="currency" class="film-currency" required="required">
                                        <option value="" hidden="">...</option>
                                        <option value="<?=CURRENCY_RUB ?>"<?=$other_eco_currency == CURRENCY_RUB ? " selected='selected'" : "" ?>>Руб</option>
                                        <option value="<?=CURRENCY_USD ?>"<?=$other_eco_currency == CURRENCY_USD ? " selected='selected'" : "" ?>>USD</option>
                                        <option value="<?=CURRENCY_EURO ?>"<?=$other_eco_currency == CURRENCY_EURO ? " selected='selected'" : "" ?>>EUR</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-outline-dark d-none" name="other_price_submit">OK</button>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // Если в списке плёнок выбрано "новая плёнка", 
            // скрываем форму добавления вариации.
            $('select#film_id').change(function() {
                if($(this).val() == '+') {
                    $('#create_film_variation').modal('hide');
                }
            });
            
            // При скрытии формы добавления плёнки,
            // показываем форму добавления вариации.
            $('#create_film').on('hidden.bs.modal', function() {
                $('input#name').val('');
                $('#create_film_variation').modal('show');
            });
            
            // При скрытии формы добавления вариации,
            // если значение плёнки было выбрано "Новая плёнка",
            // показываем форму добавленния плёнки.
            $('#create_film_variation').on('hidden.bs.modal', function() {
                if($('select#film_id').val() == '+') {
                    $('#create_film').modal('show');
                }
                
                $('select#film_id').val('');
                $('input#thickness').val('');
                $('input#weight').val('');
            });
            
            // При показе формы добавления плёнки,
            // устанавливаем фокус на текстовом поле.
            $('#create_film').on('shown.bs.modal', function() {
                $('input:text:visible:first').focus();
            });
            
            // При показе формы добавления вариации,
            // устанавливаем фокус на первом текстовом поле.
            $('#create_film_variation').on('shown.bs.modal', function() {
                if($('select#film_id').val() != '') {
                    $('input:text:visible:first').focus();
                }
            });
            
            // При редактировании цены, становится видна кнопка "ОК".
            $('.film-price').keydown(function() {
                $(this).parent().next('button').removeClass('d-none');
            })
            
            // При редактировании валюты, становится видна кнопка "ОК".
            $('.film-currency').change(function() {
                $(this).parent().parent().next('button').removeClass('d-none');
            })
            
            // Если страница открылась после отправки формы создания новой плёнки,
            // сразу открываем форму создания вариации, где новая плёнка уже выбрана в списке.
            <?php
            if(null !== filter_input(INPUT_POST, 'create_film_submit') && empty($error_message)):
                if(!empty($film_insert_id)):
                ?>
                $('select#film_id').val(<?=$film_insert_id ?>);
                <?php endif; ?>
            $('#create_film_variation').modal('show');
            <?php endif; ?>
            
            <?php if(null !== filter_input(INPUT_POST, 'film_id') && empty($error_message)): ?>
            window.scrollTo(0, $('#f_<?= filter_input(INPUT_POST, 'film_id') ?>').offset().top - $('#topmost').height());
            <?php endif; ?>
        </script>
    </body>
</html>