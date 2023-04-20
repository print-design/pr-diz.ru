<div class="queue_item">
    <div class="d-flex justify-content-between">
        <div class="d-flex justify-content-start">
            <div style="padding-top: 10px; padding-bottom: 10px; padding-right: 10px;" data-id="<?=$row['id'] ?>" draggable="true" ondragstart="DragQueue(event);"><img src="../images/icons/double-vertical-dots.svg" draggable="false" /></div>
            <div style="font-weight: bold; font-size: large; line-height: 1.4rem; padding-top: 10px;"><?=$row['calculation'] ?></div>
        </div>
        <form method="post" onsubmit="javascript: if(!confirm('Действительно удалить событие?')) { submit_clicked = false; return false; } else { return true; }">
            <input type="hidden" name="id" value="<?=$row['id'] ?>" />
            <button type="submit" name="delete_event_submit" class="btn btn-link" style="margin-top: 5px;"><i class="fas fa-times" style="color: #EC3A7A"></i></button>
        </form>
    </div>
</div>