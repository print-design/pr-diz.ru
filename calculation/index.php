<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager', 'top_manager', 'designer'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

function OrderLink($param) {
    if(array_key_exists('order', $_REQUEST) && $_REQUEST['order'] == $param) {
        echo "<strong><i class='fas fa-arrow-down' style='color: black; font-size: small;'></i></strong>";
    }
    else {
        echo "<a class='gray' href='".BuildQueryAddRemove("order", $param, "page")."' style='font-size: x-small;'><i class='fas fa-arrow-down'></i></a>";
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
                    <h1 style="font-size: 32px; font-weight: 600;" class="d-inline">Расчеты</h1>
                    <?php
                    // Фильтр
                    $where = '';
                    
                    $customer = filter_input(INPUT_GET, 'customer');
                    if(!empty($customer)) {
                        if(empty($where)) $where = " where c.customer_id=$customer";
                        else $where .= " and c.customer_id=$customer";
                    }
                    
                    $name = addslashes(filter_input(INPUT_GET, 'name'));
                    if(!empty($name)) {
                        if(empty($where)) $where = " where c.name=(select name from calculation where id=$name)";
                        else $where .= " and c.name=(select name from calculation where id=$name)";
                    }
                    
                    $unit = filter_input(INPUT_GET, 'unit');
                    if(!empty($unit)) {
                        if(empty($where)) $where = " where c.unit='$unit'";
                        else $where .= " and c.unit='$unit'";
                    }
                    
                    $work_type = filter_input(INPUT_GET, 'work_type');
                    if(!empty($work_type)) {
                        if(empty($where)) $where = " where c.work_type_id=$work_type";
                        else $where .= " and c.work_type_id=$work_type";
                    }
                    
                    $manager = filter_input(INPUT_GET, 'manager');
                    if(!empty($manager)) {
                        if(empty($where)) $where = " where c.manager_id=$manager";
                        else $where .= " and c.manager_id=$manager";
                    }
                    
                    $customer = filter_input(INPUT_GET, 'customer');
                    if(!empty($customer)) {
                        if(empty($where)) $where = " where c.customer_id=$customer";
                        else $where .= " and c.customer_id=$customer";
                    }

                    // Общее количество расчётов для установления количества страниц в постраничном выводе
                    $sql = "select count(c.id) from calculation c$where";
                    $fetcher = new Fetcher($sql);
                    
                    if($row = $fetcher->Fetch()) {
                        $pager_total_count = $row[0];
                    }
                    ?>
                    <div class="d-inline ml-3" style="color: gray; font-size: x-large;"><?=$pager_total_count ?></div>
                </div>
                <div class="p-1 text-nowrap">
                    <?php $order = filter_input(INPUT_GET, 'order'); ?>
                    <form class="form-inline d-inline" method="get">
                        <?php if(null !== $order): ?>
                        <input type="hidden" name="order" value="<?= $order ?>" />
                        <?php endif; ?>
                        <select id="customer" name="customer" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <option value="">Заказчик...</option>
                            <?php
                            $sql = "select distinct cus.id, cus.name from calculation c inner join customer cus on c.customer_id = cus.id order by cus.name";
                            $fetcher = new Fetcher($sql);
                            
                            while ($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?=$row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'customer') ? " selected='selected'" : "") ?>><?=$row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <select id="name" name="name" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <option value="">Имя заказа...</option>
                            <?php
                            $sql = "select distinct c.name, (select id from calculation where name=c.name limit 1) id from calculation c order by name";
                            $fetcher = new Fetcher($sql);
                            
                            while($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?= $row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'name') ? " selected='selected'" : "") ?>><?= $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <select id="unit" name="unit" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <option value="">Шт/кг...</option>
                            <option value="thing"<?= filter_input(INPUT_GET, 'unit') == 'thing' ? " selected='selected'" : "" ?>>Шт</option>
                            <option value="kg"<?= filter_input(INPUT_GET, 'unit') == 'kg' ? " selected='selected'" : "" ?>>Кг</option>
                        </select>
                        <select id="work_type" name="work_type" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <option value="">Тип работы...</option>
                            <?php
                            $sql = "select distinct wt.id, wt.name from calculation c inner join work_type wt on c.work_type_id = wt.id order by wt.name";
                            $fetcher = new Fetcher($sql);
                            
                            while ($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?=$row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'work_type') ? " selected='selected'" : "") ?>><?=$row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <select id="manager" name="manager" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <option value="">Менеджер...</option>
                            <?php
                            $sql = "select distinct u.id, u.last_name, u.first_name from calculation c inner join user u on c.manager_id = u.id order by u.last_name";
                            $fetcher = new Fetcher($sql);
                            
                            while ($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?=$row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'manager') ? " selected='selected'" : "") ?>><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                    <?php if(IsInRole(array('technologist', 'dev', 'manager', 'top_manager'))): ?>
                    <a href="create.php" class="btn btn-outline-dark"><i class="fas fa-plus"></i>&nbsp;Новый расчет</a>
                    <?php endif; ?>
                </div>
            </div>
            <table class="table table-hover" id="content_table">
                <thead>
                    <tr>
                        <th>ID&nbsp;&nbsp;<?= OrderLink('id') ?></th>
                        <th>Дата&nbsp;&nbsp;<?= OrderLink('date') ?></th>
                        <th>Заказчик&nbsp;&nbsp;<?= OrderLink('customer') ?></th>
                        <th>Имя заказа&nbsp;&nbsp;<?= OrderLink('name') ?></th>
                        <th class="text-center">Объем&nbsp;&nbsp;<?= OrderLink('quantity') ?></th>
                        <th>Тип работы&nbsp;&nbsp;<?= OrderLink('work_type') ?></th>
                        <th>Менеджер&nbsp;&nbsp;<?= OrderLink('manager') ?></th>
                        <th>Комментарий</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Сортировка
                    $orderby = "order by c.id desc";
                    
                    if(array_key_exists('order', $_REQUEST)) {
                        switch ($_REQUEST['order']) {
                            case 'id':
                                $orderby = "order by c.customer_id desc, c.id desc";
                                break;
                            
                            case 'date':
                                $orderby = "order by c.id desc";
                                break;
                            
                            case 'customer':
                                $orderby = "order by cus.name asc";
                                break;
                            
                            case 'name':
                                $orderby = "order by c.name asc";
                                break;
                            
                            case 'quantity':
                                $orderby = "order by c.quantity desc";
                                break;
                            
                            case 'work_type':
                                $orderby = "order by wt.name";
                                break;
                            
                            case 'manager':
                                $orderby = "order by u.last_name asc, u.first_name asc";
                                break;
                            
                            case 'status':
                                $orderby = "order by c.status_id";
                                break;
                        }
                    }
                    
                    $sql = "select c.id, c.date, c.customer_id, cus.name customer, c.name, c.unit, c.quantity, c.work_type_id, c.paints_count, "
                            . "c.percent_1, c.percent_2, c.percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
                            . "comment, "
                            . "(select id from calculation_result where calculation_id = c.id order by id desc limit 1) calculation_result_id, "
                            . "(select id from techmap where calculation_id = c.id order by id desc limit 1) techmap_id, "
                            . "wt.name work_type, u.last_name, u.first_name, "
                            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                            . "from calculation c "
                            . "inner join customer cus on c.customer_id = cus.id "
                            . "inner join work_type wt on c.work_type_id = wt.id "
                            . "inner join user u on c.manager_id = u.id$where "
                            . "$orderby limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    
                    while ($row = $fetcher->Fetch()):
                        
                    $rowcounter++;
                    $status = '';
                    $colour_style = '';
                    
                    if(!empty($row['techmap_id'])) {
                        $status = "Составлена тех. карта";
                        $colour = "green";
                        $colour_style = " color: $colour";
                    }
                    elseif(!empty ($row['calculation_result_id'])) {
                        $status = "Сделан расчёт";
                        $colour = "blue";
                        $colour_style = " color: $colour";
                    }
                    elseif(empty ($row['paints_count'])) {
                        $status = "Требуется расчёт";
                        $colour = "brown";
                        $colour_style = " color: $colour";
                    }
                    else {
                        $paints_count = $row['paints_count'];
                        $percents_exist = true;
                        
                        for($i=1; $i<=$paints_count; $i++) {
                            if(empty($row["percent_$i"])) {
                                $percents_exist = false;
                            }
                        }
                        
                        if(!$percents_exist) {
                            $status = "Требуется красочность";
                            $colour = "orange";
                            $colour_style = " color: $colour";
                        }
                        else {
                            $status = "Требуется расчёт";
                            $colour = "brown";
                            $colour_style = " color: $colour";
                        }
                    }
                    ?>
                    <tr>
                        <td class="text-nowrap"><?=$row['customer_id'].'-'.$row['num_for_customer'] ?></td>
                        <td class="text-nowrap"><?= DateTime::createFromFormat('Y-m-d H:i:s', $row['date'])->format('d.m.Y') ?></td>
                        <td><a href="javascript: void(0);" class="customer" data-toggle="modal" data-target="#customerModal" data-customer-id="<?=$row['customer_id'] ?>"><?=$row['customer'] ?></a></td>
                        <td><?= htmlentities($row['name']) ?></td>
                        <td class="text-right text-nowrap"><?=number_format($row['quantity'], 0, ",", " ") ?>&nbsp;<?=$row['unit'] == 'kg' ? 'кг' : 'шт' ?></td>
                        <td><?=$row['work_type'] ?></td>
                        <td class="text-nowrap"><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></td>
                        <td><?=$row['comment'] ?></td>
                        <td class="text-nowrap"><i class="fas fa-circle" style="color: <?=$colour ?>;"></i>&nbsp;&nbsp;<?=$status ?></td>
                        <td><a href="calculation.php<?= BuildQuery("id", $row['id']) ?>"><img src="<?=APPLICATION ?>/images/icons/vertical-dots.svg" /></a></td>
                    </tr>
                    <?php
                    endwhile;
                    ?>
                </tbody>
            </table>
            <?php
            if($rowcounter == 0) {
                echo '<p>Ничего не найдено.</p>';
            }
            
            include '../include/pager_bottom.php';
            ?>
        </div>
        <!-- Информация о заказчике -->
        <div class="modal fixed-left fade" id="customerModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-aside" role="document">
                <div class="modal-content" style="padding-left: 32px; padding-right: 32px; padding-bottom: 32px; padding-top: 84px; width: 521px; overflow-y: auto;"></div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/select2.min.js"></script>
        <script src="<?=APPLICATION ?>/js/i18n/ru.js"></script>
        <script>
            // Список с  поиском
            $('#customer').select2({
                placeholder: "Заказчик...",
                maximumSelectionLength: 1,
                language: "ru"
            });
            
            $('#name').select2({
                placeholder: "Имя заказа...",
                maximumSelectionLength: 1,
                language: "ru"
            });
            
            $('#unit').select2({
                placeholder: "Шт/кг...",
                maximumStatusLength: 1,
                language: "ru",
                width: '4rem'
            })
            
            $('#work_type').select2({
                placeholder: "Тип работы...",
                maximumSelectionLength: 1,
                language: "ru",
                width: '10rem'
            });
            
            $('#manager').select2({
                placeholder: "Менеджер...",
                maximumSelectionLength: 1,
                language: "ru"
            });
            
            
            
            // Заполнение информации о заказчике
            $('a.customer').click(function(e) {
                var customer_id = $(e.target).attr('data-customer-id');
                if(customer_id != null) {
                    $.ajax({ url: "../ajax/customer.php?id=" + customer_id })
                            .done(function(data) {
                                $('#customerModal .modal-dialog .modal-content').html(data);
                    });
                }
            });
        </script>
    </body>
</html>