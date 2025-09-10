<?php
include '../include/topscripts.php';

$calculation_id = filter_input(INPUT_GET, 'calculation_id');

$image_items = array();

$sql = "select name, image1, image2, pdf1, pdf2, 'stream' as object "
        . "from calculation_stream "
        . "where calculation_id = $calculation_id";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    array_push($image_items, $row);
}

$sql = "select concat(c.name, cq.quantity) name, cq.image1, cq.image2, cq.pdf1, cq.pdf2, 'printing' as object "
        . "from calculation_quantity cq "
        . "inner join calculation c on cq.calculation_id = c.id "
        . "where cq.calculation_id = $calculation_id";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    array_push($image_items, $row);
}

foreach($image_items as $image_item):
    if(!empty($image_item['image1'])):
?>
    <div class="d-flex justify-content-start mb-4">
        <div class="mr-3">
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$image_item['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$image_item['object'] ?>/<?=$image_item['image1'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$image_item['object'] ?>'; document.forms.download_image_form.image.value = '<?=$image_item['image1'] ?>'; document.forms.download_image_form.pdf.value = '<?=$image_item['pdf1'] ?>'; document.forms.download_image_form.name.value = '<?=$image_item['name'] ?>';">
                <img src="../content/<?=$image_item['object'] ?>/mini/<?=$image_item['image1'].'?'. time() ?>" />
            </a>
        </div>
        <div>
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$image_item['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$image_item['object'] ?>/<?=$image_item['image1'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$image_item['object'] ?>'; document.forms.download_image_form.image.value = '<?=$image_item['image1'] ?>'; document.forms.download_image_form.pdf.value = '<?=$image_item['pdf1'] ?>'; document.forms.download_image_form.name.value = '<?=$image_item['name'] ?>';">
                <?=$image_item['name'] ?>
            </a>
        </div>
        <div class="text-nowrap ml-3">
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$image_item['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$image_item['object'] ?>/<?=$image_item['image1'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$image_item['object'] ?>'; document.forms.download_image_form.image.value = '<?=$image_item['image1'] ?>'; document.forms.download_image_form.pdf.value = '<?=$image_item['pdf1'] ?>'; document.forms.download_image_form.name.value = '<?=$image_item['name'] ?>';">
                С подписью
            </a>
        </div>
    </div>
<?php
endif;
if(!empty($image_item['image2'])):
?>
    <div class="d-flex justify-content-start mb-4">
        <div class="mr-3">
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$image_item['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$image_item['object'] ?>/<?=$image_item['image2'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$image_item['object'] ?>'; document.forms.download_image_form.image.value = '<?=$image_item['image2'] ?>'; document.forms.download_image_form.pdf.value = '<?=$image_item['pdf2'] ?>'; document.forms.download_image_form.name.value = '<?=$image_item['name'] ?>';">
                <img src="../content/<?=$image_item['object'] ?>/mini/<?=$image_item['image2'].'?'. time() ?>" />
            </a>
        </div>
        <div>
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$image_item['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$image_item['object'] ?>/<?=$image_item['image2'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$image_item['object'] ?>'; document.forms.download_image_form.image.value = '<?=$image_item['image2'] ?>'; document.forms.download_image_form.pdf.value = '<?=$image_item['pdf2'] ?>'; document.forms.download_image_form.name.value = '<?=$image_item['name'] ?>';">
                <?=$image_item['name'] ?>
            </a>
        </div>
        <div class="text-nowrap ml-3">
            <a href="javascript: void(0);" data-dismiss="modal" data-toggle="modal" data-target="#big_image" onclick="javascript: $('#big_image_header').text('<?=$image_item['name'] ?>'); $('#big_image_img').attr('src', '../content/<?=$image_item['object'] ?>/<?=$image_item['image2'].'?'. time() ?>'); document.forms.download_image_form.object.value = '<?=$image_item['object'] ?>'; document.forms.download_image_form.image.value = '<?=$image_item['image2'] ?>'; document.forms.download_image_form.pdf.value = '<?=$image_item['pdf2'] ?>'; document.forms.download_image_form.name.value = '<?=$image_item['name'] ?>';">
                Без подписи
            </a>
        </div>
    </div>
<?php
endif;
endforeach;
?>