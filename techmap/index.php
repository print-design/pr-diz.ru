<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

function OrderLink($param) {
    if(array_key_exists('order', $_REQUEST) && $_REQUEST['order'] == $param) {
        echo "<strong><i class='fas fa-arrow-down' style='color: black; font-size: small;'></i></strong>";
    }
    else {
        echo "<a class='gray' href='". BuildQueryAddRemove("order", $param, "page")."' style='font-size: x-small;'><i class='fas fa-arrow-down'></i></a>";
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
                    <h1 style="font-size: 32px; font-weight: 600;" class="d-inline">Технологические карты</h1>
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
                        if(empty($where)) $where = " where c.name=(select name from request_calc where id=$name)";
                        else $where .= " and c.name=(select name from request_calc where id=$name)";
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
                    
                    $from = filter_input(INPUT_GET, 'from');
                    if(!empty($from)) {
                        if(empty($where)) $where = " where c.date >= '$from'";
                        else $where .= " and c.date >= '$from'";
                    }
                    
                    $to = filter_input(INPUT_GET, 'to');
                    if(!empty($to)) {
                        $to = date('Y-m-d', strtotime($to.' +1 days'));
                        if(empty($where)) $where = " where c.date <= '$to'";
                        else $where .= " and c.date <= '$to'";
                    }
                    
                    // Общее количество технологических карт для установления количества страниц в постраничном выводе
                    $sql = "select count(t.id) from techmap t inner join request_calc c on t.request_calc_id = c.id$where";
                    $fetcher = new Fetcher($sql);
                    
                    if($row = $fetcher->Fetch()) {
                        $pager_total_count = $row[0];
                    }
                    ?>
                </div>
                <div class="p-1 text-nowrap">
                    <?php $order = filter_input(INPUT_GET, 'order'); ?>
                    <form class="form-inline d-inline" method="get">
                        <?php if(null !== filter_input(INPUT_GET, 'customer')): ?>
                        <input type="hidden" name="customer" value="<?= filter_input(INPUT_GET, 'customer') ?>" />
                        <?php endif; ?>
                        <?php if(null !== filter_input(INPUT_GET, 'name')): ?>
                        <input type="hidden" name="name" value="<?= filter_input(INPUT_GET, 'name') ?>" />
                        <?php endif; ?>
                        <?php if(null !== filter_input(INPUT_GET, 'manager')): ?>
                        <input type="hidden" name="manager" value="<?= filter_input(INPUT_GET, 'manager') ?>" />
                        <?php endif; ?>
                        <?php
                        $from_value = filter_input(INPUT_GET, 'from');
                        if(empty($from_value)) {
                            $from_value = date("Y-m-d", strtotime('-6 months'));
                        }
                        $to_value = filter_input(INPUT_GET, 'to');
                        if(empty($to_value)) {
                            $to_value = date("Y-m-d");
                        }
                        ?>
                        <div class="d-inline ml-3 mr-1">от</div>
                        <input type="date" id="from" name="from" class="form-control form-control-sm" style="width: 140px;" value="<?=$from_value ?>" onchange="javascript: this.form.submit();"/>
                        <div class="d-inline ml-1 mr-1">до</div>
                        <input type="date" id="to" name="to" class="form-control form-control-sm" style="width: 140px;" value="<?= $to_value ?>" onchange="javascript: this.form.submit();"/>
                    </form>
                </div>
            </div>
            <table class="table table-hover" id="content-table">
                <thead>
                    <tr>
                        <th>ID&nbsp;&nbsp;<?= OrderLink('id') ?></th>
                        <th>Дата&nbsp;&nbsp;<?= OrderLink('date') ?></th>
                        <th>Заказчик&nbsp;&nbsp;<?= OrderLink('customer') ?></th>
                        <th>Имя заказа&nbsp;&nbsp;<?= OrderLink('name') ?></th>
                        <th class="text-center">Объем&nbsp;&nbsp;<?= OrderLink('quantity') ?></th>
                        <th>Тип работы&nbsp;&nbsp;<?= OrderLink('work_type') ?></th>
                        <th>Менеджер&nbsp;&nbsp;<?= OrderLink('manager') ?></th>
                        <th>В работу&nbsp;&nbsp;<?= OrderLink('work_date') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Сортировка
                    $orderby = "order by t.id desc";
                    
                    if(array_key_exists('order', $_REQUEST)) {
                        switch ($_REQUEST['order']) {
                            case 'id':
                                $orderby = "order by c.customer_id desc, t.id desc";
                                break;
                            
                            case 'date':
                                $orderby = "order by t.id desc";
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
                            
                            case 'work_date':
                                $orderby = "order by t.work_date desc";
                                break;
                        }
                    }
                    
                    $sql = "select t.id, t.date, t.work_date, t.work_shift, c.customer_id, cus.name customer, c.name, c.quantity, c.unit, wt.name work_type, u.last_name, u.first_name, "
                            . "(select count(t1.id) from techmap t1 inner join request_calc c1 on t1.request_calc_id = c1.id where c1.customer_id = c.customer_id and t1.id <= t.id) num_for_customer "
                            . "from techmap t "
                            . "inner join request_calc c on t.request_calc_id = c.id "
                            . "inner join customer cus on c.customer_id = cus.id "
                            . "inner join work_type wt on c.work_type_id = wt.id "
                            . "inner join user u on c.manager_id = u.id$where "
                            . "$orderby limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    
                    while ($row = $fetcher->Fetch()):
                        
                    $rowcounter++;
                    $colour = 'green';
                    
                    if(empty($row['work_date'])) {
                        $colour = "orange";
                    }
                    ?>
                    <tr>
                        <td class="text-nowrap"><?=$row['customer_id'].'-'.$row['num_for_customer'] ?></td>
                        <td class="text-nowrap"><?= DateTime::createFromFormat('Y-m-d H:i:s', $row['date'])->format('d.m.Y') ?></td>
                        <td><a href="javascript: void(0)'" class="customer" data-toggle="modal" data-target="#customerModal" data-customer-id="<?=$row['customer_id'] ?>"><?=$row['customer'] ?></a></td>
                        <td><a href="details.php<?= BuildQuery("id", $row['id']) ?>"><?= htmlentities($row['name']) ?></a></td>
                        <td class="text-right text-nowrap"><?= number_format($row['quantity'], 0, ",", " ") ?>&nbsp;<?=$row['unit'] == 'kg' ? 'кг' : 'шт' ?></td>
                        <td><?=$row['work_type'] ?></td>
                        <td class="text-nowrap"><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></td>
                        <td class="text-nowrap"><i class="fas fa-circle" style="color: <?=$colour ?>"></i>&nbsp;&nbsp;<?= empty($row['work_date']) || empty($row['work_shift']) ? 'черновик' : DateTime::createFromFormat('Y-m-d', $row['work_date'])->format('d.m.Y').' '.($row['work_shift'] == 'day' ? 'день' : 'ночь') ?></td>
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