<?php
include '../include/topscripts.php';
include '../include/pager_top.php';

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

// Фильтр
function GetFilter(&$params) {
    $filter = '';
    
    $from = filter_input(INPUT_GET, 'from');
    $to = filter_input(INPUT_GET, 'to');
    $date_from = null;
    $date_to = null;
    GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);
    $diff1day = new DateInterval('P1D');
    $date_to->add($diff1day);
    
    if(!empty($from)) {
        $filter .= empty($filter) ? " where p.paid_at >= ?" : " and p.paid_at >= ?";
        $params[] = $date_from->format('Y-m-d');
    }
    
    if(!empty($to)) {
        $filter .= empty($filter) ? " where p.paid_at <= ?" : " and p.paid_at <= ?";
        $params[] = $date_to->format('Y-m-d');
    }
    
    $manager = filter_input(INPUT_GET, 'manager');
    if(!empty($manager)) {
        $filter .= empty($filter) ? " where c.manager_id = ?" : " and c.manager_id = ?";
        $params[] = $manager;
    }
    
    $customer = filter_input(INPUT_GET, 'customer');
    if(!empty($customer)) {
        $filter .= empty($filter) ? " where c.customer_id = ?" : " and c.customer_id = ?";
        $params[] = $customer;
    }
    
    return $filter;
}

// Общее количество поступлений для установления количества страниц в постраничном выводе
$sql = "select count(p.id) "
        . "from payment p "
        . "inner join calculation c on p.order_id = c.id "
        . "inner join user u on p.created_by = u.id";
$params = [];
$sql .= GetFilter($params);
$fetcher = new Fetcher($sql, $params);

if($row = $fetcher->Fetch()) {
    $pager_total_count = $row[0];
}

// Список поступлений
$sql = "select p.id, p.order_id, p.paid_at, c.customer_id, c.duplicate_num_for_customer, p.payment_num, cus.name customer, c.name, p.amount, p.currency, u.last_name, u.first_name "
        . "from payment p "
        . "inner join calculation c on p.order_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join user u on c.manager_id = u.id";
$params = [];
$sql .= GetFilter($params);

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

$grabber = new Grabber($sql, $params);
$error_message = $grabber->error;
$incomes = $grabber->result;

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
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <h1 class="d-inline">Поступления</h1><div class="d-inline ml-3" style="color: gray; font-size: x-large;"><?=$pager_total_count ?></div>
                </div>
                <div class="p-1 text-nowrap d-flex justify-content-end">
                    <form class="form-inline" method="get">
                        <input type="hidden" name="to" value="<?= filter_input(INPUT_GET, 'to') ?>" />
                        <label for="from" style="font-size: larger;">от&nbsp;</label>
                        <input type="date" 
                               id="from" 
                               name="from" 
                               class="form-control mr-2" 
                               value="<?= filter_input(INPUT_GET, 'from') ?>" 
                               style="border: 0; width: 8.5rem;" 
                               onchange="javascript: this.form.submit();" />
                    </form>
                    <form class="form-inline" method="get">
                        <input type="hidden" name="from" value="<?= filter_input(INPUT_GET, 'from') ?>" />
                        <label for="to" style="font-size: larger;">до&nbsp;</label>
                        <input type="date" 
                               id="to" 
                               name="to" 
                               class="form-control mr-2" 
                               value="<?= filter_input(INPUT_GET, 'to') ?>" 
                               style="border: 0; width: 8.5rem;" 
                               onchange="javascript: this.form.submit();" />
                    </form>
                    <select id="manager" name="manager" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                        <option value="">Менеджер...</option>
                        <?php
                        $sql = "select u.id, u.last_name, u.first_name from user u where exists (select 1 from calculation c where c.manager_id = u.id) order by u.last_name, u.first_name";
                        $fetcher = new Fetcher($sql, []);
                        while ($row = $fetcher->Fetch()):
                        ?>
                        <option value="<?=$row['id'] ?>"<?=$row['id'] == filter_input(INPUT_GET, 'manager') ? " selected='selected'" : "" ?>><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <a href="create.php" class="btn btn-dark"><i class="fas fa-plus"></i>&nbsp;Новое поступление</a>
                </div>
            </div>
            
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
                foreach($incomes as $income):
                ?>
                <tr>
                    <td><?= DateTime::createFromFormat("Y-m-d H:i:s", $income['paid_at'])->format('d.m.Y H:i') ?></td>
                    <td class="text-nowrap"><?=$income['customer_id'].'-'.$income['duplicate_num_for_customer'] ?></td>
                    <td><?=$income['payment_num'] ?></td>
                    <td><?=$income['customer'] ?></td>
                    <td><?=$income['name'] ?></td>
                    <td class="text-nowrap"><?=(mb_strlen($income['first_name']) == 0 ? '' : mb_substr($income['first_name'], 0, 1).'. ').$income['last_name'] ?></td>
                    <td class="text-nowrap text-right"><?= DisplayNumber(floatval($income['amount']), 2) ?>&nbsp;<?= CURRENCY_SIGNES[$income['currency']] ?></td>
                    <td>
                        <?php
                        $get_params = $_GET;
                        $get_params['income'] = 1;
                        $get_params['id'] = $income['order_id'];
                        ?>
                        <a href="details.php?<?= http_build_query($get_params) ?>">
                            <svg viewBox="0 0 24 24" width="24" height="24" data-flexim-name="arrow-right" style="color: currentcolor; display: inline-flex; flex-shrink: 0;">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.5859 12L8.29297 17.2929L9.70718 18.7071L16.4143 12L9.70718 5.29291L8.29297 6.70712L13.5859 12Z" fill="currentColor"></path>
                            </svg>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?= APPLICATION ?>/js/select2.min.js"></script>
        <script src="<?= APPLICATION ?>/js/i18n/ru.js"></script>
        <script>
            // Список с поиском
            $('#manager').select2({
                placeholder: "Менеджер...",
                maximumSelectionLength: 1,
                language: "ru",
                width: "8rem"
            });
        </script>
    </body>
</html>