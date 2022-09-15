<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole('technologist', 'manager-senior')) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$name_valid = '';
$user1_name_valid = '';
$user2_name_valid = '';
$role_valid = '';
        
// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'machine_edit_submit')) {
    $name = filter_input(INPUT_POST, 'name');
    if(empty($name)) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $position = filter_input(INPUT_POST, 'position');
    if(empty($position)) {
        $position = 0;
    }
    
    $user1_name = filter_input(INPUT_POST, 'user1_name');
    $user2_name = filter_input(INPUT_POST, 'user2_name');
    $role_id = filter_input(INPUT_POST, 'role_id');
    $has_edition = filter_input(INPUT_POST, 'has_edition') == 'on' ? 1 : 0;
    $has_organization = filter_input(INPUT_POST, 'has_organization') == 'on' ? 1 : 0;
    $has_length = filter_input(INPUT_POST, 'has_length') == 'on' ? 1 : 0;
    $has_status = filter_input(INPUT_POST, 'has_status') == 'on' ? 1 : 0;
    $has_roller = filter_input(INPUT_POST, 'has_roller') == 'on' ? 1 : 0;
    $has_lamination = filter_input(INPUT_POST, 'has_lamination') == 'on' ? 1 : 0;
    $has_coloring = filter_input(INPUT_POST, 'has_coloring') == 'on' ? 1 : 0;
    $coloring = filter_input(INPUT_POST, 'coloring');
    if(empty($coloring)) $coloring = 0;
    $has_manager = filter_input(INPUT_POST, 'has_manager') == 'on' ? 1 : 0;
    $has_comment = filter_input(INPUT_POST, 'has_comment') == 'on' ? 1 : 0;
    $is_cutter = filter_input(INPUT_POST, 'is_cutter') == 'on' ? 1 : 0;
    
    if($form_valid) {
        $id = filter_input(INPUT_POST, 'id');
        $name = addslashes($name);
        $user1_name = addslashes($user1_name);
        $user2_name = addslashes($user2_name);
        $error_message = (new Executer("update machine set name='$name', position=$position, user1_name='$user1_name', user2_name='$user2_name', "
                . "role_id=$role_id, has_edition=$has_edition, has_organization=$has_organization, has_length=$has_length, "
                . "has_status=$has_status, has_roller=$has_roller, has_lamination=$has_lamination, has_coloring=$has_coloring, "
                . "coloring=$coloring, has_manager=$has_manager, has_comment=$has_comment, is_cutter=$is_cutter "
                . "where id=$id"))->error;
                
        if(empty($error_message)) {
            header('Location: '.APPLICATION."/machine/details.php?id=$id");
        }
    }
}

// Если нет параметра id, переход к списку
$id = filter_input(INPUT_GET, 'id');
if($id == null) {
    header('Location: '.APPLICATION.'/machine/');
}
        
// Получение объекта
$row = (new Fetcher("select name, position, user1_name, user2_name, role_id, "
        . "has_edition, has_organization, has_length, has_status, has_roller, has_lamination, has_coloring, coloring, has_manager, has_comment, is_cutter "
        . "from machine where id=$id"))->Fetch();
