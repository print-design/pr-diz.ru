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
?>
<div class="row">
    <div class="col-12 col-md-6 col-lg-4">
        <form method="post" action="<?=APPLICATION ?>/car/">
            <div class="form-group">
                <label for="id">Введите ID</label>
                <div class="d-flex">
                    <input type="text"
                           id="id"
                           name="id"
                           class="form-control no-latin"
                           required="required" 
                           value="<?=$find_value ?>" 
                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onmouseup="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" 
                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onkeyup="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" 
                           onfocusout="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" />
                    <button type="submit" class="btn btn-sm btn-primary ml-1<?=$find_btn_class ?>" id="car-submit" name="car-submit">Найти</button>
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