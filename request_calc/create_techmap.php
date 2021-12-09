<?php
include '../include/topscripts.php';
include '../qr/qrlib.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Текущее время
$current_date_time = date("dmYHis");

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select c.date, c.name, c.quantity, c.unit, c.stream_width, c.brand_name, c.thickness, c.individual_brand_name, c.individual_thickness, "
        . "c.lamination1_brand_name, c.lamination1_thickness, c.lamination1_individual_brand_name, c.lamination1_individual_thickness, "
        . "c.lamination2_brand_name, c.lamination2_thickness, c.lamination2_individual_brand_name, c.lamination2_individual_thickness, "
        . "cus.name customer, wt.name work_type, u.first_name, u.last_name "
        . "from request_calc c "
        . "inner join customer cus on c.customer_id=cus.id "
        . "inner join work_type wt on c.work_type_id = wt.id "
        . "inner join user u on c.manager_id = u.id "
        . "where c.id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];
$name = $row['name'];
$quantity = $row['quantity'];
$unit = $row['unit'];
$stream_width = $row['stream_width'];

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

$lamination1_brand_name = $row['lamination1_brand_name'];
$lamination2_brand_name = $row['lamination2_brand_name'];

$customer = $row['customer'];
$work_type = $row['work_type'];
$first_name = $row['first_name'];
$last_name = $row['last_name'];
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
        </style>
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
            <a class="btn btn-outline-dark backlink" href="request_calc.php?id=<?= $id ?>">Назад</a>
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
                    <!--img src='<?=$filename ?>' /-->
                </div>
                <div id="title_text">
                    <div id="title_customer"><?=$customer ?></div>
                    <div id="title_name"><?=$name ?></div>
                    <div id="title_date">№<?=$id ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></div>
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
                <div class="col-3">
                    <div class="table_title">Пленка</div>
                    <table>
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
                            <td><?=$stream_width ?> мм</td>
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
                    <table>
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
                            <td><?=$stream_width ?> мм</td>
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
                    <table>
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
                            <td><?=$stream_width ?> мм</td>
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
    </body>
</html>