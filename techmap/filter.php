<form class="form-inline ml-auto mr-3" method="get">
    <?php if(null !== filter_input(INPUT_GET, 'order')): ?>
    <input type="hidden" name="order" value="<?= filter_input(INPUT_GET, 'order') ?>" />
    <?php endif; ?>
    <?php if(null !== filter_input(INPUT_GET, 'from')): ?>
    <input type="hidden" name="from" value="<?= filter_input(INPUT_GET, 'from') ?>" />
    <?php endif; ?>
    <?php if(null !== filter_input(INPUT_GET, 'to')): ?>
    <input type="hidden" name="to" value="<?= filter_input(INPUT_GET, 'to') ?>" />
    <?php endif; ?>
    <select id="customer" name="customer" class="form-control form-control-sm" multiple="multiple" onchange="javascript: this.form.name.value = ''; this.form.manager.value = ''; this.form.submit();">
        <option value="">Заказчик...</option>
        <?php
        $sql = "select distinct cus.id, cus.name from request_calc c inner join customer cus on c.customer_id = cus.id inner join techmap t on t.request_calc_id = c.id order by cus.name";
        $fetcher = new Fetcher($sql);
                            
        while ($row = $fetcher->Fetch()):
        ?>
        <option value="<?=$row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'customer') ? " selected='selected'" : "") ?>><?=$row['name'] ?></option>
        <?php endwhile; ?>
    </select>
    <select id="name" name="name" class="form-control form-control-sm" multiple="multiple" onchange="javascript: this.form.submit();">
        <option value="">Имя заказа...</option>
        <?php
        $where = "";
        $customer_id = filter_input(INPUT_GET, 'customer');
        if(!empty($customer_id)) {
            $where = "where c.customer_id = $customer_id ";
        }
        $sql = "select distinct c.name, (select id from request_calc where name=c.name limit 1) id from request_calc c inner join techmap t on t.request_calc_id = c.id $where";
        $sql .= "order by name";
        $fetcher = new Fetcher($sql);
                            
        while($row = $fetcher->Fetch()):
        ?>
        <option value="<?= $row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'name') ? " selected='selected'" : "") ?>><?= $row['name'] ?></option>
        <?php endwhile; ?>
    </select>
    <select id="manager" name="manager" class="form-control form-control-sm" multiple="multiple" onchange="javascript: this.form.submit();">
        <option value="">Менеджер...</option>
        <?php
        $manager_id = null;
        
        if(!empty(filter_input(INPUT_GET, 'customer'))) {
            $customer_id = filter_input(INPUT_GET, 'customer');
            $sql = "select manager_id from customer where id = $customer_id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $manager_id = $row[0];
            }
        }
        
        $sql = "select distinct u.id, u.last_name, u.first_name from request_calc c inner join customer cus on c.customer_id = cus.id inner join user u on cus.manager_id = u.id inner join techmap t on t.request_calc_id = c.id order by u.last_name";
        $fetcher = new Fetcher($sql);
                            
        while ($row = $fetcher->Fetch()):
        $selected = "";
        
        if($row['id'] == filter_input(INPUT_GET, 'manager')) {
            $selected = " selected='selected'";
        }
        
        if(empty(filter_input(INPUT_GET, 'manager')) && $row['id'] == $manager_id) {
            $selected = " selected='selected'";
        }
        ?>
        <option value="<?=$row['id'] ?>"<?=$selected ?>><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></option>
        <?php endwhile; ?>
    </select>
</form>