<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы - удаление пользователя
if(null !== filter_input(INPUT_POST, 'delete_user_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from user where id=$id"))->error;
}

// Обработка отправки формы - смена пароля
$form_valid = true;
$error_message = '';

$user_change_password_old_valid = '';
$user_change_password_old_message = '';
$user_change_password_new_valid = '';
$user_change_password_new_message = '';
$user_change_password_confirm_valid = '';
$user_change_password_confirm_message = '';

$user_change_password_confirm_fio = '';
$graph_key_confirm_fio = '';

if(null !== filter_input(INPUT_POST, 'user_change_password_submit')) {
    if(empty(filter_input(INPUT_POST, "user_change_password_old"))) {
        $user_change_password_old_valid = ISINVALID;
        $user_change_password_old_message = "Текущий пароль обязательно";
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'user_change_password_new'))) {
        $user_change_password_new_valid = ISINVALID;
        $user_change_password_new_message = "Новый пароль обязательно";
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'user_change_password_confirm'))) {
        $user_change_password_confirm_valid = ISINVALID;
        $user_change_password_confirm_message = "Подтверждение пароля обязательно";
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'user_change_password_new') != filter_input(INPUT_POST, 'user_change_password_confirm')) {
        $user_change_password_confirm_valid = ISINVALID;
        $user_change_password_confirm_message = "Пароль и его подтверждение не совпадают";
        $form_valid = false;
    }
    
    // Проверка старого пароля
    $user_change_password_id = filter_input(INPUT_POST, "user_change_password_id");
    $user_change_password_old = filter_input(INPUT_POST, "user_change_password_old");
    $sql = "select count(id) count from user where id=$user_change_password_id and password=password('$user_change_password_old')";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if($row['count'] == 0) {
        $user_change_password_old_valid = ISINVALID;
        $user_change_password_old_message = "Неправильный текущий пароль";
        $form_valid = false;
        
        $sql = "select last_name, first_name from user where id=$user_change_password_id";
        $fetcher = new Fetcher($sql);
        $row = $fetcher->Fetch();
        $user_change_password_confirm_fio = $row['last_name'].' '.$row['first_name'];
    }
    
    if($form_valid) {
        $user_change_password_new = filter_input(INPUT_POST, "user_change_password_new");
        $sql = "update user set password=password('$user_change_password_new') where id=$user_change_password_id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Обработка отправки формы - задание графического ключа
if(null !== filter_input(INPUT_POST, 'graph_key_id')) {
    $graph_key_id = filter_input(INPUT_POST, 'graph_key_id');
    $graph_key = filter_input(INPUT_POST, 'graph_key');
    
    // Проверяем, имеется ли такой графический ключ в базе
    $sql = "select count(id) from user where graph_key = password('$graph_key')";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if($row[0] > 0) {
        $form_valid = false;
    }
    
    if($form_valid) {
        $sql = "update user set graph_key = password('$graph_key') where id = $graph_key_id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Обработка отправки формы - удаление графического ключа
if(null !== filter_input(INPUT_POST, 'graph_key_delete_submit')) {
    $graph_key_delete_id = filter_input(INPUT_POST, 'graph_key_delete_id');
    $sql = "update user set graph_key = '' where id = $graph_key_delete_id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            .figure-point {
                border: solid .2rem blue;
                height: 5.2rem;
                width: 5.2rem;
                position: absolute;
                text-align: center;
            }
            
            .figure-line {
                background-color: green;
                border-radius: .5rem;
            }
            
            #figure-area {
                position: relative;
                height: 16rem;
                width: 16rem;
            }
            
            #fp1 { top: 0rem; left: 0rem; }
            
            #fp2 { top: 0rem; left: 5rem; }
            
            #fp3 { top: 0rem; left: 10rem; }
            
            #fp4 { top: 5rem; left: 0rem; }
            
            #fp5 { top: 5rem; left: 5rem; }
            
            #fp6 { top: 5rem; left: 10rem; }
            
            #fp7 { top: 10rem; left: 0rem; }
            
            #fp8 { top: 10rem; left: 5rem; }
            
            #fp9 { top: 10rem; left: 10rem; }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_admin.php';
        ?>
        <div id="user_change_password" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <input type="hidden" id="user_change_password_id" name="user_change_password_id" value="<?= filter_input(INPUT_POST, 'user_change_password_id') ?>" />
                        <div class="modal-header">
                            <div style="font-size: xx-large;">Изменение пароля</div>
                            <button type="button" class="close user_change_password_dismiss" data-dismiss="modal"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div style="font-size: x-large;">Сотрудник: <span id="user_change_password_fio"><?=$user_change_password_confirm_fio ?></span></div>
                            <div class="form-group">
                                <label for="user_change_password_old">Текущий пароль</label>
                                <input type="password" id="user_change_password_old" name="user_change_password_old" class="form-control<?=$user_change_password_old_valid ?>" required="required" />
                                <div class="invalid-feedback"><?=$user_change_password_old_message ?></div>
                            </div>
                            <div class="form-group">
                                <label for="user_change_password_new">Новый пароль</label>
                                <input type="password" id="user_change_password_new" name="user_change_password_new" class="form-control<?=$user_change_password_new_valid ?>" required="required" />
                                <div class="invalid-feedback"><?=$user_change_password_new_message ?></div>
                            </div>
                            <div class="form-group">
                                <label for="user_change_password_confirm">Новый пароль ещё раз</label>
                                <input type="password" id="user_change_password_confirm" name="user_change_password_confirm" class="form-control<?=$user_change_password_confirm_valid ?>" required="required" />
                                <div class="invalid-feedback"><?=$user_change_password_confirm_message ?></div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-primary" id="user_change_password_submit" name="user_change_password_submit">Изменить пароль</button>
                            <button type="button" class="btn user_change_password_dismiss" data-dismiss="modal">Отменить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="graph_key_modal" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">                    
                    <div class="modal-header">
                        <div style="font-size: xx-large;">Графический ключ</div>
                        <button type="button" class="close graph_key_dismiss" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <div style="font-size: x-large;">Сотрудник: <span id="graph_key_fio"><?=$graph_key_confirm_fio ?></span></div>
                        <?php if(null !== filter_input(INPUT_POST, 'graph_key_id') && !$form_valid): ?>
                        <div class='alert alert-danger'>Этот ключ уже задан другому пользователю</div>
                        <?php endif; ?>
                        <div id="figure-area" class="mt-3">
                            <div class="figure-point" id="fp1"><div class="figure-drag" data-number="1" style="width: 100%; height: 100%;"></div></div>
                            <div class="figure-point" id="fp2"><div class="figure-drag" data-number="2" style="width: 100%; height: 100%;"></div></div>
                            <div class="figure-point" id="fp3"><div class="figure-drag" data-number="3" style="width: 100%; height: 100%;"></div></div>
                            <div class="figure-point" id="fp4"><div class="figure-drag" data-number="4" style="width: 100%; height: 100%;"></div></div>
                            <div class="figure-point" id="fp5"><div class="figure-drag" data-number="5" style="width: 100%; height: 100%;"></div></div>
                            <div class="figure-point" id="fp6"><div class="figure-drag" data-number="6" style="width: 100%; height: 100%;"></div></div>
                            <div class="figure-point" id="fp7"><div class="figure-drag" data-number="7" style="width: 100%; height: 100%;"></div></div>
                            <div class="figure-point" id="fp8"><div class="figure-drag" data-number="8" style="width: 100%; height: 100%;"></div></div>
                            <div class="figure-point" id="fp9"><div class="figure-drag" data-number="9" style="width: 100%; height: 100%;"></div></div>
                        </div>
                        <form method="post" id="graph_key_form">
                            <input type="hidden" id="graph_key_id" name="graph_key_id" value="<?= filter_input(INPUT_POST, 'graph_key_id') ?>" />
                            <input type="hidden" name="graph_key" id="graph_key" />
                        </form>
                    </div>
                    <div class="modal-footer" style="justify-content: flex-start;">
                        <form method="post" id="graph_key_delete_form">
                            <input type="hidden" id="graph_key_delete_id" name="graph_key_delete_id" value="<?= filter_input(INPUT_POST, 'graph_key_delete_id') ?>" />
                            <button type="submit" class="btn btn-primary" id="graph_key_delete_submit" name="graph_key_delete_submit">Удалить ключ</button>
                            <button type="button" class="btn graph_key_dismiss" data-dismiss="modal">Отменить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <?php
            if(null !== filter_input(INPUT_POST, 'user_change_password_submit') && $form_valid && empty($error_message)) {
                echo "<div class='alert alert-success'>Пароль изменен успешно</div>";
            }
            if(null !== filter_input(INPUT_POST, 'graph_key_id') && $form_valid && empty($error_message)) {
                echo "<div class='alert alert-success'>Графический ключ задан успешно</div>";
            }
            if(null !== filter_input(INPUT_POST, 'graph_key_delete_submit') && $form_valid && empty($error_message)) {
                echo "<div class='alert alert-success'>Графический ключ удалён успешно</div>";
            }
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-0">
                    <h1>Сотрудники</h1>
                </div>
                <div class="pt-1">
                    <a href="create.php" title="Добавить пользователя" class="btn btn-dark">
                        <i class="fas fa-plus" style="font-size: 12px;"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить сотрудника
                    </a>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="border-top: 0;">ФИО</th>
                        <th style="border-top: 0;">Должность</th>
                        <th style="border-top: 0;">Логин</th>
                        <th style="border-top: 0;">E-Mail</th>
                        <th style="border-top: 0;">Телефон</th>
                        <th style="width: 80px; border-top: 0;">Пароль</th>
                        <th class="text-center d-none" style="border-top: 0;">Граф.<br />ключ</th>
                        <th style="width: 80px; border-top: 0;">Активный</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select id, role_id, first_name, last_name, username, email, phone, active, graph_key from user order by first_name asc";
                    $fetcher = new Fetcher($sql);
                    $error_message = $fetcher->error;
                    
                    while ($row = $fetcher->Fetch()):
                    ?>
                    <tr>
                        <td><?=$row['first_name'].' '.$row['last_name'] ?></td>
                        <td><?=ROLE_LOCAL_NAMES[$row['role_id']] ?></td>
                        <td><?=$row['username'] ?></td>
                        <td><?=$row['email'] ?></td>
                        <td><?=$row['phone'] ?></td>
                        <td class='text-right'>
                            <button type="button" class="btn btn-link user_change_password_open" data-id="<?=$row['id'] ?>" data-fio="<?=$row['last_name'].' '.$row['first_name'] ?>" data-toggle="modal" data-target="#user_change_password">
                                <image src='../images/icons/edit.svg' />
                            </button>
                        </td>
                        <td class="text-center d-none">
                            <button type="button" class="btn btn-link graph_key_open" data-id="<?=$row['id'] ?>" data-fio="<?=$row['last_name'].' '.$row['first_name'] ?>" data-graph-key="<?=$row['graph_key'] ?>" data-toggle="modal" data-target="#graph_key_modal">
                                <i class="fas fa-th"<?= empty($row['graph_key']) ? '' : " style='font-size: x-large;'" ?>></i>
                            </button>
                        </td>
                        <td class='text-right switch'>
                            <?php if(filter_input(INPUT_COOKIE, USER_ID) != $row['id']): ?>
                            <input type="checkbox" data-id="<?=$row['id'] ?>"<?=$row['active'] ? " checked='checked'" : "" ?> />
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // Заполнение данных о пользователе при открытии формы изменения пароля
            $('.user_change_password_open').click(function(){
                $('#user_change_password_id').val($(this).attr('data-id'));
                $('#user_change_password_fio').text($(this).attr('data-fio'));
                $(document).trigger('keydown'); // чтобы обнулить защиту от двойного нажатия
            });
            
            // Удаление данных о пользователе при закрытии формы изменения пароля
            $('.user_change_password_dismiss').click(function(){
                $('#user_change_password_id').val('');
                $('#user_change_password_fio').text('');
                $('.is-invalid').removeClass('is-invalid');
            });
            
            // Заполнение данных о пользователе при открытии формы графического ключа
            $('.graph_key_open').click(function() {
                $('#graph_key_id').val($(this).attr('data-id'));
                $('#graph_key_delete_id').val($(this).attr('data-id'));
                $('#graph_key_fio').text($(this).attr('data-fio'));
                if($(this).attr('data-graph-key').length === 0) {
                    $('#graph_key_delete_submit').addClass('d-none');
                }
                $(document).trigger('keydown'); // чтобы обнулить защиту от двойного нажатия
            });
            
            // Удаление данных о пользователе при закрытии формы графического ключа
            $('.graph_key_dismiss').click(function() {
                $('#graph_key_id').val('');
                $('#graph_key_delete_id').val('');
                $('#graph_key_fio').text('');
                $('#graph_key_delete_submit').removeClass('d-none');
                $('.is-invalid').removeClass('is-invalid');
            });
            
            // Активирование / деактивирование пользователя
            $(".switch input[type='checkbox']").change(function() {
                $.ajax({ url: "_user.php?id=" + $(this).attr('data-id') + "&active=" + $(this).is(':checked') })
                        .fail(function() {
                            alert('Ошибка при установке / снятии флага активности пользователя');
                });
            });
            
            // Рисование графического ключа
            previous_point = 0;
            
            function AddPoint(sender) {
                let number = sender.attr('data-number');
                
                if(number !== previous_point) {
                    let figure_val = $('input#graph_key').val();
                    $('input#graph_key').val(figure_val + sender.attr('data-number'));
                    
                    
                    let figure_area_top = $('#figure-area').offset().top;
                    let figure_area_left = $('#figure-area').offset().left;
                    
                    let current_width = $('#fp' + number).width();
                    let current_height = $('#fp' + number).height();
                    
                    if(previous_point > 0) {
                        previous_top = $('#fp' + previous_point).offset().top - figure_area_top + (current_height / 2) - (current_height / 8);
                        current_top = $('#fp' + number).offset().top - figure_area_top + (current_height / 2) - (current_height / 8);
                        previous_left = $('#fp' + previous_point).offset().left - figure_area_left + (current_width / 2) - (current_width / 8);
                        current_left = $('#fp' + number).offset().left - figure_area_left + (current_width / 2) - (current_width / 8);
                        
                        line_top = previous_top < current_top ? previous_top : current_top;
                        line_left = previous_left < current_left ? previous_left : current_left;
                        line_width = Math.abs(previous_point - number) > 2 ? current_width / 4 : current_width + (current_width / 4);
                        line_height = Math.abs(previous_point - number) > 2 ? current_height + (current_height / 4) : current_height / 4;
                        
                        $('#figure-area').append($("<div class='figure-line' style='position: absolute; " + 
                                "top: " + line_top + "px;" + 
                                "left: " + line_left + "px;" + 
                                "width: " + line_width + "px;" + 
                                "height: " + line_height + "px;'>"));
                    }
                    
                    previous_point = sender.attr('data-number');
                }
            }
            
            $(document).ready(function(){
                $('.figure-drag').on('mousedown', function() {
                    if(event.which === 1) {
                        AddPoint($(this));
                        
                        $('body').on('mouseup', function() {
                            if(event.which === 1 && $('form#graph_key_form').length) {
                                $('form#graph_key_form').submit();
                            }
                        });
                    }
                });
                
                $('.figure-drag').on('mouseenter', function(event) {
                    if(event.which === 1) {
                        AddPoint($(this));
                    }
                });
                
                $('.figure-drag').on('mouseup', function() {
                    if(event.which === 1) {
                        $('form#graph_key_form').submit();
                    }
                });
                
                $('.graph-key-content').on('mouseup', function() {
                    if(event.which === 1 && $('form#graph_key_form').length) {
                        $('form#graph_key_form').submit();
                    }
                });
                
                current_point = 0;
                
                $('.figure-drag').on('touchmove', function(event) {
                    target = document.elementFromPoint(event.originalEvent.changedTouches[0].clientX, event.originalEvent.changedTouches[0].clientY);
                    if($(target).attr('data-number') !== current_point && $(target).attr('data-number') !== undefined) {
                        AddPoint($(target));
                        current_point = $(target).attr('data-number');
                    }
                });
                
                $('.figure-drag').on('touchend', function() {
                    $('form#graph_key_form').submit();
                });
            });
            
            // Открытие формы изменения пароля, если изменение пароля не было удачным
            <?php if(null !== filter_input(INPUT_POST, 'user_change_password_submit') && !$form_valid): ?>
            $('#user_change_password').modal('show');
            <?php endif; ?>
                
            // Открытие формы задания графического ключа, если задание ключа не было удачным
            <?php if(null !== filter_input(INPUT_POST, 'graph_key_id') && !$form_valid): ?>
            $('#graph_key_modal').modal('show');
            <?php endif; ?>
        </script>
    </body>
</html>