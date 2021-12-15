<?php
include '../include/topscripts.php';
include '../qr/qrlib.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Виды красок
const CMYK = 'cmyk';
const PANTON = 'panton';
const WHITE = 'white';
const LACQUER = 'lacquer';

// Виды форм
const OLD = 'old';
const FLINT = 'flint';
const KODAK = 'kodak';
const TVER = 'tver';

// Фотометки
const LEFT_SIGN = "left";
const RIGHT_SIGN = "right";
const BOTH_SIGN = "both";
const NO_SIGN = "no";

// Папка, из которой читаются картинки рулонов
function GetRollImageFolderName($sign) {
    $result = "roll";
    
    switch ($sign) {
        case LEFT_SIGN:
            $result = "roll_left";
            break;
        
        case RIGHT_SIGN:
            $result = "roll_right";
            break;
        
        case BOTH_SIGN:
            $result = "roll_both";
            break;
    }
    
    return $result;
}

// Создание технологической карты
if(null !== filter_input(INPUT_POST, 'techmap_submit')) {
    $request_calc_id = filter_input(INPUT_POST, 'request_calc_id');
    $id = filter_input(INPUT_POST, 'id');
            
    $reverse_print = filter_input(INPUT_POST, 'reverse_print');
    if($reverse_print === null) $reverse_print = "NULL";
    
    $spool = filter_input(INPUT_POST, 'spool');
    if(empty($spool)) $spool = "NULL";
    
    $winding = filter_input(INPUT_POST, 'winding');
    if(empty($winding)) $winding = "NULL";
    
    $winding_unit = filter_input(INPUT_POST, 'winding_unit');
    $sign = addslashes(filter_input(INPUT_POST, 'sign'));
    $label = addslashes(filter_input(INPUT_POST, 'label'));
    $package = addslashes(filter_input(INPUT_POST, 'package'));
    
    $roll_type = filter_input(INPUT_POST, 'roll_type');
    if(empty($roll_type)) $roll_type = "NULL";
    
    $comment = addslashes(filter_input(INPUT_POST, 'comment'));
    
    $sql = "";
    
    if(!empty($request_calc_id)) {
        $sql = "insert into techmap (request_calc_id, reverse_print, spool, winding, winding_unit, sign, label, package, roll_type, comment) "
                . "values ($request_calc_id, $reverse_print, $spool, $winding, '$winding_unit', '$sign', '$label', '$package', $roll_type, '$comment')";
    }
    elseif(!empty ($id)) {
        $sql = "update techmap set reverse_print=$reverse_print, spool=$spool, winding=$winding, winding_unit='$winding_unit', sign='$sign', label='$label', "
                . "package='$package', roll_type=$roll_type, comment='$comment' where id=$id";
    }
    
    $executer = new Executer($sql);
    $error_message = $executer->error;
    $insert_id = $executer->insert_id;
    
    if(empty($error_message)) {
        if(!empty($insert_id)) {
            header("Location: techmap.php?id=$insert_id&created=ok");
        }
        else {
            header("Location: techmap.php?id=$id&edited=ok");
        }
    }
}

// Текущее время
$current_date_time = date("dmYHis");

// Получение объекта
$sql = "";

$request_calc_id = filter_input(INPUT_POST, 'request_calc_id');
if(empty($request_calc_id)) {
    $request_calc_id = filter_input(INPUT_GET, 'request_calc_id');
}

$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

