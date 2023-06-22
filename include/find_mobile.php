<?php
$find_value = '';

if(!empty($title)) {
    $find_value = $title;
}
elseif(!empty (filter_input(INPUT_GET, 'cell'))) {
    $find_value = filter_input(INPUT_GET, 'cell');
}
elseif(!empty (filter_input(INPUT_POST, 'id'))) {
    $find_value = filter_input(INPUT_POST, 'id');
}
elseif(!empty (filter_input(INPUT_GET, 'id'))) {
    $find_value = filter_input(INPUT_GET, 'id');
}
?>
<div class="row">
    <div class="col-12 col-md-6 col-lg-4">
        <form method="post" action="index.php">
            <div class="form-group">
                <label for="id">Введите ID</label>
                <div class="input-group">
                    <input type="text" id="id" name="id" class="form-control" required="required" value="<?=$find_value ?>" autocomplete="off" />
                    <div class='input-group-append'>
                        <button type="submit" class="btn btn-primary" style="width: 5rem;" id="find-submit" name="find-submit">Найти</button>
                    </div>
                </div>
                <div style='position: absolute; top: 2.3rem; right: 6.2rem; z-index: 2000;'>
                    <button type='button' id="clear" class="d-none" style='background-color: white; border: 0;'><i class='fas fa-times'></i></button>
                </div>
            </div>
            <p id="id-valid" class="text-danger d-none">ID обязательно</p>
        </form>
    </div>
</div>
<?php
if(!empty($error_message)) {
    echo "<div class='alert alert-danger'>$error_message</div>";
}
?>