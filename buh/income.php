<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ACCOUNTANT]))) {
    include '../include/_unauthorized.php';
}

// Формирование ссылки для сортировки по столбцу
function OrderLink($param) {
    if(array_key_exists('order', $_REQUEST) && $_REQUEST['order'] == $param) {
        echo "<strong><i class='fas fa-chevron-down' style='color: black; font-size: small;'></i></strong>";
    }
    else {
        echo "<a class='gray' href='".BuildQueryAddRemove("order", $param, "page")."' style='font-size: x-small;'><i class='fas fa-chevron-down'></i></a>";
    }
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
                    <th class="text-nowrap">Дата и время&nbsp;&nbsp;<?= OrderLink('datetime') ?></th>
                    <th class="text-nowrap">ID заказа&nbsp;&nbsp;<?= OrderLink('id') ?></th>
                    <th class="text-nowrap">№ платежа</th>
                    <th>Заказчик&nbsp;&nbsp;<?= OrderLink('customer') ?></th>
                    <th>Заказ</th>
                    <th>Менеджер</th>
                    <th class="text-right">Сумма&nbsp;&nbsp;<?= OrderLink('amount') ?></th>
                    <th></th>
                </tr>
                <?php
                $sql = "select p.id, p.order_id, p.paid_at, c.customer_id, c.duplicate_num_for_customer, p.payment_num, cus.name customer, c.name, p.amount, p.currency, u.last_name, u.first_name "
                        . "from payment p "
                        . "inner join calculation c on p.order_id = c.id "
                        . "inner join customer cus on c.customer_id = cus.id "
                        . "inner join user u on c.manager_id = u.id";
                
                // Сортировка
                if(array_key_exists('order', $_REQUEST)) {
                    switch($_REQUEST['order']) {
                        case 'datetime':
                            $sql .= " order by p.paid_at desc";
                            break;
                        
                        case 'id':
                            $sql .= " order by c.customer_id asc, c.duplicate_num_for_customer asc";
                            break;
                        
                        case 'customer':
                            $sql .= " order by cus.name asc";
                            break;
                        
                        case 'amount':
                            $sql .= " order by p.amount asc";
                            break;
                        
                        default :
                            $sql .= " order by p.id desc";
                            break;
                    }
                }

                $sql .= " limit $pager_skip, $pager_take";
                
                $params = [];
                
                $fetcher = new Fetcher($sql, $params);
                while ($row = $fetcher->Fetch()):
                ?>
                <tr>
                    <td><?= DateTime::createFromFormat("Y-m-d H:i:s", $row['paid_at'])->format('d.m.Y H:i') ?></td>
                    <td class="text-nowrap"><?=$row['customer_id'].'-'.$row['duplicate_num_for_customer'] ?></td>
                    <td><?=$row['payment_num'] ?></td>
                    <td><?=$row['customer'] ?></td>
                    <td><?=$row['name'] ?></td>
                    <td class="text-nowrap"><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></td>
                    <td class="text-nowrap text-right"><?= DisplayNumber(floatval($row['amount']), 2) ?>&nbsp;<?= CURRENCY_SIGNES[$row['currency']] ?></td>
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