<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Ламинатор
$laminator_id = filter_input(INPUT_GET, 'laminator_id');

// Добавление ширины вала
if(null !== filter_input(INPUT_POST, 'roller_create_submit')) {
    $laminator_id = filter_input(INPUT_POST, 'laminator_id');
    $value = filter_input(INPUT_POST, 'value');
    
    if(!empty($value)) {
        // Проверка, имеется ли такой вал
        $sql = "select count(id) from norm_laminator_roller where laminator_id=$laminator_id and value=$value";
        $fetcher = new Fetcher($sql);
        
        $count = 0;
        if($row = $fetcher->Fetch()) {
            $count = $row[0];
        }
        
        if($count != 0) {
            $error_message = "Такой вал уже имеется.";
        }
        
        if(empty($error_message)) {
            $sql = "insert into norm_laminator_roller (laminator_id, value) values($laminator_id, $value)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
    }
    else {
        $error_message = "Пустое значение";
    }
}

// Удаление ширины вала
if(null !== filter_input(INPUT_POST, 'roller_delete_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $sql = "delete from norm_laminator_roller where id=$id";
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
    </head>
    <body>
        <?php
        include '../include/header_admin.php';
        ?>
        <div class="container-fluid">
            <?php
            include '../include/subheader_norm.php';
            
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <table class="table table-hover">
                        <tr>
                            <th style="border-top: 0;">Значение</th>
                            <th style="border-top: 0;" class="text-right">Активный</th>
                        </tr>
                        <?php
                        $sql = "select id, value, active from norm_laminator_roller where laminator_id = $laminator_id order by value";
                        $grabber = new Grabber($sql);
                        $rollers = $grabber->result;
                        foreach ($rollers as $row):
                        ?>
                        <tr>
                            <td><?=$row['value'] ?></td>
                            <td class="text-right">
                                <a class="activate" data-id='<?=$row['id'] ?>' href="javascript: void(0);" title="<?=($row['active'] ? 'Активный' : 'Неактивный') ?>" data-toggle="tooltip" data-placement="right">
                                    <?php if($row['active']): ?>
                                    <i class="fas fa-eye"></i>
                                    <?php else: ?>
                                    <i class="fas fa-eye-slash"></i>
                                    <?php endif; ?>
                                </a>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                        ?>
                    </table>
                    <h2>Новая ширина вала</h2>
                    <form method="post" class="form-inline">
                        <input type="hidden" name="laminator_id" value="<?=$laminator_id ?>" />
                        <input type="hidden" name="scroll" />
                        <input type="text" class="form-control mr-2 int-only" name="value" placeholder="Ширина вала..." required="required" />
                        <button type="submit" name="roller_create_submit" class="btn btn-outline-dark fas fa-plus"></button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('a.activate').click(function() {
                this_el = $(this);
                $.ajax({ url: "../ajax/activate.php?type=laminator_roller&id=" + $(this).attr('data-id') })
                        .done(function(data) {
                            switch(data) {
                                case "0":
                                    this_el.html("<i class='fas fa-eye-slash'></i>");
                                    this_el.attr("title", "Неактивный");
                                    break;
                                case "1":
                                    this_el.html("<i class='fas fa-eye'></i>");
                                    this_el.attr("title", "Активный");
                                    break;
                                default:
                                    alert('Ошибка при активации / деактивации');
                                    this_el.removeAttr('title');
                                    break;
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при активации / деактивации');
                        });
            });
            
            // Всплывающая подсказка
            $(function() {
                $("a.activate").tooltip({
                    position: {
                        my: "left center",
                        at: "right+10 center"
                    }
                });
            });
        </script>
    </body>
</html>