<?php
include '../include/topscripts.php';
?>
<!--div id="dialog_content" style="margin-bottom: 5rem;"-->
<div id="dialog_content">
<?php
$user_id_self = GetUserId();
$user_id_contact = filter_input(INPUT_GET, 'id');

// Крайнее сообщение
$sql = "select timestamp, message, viewed, 0 as inbox from dialog "
        . "where user_id_from = $user_id_self and user_id_to = $user_id_contact "
        . "union "
        . "select timestamp, message, viewed, 1 as inbox from dialog "
        . "where user_id_from = $user_id_contact and user_id_to = $user_id_self "
        . "order by timestamp";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
    $inoutclass = $row['inbox'] == 1 ? "inbox" : "outbox";
    $viewedclass = $row['viewed'] == 1 ? "viewed" : "unviewed";
    $alignclass = $row['inbox'] == 1 ? "text-left" : "text-right";
?>
    <div class="<?=$inoutclass ?> <?=$viewedclass ?> right <?=$alignclass ?>">
        <p><?=$row['timestamp'] ?></p>
        <p>
            <?=$row['message'] ?>
            <?php 
            if($inoutclass == 'outbox'):
            if($viewedclass == 'viewed'):
            ?>
            <i class="fas fa-check-double ml-2"></i>
            <?php else: ?>
            <i class="fas fa-check ml-2"></i>
            <?php
            endif;
            endif;
            ?>
        </p>
    </div>
<?php endwhile; ?>
</div>