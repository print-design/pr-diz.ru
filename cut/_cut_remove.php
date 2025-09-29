<div id="cut_remove" class="modal fade show">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="id" value="<?=$id ?>" />
                <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                <div class="modal-header">
                    <p class="font-weight-bold" style="font-size: x-large;">Описание проблемы</p>
                    <button type="button" class="close cut_remove_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="status_comment" id="status_comment" class="form-control" placeholder="Описание проблемы" required="required" autocomplete="off" />
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-start;">
                    <button type="submit" class="btn btn-dark" id="cut_remove_submit" name="cut_remove_submit">Снять заказ</button>
                    <button type="button" class="btn btn-light cut_remove_dismiss" data-dismiss="modal">Отменить</button>
                </div>
            </form>
        </div>
    </div>
</div>