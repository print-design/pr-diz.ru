<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Список всех машин
$sql = "select id, name from machine";
$grabber = new Grabber($sql);
$error_message = $grabber->error;

$machines = array();

foreach ($grabber->result as $row) {
    $machines[$row['id']] = $row['name'];
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

// Получение списка объектов
$sql = "select id, machine_id, value from raport order by value";
$grabber = new Grabber($sql);
$error_message = $grabber->error;
$result = $grabber->result;
$raports = array();

if(empty($error_message)) {
    foreach ($result as $row) {
        $raport = array("id" => $row['id'], "value" => $row['value']);
        
        if(array_key_exists($row['machine_id'], $raports)) {
            $raports_of_machine = $raports[$row['machine_id']];
        }
        else {
            $raports_of_machine = array();
        }
        
        array_push($raports_of_machine, $raport);
        $raports[$row['machine_id']] = $raports_of_machine;
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
                <div class="col-12 col-md-6 col-lg-3">
                    <?php
                    $machine_id = 1;
                    ?>
                    <div class="pl-4">
                        <h2><?=$machines[$machine_id] ?></h2>
                    </div>
                    <table class="w-100 table table-hover">
                        <?php
                        if(array_key_exists($machine_id, $raports)):
                        $raports_of_machine = $raports[$machine_id];
                        foreach ($raports_of_machine as $row):
                        ?>
                        <tr>
                            <td style="font-size: large;"><?= floatval($row['value']) ?></td>
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
                        endif;
                        ?>
                        <tr>
                            <td colspan="2" class="pt-3">
                                <form method="post" class="form-inline">
                                    <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                                    <input type="hidden" name="scroll" />
                                    <div class="input-group">
                                        <input type="text" class="form-control float-only" name="value" placeholder="Новый рапорт..." required="required" />
                                        <div class="input-group-append">
                                            <button type="submit" name="raport_create_submit" class="btn btn-outline-dark fas fa-plus"></button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <?php
                    $machine_id = 2;
                    ?>
                    <div class="pl-4">
                        <h2><?=$machines[$machine_id] ?></h2>
                    </div>
                    <table class="w-100 table table-hover">
                        <?php
                        if(array_key_exists($machine_id, $raports)):
                        $raports_of_machine = $raports[$machine_id];
                        foreach ($raports_of_machine as $row):
                        ?>
                        <tr>
                            <td style="font-size: large;"><?= floatval($row['value']) ?></td>
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
                        endif;
                        ?>
                        <tr>
                            <td colspan="2">
                                <form method="post" class="form-inline">
                                    <input type="hidden" name="machine_id" value="2" />
                                    <input type="hidden" name="scroll" />
                                    <div class="input-group">
                                        <input type="text" class="form-control float-only" name="value" placeholder="Новый рапорт..." required="required" />
                                        <div class="input-group-append">
                                            <button type="submit" name="raport_create_submit" class="btn btn-outline-dark fas fa-plus"></button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <?php
                    $machine_id = 3;
                    ?>
                    <div class="pl-4">
                        <h2><?=$machines[$machine_id] ?></h2>
                    </div>
                    <table class="w-100 table table-hover">
                        <?php
                        if(array_key_exists($machine_id, $raports)):
                        $raports_of_machine = $raports[$machine_id];
                        foreach ($raports_of_machine as $row):
                        ?>
                        <tr>
                            <td style="font-size: large;"><?= floatval($row['value']) ?></td>
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
                        endif;
                        ?>
                        <tr>
                            <td colspan="2">
                                <form method="post" class="form-inline">
                                    <input type="hidden" name="machine_id" value="3" />
                                    <div class="input-group">
                                        <input type="text" class="form-control float-only" name="value" placeholder="Новый рапорт..." required="required" />
                                        <input type="hidden" name="scroll" />
                                        <div class="input-group-append">
                                            <button type="submit" name="raport_create_submit" class="btn btn-outline-dark fas fa-plus"></button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <?php
                    $machine_id = 4;
                    ?>
                    <div class="pl-4">
                        <h2><?=$machines[$machine_id] ?></h2>
                    </div>
                    <table class="w-100 table table-hover">
                        <?php
                        if(array_key_exists($machine_id, $raports)):
                        $raports_of_machine = $raports[$machine_id];
                        foreach ($raports_of_machine as $row):
                        ?>
                        <tr>
                            <td style="font-size: large;"><?= floatval($row['value']) ?></td>
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
                        endif;
                        ?>
                        <tr>
                            <td colspan="2">
                                <form method="post" class="form-inline">
                                    <input type="hidden" name="machine_id" value="4" />
                                    <div class="input-group">
                                        <input type="text" class="form-control float-only" name="value" placeholder="Новый рапорт..." required="required" />
                                        <input type="hidden" name="scroll" />
                                        <div class="input-group-append">
                                            <button type="submit" name="raport_create_submit" class="btn btn-outline-dark fas fa-plus"></button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>