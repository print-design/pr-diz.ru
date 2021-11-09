<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Машина
$machine_id = filter_input(INPUT_GET, 'machine_id');

// Номер ламинатора
const MACHINE_LAMINATOR = 5;

// Страница не предназначена для ламинатора
if($machine_id == MACHINE_LAMINATOR) {
    header("Location: ".APPLICATION."/admin/machine.php".BuildQuery("machine_id", $machine_id));
}

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
        include '../include/header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-start">
                <div class="p-1">
                    <?php
                    include '../include/subheader_admin.php';
                    ?>
                </div>
            </div>
            <?php
            include '../include/subheader_norm.php';
            ?>
            <hr />
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <table class="table table-hover">
                        <tr>
                            <th>Значение</th>
                            <th></th>
                        </tr>
                        <?php
                        $sql = "select id, value from raport where machine_id = $machine_id order by value";
                        $grabber = new Grabber($sql);
                        $raports_of_machine = $grabber->result;
                        foreach ($raports_of_machine as $row):
                        ?>
                        <tr>
                            <td><?= floatval($row['value']) ?></td>
                            <td class="text-right">
                                <form method="post">
                                    <input type="hidden" name="id" value="<?=$row['id'] ?>" />
                                    <input type="hidden" name="scroll" />
                                    <button type="submit" id="raport_delete_submit" name="raport_delete_submit" class="btn btn-link fas fa-trash-alt confirmable"></button>
                                </form>
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
    </body>
</html>