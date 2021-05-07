<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Получение всех статусов
$fetcher = (new Fetcher("select id, name, colour from calculation_status"));
$statuses = array();

while ($row = $fetcher->Fetch()) {
    $status = array();
    $status['name'] = $row['name'];
    $status['colour'] = $row['colour'];
    $statuses[$row['id']] = $status;
}

function OrderLink($param) {
    if(array_key_exists('order', $_REQUEST) && $_REQUEST['order'] == $param) {
        echo "<strong><i class='fas fa-arrow-down'></i></strong>";
    }
    else {
        echo "<a href='". BuildQuery('order', $param)."'><i class='fas fa-arrow-down'></i></a>";
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
                <div class="p-1">
                    <h1 style="font-size: 32px; line-height: 48px; font-weight: 600;">Расчеты</h1>
                </div>
                <div class="p-1">
                    <?php $order = filter_input(INPUT_GET, 'order'); ?>
                    <form class="form-inline d-inline" method="get">
                        <?php if(null !== $order): ?>
                        <input type="hidden" name="order" value="<?= $order ?>" />
                        <?php endif; ?>
                        <select name="status" class="form-control" onchange="javascript: this.form.submit();">
                            <option value="">Статус...</option>
                            <?php
                            $sql = "select distinct cs.id, cs.name from calculation c inner join calculation_status cs on c.status_id = cs.id order by cs.name";
                            $fetcher = new Fetcher($sql);
                            
                            while ($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?=$row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'status') ? " selected='selected'" : "") ?>><?=$row['name'] ?></option>
                            <?php
                            endwhile;
                            ?>
                        </select>
                        <select name="work_type" class="form-control" onchange="javascript: this.form.submit();">
                            <option value="">Тип работы...</option>
                            <?php
                            $sql = "select distinct wt.id, wt.name from calculation c inner join work_type wt on c.work_type_id = wt.id order by wt.name";
                            $fetcher = new Fetcher($sql);
                            
                            while ($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?=$row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'work_type') ? " selected='selected'" : "") ?>><?=$row['name'] ?></option>
                            <?php
                            endwhile;
                            ?>
                        </select>
                        <select name="manager" class="form-control" onchange="javascript: this.form.submit();">
                            <option value="">Менеджер...</option>
                            <?php
                            $sql = "select distinct u.id, u.last_name, u.first_name from calculation c inner join user u on c.manager_id = u.id order by u.last_name";
                            $fetcher = new Fetcher($sql);
                            
                            while ($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?=$row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'manager') ? " selected='selected'" : "") ?>><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></option>
                            <?php
                            endwhile;
                            ?>
                        </select>
                        <select name="customer" class="form-control" onchange="javascript: this.form.submit();">
                            <option value="">Заказчик...</option>
                            <?php
                            $sql = "select distinct cus.id, cus.name from calculation c inner join customer cus on c.customer_id = cus.id order by cus.name";
                            $fetcher = new Fetcher($sql);
                            
                            while ($row = $fetcher->Fetch()):
                            ?>
                            <option value="<?=$row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'customer') ? " selected='selected'" : "") ?>><?=$row['name'] ?></option>
                            <?php
                            endwhile;
                            ?>
                        </select>
                    </form>
                    <a href="create.php" class="btn btn-outline-dark"><i class="fas fa-plus"></i>&nbsp;Новый расчет</a>
                </div>
            </div>
            <table class="table table-hover" id="content_table">
                <thead>
                    <tr>
                        <th>ID&nbsp;<?= OrderLink('id') ?></th>
                        <th>Дата&nbsp;<?= OrderLink('date') ?></th>
                        <th>Заказчик&nbsp;<?= OrderLink('customer') ?></th>
                        <th>Имя заказа&nbsp;<?= OrderLink('name') ?></th>
                        <th>Объем кг.&nbsp;<?= OrderLink('weight') ?></th>
                        <th>Тип работы&nbsp;<?= OrderLink('work_type') ?></th>
                        <th>Менеджер&nbsp;<?= OrderLink('manager') ?></th>
                        <th>Статус&nbsp;<?= OrderLink('status') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Фильтр
                    $where = '';
                    
                    $status = filter_input(INPUT_GET, 'status');
                    if(!empty($status)) {
                        if(empty($where)) $where = " where c.status_id=$status";
                        else $where .= " and c.status_id=$status";
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
                    
                    // Сортировка
                    $orderby = "order by c.id desc";
                    
                    if(array_key_exists('order', $_REQUEST)) {
                        switch ($_REQUEST['order']) {
                            case 'id':
                                $orderby = "order by c.customer_id desc, c.id desc";
                                break;
                            
                            case 'date':
                                $orderby = "order by c.date desc";
                                break;
                            
                            case 'customer':
                                $orderby = "order by cus.name asc";
                                break;
                            
                            case 'name':
                                $orderby = "order by c.name asc";
                                break;
                            
                            case 'weight':
                                $orderby = "order by c.weight desc";
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
                    
                    $sql = "select c.id, c.date, c.customer_id, cus.name customer, c.name, c.weight, wt.name work_type, u.last_name, u.first_name, c.status_id "
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
                    
                    if(!empty($statuses[$row['status_id']]['name'])) {
                        $status = $statuses[$row['status_id']]['name'];
                    }
                    
                    if(!empty($statuses[$row['status_id']]['colour'])) {
                        $colour = $statuses[$row['status_id']]['colour'];
                        $colour_style = " color: $colour";
                    }
                    ?>
                    <tr>
                        <td><?=$row['customer_id'].'-'.$row['id'] ?></td>
                        <td><?=$row['date'] ?></td>
                        <td><?=$row['customer'] ?></td>
                        <td><?= htmlentities($row['name']) ?></td>
                        <td><?=$row['weight'] ?></td>
                        <td><?=$row['work_type'] ?></td>
                        <td><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></td>
                        <td><i class="fas fa-circle" style="color: <?=$colour ?>;"></i>&nbsp;&nbsp;<?=$status ?></td>
                        <td><a href="calculation.php<?= BuildQuery("id", $row['id']) ?>"><i class="fas fa-ellipsis-h"></i></a></td>
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
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
    </body>
</html>