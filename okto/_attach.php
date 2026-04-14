<?php
include '../include/topscripts.php';

$user_id = GetUserId();
$sql = "select image, pdf from dialog_user_image where user_id = $user_id";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
?>
<p><?=$row['image'] ?></p>
<?php endwhile; ?>
<div id="waiting_attach" class="d-none"><img src="../images/loading-cargando.gif" /></div>