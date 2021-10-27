<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Перенаправление при пустом id
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION.'/techmap/');
}

if(null !== filter_input(INPUT_POST, 'add-date-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $work_date = filter_input(INPUT_POST, 'work_date');
    
    if(!empty($work_date)) {
        $sql = "update techmap set work_date='$work_date' where id=$id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

if(null !== filter_input(INPUT_POST, 'remove-date-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "update techmap set work_date=NULL where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select c.name, t.work_date "
        . "from techmap t "
        . "inner join calculation c on t.calculation_id = c.id "
        . "where t.id = $id";
$row = (new Fetcher($sql))->Fetch();

$name = $row['name'];
$work_date = $row['work_date'];
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
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/techmap/<?= BuildQueryRemove("id") ?>">К списку</a>
            <h1 style="font-size: 32px; font-weight: 600;"><?= htmlentities($name) ?></h1>
            <h2>Дата печати тиража</h2>
            <form method="post" class="form-inline">
                <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                <div class="form-group">
                    <div class="input-group">
                        <input type="date" id="work_date" name="work_date" value="<?=$work_date ?>" class="form-control" />
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-dark" name="add-date-submit">OK</button>
                        </div>
                    </div>
                </div>
                <div class="form-group ml-3">
                    <button type="submit" class="btn btn-outline-dark" name="remove-date-submit"<?= empty($work_date) ? " disabled='disabled'" : "" ?>>В черновики</button>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>