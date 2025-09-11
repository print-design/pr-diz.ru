<?php
include '../include/topscripts.php';

$object = filter_input(INPUT_GET, 'object');
$id = filter_input(INPUT_GET, 'id');
$image = filter_input(INPUT_GET, 'image');
$stream_id = filter_input(INPUT_GET, 'stream_id');

// Вариант 1. object + id + image
if(!empty($object) && !empty($id) && !empty($image)):
    $sql = "";
    
    if($object == STREAM && $image == 1) {
        $sql = "select name, image2 as filename from calculation_stream where id = $id";
    }
    elseif($object == STREAM && $image == 2) {
        $sql = "select name, image1 as filename from calculation_stream where id = $id";
    }
    elseif($object == PRINTING && $image == 1) {
        $sql = "select concat(c.name, cq.id) name, cq.image2 as filename from calculation_quantity cq inner join calculation c on cq.calculation_id = c.id where cq.id = $id";
    }
    elseif($object == PRINTING && $image == 2) {
        $sql = "select concat(c.name, cq.id) name, cq.image1 as filename from calculation_quantity cq inner join calculation c on cq.calculation_id = c.id where cq.id = $id";
    }
    
    $fetcher = new Fetcher($sql);
    
if($row = $fetcher->Fetch()):
    $name = $row['name'];
$filename = $row['filename'];

if(!empty($filename)):
$delete_file_name = '';
$target_image = null;
$target_text = "";

if($image == 1 && !empty($row['filename'])) {
    $delete_file_name = $row['name'].", без подписи заказчика";
    $target_image = 2;
    $target_text = "&gt;";
}
elseif($image == 2 && !empty ($row['filename'])) {
    $delete_file_name = $row['name'].", с подписью заказчика";
    $target_image = 1;
    $target_text = "&lt;";
}
?>
<button type="button" class="btn btn-light" style="font-size: x-large;" onclick="javascript: $('#big_image_header').text('<?= htmlentities($name) ?>'); $('#big_image_img').attr('src', '../content/<?=$object ?>/<?=$filename.'?'. time() ?>'); $('#deleted_file_name').text('<?=$delete_file_name ?>'); document.forms.delete_image_form.object.value = '<?=$object ?>'; document.forms.delete_image_form.id.value = <?=$id ?>; document.forms.delete_image_form.image.value = <?=$target_image ?>; document.forms.download_image_form.object.value = '<?=$object ?>'; document.forms.download_image_form.id.value = <?=$id ?>; document.forms.download_image_form.image.value = <?=$target_image ?>; ShowImageButtons('<?=$object ?>', <?=$id ?>, <?=$target_image ?>);"><?=$target_text ?></button>
<?php
endif;
endif;
endif;

// Вариант 2. stream_id + image
if(!empty($stream_id) && !empty($image)):
    $sql = "select name, image1, image2 from calculation_stream where id = $stream_id";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()):
    $name = $row['name'];
$filename = '';

if(!empty($row['image1']) && !empty($row['image2'])):
    if($image == 1) {
        $filename = $row['image2'];
        $target_image = 2;
        $target_text = "&gt;";
    }
    elseif($image == 2) {
        $filename = $row['image1'];
        $target_image = 1;
        $target_text = "&lt;";
    }
?>
<button type="button" class="btn btn-light" style="font-size: x-large;" onclick="javascript: $('#big_image_header').text('<?= htmlentities($name) ?>'); $('#big_image_img').attr('src', '../content/stream/<?=$filename.'?'. time() ?>'); document.forms.download_image_form.object.value = 'stream'; document.forms.download_image_form.id.value = <?=$stream_id ?>; document.forms.download_image_form.image.value = <?=$target_image ?>; ShowImageButtons(<?=$stream_id ?>, <?=$target_image ?>);"><?=$target_text ?></button>
<?php
endif;
endif;
endif;
?>