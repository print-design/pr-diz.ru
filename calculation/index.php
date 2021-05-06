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
                        <th>ID<a href="?sort=id">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Дата<a href="?sort=date">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Заказчик<a href="?sort=customer">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Имя заказа<a href="?sort=order">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Объем кг.<a href="?sort=weight">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Тип работы<a href="?sort=work_type">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Менеджер<a href="?sort=manager">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Статус<a href="?sort=status">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
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
                    
                    $sql = "select c.id, c.date, c.customer_id, cus.name customer, c.name, c.weight, wt.name work_type, u.last_name, u.first_name, c.status_id, c.status_id "
                            . "from calculation c "
                            . "inner join customer cus on c.customer_id = cus.id "
                            . "inner join work_type wt on c.work_type_id = wt.id "
                            . "inner join user u on c.manager_id = u.id "
                            . "order by c.date desc";
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