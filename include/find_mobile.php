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
                        <input type="text" id="id" name="id" class="form-control" required="required" value="<?=$find_value ?>" autocomplete="off" />
                        <div class='input-group-append'></div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary ml-1<?=$find_btn_class ?>" id="find-submit" name="find-submit">Найти</button>
                </div>
            </div>
            <p id="id-valid" class="text-danger d-none">Только цифры и русские буквы</p>
        </form>
    </div>
</div>
<div id="codeReaderWrapper" class="modal fade show">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Штрих-код / QR-код
                <button type="button" class="close" data-dismiss="modal" id="close_video"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div id="waiting2" style="position: absolute; top: 20px; left: 20px;">
                    <img src="<?=APPLICATION ?>/images/waiting2.gif" />
                </div>
                <video id="video" class="w-100"></video>
            </div>
        </div>
    </div>
</div>
<?php
if(!empty($error_message)) {
    echo "<div class='alert alert-danger'>$error_message</div>";
}
?>