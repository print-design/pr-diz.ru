<?php
include '../include/topscripts.php';

$user_id = GetUserId();
$sql = "select id, image, pdf from dialog_user_image where user_id = $user_id";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
?>
<a href="javascript: void(0);" 
   data-toggle="modal" 
   data-target="#big_image" 
   onclick="javascript: ShowDialogUserImage(<?=$row['id'] ?>);">
    <img src="../content/dialog/mini/<?=$row['image'] ?>" style="margin-right: 10px; margin-bottom: 10px;" />
</a>
<?php endwhile; ?>
<div id="waiting_attach" class="d-none"><img src="../images/loading-cargando.gif" /></div>