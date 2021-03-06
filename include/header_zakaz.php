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
if(null !== filter_input(INPUT_POST, 'create_customer_submit')) {
    if(!empty(filter_input(INPUT_POST, 'customer_name'))) {
        $customer_name = addslashes(filter_input(INPUT_POST, 'customer_name'));
        $customer_person = addslashes(filter_input(INPUT_POST, 'customer_person'));
        $customer_phone = filter_input(INPUT_POST, 'customer_phone');
        $customer_extension = filter_input(INPUT_POST, 'customer_extension');
        $customer_email = filter_input(INPUT_POST, 'customer_email');
        
        $sql = "insert into customer (name, person, phone, extension, email) values ('$customer_name', '$customer_person', '$customer_phone', '$customer_extension', '$customer_email')";
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
                        <input type="text" 
                               id="customer_name" 
                               name="customer_name" 
                               class="form-control" 
                               placeholder="Название компании" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('id', 'customer_name'); $(this).attr('name', 'customer_name'); $(this).attr('placeholder', 'Название компании');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('id', 'customer_name'); $(this).attr('name', 'customer_name'); $(this).attr('placeholder', 'Название компании');" 
                               onfocusout="javascript: $(this).attr('id', 'customer_name'); $(this).attr('name', 'customer_name'); $(this).attr('placeholder', 'Название компании');" />
                        <div class="invalid-feedback">Название компании обязательно</div>
                    </div>
                    <div class="form-group">
                        <input type="text" 
                               id="customer_person" 
                               name="customer_person" 
                               class="form-control" 
                               placeholder="Имя представителя" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('id', 'customer_person'); $(this).attr('name', 'customer_person'); $(this).attr('placeholder', 'Имя представителя');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('id', 'customer_person'); $(this).attr('name', 'customer_person'); $(this).attr('placeholder', 'Имя представителя');"
                               onfocusout="javascript: $(this).attr('id', 'customer_person'); $(this).attr('name', 'customer_person'); $(this).attr('placeholder', 'Имя представителя');" />
                        <div class="invalid-feedback">Имя представителя обязательно</div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="form-group">
                                <input type="tel" 
                                       id="customer_phone" 
                                       name="customer_phone" 
                                       class="form-control" 
                                       placeholder="Номер телефона" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'customer_phone'); $(this).attr('name', 'customer_phone'); $(this).attr('placeholder', 'Номер телефона');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'customer_phone'); $(this).attr('name', 'customer_phone'); $(this).attr('placeholder', 'Номер телефона');" 
                                       onfocusout="javascript: $(this).attr('id', 'customer_phone'); $(this).attr('name', 'customer_phone'); $(this).attr('placeholder', 'Номер телефона');" />
                                <div class="invalid-feedback">Номер телефона обязательно</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <input type="tel" 
                                       id="customer_extension" 
                                       name="customer_extension" 
                                       class="form-control" 
                                       placeholder="Добавочный" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'customer_extension'); $(this).attr('name', 'customer_extension'); $(this).attr('placeholder', 'Добавочный');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'customer_extension'); $(this).attr('name', 'customer_extension'); $(this).attr('placeholder', 'Добавочный');" 
                                       onfocusout="javascript: $(this).attr('id', 'customer_extension'); $(this).attr('name', 'customer_extension'); $(this).attr('placeholder', 'Добавочный');" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="email" 
                               id="customer_email" 
                               name="customer_email" 
                               class="form-control" 
                               placeholder="E-Mail" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('id', 'customer_email'); $(this).attr('name', 'customer_email'); $(this).attr('placeholder', 'E-Mail');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('id', 'customer_email'); $(this).attr('name', 'customer_email'); $(this).attr('placeholder', 'E-Mail');" 
                               onfocusout="javascript: $(this).attr('id', 'customer_email'); $(this).attr('name', 'customer_email'); $(this).attr('placeholder', 'E-Mail');" />
                        <div class="invalid-feedback">E-Mail обязательно</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark mt-3" data-dismiss="modal">Отмена</button>
                    <button type="submit" id="create_customer_submit" name="create_customer_submit" class="btn btn-dark mt-3">Создать</button>
                </div>
            </form>
        </div>
    </div>
</div>