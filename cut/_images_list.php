<?php
include '../include/topscripts.php';

$stream_id = filter_input(INPUT_GET, 'stream_id');

$sql = "select name, image1, image2, pdf1, pdf2 from calculation_stream where id = $stream_id";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
    if(!empty($row['image1'])):
?>
<div class="d-flex justify-content-start mb-4" id="images_list_item_1" data-image="<?=$row['image1'] ?>" data-pdf="<?=$row['pdf1'] ?>">
    <div class="mr-3">
        <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/stream/<?=$row['image1'].'?'. time() ?>'); $('#big_image_left').addClass('disabled'); $('#big_image_right').removeClass('disabled'); document.forms.download_image_form.image.value = '<?=$row['image1'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf1'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
            <img src="../content/stream/mini/<?=$row['image1'].'?'. time() ?>" />
        </a>
    </div>
    <div>
        <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/stream/<?=$row['image1'].'?'. time() ?>'); $('#big_image_left').addClass('disabled'); $('#big_image_right').removeClass('disabled'); document.forms.download_image_form.image.value = '<?=$row['image1'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf1'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
            <?=$row['name'] ?>
        </a>
    </div>
    <div class="text-nowrap ml-3">
        <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/stream/<?=$row['image1'].'?'. time() ?>'); $('#big_image_left').addClass('disabled'); $('#big_image_right').removeClass('disabled'); document.forms.download_image_form.image.value = '<?=$row['image1'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf1'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
            С подписью
        </a>
    </div>
</div>
<?php
endif;
if(!empty($row['image2'])):
?>
<div class="d-flex justify-content-start mb-4" id="images_list_item_2" data-image="<?=$row['image2'] ?>" data-pdf="<?=$row['pdf2'] ?>">
    <div class="mr-3">
        <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/stream/<?=$row['image2'].'?'. time() ?>'); $('#big_image_right').addClass('disabled'); $('#big_image_left').removeClass('disabled'); document.forms.download_image_form.image.value = '<?=$row['image2'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf2'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
            <img src="../content/stream/mini/<?=$row['image2'].'?'. time() ?>" />
        </a>
    </div>
    <div>
        <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/stream/<?=$row['image2'].'?'. time() ?>'); $('#big_image_right').addClass('disabled'); $('#big_image_left').removeClass('disabled'); document.forms.download_image_form.image.value = '<?=$row['image2'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf2'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
            <?=$row['name'] ?>
        </a>
    </div>
    <div class="text-nowrap ml-3">
        <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/stream/<?=$row['image2'].'?'. time() ?>'); $('#big_image_right').addClass('disabled'); $('#big_image_left').removeClass('disabled'); document.forms.download_image_form.image.value = '<?=$row['image2'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf2'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
            С подписью
        </a>
    </div>
</div>
<?php
endif;
endwhile;
?>