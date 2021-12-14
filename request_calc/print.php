<?php
include '../include/topscripts.php';
include '../qr/qrlib.php';

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

// Текущее время
$current_date_time = date("dmYHis");

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select c.id request_calc_id, c.date, c.name, c.unit, c.quantity, c.work_type_id, c.stream_width, c.streams_number, c.length, c.raport, "
        . "c.brand_name, c.thickness, c.individual_brand_name, c.individual_thickness, "
        . "c.lamination1_brand_name, c.lamination1_thickness, c.lamination1_individual_brand_name, c.lamination1_individual_thickness, "
        . "c.lamination2_brand_name, c.lamination2_thickness, c.lamination2_individual_brand_name, c.lamination2_individual_thickness, "
        . "c.ink_number, c.ink_1, c.ink_2, c.ink_3, c.ink_4, c.ink_5, c.ink_6, c.ink_7, c.ink_8, "
        . "c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, "
        . "c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
        . "c.cliche_1, c.cliche_2, c.cliche_3, c.cliche_4, c.cliche_5, c.cliche_6, c.cliche_7, c.cliche_8, "
        . "t.request_calc_id, t.date techmap_date, t.reverse_print, t.shipment, t.spool, t.winding, t.sign, t.label, t.package, t.roll_type, t.comment, "
        . "cus.name customer, wt.name work_type, u.first_name, u.last_name "
        . "from request_calc c "
        . "inner join techmap t on t.request_calc_id = c.id "
        . "inner join customer cus on c.customer_id=cus.id "
        . "inner join work_type wt on c.work_type_id = wt.id "
        . "inner join user u on c.manager_id = u.id "
        . "where t.id=$id";
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

