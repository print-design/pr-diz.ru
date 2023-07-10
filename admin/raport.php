<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Машина
$machine_id = filter_input(INPUT_GET, 'machine_id');

// Добавление рапорта
if(null !== filter_input(INPUT_POST, 'raport_create_submit')) {
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    $value = filter_input(INPUT_POST, 'value');
    
    if(!empty($value)) {
        // Проверка, имеется ли такой рапорт у данной машины
        $sql = "select count(id) from raport where machine_id=$machine_id and value=$value";
        $fetcher = new Fetcher($sql);
        
        $count = 0;
        if($row = $fetcher->Fetch()) {
            $count = $row[0];
        }
        
        if($count != 0) {
            $error_message = "Для этой машины уже имеется такой рапорт.";
        }
        
        if(empty($error_message)) {
            $sql = "insert into raport (machine_id, value) values ($machine_id, $value)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
    }
    else {
        $error_message = "Пустое значение";
    }
}

// Удаление рапорта
if(null !== filter_input(INPUT_POST, 'raport_delete_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $sql = "delete from raport where id=$id";
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
                        $sql = "select id, value, active from raport where machine_id = $machine_id order by value";
                        $grabber = new Grabber($sql);
                        $raports_of_machine = $grabber->result;
                        foreach ($raports_of_machine as $row):
                        ?>
                        <tr>
                            <td><?= floatval($row['value']) ?></td>
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
                    <h2>Новый рапорт</h2>
                    <form method="post" class="form-inline">
                        <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                        <input type="hidden" name="scroll" />
                        <input type="text" class="form-control mr-2 float-only" name="value" placeholder="Шаг..." required="required" />
                        <button type="submit" name="raport_create_submit" class="btn btn-outline-dark fas fa-plus"></button>
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
                $.ajax({ url: "_activate.php?type=raport&id=" + $(this).attr('data-id') })
                        .done(function(data) {
                            switch(data) {
                                case "0":
                                    this_el.html("<i class='fas fa-eye-slash'></i>");
                                    this_el.attr("title", "Неактивный");
                                    this_el.tooltip();
                                    break;
                                case "1":
                                    this_el.html("<i class='fas fa-eye'></i>");
                                    this_el.attr("title", "Активный");
                                    this_el.tooltip();
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