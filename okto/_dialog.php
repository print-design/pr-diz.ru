<?php
include '../include/topscripts.php';
?>
<div id="dialog_content">
<?php
$user_id_self = GetUserId();
$user_id_contact = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Крайнее сообщение
$sql = "select d.id, d.timestamp, d.message, d.viewed, 0 as inbox, (select count(id) from dialog_image where dialog_id = d.id) images_count from dialog d "
        . "where d.user_id_from = ? and d.user_id_to = ? "
        . "union "
        . "select d.id, d.timestamp, d.message, d.viewed, 1 as inbox, (select count(id) from dialog_image where dialog_id = d.id) images_count from dialog d "
        . "where d.user_id_from = ? and d.user_id_to = ? "
        . "order by timestamp";
$fetcher = new Fetcher($sql, [$user_id_self, $user_id_contact, $user_id_contact, $user_id_self]);
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
            $sql1 = "select id, image, pdf from dialog_image where dialog_id = ?";
            $fetcher1 = new Fetcher($sql1, [$row['id']]);
            while($row1 = $fetcher1->Fetch()):
        ?>
        <a href="javascript: void(0);" 
           data-toggle="modal" 
           data-target="#big_image_dialog" 
           onclick="javascript: ShowImageDialog(<?=$row1['id'] ?>, 0);">
            <img src="../content/dialog/mini/<?=$row1['image'] ?>" style="margin-right: 10px; margin-bottom: 10px;" />
        </a>
        <?php
        endwhile;
        endif;
        ?>
    </div>
<?php endwhile; ?>
</div>