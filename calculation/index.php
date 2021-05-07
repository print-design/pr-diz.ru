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
        echo "<a href='?order=$param'><i class='fas fa-arrow-down'></i></a>";
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
                    <a href="new.php" class="btn btn-outline-dark"><i class="fas fa-plus"></i>&nbsp;Новый расчет</a>
                </div>
            </div>
            <table class="table" id="content_table">
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
                    $sql = "select count(id) from calculation";
                    $fetcher = new Fetcher($sql);
                    
                    if($row = $fetcher->Fetch()) {
                        $pager_total_count = $row[0];
                    }
                    
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
                            . "inner join user u on c.manager_id = u.id "
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
                        <td><i class="fas fa-ellipsis-h"></i></td>
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