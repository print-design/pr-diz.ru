<?php
include '../include/topscripts.php';

// Авторизация
if(!LoggedIn()) {
    include '../include/_unauthorized.php';
}

$user_id = GetUserId();

// Выгрузка картинки
if(null !== filter_input(INPUT_POST, 'download_image_dialog_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $is_user_image = filter_input(INPUT_POST, 'is_user_image');
    
    if(!empty($id) && $is_user_image !== null) {
        $sql = "";
        
        if($is_user_image == 1) {
            $sql = "select image, pdf from dialog_user_image where id = $id";
        }
        else {
            $sql = "select image, pdf from dialog_image where id = $id";
        }
        
        if(!empty($sql)) {
            $targetname = "image";
            $fetcher = new Fetcher($sql);
            
            if($row = $fetcher->Fetch()) {
                $targetname = "Изображение $id";
                $targetname = str_replace('.', '', $targetname);
                $targetname = str_replace(',', '', $targetname);
                $targetname = str_replace(';', '', $targetname);
                $targetname = str_replace('"', '', $targetname);
                $targetname = htmlspecialchars($targetname);
            }
            
            $filename = $row['image'];
            $filepath = "../content/dialog/$filename";
            $extension = "";
            
            if(!empty($row['pdf'])) {
                $filename = $row['pdf'];
                $filepath = "../content/dialog/pdf/$filename";
                $extension = "pdf";
            }
            else {
                $substrings = explode('.', $filename);
                if(count($substrings) > 1) {
                    $extension = $substrings[count($substrings) - 1];
                }
            }
            
            $targetname = $targetname.'.'.$extension;
            
            DownloadSendHeaders($targetname);
            readfile($filepath);
            exit();
        }
    }
}
?> 
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            .wrapper {
                display: flex;
                width: 100%;
                align-items: stretch;
            }
            
            #sidebar {
                position: relative;
                min-width: 397px;
                max-width: 397px;
                padding-right: 15px;
                transition: all 0.3s;
            }
            
            #sidebar.active {
                margin-left: -397px;
            }
            
            #sidebar_toggle_button {
                position: absolute;
                top: 0px;
                right: 3px;
            }
            
            .modal-content {
                border-radius: 20px;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div id="big_image_dialog" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header font-weight-bold" style="font-size: x-large;">
                        <div id="big_image_header"></div>
                        <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center align-items-baseline align-self-start" style="height: 500px; width: 500px; overflow: auto;"><img id="big_image_img" class="img-fluid" alt="Изображение" style="cursor: zoom-in; position: absolute; top: 0px; left: 0px;" draggable="false" onmousedown="javascript: MouseDownImage($(this), event);" onmouseup="javascript: MouseUpImage($(this), event);" onmousemove="javascript: MouseMoveImage($(this), event);" /></div>
                    <div class="modal-footer d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-dark" onclick="javascript: document.forms.download_image_dialog_form.submit();"><img src="../images/icons/download.svg" class="mr-2 align-middle" />Скачать</button>
                            <button type="button" class="btn btn-light ml-2 d-none" id="big_image_dialog_delete" onclick="javascript: if(confirm('Действительно удалить?')) { DeleteImageDialog($(this).attr('data-id')); }"><img src="../images/icons/trash3.svg" class="mr-2 align-middle" />Удалить</button>
                        </div>
                        <div id="big_image_dialog_buttons"></div>
                    </div>
                </div>
            </div>
        </div>
        <form id="download_image_dialog_form" method="post">
            <input type="hidden" id="id" name="id" />
            <input type="hidden" id="is_user_image" name="is_user_image" />
            <input type="hidden" name="download_image_dialog_submit" value="1" />
        </form>
        <form id="delete_image_dialog_form" method="post">
            <input type="hidden" id="id" name="id" />
            <input type="hidden" id="is_user_image" name="is_user_image" />
            <input type="hidden" name="delete_image_dialog_submit" value="1" />
        </form>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Octopus</h1>
            <div class="wrapper" style="position: absolute; top: 100px; bottom: 0; left: 0; right: 0; padding-left: 75px;">
                <nav id="sidebar">
                    <div id="contacts" style="overflow: auto; position: absolute; top: 0px; bottom: 0; left: 0; right: 15px;"></div>
                </nav>
                <div id="content" style="width: 100%; position: relative;">
                    <div id="dialog" class="d-none" style="overflow: auto; position: absolute; top: 4px; bottom: 150px; left: 0; right: 10px; padding: 15px; border: solid 1px lightgray; border-radius: 20px;"></div>
                    <div id="input" class="d-none" style="position: fixed; bottom: 10px; left: 472px; right: 10px;">
                        <div id="attach"><div id="waiting_attach" class="d-none"><img src="../images/loading-cargando.gif" /></div></div>
                        <input type="file" accept="image/*,application/pdf" name="dialog_file" id="dialog_file" class="d-none" onchange="UploadAttachImage(300);" />
                        <form method="post" id="message_form" onsubmit="javascript: MessageSubmit(event);">
                            <input type="hidden" name="user_id_from" id="user_id_from" value="<?= $user_id ?>" />
                            <input type="hidden" name="user_id_to" id="user_id_to" />
                            <textarea name="message" id="message" class="form-control" required="required"></textarea>
                            <div class="d-flex justify-content-between mt-3">
                                <div><button type="button" class="btn btn-dark ui_tooltip top" title="Загрузить изображение" tabindex="1" onclick="javascript: $('#dialog_file').click();"><i class="fas fa-image"></i></button></div>
                                <div><button type='submit' class='btn btn-dark' style="width: 100px;" tabindex="0"><i class="fas fa-chevron-right"></i></button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            intervalContactsID = 0;
            intervalDialogID = 0;
            scrollTop = 0;
            user_id_to = 0;
            
            $(document).ready(function() {
                
                $('#contacts').load('_contacts.php');
                
                intervalContactsID = setInterval(function() {
                    if(user_id_to === 0) {
                        $('#contacts').load('_contacts.php');
                    }
                    else {
                        $('#contacts').load('_contacts.php?id=' + user_id_to);
                    }
                }, 2000);
                
                $('#dialog').on('scroll', function() {
                    scrollTop = $(this).scrollTop();
                });
            });
            
            function ChooseContact(btn, id) {
                user_id_to = id;
                $('.btn_contact').removeClass('btn-dark');
                $('.btn_contact').addClass('btn-light');
                btn.removeClass('btn-light');
                btn.addClass('btn-dark');
                
                $('#attach').load('_attach.php', function() {
                    $('#dialog').css('bottom', ($('#input').height() + 20) + 'px');
                });
                $('#dialog').removeClass('d-none');
                $('#dialog').load('_dialog.php?id=' + id, function() {
                    $('#dialog').scrollTop($('#dialog_content').height());
                    clearInterval(intervalDialogID);
                    intervalDialogID = setInterval(function() {
                        $('#dialog').load('_dialog.php?id=' + user_id_to, function() {
                            $('#dialog').scrollTop(scrollTop);
                        });
                        CheckViewed();
                    }, 2000);
                });
                
                $('#input').removeClass('d-none');
                $('#user_id_to').val(id);
                $('#message').focus();
            }
            
            function MessageSubmit(event) {
                event.preventDefault();
                var formData = $('form#message_form').serialize();
                
                $.ajax({
                    url: '_message_submit.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if(response.error === '') {
                            $('#dialog').load('_dialog.php?id=' + response.id, function() {
                                $('#dialog').scrollTop($('#dialog_content').height());
                            });
                            
                            $('#attach').load('_attach.php', function() {
                                $('#dialog').css('bottom', ($('#input').height() + 20) + 'px');
                            });

                            $('#message').val('');
                            $('#message').focus();
                        }
                        else {
                            alert(response.error);
                        }
                    },
                    error: function() {
                        alert('Ошибка при отправки сообщения');
                    }
                });
            }
            
            function CheckViewed() {
                $('.inbox.unviewed').each(function() {
                    if($(this).position().top < $('#dialog').height()) {
                        $.ajax({ url: '_set_viewed.php?id=' + $(this).attr('data-id'),
                            success: function(response) {
                                if(response != 1) {
                                    alert(response);
                                }
                            },
                            error: function() {
                                alert('Ошибка при установке сообщения прочитанным');
                            }
                        });
                    }
                });
            }
            
            function UploadAttachImage(resolution) {
                $('#waiting_attach').removeClass('d-none');
                
                var formData = new FormData();
                formData.set('user_id', <?=$user_id ?>);
                formData.set('resolution', resolution);
                formData.set('file', $('#dialog_file')[0].files[0]);
                
                $.ajax({
                    url: "_upload_attach.php",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    processData: false, // Prevent jQuery from processing data
                    contentType: false, // Prevent jQuery from setting
                    success: function(response) {
                        if(response.error.length > 0) {
                            alert(response.error);
                            $('#waiting_attach').addClass('d-none');
                        }
                        else {
                            if(response.filename.length > 0) {
                                $('#waiting_attach').addClass('d-none');
                                $('#attach').load('_attach.php', function() {
                                    $('#dialog').css('bottom', ($('#input').height() + 20) + 'px');
                                });
                            }
                            
                            if(response.info.length > 0) {
                                alert(response.info);
                                $('#waiting_attach').addClass('d-none');
                            }
                        }
                    },
                    error: function() {
                        if(resolution > 250) {
                            UploadAttachImage(250);
                        }
                        else if(resolution > 200) {
                            UploadAttachImage(200);
                        }
                        else if(resolution > 150) {
                            UploadAttachImage(150);
                        }
                        else if(resolution > 100) {
                            UploadAttachImage(100);
                        }
                        else if(resolution > 50) {
                            UploadAttachImage(50);
                        }
                        else if(resolution > 10) {
                            UploadAttachImage(10);
                        }
                        else {
                            alert('Ошибка при загрузке файла');
                            $('#waiting_attach').addClass('d-none');
                        }
                    }
                });
            }
            
            function ShowImageDialog(id, is_user_image) {
                if(is_user_image === 1) {
                    $('#big_image_dialog_delete').removeClass('d-none');
                    $('#big_image_dialog_delete').attr('data-id', id);
                }
                else {
                    $('#big_image_dialog_delete').addClass('d-none');
                }
                        
                $.ajax({ url: "_big_image_show_dialog.php?id=" + id + "&is_user_image=" + is_user_image, 
                    dataType: "json", 
                    success: function(response) {
                        $('#big_image_header').text(response.name);
                        $('#big_image_img').attr('src', '../content/dialog/' + response.filename + '?' + Date.now());
                        document.forms.download_image_dialog_form.id.value = response.id;
                        document.forms.download_image_dialog_form.is_user_image.value = response.is_user_image;
                        ShowImageDialogButtons(id, is_user_image);
                    }, 
                    error: function() {
                        alert('Ошибка при открытии изображения');
                    }
                });
            }
            
            function ShowImageDialogButtons(id, is_user_image) {
                $.ajax({ url: "_big_image_buttons_dialog.php?id=" + id + "&is_user_image=" + is_user_image, 
                    success: function(response) {
                        $('#big_image_dialog_buttons').html(response);
                    },
                    error: function() {
                        alert('Ошибка при создании кнопок всплывающего окна');
                    }
                });
            }
            
            function DeleteImageDialog(id) {
                $.ajax({ url: "_delete_image_dialog_user.php?id=" + id, 
                    dataType: "json",
                    success: function(response) {
                        if(response.error != '') {
                            alert('Ошибка при удалении картинки');
                        }
                        else {
                            $('#big_image_dialog').modal('hide');
                            $('#attach').load('_attach.php', function() {
                                $('#dialog').css('bottom', ($('#input').height() + 20) + 'px');
                            });
                        }
                    },
                    error: function() {
                        alert('Ощибка при удалении картинки');
                    }
                });
            }
        </script>
    </body>
</html>