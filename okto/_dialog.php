<?php
include '../include/topscripts.php';
?>
<!--div id="dialog_content" style="margin-bottom: 5rem;"-->
<div id="dialog_content">
<?php
$user_id_self = GetUserId();
$user_id_contact = filter_input(INPUT_GET, 'id');

// Крайнее сообщение
$sql = "select timestamp, message from dialog "
        . "where user_id_from = $user_id_self and user_id_to = $user_id_contact "
        . "union "
        . "select timestamp, message from dialog "
        . "where user_id_from = $user_id_contact and user_id_to = $user_id_self "
        . "order by timestamp";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
?>
<p><?=$row['timestamp'] ?></p>
<p><?=$row['message'] ?></p>
<?php endwhile; ?>
</div>