$request_calc_id = $row['request_calc_id'];
$techmap_date = $row['techmap_date'];
$reverse_print = $row['reverse_print'];
$shipment = $row['shipment'];
$spool = $row['spool'];
$winding = $row['winding'];
$sign = $row['sign'];
$label = $row['label'];
$package = $row['package'];
$roll_type = $row['roll_type'];
$comment = $row['comment'];
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
                margin-top: 30px;
                margin-bottom: 30px;
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
                margin-top: 15px;
                margin-bottom: 10px;
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
                padding-top: 5px;
                padding-bottom: 5px;
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
                <div id="title_date">№<?=$id ?> от <?= empty($date) ? DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') : $date ?></div>
            </div>
        </div>
        <div id="params_top">
            <table>
                <tr>
                    <th>Объем заказа</th>
                    <td><?=$quantity.' '.($unit == 'kg' ? 'кг' : 'шт') ?></td>
                </tr>
                <tr>
                    <th>Менеджер</th>
                    <td><?=$first_name.' '.$last_name ?></td>
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
            <div class="col-4">
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
            <div class="col-4">
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
            <div class="col-4">
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
        <div class="table_title">Красочность (<?=$ink_number ?>)</div>
        <div class="row params_main">
            <div class="col-4">
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
            <div class="col-4">
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
            <?php if($work_type_id == 2): ?>
        <div class="table_title">Печать</div>
        <p>
            <?php
            if(isset($reverse_print) && $reverse_print !== null) {
                if($reverse_print == 0) {
                    echo 'Лицевая';
                }
                elseif($reverse_print == 1) {
                    echo 'Обратная';
                }
            }
            ?>
        </p>
        <br />
            <?php endif; ?>
        <div class="table_title">Информация для резчика</div>
        <div class="row params_main">
            <div class="col-4">
                <table class="w-75">
                    <tr>
                        <th>Отгрузка, кг</th>
                        <td><?= empty($shipment) ? '' : $shipment ?></td>
                    </tr>
                    <tr>
                        <th>Шпуля</th>
                        <td><?=$spool ?></td>
                    </tr>
                        <?php if($work_type_id == 2): ?>
                    <tr>
                        <th>Фотометка</th>
                        <td></td>
                    </tr>
                        <?php endif; ?>
                    
                </table>
            </div>
            <div class="col-4">
                <table class="w-75">
                    <tr>
                        <th>Намотка</th>
                        <td><?= empty($winding) ? '' : $winding ?></td>
                    </tr>
                    <tr>
                        <th>Упаковка</th>
                        <td></td>
                    </tr>
                    <?php if($work_type_id == 2): ?>
                    <tr>
                        <th>Бирки</th>
                        <td></td>
                    </tr>
                        <?php endif; ?>
                </table>
            </div>
            <div class="col-4">
                <p><?=$comment ?></p>
            </div>
        </div>
            <?php if($work_type_id == 2): ?>
        <div class="row">
            <div class="col-8">
                <table class="w-100" id="roll_type_table">
                    <tr>
                        <td style="text-align: center;">
                            <image class="align-self-end" src="<?=APPLICATION ?>/images/roll/roll_type_1.png" style="margin-top: 5px;" />
                            <div style="width: 100%; text-align: end; padding-right: 5px;">
                                <?php if(!empty($roll_type) && $roll_type == 1): ?>
                                <i class="fa fa-check" aria-hidden="true"></i>
                                <?php else: echo "&nbsp;"; endif; ?>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <image class="align-self-end" src="<?=APPLICATION ?>/images/roll/roll_type_2.png" style="margin-top: 5px;" />
                            <div style="width: 100%; text-align: end; padding-right: 5px;">
                                <?php if(!empty($roll_type) && $roll_type == 2): ?>
                                <i class="fa fa-check" aria-hidden="true"></i>
                                <?php else: echo "&nbsp;"; endif; ?>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <image class="align-self-end" src="<?=APPLICATION ?>/images/roll/roll_type_3.png" style="margin-top: 5px;" />
                            <div style="width: 100%; text-align: end; padding-right: 5px;">
                                <?php if(!empty($roll_type) && $roll_type == 3): ?>
                                <i class="fa fa-check" aria-hidden="true"></i>
                                <?php else: echo "&nbsp;"; endif; ?>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <image class="align-self-end" src="<?=APPLICATION ?>/images/roll/roll_type_4.png" style="margin-top: 5px;" />
                            <div style="width: 100%; text-align: end; padding-right: 5px;">
                                <?php if(!empty($roll_type) && $roll_type == 4): ?>
                                <i class="fa fa-check" aria-hidden="true"></i>
                                <?php else: echo "&nbsp;"; endif; ?>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <image class="align-self-end" src="<?=APPLICATION ?>/images/roll/roll_type_5.png" style="margin-top: 5px;" />
                            <div style="width: 100%; text-align: end; padding-right: 5px;">
                                <?php if(!empty($roll_type) && $roll_type == 5): ?>
                                <i class="fa fa-check" aria-hidden="true"></i>
                                <?php else: echo "&nbsp;"; endif; ?>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <image class="align-self-end" src="<?=APPLICATION ?>/images/roll/roll_type_6.png" style="margin-top: 5px;" />
                            <div style="width: 100%; text-align: end; padding-right: 5px;">
                                <?php if(!empty($roll_type) && $roll_type == 6): ?>
                                <i class="fa fa-check" aria-hidden="true"></i>
                                <?php else: echo "&nbsp;"; endif; ?>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <image class="align-self-end" src="<?=APPLICATION ?>/images/roll/roll_type_7.png" style="margin-top: 5px;" />
                            <div style="width: 100%; text-align: end; padding-right: 5px;">
                                <?php if(!empty($roll_type) && $roll_type == 7): ?>
                                <i class="fa fa-check" aria-hidden="true"></i>
                                <?php else: echo "&nbsp;"; endif; ?>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <image class="align-self-end" src="<?=APPLICATION ?>/images/roll/roll_type_8.png" style="margin-top: 5px;" />
                            <div style="width: 100%; text-align: end; padding-right: 5px;">
                                <?php if(!empty($roll_type) && $roll_type == 8): ?>
                                <i class="fa fa-check" aria-hidden="true"></i>
                                <?php else: echo "&nbsp;"; endif; ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
            <?php endif; ?>        
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
            ?>
        <script>
            var css = '@page { size: portrait; margin: 8mm; }',
                    head = document.head || document.getElementsByTagName('head')[0],
                    style = document.createElement('style');
            
            style.type = 'text/css';
            style.media = 'print';
            
            if (style.styleSheet){
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            
            head.appendChild(style);
            
            window.print();
        </script>
    </body>
</html>