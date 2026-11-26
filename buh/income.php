<?php
include '../include/topscripts.php';
include '../include/pager_top.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ACCOUNTANT], ROLE_NAMES[ROLE_MANAGER]))) {
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

// Валидация формы создания нового поступления
$form_valid = true;
$error_message = '';

$new_order_id_valid = '';
$new_paid_at_valid = '';
$new_payment_num_valid = '';
$new_amount_valid = '';

// Обработка отправки формы создания нового поступления
if(null !== filter_input(INPUT_POST, 'new_income_submit')) {
    $new_order_id = filter_input(INPUT_POST, 'new_order_id', FILTER_VALIDATE_INT);
    if(empty($new_order_id)) {
        $new_order_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $new_paid_at = filter_input(INPUT_POST, 'new_paid_at');
    if(empty($new_paid_at)) {
        $new_paid_at_valid = ISINVALID;
        $form_valid = false;
    }
    
    $new_payment_num = filter_input(INPUT_POST, 'new_payment_num');
    if(empty($new_payment_num)) {
        $new_payment_num_valid = ISINVALID;
        $form_valid = false;
    }
    
    $new_amount = filter_input(INPUT_POST, 'new_amount');
    $new_amount = str_replace([' ', "\xC2\xA0", ','], ['', '', '.'], $new_amount ?? '');
    if(empty($new_amount) || !is_numeric($new_amount)) {
        $new_amount_valid = ISINVALID;
        $form_valid = false;
    }
    
    $new_currency = filter_input(INPUT_POST, 'new_currency');
    if(!in_array($new_currency, CURRENCIES)) {
        $new_currency = CURRENCY_RUB;
    }
    
    $new_created_by = GetUserId();
    
    if($form_valid) {
        $sql = "insert into payment (order_id, paid_at, payment_num, amount, currency, created_by) values (?, ?, ?, ?, ?, ?)";
        $executer = new Executer($sql, [$new_order_id, $new_paid_at, $new_payment_num, $new_amount, $new_currency, $new_created_by]);
        $error_message = $executer->error;
        
        // После успешного добавления перезагружаем страницу, чтобы обновление (F5) не добавило поступление повторно
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/buh/income.php'.(empty($_SERVER['QUERY_STRING']) ? '' : '?'.$_SERVER['QUERY_STRING']));
            exit();
        }
    }
    else {
        $error_message = "Ошибка при добавлении поступления";
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
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join user u on c.manager_id = u.id";
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
else {
    $sql .= " order by p.id desc";
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
                    <form class="form-inline d-inline" method="get">
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
                        <select id="customer" name="customer" class="form-control" multiple="multiple" onchange="javascript: this.form.submit();">
                            <?php
                            $get_customer = filter_input(INPUT_GET, "customer");
                            if(null !== $get_customer):
                                $sql = "select name from customer where id = ?";
                            $fetcher = new Fetcher($sql, [$get_customer]);
                            if($row = $fetcher->Fetch()):
                            ?>
                            <option selected="selected" value="<?=$get_customer ?>"><?=$row[0] ?></option>
                            <?php
                            endif;
                            endif;
                            ?>
                        </select>
                    </form>
                    <?php if(IsInRole([ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ACCOUNTANT]])): ?>
                    <a href="javascript: void(0);" class="btn btn-dark ml-1" data-toggle="modal" data-target="#newIncome"><i class="fas fa-plus ml-2 mr-2"></i>&nbsp;Новое поступление</a>
                    <?php endif; ?>
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
        <!-- Новое поступление -->
        <div class="modal fixed-left fade" id="newIncome" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-aside" role="document">
                <div class="modal-content" style="padding-left: 32px; padding-right: 32px; padding-bottom: 32px; padding-top: 32px; width: 521px; overflow-y: auto;">
                    <button type="button" class="close" data-dismiss='modal' style="position: absolute; right: 34px; top: 34px; z-index: 2000;"><img src="../images/icons/close_modal_red.svg" /></button>
                    <h2><span class="mr-4" style="color: #EC3A7A;">₽</span>Добавить поступление</h2>
                    <form method="post">
                        <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
                        <div class="form-group">
                            <label for="new_customer">Заказчик</label>
                            <select id="new_customer" name="new_customer" class="form-control" multiple="multiple" required="required"></select>
                        </div>
                        <div class="form-group">
                            <label for="new_order_id">Заказ</label>
                            <select id="new_order_id" name="new_order_id" class="form-control<?=$new_order_id_valid ?>" multiple="multiple" required="required"></select>
                            <div class="invalid-feedback">Заказ обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="new_paid_at">Дата и время</label>
                            <input type="datetime-local" max="<?=date('Y-m-d') ?>T23:59" name="new_paid_at" class="form-control<?=$new_paid_at_valid ?>" required="required" />
                            <div class="invalid-feedback">Дата и время обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="new_payment_num">Номер платежа</label>
                            <input type="text" name="new_payment_num" class="form-control<?=$new_payment_num_valid ?>" required="required" autocomplete="new-password" placeholder="№" />
                            <div class="invalid-feedback">Номер платежа обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="new_amount">Сумма</label>
                            <div class="input-group">
                                <input type="text" class="form-control float-only float-format<?=$new_amount_valid ?>" name="new_amount" required="required" autocomplete="new-password" placeholder="0.00" />
                                <div class="input-group-append">
                                    <select name="new_currency" required="required">
                                        <option value="<?= CURRENCY_RUB ?>"><?= CURRENCY_SIGNES[CURRENCY_RUB] ?></option>
                                        <option value="<?= CURRENCY_USD ?>"><?= CURRENCY_SIGNES[CURRENCY_USD] ?></option>
                                        <option value="<?= CURRENCY_EURO ?>"><?= CURRENCY_SIGNES[CURRENCY_EURO] ?></option>
                                    </select>
                                </div>
                                <div class="invalid-feedback">Сумма обязательно</div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark mt-4" id="new_income_submit" name="new_income_submit"><i class="fas fa-plus ml-2 mr-2"></i>&nbsp;Добавить</button>
                        <button type="button" class="btn btn-link mt-4" data-dismiss="modal">Отмена</button>
                    </form>
                </div>
            </div>
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
            
            $('#customer').select2({
                placeholder: "Заказчик...",
                maximumSelectionLength: 1,
                language: "ru",
                width: "15rem",
                minimumInputLength: 3,
                ajax: {
                    url: '_customer_select2.php',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return { results: data };
                    },
                    cache: false
                }
            });
            
            $('#new_customer').select2({
                placeholder: "Выберите заказчика",
                maximumSelectionLength: 1,
                language: "ru",
                width: "100%",
                minimumInputLength: 3,
                ajax: {
                    url: '_customer_select2.php',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return { results: data };
                    },
                    cache: false
                }
            });
        
            $('#new_order_id').select2({
                placeholder: "Выберите заказ",
                maximumSelectionLength: 1,
                language: "ru",
                width: "100%",
                minimumInputLength: 3,
                ajax: {
                    url: "_order_select2.php",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        var customerVal = $('#new_customer').val();
                        return {
                            q: params.term,
                            customer: (customerVal && customerVal.length > 0) ? customerVal[0] : ''
                        };
                    },
                    processResults: function(data) {
                        return { results: data };
                    },
                    cache: false
                }
            });

            // Поле поиска select2 в скрытом окне получает ширину 0 и placeholder не виден (см. select2 4.0.13).
            // Когда окно показалось, растягиваем поле поиска на всю ширину.
            $('#newIncome').on('shown.bs.modal', function() {
                $('#new_customer, #new_order_id').each(function() {
                    if(!$(this).val() || $(this).val().length == 0) {
                        $(this).next('.select2').find('.select2-search__field').css('width', '100%');
                    }
                });
            });
        </script>
    </body>
</html>