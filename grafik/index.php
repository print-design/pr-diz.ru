<?php
include 'include/topscripts.php';

const ALLOW_EDIT = 'allow_edit';

if(null !== filter_input(INPUT_POST, 'settings_submit')) {
    $allow_edit = 0;
    
    if(filter_input(INPUT_POST, 'allow_edit')) {
        $allow_edit = 1;
    }
    
    $sql = "delete from settings where name = '".ALLOW_EDIT."'";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "insert into settings (name, bool_value) values ('".ALLOW_EDIT."', $allow_edit)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Получение настроек
$allow_edit = 0;

$sql = "select name, bool_value from settings";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    if($row['name'] == ALLOW_EDIT) {
        $allow_edit = $row['bool_value'];
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include 'include/head.php';
        ?>
    </head>
    <body>
        <?php
        // put your code here
        include 'include/header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Принт-дизайн</h1>
            <h2>График печати</h2>
            <?php if(filter_input(INPUT_COOKIE, USERNAME) == TECHNOLOGIST): ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <table class="table table-bordered mb-2">
                            <tr>
                                <th class="w-50">Разрешается редактирование</th>
                                <td style="vertical-align: middle;">
                                    <?php
                                    $allow_edit_checked = $allow_edit == 1 ? " checked='checked'" : "";
                                    ?>
                                    <input type="checkbox" name="allow_edit" class="form-check"<?=$allow_edit_checked ?> />
                                </td>
                            </tr>
                        </table>
                        <button type="submit" name="settings_submit" class="btn btn-outline-dark">Сохранить</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
        include 'include/footer.php';
        ?>
    </body>
</html>
