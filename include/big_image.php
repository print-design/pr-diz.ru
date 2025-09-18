<div id="big_image" class="modal fade show">
    <div class="modal-dialog">
        <div class="modal-content" style="max-height: 600px; overflow: auto;">
            <div class="modal-header font-weight-bold" style="font-size: x-large;">
                <div id="big_image_header"></div>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body d-flex justify-content-center align-items-baseline align-self-start"><img id="big_image_img" class="img-fluid" alt="Изображение" style="cursor: zoom-in; position: relative; top: 0px; left: 0px;" onclick="javascript: if($(this).hasClass('img-fluid')) { $(this).removeClass('img-fluid'); $(this).css('cursor', 'zoom-out'); } else { $(this).addClass('img-fluid'); $(this).css('cursor', 'zoom-in'); }" /></div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-dark" onclick="javascript: document.forms.download_image_form.submit();"><img src="../images/icons/download.svg" class="mr-2 align-middle" />Скачать</button>
                    <button type="button" class="btn btn-light ml-2 d-none" id="big_image_delete" data-toggle="modal" data-target="#confirm_delete" data-dismiss="modal"><img src="../images/icons/trash3.svg" class="mr-2 align-middle" />Удалить</button>
                </div>
                <div id="big_image_buttons"></div>
            </div>
        </div>
    </div>
</div>
<form id="download_image_form" method="post">
    <input type="hidden" id="object" name="object" />
    <input type="hidden" id="id" name="id" />
    <input type="hidden" id="image" name="image" />
    <input type="hidden" name="download_image_submit" value="1" />
</form>