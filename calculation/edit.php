<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$customer_id_valid = '';
$name_valid = '';
$work_type_valid = '';
$brand_name_valid = '';
$thickness_valid = '';
$width_valid = '';
$weight_valid = '';
$diameter_valid = '';

// Сохранение в базу расчёта
if(null !== filter_input(INPUT_POST, 'create_calculation_submit')) {
    if(empty(filter_input(INPUT_POST, "customer_id"))) {
        $customer_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, "name"))) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'work_type_id'))) {
        $work_type_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'brand_name'))) {
        $brand_name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'thickness'))) {
        $thickness_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'width'))) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'weight'))) {
        $weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'diameter'))) {
        $diameter_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $id = filter_input(INPUT_POST, 'id');
        $customer_id = filter_input(INPUT_POST, 'customer_id');
        $name = addslashes(filter_input(INPUT_POST, 'name'));
        $work_type_id = filter_input(INPUT_POST, 'work_type_id');
        $brand_name = addslashes(filter_input(INPUT_POST, 'brand_name'));
        $thickness = filter_input(INPUT_POST, 'thickness');
        $width = filter_input(INPUT_POST, 'width');
        $weight = filter_input(INPUT_POST, 'weight');
        $diameter = filter_input(INPUT_POST, 'diameter');
        
        $manager_id = GetUserId();
        
        $sql = "update calculation set customer_id=$customer_id, name='$name', work_type_id=$work_type_id, weight=$weight, manager_id=$manager_id, status_id=$status_id where id=$id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
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
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="backlink">
                <a href="<?=APPLICATION ?>/calculation/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
    </body>
</html>