$name = htmlentities($row['name']);
$position = $row['position'];
$user1_name = htmlentities($row['user1_name']);
$user2_name = htmlentities($row['user2_name']);
$role_id = $row['role_id'];
$has_edition = $row['has_edition'];
$has_organization = $row['has_organization'];
$has_length = $row['has_length'];
$has_status = $row['has_status'];
$has_roller = $row['has_roller'];
$has_lamination = $row['has_lamination'];
$has_coloring = $row['has_coloring'];
$coloring = $row['coloring'];
$has_manager = $row['has_manager'];
$has_comment = $row['has_comment'];
$is_cutter = $row['is_cutter'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            body {
                padding-left: 0;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1>Редактирование машины</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/machine/details.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-undo-alt"></i>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr />
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$id ?>"/>
                        <div class="form-group">
                            <label for="name">Наименование</label>
                            <input type="text" id="name" name="name" class="form-control<?=$name_valid ?>" value="<?=$name ?>" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Наименование обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="position">Позиция</label>
                            <input type="number" min="0" id="position" name="position" class="form-control" value="<?=$position ?>" />
                        </div>
                        <div class="form-group">
                            <label for="user1_name">Пользователь 1</label>
                            <input type="text" id="user1_name" name="user1_name" class="form-control<?=$user1_name_valid ?>" value="<?=$user1_name ?>" autocomplete="off" />
                        </div>
                        <div class="form-group">
                            <label for="user2_name">Пользователь 2</label>
                            <input type="text" id="user2_name" name="user2_name" class="form-control<?=$user2_name_valid ?>" value="<?=$user2_name ?>" autocomplete="off" />
                        </div>
                        <div class="form-group">
                            <label for="role_id">Роль</label>
                            <select id="role_id" name="role_id" class="form-control">
                                <option value="" hidden="hidden">...</option>
                                <?php
                                $sql = "select id, local_name from role";
                                $fetcher = new Fetcher($sql);
                                while($row = $fetcher->Fetch()):
                                    $role_id_class = '';
                                if($role_id == $row['id']) $role_id_class = " selected='selected'";
                                ?>
                                <option value="<?=$row['id'] ?>"<?=$role_id_class ?>><?=$row['local_name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-check">
                            <?php
                            $has_organization_checked = "";
                            if($has_organization) $has_organization_checked = " checked='checked'";
                            ?>
                            <input type="checkbox" class="form-check-input" id="has_organization" name="has_organization"<?=$has_organization_checked ?> />
                            <label class="form-check-label" for="has_organization">Есть организация</label>
                        </div>
                        <div class="form-check">
                            <?php
                            $has_edition_checked = "";
                            if($has_edition) $has_edition_checked = " checked='checked'";
                            ?>
                            <input type="checkbox" class="form-check-input" id="has_edition" name="has_edition"<?=$has_edition_checked ?> />
                            <label class="form-check-label" for="has_edition">Есть тираж</label>
                        </div>
                        <div class="form-check">
                            <?php
                            $has_length_checked = "";
                            if($has_length) $has_length_checked = " checked='checked'";
                            ?>
                            <input type="checkbox" class="form-check-input" id="has_length" name="has_length"<?=$has_length_checked ?> />
                            <label class="form-check-label" for="has_length">Есть длина</label>
                        </div>
                        <div class="form-check">
                            <?php
                            $has_status_checked = "";
                            if($has_status) $has_status_checked = " checked='checked'";
                            ?>
                            <input type="checkbox" class="form-check-input" id="has_status" name="has_status"<?=$has_status_checked ?> />
                            <label class="form-check-label" for="has_status">Есть статус</label>
                        </div>
                        <div class="form-check">
                            <?php
                            $has_roller_checked = "";
                            if($has_roller) $has_roller_checked = " checked='checked'";
                            ?>
                            <input type="checkbox" class="form-check-input" id="has_roller" name="has_roller"<?=$has_roller_checked ?> />
                            <label class="form-check-label" for="has_roller">Есть вал</label>
                        </div>
                        <div class="form-check">
                            <?php
                            $has_lamination_checked = "";
                            if($has_lamination) $has_lamination_checked = " checked='checked'";
                            ?>
                            <input type="checkbox" class="form-check-input" id="has_lamination" name="has_lamination"<?=$has_lamination_checked ?> />
                            <label class="form-check-label" for="has_lamination">Есть ламинация</label>
                        </div>
                        <div class="form-check">
                            <?php
                            $has_coloring_checked = "";
                            if($has_coloring) $has_coloring_checked = " checked='checked'";
                            ?>
                            <input type="checkbox" class="form-check-input" id="has_coloring" name="has_coloring"<?=$has_coloring_checked ?> />
                            <label class="form-check-label" for="has_coloring">Есть красочность</label>
                        </div>
                        <div class="form-group form-inline mt-2">
                            <input type="number" class="form-control mr-2" id="coloring" name="coloring" min="0" value="<?=$coloring ?>" style="width: 70px;" />
                            <label for="coloring">Красочность</label>
                        </div>
                        <div class="form-check">
                            <?php
                            $has_manager_checked = "";
                            if($has_manager) $has_manager_checked = " checked='checked'";
                            ?>
                            <input type="checkbox" class="form-check-input" id="has_manager" name="has_manager"<?=$has_manager_checked ?> />
                            <label class="form-check-label" for="has_manager">Есть менеджер</label>
                        </div>
                        <div class="form-check">
                            <?php
                            $has_comment_checked = "";
                            if($has_comment) $has_comment_checked = " checked='checked'";
                            ?>
                            <input type="checkbox" class="form-check-input" id="has_comment" name="has_comment"<?=$has_comment_checked ?> />
                            <label class="form-check-label" for="has_comment">Есть комментарий</label>
                        </div>
                        <div class="form-check">
                            <?php
                            $is_cutter_checked = "";
                            if($is_cutter) $is_cutter_checked = " checked='checked'";
                            ?>
                            <input type="checkbox" class="form-check-input" id="is_cutter" name="is_cutter"<?=$is_cutter_checked ?> />
                            <label class="form-check-label" for="is_cutter">Это резка?</label>
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-outline-dark" id="machine_edit_submit" name="machine_edit_submit">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>