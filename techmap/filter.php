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
    <select id="customer" name="customer" class="form-control form-control-sm" multiple="multiple" onchange="javascript: this.form.submit();">
        <option value="">Заказчик...</option>
        <?php
        $sql = "select distinct cus.id, cus.name from request_calc c inner join customer cus on c.customer_id = cus.id order by cus.name";
        $fetcher = new Fetcher($sql);
                            
        while ($row = $fetcher->Fetch()):
        ?>
        <option value="<?=$row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'customer') ? " selected='selected'" : "") ?>><?=$row['name'] ?></option>
        <?php endwhile; ?>
    </select>
    <select id="name" name="name" class="form-control form-control-sm" multiple="multiple" onchange="javascript: this.form.submit();">
        <option value="">Имя заказа...</option>
        <?php
        $sql = "select distinct c.name, (select id from request_calc where name=c.name limit 1) id from request_calc c order by name";
        $fetcher = new Fetcher($sql);
                            
        while($row = $fetcher->Fetch()):
        ?>
        <option value="<?= $row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'name') ? " selected='selected'" : "") ?>><?= $row['name'] ?></option>
        <?php endwhile; ?>
    </select>
    <select id="manager" name="manager" class="form-control form-control-sm" multiple="multiple" onchange="javascript: this.form.submit();">
        <option value="">Менеджер...</option>
        <?php
        $sql = "select distinct u.id, u.last_name, u.first_name from request_calc c inner join user u on c.manager_id = u.id order by u.last_name";
        $fetcher = new Fetcher($sql);
                            
        while ($row = $fetcher->Fetch()):
        ?>
        <option value="<?=$row['id'] ?>"<?=($row['id'] == filter_input(INPUT_GET, 'manager') ? " selected='selected'" : "") ?>><?=(mb_strlen($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1).'. ').$row['last_name'] ?></option>
        <?php endwhile; ?>
    </select>
</form>