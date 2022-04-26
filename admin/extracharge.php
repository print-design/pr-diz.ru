<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Список типов наценки
$sql = "select id, name from extracharge_type";
$grabber = new Grabber($sql);
$error_message = $grabber->error;

$extracharge_types = array();

foreach ($grabber->result as $row) {
    $extracharge_types[$row['id']] = $row['name'];
}

// Добавление наценки
if(null !== filter_input(INPUT_POST, 'create_extracharge_submit')) {
    $extracharge_type_id = filter_input(INPUT_POST, 'extracharge_type_id');
    $from_weight = filter_input(INPUT_POST, 'from_weight');
    $to_weight = filter_input(INPUT_POST, 'to_weight');
    if(empty($to_weight)) $to_weight = "NULL";
    $value = filter_input(INPUT_POST, 'value');
    
    if(!empty($value)) {
        $sql = "insert into extracharge (extracharge_type_id, from_weight, to_weight, value) values ($extracharge_type_id, $from_weight, $to_weight, $value)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Удаление наценки
if(null !== filter_input(INPUT_POST, 'delete_extracharge_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $sql = "delete from extracharge where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Получение списка объектов
$sql = "select id, extracharge_type_id, from_weight, to_weight, value from extracharge order by from_weight, to_weight";
$grabber = new Grabber($sql);
$error_message = $grabber->error;
$result = $grabber->result;
$extracharges = array();

if(empty($error_message)) {
    foreach ($result as $row) {
        $extracharge = array("id" => $row['id'], "from_weight" => $row['from_weight'], "to_weight" => $row['to_weight'], "value" => $row['value']);
        
        if(array_key_exists($row['extracharge_type_id'], $extracharges)) {
            $extracharges_of_type = $extracharges[$row['extracharge_type_id']];
        }
        else {
            $extracharges_of_type = array();
        }
        
        array_push($extracharges_of_type, $extracharge);
        $extracharges[$row['extracharge_type_id']] = $extracharges_of_type;
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
        include '../include/header_admin.php';
        ?>
        <div class="container-fluid">
            <?php
            include '../include/subheader_norm.php';
            ?>
            <hr />
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <?php
                    $extracharge_type_id = 1;
                    ?>
                    <h2><?=$extracharge_types[$extracharge_type_id] ?></h2>
                    <table class="table table-hover">
                        <tr>
                            <th class="pl-0 font-weight-bold">Масса тиража</th>
                            <th class="pl-0 font-weight-bold">Наценка</th>
                            <th class="text-right"></th>
                        </td>
                        <?php
                        if(array_key_exists($extracharge_type_id, $extracharges)):
                        $extracharges_of_type = $extracharges[$extracharge_type_id];
                        foreach ($extracharges_of_type as $row):
                        ?>
                        <tr>
                            <td class="pl-0"><?= (empty($row['to_weight']) ? 'от ' : '').floatval($row['from_weight']).' кг'.(empty($row['to_weight']) ? '' : ' &ndash; '.floatval($row['to_weight']).' кг') ?></td>
                            <td class="pl-0"><?= floatval($row['value']).'%' ?></td>
                            <td class="text-right">
                                <form method="post">
                                    <input type="hidden" name="scroll" />
                                    <input type="hidden" name="id" value="<?=$row['id'] ?>" />
                                    <button type="submit" name="delete_extracharge_submit" class="btn btn-link fas fa-trash-alt confirmable" />
                                </form>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                        endif;
                        ?>
                    </table>
                    <button type="button" class="btn btn-light mb-4 show-btn"><i class="fas fa-plus"></i>&nbsp;&nbsp;Добавить</button>
                    <form method="post" class="form-inline d-none add-form">
                        <input type="hidden" name="scroll" />
                        <input type="hidden" name="extracharge_type_id" value="<?=$extracharge_type_id ?>" />
                        <input type="text" 
                               name="from_weight" 
                               class="form-control float-only mr-2 w-25" 
                               placeholder="От, кг" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" 
                               onfocusout="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" />
                        &ndash;
                        <input type="text" 
                               name="to_weight" 
                               class="form-control float-only ml-2 w-25" 
                               placeholder="До, кг" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" 
                               onfocusout="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" />
                        <input type="text" 
                               name="value" 
                               class="form-control float-only ml-2 w-25" 
                               placeholder="Наценка, %" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" 
                               onfocusout="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" />
                        <div class="form-group ml-2">
                            <button type="submit" name="create_extracharge_submit" class="btn btn-dark fas fa-plus" />
                        </div>
                        <div class="form-group ml-2">
                            <button type="button" class="btn btn-outline-dark fas fa-undo hide-btn" />
                        </div>
                    </form>
                    <br />
                    <?php
                    $extracharge_type_id = 2;
                    ?>
                    <h2><?=$extracharge_types[$extracharge_type_id] ?></h2>
                    <table class="table table-hover">
                        <tr>
                            <th class="pl-0 font-weight-bold">Масса тиража</th>
                            <th class="pl-0 font-weight-bold">Наценка</th>
                            <th class="text-right"></th>
                        </td>
                        <?php
                        if(array_key_exists($extracharge_type_id, $extracharges)):
                        $extracharges_of_type = $extracharges[$extracharge_type_id];
                        foreach ($extracharges_of_type as $row):
                        ?>
                        <tr>
                            <td class="pl-0"><?= (empty($row['to_weight']) ? 'от ' : '').floatval($row['from_weight']).' кг'.(empty($row['to_weight']) ? '' : ' &ndash; '.floatval($row['to_weight']).' кг') ?></td>
                            <td class="pl-0"><?= floatval($row['value']).'%' ?></td>
                            <td class="text-right">
                                <form method="post">
                                    <input type="hidden" name="scroll" />
                                    <input type="hidden" name="id" value="<?=$row['id'] ?>" />
                                    <button type="submit" name="delete_extracharge_submit" class="btn btn-link fas fa-trash-alt confirmable" />
                                </form>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                        endif;
                        ?>
                    </table>
                    <button type="button" class="btn btn-light mb-4 show-btn"><i class="fas fa-plus"></i>&nbsp;&nbsp;Добавить</button>
                    <form method="post" class="form-inline d-none add-form">
                        <input type="hidden" name="scroll" />
                        <input type="hidden" name="extracharge_type_id" value="<?=$extracharge_type_id ?>" />
                        <input type="text" 
                               name="from_weight" 
                               class="form-control float-only mr-2 w-25" 
                               placeholder="От, кг" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" 
                               onfocusout="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" />
                        &ndash;
                        <input type="text" 
                               name="to_weight" 
                               class="form-control float-only ml-2 w-25" 
                               placeholder="До, кг" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" 
                               onfocusout="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" />
                        <input type="text" 
                               name="value" 
                               class="form-control float-only ml-2 w-25" 
                               placeholder="Наценка, %" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" 
                               onfocusout="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" />
                        <div class="form-group ml-2">
                            <button type="submit" name="create_extracharge_submit" class="btn btn-dark fas fa-plus" />
                        </div>
                        <div class="form-group ml-2">
                            <button type="button" class="btn btn-outline-dark fas fa-undo hide-btn" />
                        </div>
                    </form>
                    <br />
                    <?php
                    $extracharge_type_id = 3;
                    ?>
                    <h2><?=$extracharge_types[$extracharge_type_id] ?></h2>
                    <table class="table table-hover">
                        <tr>
                            <th class="pl-0 font-weight-bold">Масса тиража</th>
                            <th class="pl-0 font-weight-bold">Наценка</th>
                            <th class="text-right"></th>
                        </td>
                        <?php
                        if(array_key_exists($extracharge_type_id, $extracharges)):
                        $extracharges_of_type = $extracharges[$extracharge_type_id];
                        foreach ($extracharges_of_type as $row):
                        ?>
                        <tr>
                            <td class="pl-0"><?= (empty($row['to_weight']) ? 'от ' : '').floatval($row['from_weight']).' кг'.(empty($row['to_weight']) ? '' : ' &ndash; '.floatval($row['to_weight']).' кг') ?></td>
                            <td class="pl-0"><?= floatval($row['value']).'%' ?></td>
                            <td class="text-right">
                                <form method="post">
                                    <input type="hidden" name="scroll" />
                                    <input type="hidden" name="id" value="<?=$row['id'] ?>" />
                                    <button type="submit" name="delete_extracharge_submit" class="btn btn-link fas fa-trash-alt confirmable" />
                                </form>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                        endif;
                        ?>
                    </table>
                    <button type="button" class="btn btn-light mb-4 show-btn"><i class="fas fa-plus"></i>&nbsp;&nbsp;Добавить</button>
                    <form method="post" class="form-inline d-none add-form">
                        <input type="hidden" name="scroll" />
                        <input type="hidden" name="extracharge_type_id" value="<?=$extracharge_type_id ?>" />
                        <input type="text" 
                               name="from_weight" 
                               class="form-control float-only mr-2 w-25" 
                               placeholder="От, кг" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" 
                               onfocusout="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" />
                        &ndash;
                        <input type="text" 
                               name="to_weight" 
                               class="form-control float-only ml-2 w-25" 
                               placeholder="До, кг" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" 
                               onfocusout="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" />
                        <input type="text" 
                               name="value" 
                               class="form-control float-only ml-2 w-25" 
                               placeholder="Наценка, %" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" 
                               onfocusout="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" />
                        <div class="form-group ml-2">
                            <button type="submit" name="create_extracharge_submit" class="btn btn-dark fas fa-plus" />
                        </div>
                        <div class="form-group ml-2">
                            <button type="button" class="btn btn-outline-dark fas fa-undo hide-btn" />
                        </div>
                    </form>
                    <br />
                    <?php
                    $extracharge_type_id = 4;
                    ?>
                    <h2><?=$extracharge_types[$extracharge_type_id] ?></h2>
                    <table class="table table-hover">
                        <tr>
                            <th class="pl-0 font-weight-bold">Масса тиража</th>
                            <th class="pl-0 font-weight-bold">Наценка</th>
                            <th class="text-right"></th>
                        </td>
                        <?php
                        if(array_key_exists($extracharge_type_id, $extracharges)):
                        $extracharges_of_type = $extracharges[$extracharge_type_id];
                        foreach ($extracharges_of_type as $row):
                        ?>
                        <tr>
                            <td class="pl-0"><?= (empty($row['to_weight']) ? 'от ' : '').floatval($row['from_weight']).' кг'.(empty($row['to_weight']) ? '' : ' &ndash; '.floatval($row['to_weight']).' кг') ?></td>
                            <td class="pl-0"><?= floatval($row['value']).'%' ?></td>
                            <td class="text-right">
                                <form method="post">
                                    <input type="hidden" name="scroll" />
                                    <input type="hidden" name="id" value="<?=$row['id'] ?>" />
                                    <button type="submit" name="delete_extracharge_submit" class="btn btn-link fas fa-trash-alt confirmable" />
                                </form>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                        endif;
                        ?>
                    </table>
                    <button type="button" class="btn btn-light mb-4 show-btn"><i class="fas fa-plus"></i>&nbsp;&nbsp;Добавить</button>
                    <form method="post" class="form-inline d-none add-form">
                        <input type="hidden" name="scroll" />
                        <input type="hidden" name="extracharge_type_id" value="<?=$extracharge_type_id ?>" />
                        <input type="text" 
                               name="from_weight" 
                               class="form-control float-only mr-2 w-25" 
                               placeholder="От, кг" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" 
                               onfocusout="javascript: $(this).attr('name', 'from_weight'); $(this).attr('placeholder', 'От, кг');" />
                        &ndash;
                        <input type="text" 
                               name="to_weight" 
                               class="form-control float-only ml-2 w-25" 
                               placeholder="До, кг" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" 
                               onfocusout="javascript: $(this).attr('name', 'to_weight'); $(this).attr('placeholder', 'До, кг');" />
                        <input type="text" 
                               name="value" 
                               class="form-control float-only ml-2 w-25" 
                               placeholder="Наценка, %" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                               onmouseup="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                               onkeyup="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" 
                               onfocusout="javascript: $(this).attr('name', 'value'); $(this).attr('placeholder', 'Наценка, %');" />
                        <div class="form-group ml-2">
                            <button type="submit" name="create_extracharge_submit" class="btn btn-dark fas fa-plus" />
                        </div>
                        <div class="form-group ml-2">
                            <button type="button" class="btn btn-outline-dark fas fa-undo hide-btn" />
                        </div>
                    </form>
                    <br />
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('.show-btn').click(function() {
                $('form.add-form').addClass('d-none');
                $(this).next('form.add-form').removeClass('d-none');
                $(this).next('form.add-form').children('input[name=from_weight]').focus();
                $(this).addClass('d-none');
            });
            
            $('.hide-btn').click(function() {
                $('form.add-form').addClass('d-none');
                $('.show-btn').removeClass('d-none');
            });
        </script>
    </body>
</html>