if(!empty($request_calc_id)) {
    $sql = "select c.id request_calc_id, c.date, c.name, c.unit, c.quantity, c.work_type_id, c.stream_width, c.streams_number, c.length, c.raport, "
            . "c.brand_name, c.thickness, c.individual_brand_name, c.individual_thickness, "
            . "c.lamination1_brand_name, c.lamination1_thickness, c.lamination1_individual_brand_name, c.lamination1_individual_thickness, "
            . "c.lamination2_brand_name, c.lamination2_thickness, c.lamination2_individual_brand_name, c.lamination2_individual_thickness, "
            . "c.ink_number, c.ink_1, c.ink_2, c.ink_3, c.ink_4, c.ink_5, c.ink_6, c.ink_7, c.ink_8, "
            . "c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, "
            . "c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
            . "c.cliche_1, c.cliche_2, c.cliche_3, c.cliche_4, c.cliche_5, c.cliche_6, c.cliche_7, c.cliche_8, "
            . "cus.name customer, wt.name work_type, u.first_name, u.last_name "
            . "from request_calc c "
            . "inner join customer cus on c.customer_id=cus.id "
            . "inner join work_type wt on c.work_type_id = wt.id "
            . "inner join user u on c.manager_id = u.id "
            . "where c.id=$request_calc_id";
}
elseif (!empty($id)) {
    $sql = "select c.id request_calc_id, c.date, c.name, c.unit, c.quantity, c.work_type_id, c.stream_width, c.streams_number, c.length, c.raport, "
            . "c.brand_name, c.thickness, c.individual_brand_name, c.individual_thickness, "
            . "c.lamination1_brand_name, c.lamination1_thickness, c.lamination1_individual_brand_name, c.lamination1_individual_thickness, "
            . "c.lamination2_brand_name, c.lamination2_thickness, c.lamination2_individual_brand_name, c.lamination2_individual_thickness, "
            . "c.ink_number, c.ink_1, c.ink_2, c.ink_3, c.ink_4, c.ink_5, c.ink_6, c.ink_7, c.ink_8, "
            . "c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, "
            . "c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
            . "c.cliche_1, c.cliche_2, c.cliche_3, c.cliche_4, c.cliche_5, c.cliche_6, c.cliche_7, c.cliche_8, "
            . "t.request_calc_id, t.date techmap_date, t.reverse_print, t.spool, t.winding, t.winding_unit, t.sign, t.label, t.package, t.roll_type, t.comment, "
            . "cus.name customer, wt.name work_type, u.first_name, u.last_name "
            . "from request_calc c "
            . "inner join techmap t on t.request_calc_id = c.id "
            . "inner join customer cus on c.customer_id=cus.id "
            . "inner join work_type wt on c.work_type_id = wt.id "
            . "inner join user u on c.manager_id = u.id "
            . "where t.id=$id";
}
$row = (new Fetcher($sql))->Fetch();

$request_calc_id = $row['request_calc_id'];
$date = $row['date'];
$name = $row['name'];
$unit = $row['unit'];
$quantity = $row['quantity'];
$work_type_id = $row['work_type_id'];
$stream_width = $row['stream_width'];
$streams_number = $row['streams_number'];
$length = $row['length'];
$raport = $row['raport'];

$brand_name = $row['brand_name'];
$thickness = $row['thickness'];
$individual_brand_name = $row['individual_brand_name'];
$individual_thickness = $row['individual_thickness'];
$lamination1_brand_name = $row['lamination1_brand_name'];
$lamination1_thickness = $row['lamination1_thickness'];
$lamination1_individual_brand_name = $row['lamination1_individual_brand_name'];
$lamination1_individual_thickness = $row['lamination1_individual_thickness'];
$lamination2_brand_name = $row['lamination2_brand_name'];
$lamination2_thickness = $row['lamination2_thickness'];
$lamination2_individual_brand_name = $row['lamination2_individual_brand_name'];
$lamination2_individual_thickness = $row['lamination2_individual_thickness'];
$ink_number = $row['ink_number'];

for($i=1; $i<=8; $i++) {
    $ink_var = "ink_$i";
    $$ink_var = $row[$ink_var];
    
    $color_var = "color_$i";
    $$color_var = $row[$color_var];
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = $row[$cmyk_var];
    
    $cliche_var = "cliche_$i";
    $$cliche_var = $row[$cliche_var];
}

$customer = $row['customer'];
$work_type = $row['work_type'];
$first_name = $row['first_name'];
$last_name = $row['last_name'];

