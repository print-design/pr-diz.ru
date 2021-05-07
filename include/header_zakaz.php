<?php
include 'left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            $calculation_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/calculation')) == APPLICATION.'/calculation' ? ' disabled' : '';
            if(IsInRole(array('technologist', 'dev', 'senior'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$calculation_status ?>" href="<?=APPLICATION ?>/calculation/">Расчеты</a>
            </li>
            <?php
            endif;
            ?>
        </ul>
        <?php
        if(file_exists('find.php')) {
            include 'find.php';
        }
        else {
            echo "<div class='ml-auto'></div>";
        }
        
        include 'header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>
<?php
// Создание заказчика
$customer_id = null;

if(null !== filter_input(INPUT_POST, 'create_customer_submit')) {
    if(!empty(filter_input(INPUT_POST, 'customer_name'))) {
        $name = addslashes(filter_input(INPUT_POST, 'customer_name'));
        $person = addslashes(filter_input(INPUT_POST, 'person'));
        $phone = filter_input(INPUT_POST, 'phone');
        $email = filter_input(INPUT_POST, 'email');
        
        $sql = "insert into customer (name, person, phone, email) values ('$name', '$person', '$phone', '$email')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $customer_id = $executer->insert_id;
    }
}

?>
<!-- Форма создания заказчика -->
<div id="new_customer" class="modal fade show">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <i class="fas fa-user"></i>&nbsp;&nbsp;Новый заказчик
                    <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Название компании" required="required" />
                        <div class="invalid-feedback">Название компании обязательно</div>
                    </div>
                    <div class="form-group">
                        <input type="text" id="person" name="person" class="form-control" placeholder="Имя представителя" required="required" />
                        <div class="invalid-feedback">Имя представителя обязательно</div>
                    </div>
                    <div class="form-group">
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="Номер телефона" required="required" />
                        <div class="invalid-feedback">Номер телефона обязательно</div>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" class="form-control" placeholder="E-Mail" required="required" />
                        <div class="invalid-feedback">E-Mail обязательно</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark mt-3" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="create_customer_submit" name="create_customer_submit" class="btn btn-dark mt-3">Complete</button>
                </div>
            </form>
        </div>
    </div>
</div>