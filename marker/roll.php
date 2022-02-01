<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'marker'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$supplier_id_valid = '';
$film_brand_id_valid = '';
$thickness_valid = '';
$width_valid = '';
$radius_valid = '';

if(null !== filter_input(INPUT_POST, 'create-submit')) {
    //
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include './_head.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-between">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="<?=APPLICATION ?>/marker/" class="btn btn-link nav-link"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" id="logout-submit" href="logout.php?link=<?= urlencode($_SERVER['REQUEST_URI']) ?>"><i class="fa fa-user-alt" aria-hidden="true"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Новый рулон</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <div class="form-group">
                            <label for="supplier_id">Поставщик</label>
                            <select class="form-control<?=$supplier_id_valid ?>" id="supplier_id" name="supplier_id" required="required">
                                <option value="" hidden="hidden">Выберите поставщика</option>
                                <?php
                                $suppliers = (new Grabber("select id, name from supplier order by name"))->result;
                                foreach($suppliers as $supplier) {
                                    $id = $supplier['id'];
                                    $name = $supplier['name'];
                                    $selected = '';
                                    if(filter_input(INPUT_POST, 'supplier_id') == $supplier['id']) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$name</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Поставщик обязательно</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Позиционируем кнопку "Далее" относительно нижнего края экрана только если она не перекроет другие элементы
        </script>
    </body>
</html>