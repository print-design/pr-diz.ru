<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$is_user_image = filter_input(INPUT_GET, 'is_user_image');

$previous_id = 0;
$next_id = 0;

// Картинка пользователя
if(!empty($id) && $is_user_image !== null && $is_user_image == 1):
    $sql = "select dui.id as previous_id, dui.image as filename from dialog_user_image dui where dui.id < $id and user_id = (select user_id from dialog_user_image where id = $id) order by dui.id desc limit 1";
$fetcher = new Fetcher($sql);
    
if($row = $fetcher->Fetch()) {
    $previous_id = $row['previous_id'];
}
    
if(!empty($previous_id)):
    $name = "Изображение";
$filename = $row['filename'];
$delete_file_name = "Изображение $previous_id";
$target_text = "&lt;";
?>
<button type="button" class="btn btn-light" style="font-size: x-large;" onclick="javascript: $('#big_image_header').text('<?= htmlentities($name) ?>'); $('#big_image_img').attr('src', '../content/dialog/<?=$filename.'?'. time() ?>'); document.forms.download_image_dialog_form.id.value=<?=$previous_id ?>; document.forms.download_image_dialog_form.is_user_image=1; ShowImageDialogButtons(<?=$previous_id ?>, 1);"><?=$target_text ?></button>
<?php
endif;

$sql = "select dui.id as next_id, dui.image as filename from dialog_user_image dui where dui.id > $id and user_id = (select user_id from dialog_user_image where id = $id) order by dui.id asc limit 1";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $next_id = $row['next_id'];
}

if(!empty($next_id)):
    $name = "Изображение";
$filename = $row['filename'];
$delete_file_name = "Изображение $next_id";
$target_text = "&gt;";
?>
<button type="button" class="btn btn-light" style="font-size: x-large;" onclick="javascript: $('#big_image_header').text('<?= htmlentities($name) ?>'); $('#big_image_img').attr('src', '../content/dialog/<?=$filename.'?'. time() ?>'); document.forms.download_image_dialog_form.id.value=<?=$next_id ?>; document.forms.download_image_dialog_form.is_user_image=1; ShowImageDialogButtons(<?=$next_id ?>, 1);"><?=$target_text ?></button>
<?php
endif;
endif;

// Картинка диалога
if(!empty($id) && $is_user_image !== null && $is_user_image == 0):
    $sql = "select di.id as previous_id, di.image as filename from dialog_image di where di.id < $id and di.dialog_id = (select dialog_id from dialog_image where id = $id) order by di.id desc limit 1";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $previous_id = $row['previous_id'];
}

if(!empty($previous_id)):
    $name = "Изображение";
$filename = $row['filename'];
$delete_file_name = "Изображение $previous_id";
$target_text = "&lt;";
?>
<button type="button" class="btn btn-light" style="font-size: x-large;" onclick="javascript: $('#big_image_header').text('<?= htmlentities($name) ?>'); $('#big_image_img').attr('src', '../content/dialog/<?=$filename.'?'. time() ?>'); document.forms.download_image_dialog_form.id.value=<?=$previous_id ?>; document.forms.download_image_dialog_form.is_user_image=0; ShowImageDialogButtons(<?=$previous_id ?>, 0);"><?=$target_text ?></button>
<?php
endif;

$sql = "select di.id as next_id, di.image as filename from dialog_image di where di.id > $id and di.dialog_id = (select dialog_id from dialog_image where id = $id) order by di.id asc limit 1";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $next_id = $row['next_id'];
}

if(!empty($next_id)):
    $name = "Изображение";
$filename = $row['filename'];
$delete_file_name = "Изображение $next_id";
$target_text = "&gt;";
?>
<button type="button" class="btn btn-light" style="font-size: x-large;" onclick="javascript: $('#big_image_header').text('<?= htmlentities($name) ?>'); $('#big_image_img').attr('src', '../content/dialog/<?=$filename.'?'. time() ?>'); document.forms.download_image_dialog_form.id.value=<?=$next_id ?>; document.forms.download_image_dialog_form.is_user_image=0; ShowImageDialogButtons(<?=$next_id ?>, 0);"><?=$target_text ?></button>
<?php
endif;
endif;
?>