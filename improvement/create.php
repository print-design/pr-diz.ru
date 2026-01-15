<?php
include '../include/topscripts.php';

// Валидация формы
$form_valid = true;
$error_message = '';

$employee_valid = '';
$title_valid = '';
$body_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'improvement_create_submit')) {
    $employee = '';
    $anonymous = filter_input(INPUT_POST, 'anonymous');
    if($anonymous != 'on') {
        $employee = filter_input(INPUT_POST, 'employee');
        if(filter_input(INPUT_POST, 'anonymous') != 'on' && empty($employee)) {
            $employee_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    $title = filter_input(INPUT_POST, 'title');
    if(empty($title)) {
        $title_valid = ISINVALID;
        $form_valid = false;
    }
    
    $body = filter_input(INPUT_POST, 'body');
    if(empty($body)) {
        $body_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $employee = addslashes($employee);
        $title = addslashes($title);
        $body = addslashes($body);
        
        $sql = "insert into improvement (employee, title, body) values ('$employee', '$title', '$body')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '../include/style_mobile.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <?php if(!empty(filter_input(INPUT_COOKIE, USERNAME))): ?>
            <nav class="navbar navbar-expand-sm justify-content-between">
                <div><a href="<?= APPLICATION ?>/improvement/" title="К списку">К списку</a></div>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" href="<?=APPLICATION ?>/user_mobile.php?link=<?= urlencode($_SERVER['REQUEST_URI']) ?>"><i class="fa fa-cog" aria-hidden="true"></i></a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            if(null !== filter_input(INPUT_POST, 'improvement_create_submit') && empty($error_message)):
            ?>
            <h1>Ваше предложение отправлено</h1>
            <a href="<?= APPLICATION ?>/improvement/create.php" class="btn btn-dark" title="OK">OK</a>
            <?php else: ?>
            <h1>Предложение по улучшению</h1>
            <form method="post">
                <div class="form-group">
                    <label for="employee">Имя, фамилия</label>
                    <input type="text" class="form-control" id="employee" name="employee" required="required" />
                    <div class="invalid-feedback">Имя, фамилия обязательно</div>
                </div>
                <div class="form-check">
                    <label class="form-check-label mb-2" style="line-height: 25px;">
                        <input type="checkbox" class="form-check-input" id="anonymous" name="anonymous" value="on" />Хочу остаться анонимным
                    </label>
                </div>
                <div class="form-group">
                    <label for="title">Коротко</label>
                    <input type="text" class="form-control" name="title" required="required" />
                    <div class="invalid-feedback">Заголовок обязательно</div>
                </div>
                <div class="form-group">
                    <label for="body">Подробно</label>
                    <textarea class="form-control" name="body" rows="4" required="required"></textarea>
                    <div class="invalid-feedback">Текст предложения обязательно</div>
                </div>
                <button type="submit" class="btn btn-dark" id="improvement_create_submit" name="improvement_create_submit">Подать</button>
            </form>
            <?php endif; ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
        <script>
            $("#anonymous").change(function() {
                if($(this).prop('checked') === true) {
                    $("#employee").val('');
                    $("#employee").prop('disabled', true);
                    $("#employee").removeAttr('required');
                }
                else {
                    $("#employee").prop('disabled', false);
                    $("#employee").attr('required', 'required');
                }
            });
        </script>
    </body>
</html>