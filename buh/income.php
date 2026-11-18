<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ACCOUNTANT]))) {
    include '../include/_unauthorized.php';
}

// Чтобы заголовок не выдавал ошибку
$paying = false;
$paid = false;
$production = false;
$status_id = null;
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php
        include '../include/head.php';
        ?>
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
        include 'header.php';
        include '../include/pager_top.php';
        
        // Общее количество поступлений для установления количества страниц в постраничном выводе
        $sql = "select count(p.id) "
                . "from payment p "
                . "inner join calculation c on p.order_id = c.id "
                . "inner join user u on p.created_by = u.id";
        $fetcher = new Fetcher($sql, []);
        
        if($row = $fetcher->Fetch()) {
            $pager_total_count = $row[0];
        }
        ?>
        <div class="container-fluid">
            <h1 class="d-inline">Поступления</h1><div class="d-inline ml-3" style="color: gray; font-size: x-large;"><?=$pager_total_count ?></div>
            <table class="table table-hover typography">
                <tr>
                    <th class="text-nowrap">ID заказа</th>
                    <th>Заказ</th>
                    <th>Сумма</th>
                    <th>Валюта</th>
                    <th class="text-nowrap">№ платежа</th>
                    <th>Дата</th>
                    <th>Бухгалтер</th>
                    <th></th>
                </tr>
                <?php
                $sql = "select p.id, p.order_id, c.customer_id, c.duplicate_num_for_customer, c.name, p.amount, p.currency, p.payment_num, p.paid_at, u.last_name, u.first_name "
                        . "from payment p "
                        . "inner join calculation c on p.order_id = c.id "
                        . "inner join user u on p.created_by = u.id "
                        . "order by p.id desc limit ?, ?";
                $fetcher = new Fetcher($sql, [$pager_skip, $pager_take]);
                while ($row = $fetcher->Fetch()):
                ?>
                <tr>
                    <td class="text-nowrap"><?=$row['customer_id'].'-'.$row['duplicate_num_for_customer'] ?></td>
                    <td><?=$row['name'] ?></td>
                    <td><?= DisplayNumber(floatval($row['amount']), 2) ?></td>
                    <td><?= CURRENCY_SIGNES[$row['currency']] ?></td>
                    <td><?=$row['payment_num'] ?></td>
                    <td><?= DateTime::createFromFormat("Y-m-d H:i:s", $row['paid_at'])->format('d.m.Y H:i') ?></td>
                    <td><?=$row['last_name'].' '.$row['first_name'] ?></td>
                    <td>
                        <?php
                        $get_params = $_GET;
                        $get_params['income'] = 1;
                        $get_params['id'] = $row['order_id'];
                        ?>
                        <a href="details.php?<?= http_build_query($get_params) ?>">
                            <svg viewBox="0 0 24 24" width="24" height="24" data-flexim-name="arrow-right" style="color: currentcolor; display: inline-flex; flex-shrink: 0;">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.5859 12L8.29297 17.2929L9.70718 18.7071L16.4143 12L9.70718 5.29291L8.29297 6.70712L13.5859 12Z" fill="currentColor"></path>
                            </svg>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>