if(!empty($id)) {
    $request_calc_id = $row['request_calc_id'];
    $techmap_date = $row['techmap_date'];
    $reverse_print = $row['reverse_print'];
    $spool = $row['spool'];
    $winding = $row['winding'];
    $winding_unit = $row['winding_unit'];
    $sign = $row['sign'];
    $label = $row['label'];
    $package = $row['package'];
    $roll_type = $row['roll_type'];
    $comment = $row['comment'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            #title_qr {
                float: left;
                margin-right: 40px;
            }
            
            #title_customer {
                font-size: 36px;
                font-weight: 700;
                line-height: 44px;
                margin-bottom: 10px;
            }
            
            #title_name {
                font-size: 24px;
                font-weight: 700;
                line-height: 40px;
                margin-bottom: 10px;
            }
            
            #title_date {
                font-size: 18px;
                font-weight: 700;
                line-height: 32px;
            }
            
            #params_top {
                margin-top: 40px;
                margin-bottom: 40px;
            }
            
            #params_top table tr th {
                padding-right: 20px;
                padding-bottom: 10px;
            }
            
            #params_top table tr td {
                padding-bottom: 10px;
            }
            
            .table_title {
                font-weight: 700;
                font-size: 18px;
                line-height: 32px;
                margin-top: 25px;
                margin-bottom: 18px;
            }
            
            .params_main table tr th {
                font-weight: 400;
                padding-right: 20px;
                padding-top: 10px;
                padding-bottom: 10px;
                border-bottom: 1px solid #E3E3E3;
            }
            
            .params_main table tr td {
                font-weight: 700;
                padding-top: 10px;
                padding-bottom: 10px;
                border-bottom: 1px solid #E3E3E3;
            }
            
            .form-check-label {
                font-size: 14px;
                font-weight: 400;
                line-height: 20px;
            }
            
            #roll_type_table {
                margin-bottom: 20px;
            }
            
            #roll_type_table tr td {
                border: solid 1px #ced4da;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_zakaz.php';
        ?>
        <div id="reverse_print_message" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div style="font-size: xx-large;" class="text-primary">Внимание!</div>
                        <button type="button" class="close user_change_password_dismiss" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <p style="font-size: xx-large;">При наличии ламинации печать всегда обратная</p>
                    </div>
                    <div class="modal-footer" style="justify-content: flex-start;">
                        <button type="button" class="btn btn-primary w-25" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)):
            ?>
            <div class='alert alert-danger'><?=$error_message ?></div>
            <?php
            endif;
            if(filter_input(INPUT_GET, 'created') == 'ok'):
            ?>
            <div class='alert alert-info'>Карта создана</div>
            <?php
            endif;
            if(filter_input(INPUT_GET, 'edited') == 'ok'):
            ?>
            <div class='alert alert-info'>Карта отредактирована</div>
            <?php
            endif;
            ?>
            <a class="btn btn-outline-dark backlink" href="request_calc.php?id=<?=$request_calc_id ?>">Назад</a>
            <div id="title_zone">
                <div id="title_qr">
                    <?php
                    $errorCorrectionLevel = 'L'; // 'L','M','Q','H'
                    $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/request_calc/request_calc.php?id='.$id;
                    $filename = "../temp/techmap".$id."_".$current_date_time.".png";
                
                    do {
                        QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 20, 0, true);
                    } while (!file_exists($filename));
                    ?>
                    <img src='<?=$filename ?>' style="height: 136px; width: 136px;" />
                </div>
                <div id="title_text">
                    <div id="title_customer"><?=$customer ?></div>
                    <div id="title_name"><?=$name ?></div>
                    <div id="title_date"><?= empty($id) ? "" : "№$id" ?> от <?= empty($date) ? DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') : $date ?></div>
                </div>
            </div>
            <div id="params_top">
                <table>
                    <tr>
                        <th>Объем заказа</th>
                        <td><?=$quantity.'&nbsp;'.($unit == 'kg' ? 'кг' : 'шт') ?></td>
                    </tr>
                    <tr>
                        <th>Менеджер</th>
                        <td><?=$first_name.'&nbsp;'.$last_name ?></td>
                    </tr>
                    <tr>
                        <th>Тип работы</th>
                        <td><?=$work_type ?></td>
                    </tr>
                    <tr>
                        <th>Карта составлена</th>
                        <td>
                            <?php
                            $current_date_time = date("d.m.Y H:i");
                            ?>
                            <?=$current_date_time ?>
                        </td>
                    </tr>
                </table>
            </div>
            <hr />
            <div class="row params_main">
                <div class="col-3">
                    <div class="table_title">Пленка</div>
                    <table class="w-75">
                        <tr>
                            <th>Марка пленки</th>
                            <td><?=($brand_name == INDIVIDUAL ? $individual_brand_name : $brand_name) ?></td>
                        </tr>
                        <tr>
                            <th>Толщина</th>
                            <td><?=($brand_name == INDIVIDUAL ? $individual_thickness : $thickness) ?> мкм</td>
                        </tr>
                        <tr>
                            <th>Ширина</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Метраж на приладку</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Метраж на тираж</th>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <?php if(!empty($lamination1_brand_name)): ?>
                <div class="col-3">
                    <div class="table_title">Ламинация 1</div>
                    <table class="w-75">
                        <tr>
                            <th>Марка пленки</th>
                            <td><?=($lamination1_brand_name == INDIVIDUAL ? $lamination1_individual_brand_name : $lamination1_brand_name) ?></td>
                        </tr>
                        <tr>
                            <th>Толщина</th>
                            <td><?=($lamination1_brand_name == INDIVIDUAL ? $lamination1_individual_thickness : $lamination1_thickness) ?> мкм</td>
                        </tr>
                        <tr>
                            <th>Ширина</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Метраж на приладку</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Метраж на тираж</th>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <?php
                endif;
                if(!empty($lamination2_brand_name)):
                ?>
                <div class="col-3">
                    <div class="table_title">Ламинация 2</div>
                    <table class="w-75">
                        <tr>
                            <th>Марка пленки</th>
                            <td><?=($lamination2_brand_name == INDIVIDUAL ? $lamination2_individual_brand_name : $lamination2_brand_name) ?></td>
                        </tr>
                        <tr>
                            <th>Толщина</th>
                            <td><?=($lamination2_brand_name == INDIVIDUAL ? $lamination2_individual_thickness : $lamination2_thickness) ?> мкм</td>
                        </tr>
                        <tr>
                            <th>Ширина</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Метраж на приладку</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Метраж на тираж</th>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <br />
            <?php if($work_type_id == 2): ?>
            <div class="table_title">Красочность <?=$ink_number ?></div>
            <div class="row params_main">
                <div class="col-3">
                    <table class="w-75">
                        <?php
                        for($i=1; $i<=8; $i++):
                        $ink_var = "ink_$i";
                        $cmyk_var = "cmyk_$i";
                        $color_var = "color_$i";
                        $cliche_var = "cliche_$i";
                        if(!empty($$ink_var)):
                        ?>
                        <tr>
                            <th>
                                <?php
                                switch ($$ink_var) {
                                case CMYK:
                                    echo ucfirst($$cmyk_var);
                                    break;
                                
                                case PANTON:
                                    echo $$color_var;
                                    break;
                                
                                case WHITE:
                                    echo "Белая";
                                    break;
                                
                                case LACQUER:
                                    echo 'Лак';
                                    break;
                                }
                                ?>
                            </th>
                            <td>
                                <?php
                                switch ($$cliche_var) {
                                case OLD:
                                    echo "Старая";
                                    break;
                                
                                case FLINT:
                                    echo "Новая Флинт";
                                    break;
                                
                                case KODAK:
                                    echo "Новая Кодак";
                                    break;
                                
                                case TVER:
                                    echo "Новая Тверь";
                                    break;
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        endif;
                        endfor;
                        ?>
                    </table>
                </div>
                <div class="col-3">
                    <table class="w-75">
                        <tr>
                            <th>Рапорт</th>
                            <td><?=rtrim(rtrim(number_format($raport, 3, ",", ""), "0"), ",") ?> мм</td>
                        </tr>
                        <tr>
                            <th>Растяг</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Длина <span style="font-size: smaller;">(от метки до метки)</span></th>
                            <td><?=$length ?> мм</td>
                        </tr>
                        <tr>
                            <th>Ширина ручья</th>
                            <td><?=$stream_width ?> мм</td>
                        </tr>
                        <tr>
                            <th>Количество ручьёв</th>
                            <td><?=$streams_number ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <br />
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="request_calc_id" value="<?= empty(filter_input(INPUT_GET, 'request_calc_id')) ? '' : filter_input(INPUT_GET, 'request_calc_id') ?>" />
                <input type="hidden" name="id" value="<?= empty(filter_input(INPUT_GET, 'id')) ? '' : filter_input(INPUT_GET, 'id') ?>" />
                <?php if($work_type_id == 2): ?>
                <div class="params_main">
                    <div class="table_title">Печать</div>
                    <div class="form-group">
                        <input type="radio" class="form-check-inline" id="reverse_print_0" name="reverse_print" value="0"<?= isset($reverse_print) && $reverse_print == 0 ? " checked='checked'" : "" ?> />
                        <label for="reverse_print_0" class="form-check-label">Лицевая</label>
                        <?php
                        if(!empty($lamination1_brand_name) && !isset($reverse_print)) {
                            $reverse_print = 1;
                        }
                        ?>
                        <input type="radio" class="form-check-inline ml-3" id="reverse_print_1" name="reverse_print" value="1"<?= isset($reverse_print) && $reverse_print == 1 ? " checked='checked'" : "" ?> />
                        <label for="reverse_print_1" class="form-check-label">Обратная</label>
                    </div>
                </div>
                <br />
                <?php endif; ?>
                <div class="row">
                    <div class="col-4">
                        <div class="table_title">Информация для резчика</div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="winding">Намотка</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">До</div>
                                        </div>
                                        <input type="text" id="winding" name="winding" class="form-control int-only" value="<?= empty($winding) ? '' : $winding ?>" />
                                        <div class="input-group-append">
                                            <select name="winding_unit">
                                                <option value="" hidden="hidden">...</option>
                                                <option value="kg"<?= !empty($winding_unit) && $winding_unit == 'kg' ? " selected='selected'" : "" ?>>кг</option>
                                                <option value="mm"<?= !empty($winding_unit) && $winding_unit == 'mm' ? " selected='selected'" : "" ?>>мм</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="spool">Шпуля, мм</label>
                                    <select id="spool" name="spool" class="form-control">
                                        <option value="" hidden="hidden">Шпуля...</option>
                                        <option<?= !empty($spool) && $spool == 40 ? " selected='selected'" : "" ?>>40</option>
                                        <option<?= !empty($spool) && $spool == 50 ? " selected='selected'" : "" ?>>50</option>
                                        <option<?= !empty($spool) && $spool == 76 ? " selected='selected'" : "" ?>>76</option>
                                        <option<?= !empty($spool) && $spool == 152 ? " selected='selected'" : "" ?>>152</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="label">Бирки</label>
                                    <select id="label" name="label" class="form-control">
                                        <option value="" hidden="hidden">Бирки...</option>
                                        <option<?= !empty($label) && $label == "Принт дизайн" ? " selected='selected'" : "" ?>>Принт дизайн</option>
                                        <option<?= !empty($label) && $label == "Безликие" ? " selected='selected'" : "" ?>>Безликие</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="package">Упаковка</label>
                                    <select id="package" name="package" class="form-control">
                                        <option value="" hidden="hidden">Упаковка...</option>
                                        <option<?= !empty($package) && $package == "Паллетирование" ? " selected='selected'" : "" ?>>Паллетирование</option>
                                        <option<?= !empty($package) && $package == "Россыпью" ? " selected='selected'" : "" ?>>Россыпью</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php if($work_type_id == 2): ?>
                        <div class="form-group">
                            <label for="sign">Фотометка</label>
                            <select id="sign" name="sign" class="form-control">
                                <option value="" hidden="hidden">Фотометка...</option>
                                <option value="<?=LEFT_SIGN ?>"<?=!empty($sign) && $sign == LEFT_SIGN ? " selected='selected'" : "" ?>>Левая фотометка</option>
                                <option value="<?=RIGHT_SIGN ?>"<?=!empty($sign) && $sign == RIGHT_SIGN ? " selected='selected'" : "" ?>>Правая фотометка</option>
                                <option value="<?=BOTH_SIGN ?>"<?=!empty($sign) && $sign == BOTH_SIGN ? " selected='selected'" : "" ?>>Две фотометки</option>
                                <option value="<?=NO_SIGN ?>"<?=!empty($sign) && $sign == NO_SIGN ? " selected='selected'" : "" ?>>Без фотометки</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="comment">Комментарий</label>
                            <textarea class="form-control" rows="4" id="comment" name="comment"><?= empty($comment) ? '' : $comment ?></textarea>
                        </div>
                        <?php
                        if($work_type_id == 2):
                            $roll_image_folder = GetRollImageFolderName(isset($sign) ? $sign : "");
                        ?>
                        <table class="w-100" id="roll_type_table">
                            <tr>
                                <td class="roll_type_zone" style="text-align: center;">
                                    <image id="img_1" src="<?=APPLICATION ?>/images/<?= $roll_image_folder ?>/roll_type_1.png" style="margin-top: 5px;" />
                                    <div style="width: 100%; text-align: end;">
                                        <input type="checkbox" class="roll_type" name="roll_type" value="1" style="margin-right: 5px; margin-top: 5px;"<?= !empty($roll_type) && $roll_type == 1 ? " checked='checked'" : "" ?> />
                                    </div>
                                </td>
                                <td class="roll_type_zone" style="text-align: center;">
                                    <image id="img_2" src="<?=APPLICATION ?>/images/<?= $roll_image_folder ?>/roll_type_2.png" style="margin-top: 5px;" />
                                    <div style="width: 100%; text-align: end;">
                                        <input type="checkbox" class="roll_type" name="roll_type" value="2" style="margin-right: 5px; margin-top: 5px;"<?= !empty($roll_type) && $roll_type == 2 ? " checked='checked'" : "" ?> />
                                    </div>
                                </td>
                                <td class="roll_type_zone" style="text-align: center;">
                                    <image id="img_3" src="<?=APPLICATION ?>/images/<?= $roll_image_folder ?>/roll_type_3.png" style="margin-top: 5px;" />
                                    <div style="width: 100%; text-align: end;">
                                        <input type="checkbox" class="roll_type" name="roll_type" value="3" style="margin-right: 5px; margin-top: 5px;"<?= !empty($roll_type) && $roll_type == 3 ? " checked='checked'" : "" ?> />
                                    </div>
                                </td>
                                <td class="roll_type_zone" style="text-align: center;">
                                    <image id="img_4" src="<?=APPLICATION ?>/images/<?= $roll_image_folder ?>/roll_type_4.png" style="margin-top: 5px;" />
                                    <div style="width: 100%; text-align: end;">
                                        <input type="checkbox" class="roll_type" name="roll_type" value="4" style="margin-right: 5px; margin-top: 5px;"<?= !empty($roll_type) && $roll_type == 4 ? " checked='checked'" : "" ?> />
                                    </div>
                                </td>
                                <td class="roll_type_zone" style="text-align: center;">
                                    <image id="img_5" src="<?=APPLICATION ?>/images/<?= $roll_image_folder ?>/roll_type_5.png" style="margin-top: 5px;" />
                                    <div style="width: 100%; text-align: end;">
                                        <input type="checkbox" class="roll_type" name="roll_type" value="5" style="margin-right: 5px; margin-top: 5px;"<?= !empty($roll_type) && $roll_type == 5 ? " checked='checked'" : "" ?> />
                                    </div>
                                </td>
                                <td class="roll_type_zone" style="text-align: center;">
                                    <image id="img_6" src="<?=APPLICATION ?>/images/<?= $roll_image_folder ?>/roll_type_6.png" style="margin-top: 5px;" />
                                    <div style="width: 100%; text-align: end;">
                                        <input type="checkbox" class="roll_type" name="roll_type" value="6" style="margin-right: 5px; margin-top: 5px;"<?= !empty($roll_type) && $roll_type == 6 ? " checked='checked'" : "" ?> />
                                    </div>
                                </td>
                                <td class="roll_type_zone" style="text-align: center;">
                                    <image id="img_7" src="<?=APPLICATION ?>/images/<?= $roll_image_folder ?>/roll_type_7.png" style="margin-top: 5px;" />
                                    <div style="width: 100%; text-align: end;">
                                        <input type="checkbox" class="roll_type" name="roll_type" value="7" style="margin-right: 5px; margin-top: 5px;"<?= !empty($roll_type) && $roll_type == 7 ? " checked='checked'" : "" ?> />
                                    </div>
                                </td>
                                <td class="roll_type_zone" style="text-align: center;">
                                    <image id="img_8" src="<?=APPLICATION ?>/images/<?= $roll_image_folder ?>/roll_type_8.png" style="margin-top: 5px;" />
                                    <div style="width: 100%; text-align: end;">
                                        <input type="checkbox" class="roll_type" name="roll_type" value="8" style="margin-right: 5px; margin-top: 5px;"<?= !empty($roll_type) && $roll_type == 8 ? " checked='checked'" : "" ?> />
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-dark w-100" name="techmap_submit">OK</button>
                                </div>
                            </div>
                            <?php if(!empty($id)): ?>
                            <div class="col-6">
                                <div class="form-group">
                                    <a href="techmap_print.php?id=<?=$id ?>" target="_blank" class="btn btn-outline-dark w-100">Печать</a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        // Удаление всех файлов, кроме текущих (чтобы диск не переполнился).
        $files = scandir("../temp/");
        foreach ($files as $file) {
            $created = filemtime("../temp/".$file);
            $now = time();
            $diff = $now - $created;
            
            if($diff > 20 &&
                    $file != "techmap".$id."_".$current_date_time.".png" &&
                    !is_dir($file)) {
                unlink("../temp/$file");
            }
        }
        
        include '../include/footer.php';
        ?>
        <script>
            // При выборе типа ролика, с остальных типов выбор снимается
            $('input.roll_type').change(function() {
                val = $(this).val();
                $('input.roll_type[value!=' + val + ']').prop( "checked", false);
            });
            
            // Чекбокс с типом ролика выбирается не только при щелчке на чекбоксе, но и при щелчке на всей ячейке
            $('.roll_type_zone').click(function() {
                chk = $(this).find('input.roll_type');
                if(chk.attr('disabled') != 'disabled') {
                    chk.change();
                    chk.prop("checked", true);
                }
            });
            
            // При выборе фотометки меняются картинки типов роликов
            $('select#sign').change(function() {
                switch($(this).val()) {
                    case '<?=LEFT_SIGN ?>':
                        $('img#img_1').attr('src', '<?=APPLICATION ?>/images/roll_left/roll_type_1.png');
                        $('img#img_2').attr('src', '<?=APPLICATION ?>/images/roll_left/roll_type_2.png');
                        $('img#img_3').attr('src', '<?=APPLICATION ?>/images/roll_left/roll_type_3.png');
                        $('img#img_4').attr('src', '<?=APPLICATION ?>/images/roll_left/roll_type_4.png');
                        $('img#img_5').attr('src', '<?=APPLICATION ?>/images/roll_left/roll_type_5.png');
                        $('img#img_6').attr('src', '<?=APPLICATION ?>/images/roll_left/roll_type_6.png');
                        $('img#img_7').attr('src', '<?=APPLICATION ?>/images/roll_left/roll_type_7.png');
                        $('img#img_8').attr('src', '<?=APPLICATION ?>/images/roll_left/roll_type_8.png');
                        break;
                        
                    case '<?=RIGHT_SIGN ?>':
                        $('img#img_1').attr('src', '<?=APPLICATION ?>/images/roll_right/roll_type_1.png');
                        $('img#img_2').attr('src', '<?=APPLICATION ?>/images/roll_right/roll_type_2.png');
                        $('img#img_3').attr('src', '<?=APPLICATION ?>/images/roll_right/roll_type_3.png');
                        $('img#img_4').attr('src', '<?=APPLICATION ?>/images/roll_right/roll_type_4.png');
                        $('img#img_5').attr('src', '<?=APPLICATION ?>/images/roll_right/roll_type_5.png');
                        $('img#img_6').attr('src', '<?=APPLICATION ?>/images/roll_right/roll_type_6.png');
                        $('img#img_7').attr('src', '<?=APPLICATION ?>/images/roll_right/roll_type_7.png');
                        $('img#img_8').attr('src', '<?=APPLICATION ?>/images/roll_right/roll_type_8.png');
                        break;
        
                    case '<?=BOTH_SIGN ?>':
                        $('img#img_1').attr('src', '<?=APPLICATION ?>/images/roll_both/roll_type_1.png');
                        $('img#img_2').attr('src', '<?=APPLICATION ?>/images/roll_both/roll_type_2.png');
                        $('img#img_3').attr('src', '<?=APPLICATION ?>/images/roll_both/roll_type_3.png');
                        $('img#img_4').attr('src', '<?=APPLICATION ?>/images/roll_both/roll_type_4.png');
                        $('img#img_5').attr('src', '<?=APPLICATION ?>/images/roll_both/roll_type_5.png');
                        $('img#img_6').attr('src', '<?=APPLICATION ?>/images/roll_both/roll_type_6.png');
                        $('img#img_7').attr('src', '<?=APPLICATION ?>/images/roll_both/roll_type_7.png');
                        $('img#img_8').attr('src', '<?=APPLICATION ?>/images/roll_both/roll_type_8.png');
                        break;
            
                    case '<?=NO_SIGN ?>':
                        $('img#img_1').attr('src', '<?=APPLICATION ?>/images/roll/roll_type_1.png');
                        $('img#img_2').attr('src', '<?=APPLICATION ?>/images/roll/roll_type_2.png');
                        $('img#img_3').attr('src', '<?=APPLICATION ?>/images/roll/roll_type_3.png');
                        $('img#img_4').attr('src', '<?=APPLICATION ?>/images/roll/roll_type_4.png');
                        $('img#img_5').attr('src', '<?=APPLICATION ?>/images/roll/roll_type_5.png');
                        $('img#img_6').attr('src', '<?=APPLICATION ?>/images/roll/roll_type_6.png');
                        $('img#img_7').attr('src', '<?=APPLICATION ?>/images/roll/roll_type_7.png');
                        $('img#img_8').attr('src', '<?=APPLICATION ?>/images/roll/roll_type_8.png');
                        break;
                }
                
                SetRollTypeCheckboxEnabled($(this).val());
            });
            
            // Тип ролика можно выбирать только при выбранной фотометке
            function SetRollTypeCheckboxEnabled(sign) {
                if(sign == '') {
                    $('input.roll_type').attr('disabled', 'disabled');
                }
                else {
                    $('input.roll_type').removeAttr('disabled');
                }
            }
            
            SetRollTypeCheckboxEnabled($('select#sign').val());
            
            // При наличии ламинации запрещается выбор прямой печати
            <?php if(!empty($lamination1_brand_name)): ?>
                $('#reverse_print_0').click(function() {
                    $('#reverse_print_message').modal('show');
                    $('#reverse_print_1').click();
                });
            <?php endif; ?>
        </script>
    </body>
</html>