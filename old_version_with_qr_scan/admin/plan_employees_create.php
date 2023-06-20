<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR], ROLE_NAMES[ROLE_SCHEDULER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', 'is-invalid');
$form_valid = true;
$error_message = '';

$role_id_valid = '';
$first_name_valid = '';
$last_name_valid = '';
$phone_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'employee_create_submit')) {
    $role_id = filter_input(INPUT_POST, 'role_id');
    if(empty($role_id)) {
        $role_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $first_name = filter_input(INPUT_POST, 'first_name');
    if(empty($first_name)) {
        $first_name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $last_name = filter_input(INPUT_POST, 'last_name');
    if(empty($last_name)) {
        $last_name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $phone = filter_input(INPUT_POST, 'phone');
    if(empty($phone)) {
        $phone_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $first_name = addslashes($first_name);
        $last_name = addslashes($last_name);
        $phone = addslashes($phone);
        
        $sql = "insert into plan_employee (first_name, last_name, role_id, phone) values ('$first_name', '$last_name', $role_id, '$phone')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header('Location: plan_employees.php');
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_admin.php';
        ?>
        <div class="container-fluid">
            <?php
            include '../include/subheader_plan.php';
            
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="plan_employees.php">Назад</a>
            <div style="width: 387px;">
                <h1 style="font-size: 24px; font-weight: 600;">Добавление сотрудника</h1>
                <form method="post">
                    <div class="form-group">
                        <select id="role_id" name="role_id" class="form-control" required="required">
                            <option value="" hidden="hidden">ВЫБЕРИТЕ ДОЛЖНОСТЬ</option>
                            <?php
                            foreach(PLAN_ROLES as $role):
                                $selected = '';
                                if(filter_input(INPUT_POST, 'role_id') == $role) $selected = " selected='selected'";
                            ?>
                            <option value="<?=$role ?>"<?=$selected ?>><?=PLAN_ROLE_NAMES[$role] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="first_name">Имя</label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   class="form-control<?=$first_name_valid ?>" 
                                   value="<?= filter_input(INPUT_POST, 'first_name') ?>" 
                                   required="required" 
                                   autocomplete="off" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                   onfocus="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                   onmouseup="javascript: $(this).attr('id', 'first_name'); $(this).attr('name', 'first_name');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'first_name'); $(this).attr('name', 'first_name');" 
                                   onfocusout="javascript: $(this).attr('id', 'first_name'); $(this).attr('name', 'first_name');" />
                            <div class="invalid-feedback">Имя обязательно</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   class="form-control<?=$last_name_valid ?>" 
                                   value="<?= filter_input(INPUT_POST, 'last_name') ?>" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                   onfocus="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                   onmouseup="javascript: $(this).attr('id', 'last_name'); $(this).attr('name', 'last_name');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'last_name'); $(this).attr('name', 'last_name');" 
                                   onfocusout="javascript: $(this).attr('id', 'last_name'); $(this).attr('name', 'last_name');" />
                            <div class="invalid-feedback">Фамилия обязательно</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="phone">Телефон</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control<?=$phone_valid ?>" 
                                   value="<?= filter_input(INPUT_POST, 'phone') ?>" 
                                   required="required" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                   onfocus="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                   onmouseup="javascript: $(this).attr('id', 'phone'); $(this).attr('name', 'phone');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                                   onkeyup="javascript: $(this).attr('id', 'phone'); $(this).attr('name', 'phone');" 
                                   onfocusout="javascript: $(this).attr('id', 'phone'); $(this).attr('name', 'phone');" />
                            <div class="invalid-feedback">Телефон обязательно</div>
                        </div>
                    </div>
                    <div class="form-group" style="padding-top: 24px;">
                        <button type="submit" class="btn btn-dark" id="employee_create_submit" name="employee_create_submit">Создать</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>