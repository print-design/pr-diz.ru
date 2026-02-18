<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Статус
$status_id = null;

if(null !== filter_input(INPUT_GET, 'status_id')) {
    $status_id = filter_input(INPUT_GET, 'status_id');
}

// Ошибки при расчётах (если есть)
if(null !== filter_input(INPUT_GET, 'error_message')) {
    $error_message = filter_input(INPUT_GET, 'error_message');
}

// Отображение статуса заказа
function ShowOrderStatus($status_id, $length_cut, $weight_cut, $quantity_sum, $quantity, $unit, $raport, $length, $gap_raport, $status_comment) {
    include '../include/order_status_index.php';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/select2.min.css" rel="stylesheet"/>
        <style>
            table.typography {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 15px;
                color: #191919;
            }
            
            table.typography tr th {
                color: #68676C;
                border-top: 0;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_pack.php';
        include '../include/status_track.php';
        include '../include/pager_top.php';
        $rowcounter = 0;
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1 text-nowrap">
                    <h1 class="d-inline"><?= key_exists($status_id, ORDER_STATUS_NAMES) ? ORDER_STATUS_NAMES[$status_id] : "Производят" ?></h1>
                    <?php
                    // Фильтр
                    $filter = '';
                    
                    $unit = filter_input(INPUT_GET, 'unit');
                    if(!empty($unit)) {
                        $filter .= " and c.unit = '$unit'";
                    }
                    
                    $work_type = filter_input(INPUT_GET, 'work_type');
                    if(!empty($work_type)) {
                        $filter .= " and c.work_type_id = $work_type";
                    }
                    
                    $manager = filter_input(INPUT_GET, 'manager');
                    if(!empty($manager)) {
                        $filter .= " and c.manager_id = $manager";
                    }
                    
                    $customer = filter_input(INPUT_GET, 'customer');
                    if(!empty($customer)) {
                        $filter .= " and c.customer_id = $customer";
                    }
                    
                    $name = filter_input(INPUT_GET, 'name');
                    if(!empty($name)) {
                        $filter .= " and trim(c.name) = '$name'";
                    }
                    
                    $find = trim(filter_input(INPUT_GET, 'find') ?? '');
                    if(!empty($find)) {
                        $find_substrings = explode('-', $find);
                        if(count($find_substrings) != 2 || intval($find_substrings[0]) == 0 || intval($find_substrings[1]) == 0) {
                            $filter .= " and false";
                        }
                        else {
                            $filter .= " and c.customer_id = ". intval($find_substrings[0])." and (select count(id) from calculation where customer_id = c.customer_id and id <= c.id) = ". intval($find_substrings[1]);
                        }
                    }
                    
                    // Общее количество работ для установления количества страниц в постраничном выводе
                    $sql = "select count(distinct c.id) "
                            . "from calculation c "
                            . "inner join customer cus on c.customer_id = cus.id "
                            . "inner join calculation_result cr on cr.calculation_id = c.id "
                            . "inner join user u on c.manager_id = u.id "
                            . "inner join (select calculation_id, max(date) as time from calculation_status_history group by calculation_id) cs on cs.calculation_id = c.id "
                            . "left join (select calculation_id, max(timestamp) as time from calculation_take group by calculation_id) ct on ct.calculation_id = c.id ";
                    if(!empty($status_id)) {
                        $sql .= "where (select status_id from calculation_status_history where calculation_id = c.id order by date desc limit 1) = ".$status_id;
                    }
                    else {
                        $sql .= "where (select status_id from calculation_status_history where calculation_id = c.id order by date desc limit 1) in (". ORDER_STATUS_CUT_PRILADKA.", ". ORDER_STATUS_CUTTING.", ". ORDER_STATUS_CUT_REMOVED.")";
                    }
                    $sql .= $filter;
                    $fetcher = new Fetcher($sql); 
                    
                    if($row = $fetcher->Fetch()) {
                        $pager_total_count = $row[0];
                    }
                    ?>
                    <div class="d-inline ml-3" style="color: gray; font-size: x-large;"><?=$pager_total_count ?></div>
                </div>
                <div class="p-1 text-nowrap">
                    <form class="form-inline d-inline" method="get">
                        <?php if(null !== $status_id): ?>
                        <input type="hidden" name="status_id" value="<?=$status_id ?>" />
                        <?php endif; ?>
                        <select id="unit" name="unit" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <option value="">Шт/кг...</option>
                            <option value="<?= PIECES ?>"<?= filter_input(INPUT_GET, 'unit') == PIECES ? " selected='selected'" : "" ?>>Шт</option>
                            <option value="<?= KG ?>"<?= filter_input(INPUT_GET, 'unit') == KG ? " selected='selected'" : "" ?>>Кг</option>
                        </select>
                        <select id="work_type" name="work_type" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <option value="">Тип работы...</option>
                            <?php foreach(WORK_TYPES as $item): ?>
                            <option value="<?=$item ?>"<?= ($item == filter_input(INPUT_GET, 'work_type') ? " selected='selected'" : "") ?>><?= WORK_TYPE_NAMES[$item] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="manager" name="manager" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <option value="">Менеджер...</option>
                            <?php
                            $sql = "select distinct u.id, u.last_name, u.first_name from calculation c inner join user u on c.manager_id = u.id order by u.last_name, u.first_name";
                            $fetcher = new Fetcher($sql);
                            while ($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?=$row['id'] ?>"<?=$row['id'] == filter_input(INPUT_GET, 'manager') ? " selected='selected'" : "" ?>><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <select id="customer" name="customer" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <option value="">Заказчик...</option>
                            <?php
                            $sql = "select distinct cus.id, cus.name from calculation c inner join customer cus on c.customer_id = cus.id order by cus.name";
                            $fetcher = new Fetcher($sql);
                            while($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?=$row['id'] ?>"<?=$row['id'] == filter_input(INPUT_GET, 'customer') ? " selected='selected'" : "" ?>><?=$row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <select id="name" name="name" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <option value="">Наименование...</option>
                            <?php
                            $name_where = "";
                            
                            if(!empty($status_id)) {
                                $name_where = "where (select status_id from calculation_status_history where calculation_id = c.id order by date desc limit 1) = $status_id";
                            }
                            
                            $customer = filter_input(INPUT_GET, 'customer');
                            if(!empty($customer)) {
                                $name_where .= " and c.customer_id = $customer";
                                
                                $customer_manager = filter_input(INPUT_GET, 'manager');
                                
                                if(!empty($customer_manager)) {
                                    $name_where .= " and c.manager_id = $customer_manager";
                                }
                            }
                            
                            $sql = "select distinct trim(c.name) name from calculation c $name_where order by trim(c.name)";
                            $fetcher = new Fetcher($sql);
                            while($row = $fetcher->Fetch()):
                            ?>
                            <option<?=$row['name'] == filter_input(INPUT_GET, 'name') ? " selected='selected'" : "" ?>><?=$row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                </div>
            </div>
            <table class="table table-hover typography">
                <tr>
                    <th class="text-nowrap">Дата</th>
                    <?php if($status_id == ORDER_STATUS_SHIPPED): ?>
                    <th>Дата отгрузки</th>
                    <?php endif; ?>
                    <th>№</th>
                    <th>Заказ</th>
                    <th>Метраж</th>
                    <th>Масса</th>
                    <th>Менеджер</th>
                    <th>Статус</th>
                    <th>Комментарий</th>
                    <th></th>
                </tr>
            <?php
            $sql = "select distinct c.id, ifnull(ifnull(ct.time, cs.time), '1900-01-01') as time, c.customer_id, "
                    . "(select comment from plan_edition where calculation_id = c.id and work_id = ". WORK_CUTTING." and comment is not null and comment <> '' limit 1) as comment, "
                    . "(select comment from plan_continuation where plan_edition_id in (select id from plan_edition where calculation_id = c.id and work_id = ". WORK_CUTTING.") and comment is not null and comment <> '' limit 1) as continuation_comment, "
                    . "cus.name as customer, c.name as calculation, cr.length_pure_1, concat(u.last_name, ' ', left(first_name, 1), '.') as manager, c.raport, c.length, c.unit, c.quantity, "
                    . ($status_id == ORDER_STATUS_SHIPPED ? "(select date from calculation_status_history where calculation_id = c.id and status_id = ". ORDER_STATUS_SHIPPED." order by id desc limit 1) as shipping_date, " : "")
                    . "(select sum(quantity) from calculation_quantity where calculation_id = c.id) quantity_sum, "
                    . "(select gap_raport from norm_gap where date <= c.date order by id desc limit 1) as gap_raport, "
                    . "ifnull((select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                    . "+ ifnull((select sum(length) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) length_cut, "
                    . "ifnull((select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                    . "+ ifnull((select sum(weight) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) weight_cut, "
                    . "(select status_id from calculation_status_history where calculation_id = c.id order by date desc limit 1) status_id, "
                    . "(select comment from calculation_status_history where calculation_id = c.id order by date desc limit 1) status_comment, "
                    . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                    . "from calculation c "
                    . "inner join customer cus on c.customer_id = cus.id "
                    . "inner join calculation_result cr on cr.calculation_id = c.id "
                    . "inner join user u on c.manager_id = u.id "
                    . "inner join (select calculation_id, max(date) as time from calculation_status_history group by calculation_id) cs on cs.calculation_id = c.id "
                    . "left join (select calculation_id, max(timestamp) as time from calculation_take group by calculation_id) ct on ct.calculation_id = c.id ";
            if(!empty($status_id)) {
                $sql .= "where (select status_id from calculation_status_history where calculation_id = c.id order by date desc limit 1) = ".$status_id;
            }
            else {
                $sql .= "where (select status_id from calculation_status_history where calculation_id = c.id order by date desc limit 1) in (". ORDER_STATUS_CUT_PRILADKA.", ". ORDER_STATUS_CUTTING.", ". ORDER_STATUS_CUT_REMOVED.")";
            }
            $sql .= $filter." order by time desc limit $pager_skip, $pager_take";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
                $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $row['time']);
            ?>
                <tr>
                    <td><?=$datetime->format('d.m') ?><br /><span style="font-size: smaller;"><?=$datetime->format('H:i') ?></span></td>
                    <?php
                    if($status_id == ORDER_STATUS_SHIPPED):
                        $shipping_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['shipping_date']);
                    ?>
                    <td><?=$shipping_date->format('d.m') ?><br /><span style="font-size: smaller;"><?=$shipping_date->format("H:i") ?></span></td>
                    <?php endif; ?>
                    <td class="text-nowrap"><?=$row['customer_id'].'-'.$row['num_for_customer'] ?></td>
                    <td><?=$row['calculation'] ?><br /><span style="font-size: smaller;"><?=$row['customer'] ?></span></td>
                    <td class="text-nowrap"><?= DisplayNumber(floatval($row['length_pure_1']), 0) ?> м</td>
                    <td class="text-nowrap"><?= DisplayNumber(floatval($row['weight_cut']), 1) ?> кг</td>
                    <td class="text-nowrap"><?=$row['manager'] ?></td>
                    <td data-toggle="modal" data-target="#status_track" style="cursor: pointer;" onclick="javascript:  StatusTrack(<?=$row['id'] ?>);"><?php ShowOrderStatus($row['status_id'], $row['length_cut'], $row['weight_cut'], $row['quantity_sum'], $row['quantity'], $row['unit'], $row['raport'], $row['length'], $row['gap_raport'], $row['status_comment']); ?></td>
                    <td><?= trim($row['comment'].' '.$row['continuation_comment'], ' ') ?></td>
                    <td>
                        <a href="details.php<?= BuildQuery('id', $row['id']) ?>" class="btn btn-light" style="width: 150px;">Приступить</a>
                    </td>
                </tr>
            <?php
            endwhile;
            ?>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
    <script src="<?= APPLICATION ?>/js/select2.min.js"></script>
    <script src="<?= APPLICATION ?>/js/i18n.js"></script>
    <script>
        // Список с поиском
        $('#unit').select2({
            placeholder: "Шт/кг...",
            maximumStatusLength: 1,
            language: "ru",
            width: '4rem'
        });
        
        $('#status').select2({
            placeholder: "Статус...",
            maximumSelectionLength: 1,
            language: "ru"
        });
        
        $('#work_type').select2({
            placeholder: "Тип работы...",
            maximumSelectionLength: 1,
            language: "ru",
            width: '8rem'
        });
        
        $('#manager').select2({
            placeholder: "Менеджер...",
            maximumSelectionLength: 1,
            language: "ru",
            width: "8rem"
        });
        
        $('#customer').select2({
            placeholder: "Заказчик...",
            maximumSelectionLength: 1,
            language: "ru",
            width: "15rem"
        });
        
        $('#name').select2({
            placeholder: "Наименование...",
            maximumSelectionLength: 1,
            language: "ru",
            width: "15rem"
        });
    </script>
</html>