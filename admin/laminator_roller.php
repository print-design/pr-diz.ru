<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'top_manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Добавление ширины вала
if(null !== filter_input(INPUT_POST, 'roller_create_submit')) {
    $value = filter_input(INPUT_POST, 'value');
    
    if(!empty($value)) {
        // Проверка, имеется ли такой вал
        $sql = "select count(id) from norm_laminator_roller where value=$value";
        $fetcher = new Fetcher($sql);
        
        $count = 0;
        if($row = $fetcher->Fetch()) {
            $count = $row[0];
        }
        
        if($count != 0) {
            $error_message = "Такой вал уже имеется.";
        }
        
        if(empty($error_message)) {
            $sql = "insert into norm_laminator_roller (value) values($value)";
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
                        $sql = "select id, value from norm_laminator_roller order by value";
                        $grabber = new Grabber($sql);
                        $rollers = $grabber->result;
                        foreach ($rollers as $row):
                        ?>
                        <tr>
                            <td><?=$row['value'] ?></td>
                            <td class="text-right">
                                <form method="post">
                                    <input type="hidden" name="id" value="<?=$row['id'] ?>" />
                                    <input type="hidden" name="scroll" />
                                    <button type="submit" id="roller_delete_submit" name="roller_delete_submit" class="btn btn-link fas fa-trash-alt confirmable"></button>
                                </form>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                        ?>
                    </table>
                    <h2>Новая ширина вала</h2>
                    <form method="post" class="form-inline">
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
    </body>
</html>