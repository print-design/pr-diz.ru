<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager', 'administrator', 'designer'))) {
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
                        if(empty($where)) $where = " where c.name=(select name from request_calc where id=$name)";
                        else $where .= " and c.name=(select name from request_calc where id=$name)";
                    }
                    
                    $manager = filter_input(INPUT_GET, 'manager');
                    if(!empty($manager)) {
                        if(empty($where)) $where = " where cus.manager_id=$manager";
                        else $where .= " and cus.manager_id=$manager";
                    }
                    
                    $status = filter_input(INPUT_GET, 'status');
                    if(!empty($status)) {
                        if(empty($where)) $where = " where c.status_id=$status";
                        else $where .= " and c.status_id=$status";
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

                    // Общее количество расчётов для установления количества страниц в постраничном выводе
                    $sql = "select count(c.id) from request_calc c left join customer cus on c.customer_id=cus.id$where";
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
                        <?php if(null !== filter_input(INPUT_GET, 'status')): ?>
                        <input type="hidden" name="status" value="<?= filter_input(INPUT_GET, 'status') ?>" />
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
                    <?php if(IsInRole(array('technologist', 'dev', 'manager', 'administrator'))): ?>
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
                        <th></th>
                        <th class="text-center">Объем заказа&nbsp;&nbsp;<?= OrderLink('quantity') ?></th>
                        <th>Тип работы&nbsp;&nbsp;<?= OrderLink('work_type') ?></th>
                        <th>Менеджер&nbsp;&nbsp;<?= OrderLink('manager') ?></th>
                        <th>Комментарий</th>
                        <th>Статус&nbsp;&nbsp;<?= OrderLink('status') ?></th>
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
                    
                    $sql = "select c.id, c.date, c.customer_id, cus.name customer, c.name, c.unit, c.quantity, c.work_type_id, c.ink_number, "
                            . "c.percent_1, c.percent_2, c.percent_3, c.percent_4, c.percent_5, c.percent_6, c.percent_7, c.percent_8, "
                            . "c.comment, c.confirm, c.status_id, c.finished, "
                            . "(select id from request_calc_result where request_calc_id = c.id order by id desc limit 1) request_calc_result_id, "
                            . "(select id from techmap where request_calc_id = c.id order by id desc limit 1) techmap_id, "
                            . "wt.name work_type, u.last_name, u.first_name, "
                            . "(select count(id) from request_calc where customer_id = c.customer_id and id <= c.id) num_for_customer "
                            . "from request_calc c "
                            . "left join customer cus on c.customer_id = cus.id "
                            . "left join work_type wt on c.work_type_id = wt.id "
                            . "left join user u on cus.manager_id = u.id$where "
                            . "$orderby limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    
                    while ($row = $fetcher->Fetch()):
                        
                    $rowcounter++;
                    $status = '';
                    $colour_style = '';
                    
                    if(!empty($row['status_id'])) {
                        if(!$row['finished']) {
                            $status = "Не закончено редактирование";
                            $colour = "red";
                            $colour_style = " color: $colour";
                        }
                        elseif(!empty($row['techmap_id'])) {
                            $status = "Составлена тех. карта";
                            $colour = "green";
                            $colour_style = " color: $colour";
                        }
                        elseif($row['confirm']) {
                            $status = "Утверждено администратором";
                            $colour = "navy";
                            $colour_style = " color: $colour";
                        }
                        elseif(!empty ($row['request_calc_result_id'])) {
                            $status = "Сделан расчёт";
                            $colour = "blue";
                            $colour_style = " color: $colour";
                        }
                        elseif(empty ($row['ink_number'])) {
                            $status = "Требуется расчёт";
                            $colour = "brown";
                            $colour_style = " color: $colour";
                        }
                        else {
                            $ink_number = $row['ink_number'];
                            $percents_exist = true;
                        
                            for($i=1; $i<=$ink_number; $i++) {
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
                    }
                    ?>
                    <tr>
                        <td class="text-nowrap"><?=$row['customer_id'].'-'.$row['num_for_customer'] ?></td>
                        <td class="text-nowrap"><?= DateTime::createFromFormat('Y-m-d H:i:s', $row['date'])->format('d.m.Y') ?></td>
                        <td>
                            <?php if(!empty($row['customer'])): ?>
                            <?=$row['customer'] ?>&nbsp;<a href="javascript: void(0);" class="customer" data-toggle="modal" data-target="#customerModal" data-customer-id="<?=$row['customer_id'] ?>"><i class="fa fa-question-circle" data-customer-id="<?=$row['customer_id'] ?>"></i></a>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlentities($row['name']) ?></td>
                        <td><a href="request_calc.php<?= BuildQuery("id", $row['id']) ?>"><img src="../images/icons/vertical-dots.svg" /></a></td>
                        <td class="text-right text-nowrap">
                            <?php if(!empty($row['quantity'])): ?>
                            <?=number_format($row['quantity'], 0, ",", " ") ?>&nbsp;<?=$row['unit'] == 'kg' ? 'кг' : 'шт' ?>
                            <?php endif; ?>
                        </td>
                        <td><?=$row['work_type'] ?></td>
                        <td class="text-nowrap"><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></td>
                        <td><?=$row['comment'] ?></td>
                        <td class="text-nowrap"><i class="fas fa-circle" style="color: <?=$colour ?>;"></i>&nbsp;&nbsp;<?=$status ?></td>
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
            
            $('#status').select2({
                placeholder: "Статус...",
                maximumSelectionLength: 1,
                language: "ru"
            })
            
            // Заполнение информации о заказчике
            $('a.customer').click(function(e) {
                var customer_id = $(e.target).attr('data-customer-id');
                if(customer_id != null) {
                    $.ajax({ url: "../ajax/customer.php?id=" + customer_id })
                            .done(function(data) {
                                $('#customerModal .modal-dialog .modal-content').html(data);
                    })
                            .fail(function() {
                                alert('Ошибка при получении данных о заказчике');
                    });
                }
            });
        </script>
    </body>
</html>