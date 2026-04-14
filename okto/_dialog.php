<?php
include '../include/topscripts.php';
?>
<div id="dialog_content">
<?php
$user_id_self = GetUserId();
$user_id_contact = filter_input(INPUT_GET, 'id');

// Крайнее сообщение
$sql = "select d.id, d.timestamp, d.message, d.viewed, 0 as inbox, (select count(id) from dialog_image where dialog_id = d.id) images_count from dialog d "
        . "where d.user_id_from = $user_id_self and d.user_id_to = $user_id_contact "
        . "union "
        . "select d.id, d.timestamp, d.message, d.viewed, 1 as inbox, (select count(id) from dialog_image where dialog_id = d.id) images_count from dialog d "
        . "where d.user_id_from = $user_id_contact and d.user_id_to = $user_id_self "
        . "order by timestamp";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
    $inoutclass = $row['inbox'] == 1 ? "inbox" : "outbox";
    $viewedclass = $row['viewed'] == 1 ? "viewed" : "unviewed";
    $alignclass = $row['inbox'] == 1 ? "text-left" : "text-right";
?>
    <div class="<?=$inoutclass ?> <?=$viewedclass ?> right <?=$alignclass ?>" data-id="<?=$row['id'] ?>">
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
        <?php
        if($row['images_count'] > 0):
            $sql1 = "select id, image, pdf from dialog_image where dialog_id = ".$row['id'];
            $fetcher1 = new Fetcher($sql1);
            while($row1 = $fetcher1->Fetch()):
        ?>
        <a href="javascript: void(0);" 
           data-toggle="modal" 
           data-target="#big_image" 
           onclick="javascript: ShowDialogUserImage(<?=$row['id'] ?>);">
            <img src="../content/dialog/mini/<?=$row1['image'] ?>" style="margin-right: 10px; margin-bottom: 10px;" />
        </a>
        <?php
        endwhile;
        endif;
        ?>
    </div>
<?php endwhile; ?>
</div>