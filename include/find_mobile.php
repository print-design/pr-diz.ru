<?php
$find_value = '';
$find_btn_class = ' d-none';

if(!empty($title)) {
    $find_value = $title;
    $find_btn_class = '';
}
elseif(!empty (filter_input(INPUT_GET, 'cell'))) {
    $find_value = filter_input(INPUT_GET, 'cell');
    $find_btn_class = '';
}
elseif(!empty (filter_input(INPUT_POST, 'id'))) {
    $find_value = filter_input(INPUT_POST, 'id');
    $find_btn_class = '';
}
elseif(!empty (filter_input(INPUT_GET, 'id'))) {
    $find_value = filter_input(INPUT_GET, 'id');
    $find_btn_class = '';
}

$action = '';

if(IsInRole(array('electrocarist'))) {
    $action = APPLICATION.'/car/';
}
elseif (IsInRole(array('cutter'))) {
    $action = APPLICATION.'/cut/';
}
?>
<div class="row">
    <div class="col-12 col-md-6 col-lg-4">
        <form method="post" action="<?=$action ?>">
            <div class="form-group">
                <label for="id">Введите ID</label>
                <div class="d-flex">
                    <div class="input-group" id="find-group">
                        <input type="text" id="id" name="id" class="form-control no-latin" required="required" value="<?=$find_value ?>" autocomplete="off" />
                        <?php if(!empty($find_value)): ?>
                        <div class='input-group-append'>
                            <button type='button' class='btn' id='find-clear'><i class='fas fa-times'></i></button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary ml-1<?=$find_btn_class ?>" id="find-submit" name="find-submit">Найти</button>
                </div>
            </div>
            <p id="id-valid" class="text-danger d-none">Только цифры и русские буквы</p>
        </form>
    </div>
</div>
<?php
if(!empty($error_message)) {
    echo "<div class='alert alert-danger'>$error_message</div>";
}
?>