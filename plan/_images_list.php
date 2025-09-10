<?php
include '../include/topscripts.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');

$sql = "select name, image1, image2, pdf1, pdf2, 'stream' as object "
        . "from calculation_stream "
        . "where calculation_id = $calculation_id "
        . "union "
        . "select concat(c.name, cq.quantity) name, cq.image1, cq.image2, cq.pdf1, cq.pdf2, 'printing' as object, "
        . "from calculation_quantity cq "
        . "inner join calculation c on cq.calculation_id = c.id "
        . "where cq.calculation_id = $calculation_id";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
    if(!empty($row['image1'])):
?>
    <div class="d-flex justify-content-start mb-4">
        <div class="mr-3">
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$row['object'] ?>/<?=$row['image1'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$row['object'] ?>'; document.forms.download_image_form.image.value = '<?=$row['image1'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf1'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
                <img src="../content/<?=$row['object'] ?>/mini/<?=$row['image1'].'?'. time() ?>" />
            </a>
        </div>
        <div>
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$row['object'] ?>/<?=$row['image1'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$row['object'] ?>'; document.forms.download_image_form.image.value = '<?=$row['image1'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf1'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
                <?=$row['name'] ?>
            </a>
        </div>
        <div class="text-nowrap ml-3">
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$row['object'] ?>/<?=$row['image1'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$row['object'] ?>'; document.forms.download_image_form.image.value = '<?=$row['image1'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf1'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
                С подписью
            </a>
        </div>
    </div>
<?php
endif;
if(!empty($row['image2'])):
?>
    <div class="d-flex justify-content-start mb-4">
        <div class="mr-3">
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$row['object'] ?>/<?=$row['image2'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$row['object'] ?>'; document.forms.download_image_form.image.value = '<?=$row['image2'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf2'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
                <img src="../content/<?=$row['object'] ?>/mini/<?=$row['image2'].'?'. time() ?>" />
            </a>
        </div>
        <div>
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$row['object'] ?>/<?=$row['image2'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$row['object'] ?>'; document.forms.download_image_form.image.value = '<?=$row['image2'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf2'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
                <?=$row['name'] ?>
            </a>
        </div>
        <div class="text-nowrap ml-3">
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$row['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$row['object'] ?>/<?=$row['image2'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$row['object'] ?>'; document.forms.download_image_form.image.value = '<?=$row['image2'] ?>'; document.forms.download_image_form.pdf.value = '<?=$row['pdf2'] ?>'; document.forms.download_image_form.name.value = '<?=$row['name'] ?>';">
                Без подписи
            </a>
        </div>
    </div>
<?php
endif;
endwhile;
?>