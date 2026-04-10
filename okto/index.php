<?php
include '../include/topscripts.php';

// Авторизация
if(!LoggedIn()) {
    include '../include/_unauthorized.php';
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
                <div id="contacts1"></div>
                <div id="content" style="width: 100%; position: relative;">
                    <div id="dialog" class="d-none" style="overflow: auto; position: absolute; top: 4px; bottom: 150px; left: 0; right: 10px; padding: 15px; border: solid 1px lightgray; border-radius: 20px;"></div>
                    <div id="input" class="d-none" style="position: fixed; bottom: 10px; left: 472px; right: 10px;">
                        <form method="post" id="message_form" onsubmit="javascript: MessageSubmit(event);">
                            <input type="hidden" name="user_id_from" id="user_id_from" value="<?= GetUserId() ?>" />
                            <input type="hidden" name="user_id_to" id="user_id_to" />
                            <textarea name="message" id="message" class="form-control" required="required"></textarea>
                            <div class="d-flex justify-content-end mt-3">
                                <div><button type='submit' class='btn btn-dark right' style="width: 100px;"><i class="fas fa-chevron-right"></i></button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="../js/waypoints/lib/jquery.waypoints.min.js"></script>
        <script src="../js/waypoints/lib/shortcuts/inview.min.js"></script>
        <script>
            intervalContactsID = 0;
            intervalDialogID = 0;
            scrollTop = 0;
            
            $(document).ready(function() {
                $('#contacts').text('CFR');
                $('#contacts').load('_contacts.php', ChooseContact);
                
                intervalContactsID = setInterval(function() {
                    $('#contacts').load('_contacts.php', ChooseContact);
                }, 2000);
                
                $('#dialog').on('scroll', function() {
                    scrollTop = $(this).scrollTop();
                });
            });
            
            function ChooseContact() {
                $('.btn_contact').click(function() {
                    $('.btn_contact').removeClass('btn-dark');
                    $('.btn_contact').addClass('btn-light');
                    $(this).removeClass('btn-light');
                    $(this).addClass('btn-dark');
                    
                    $('#dialog').removeClass('d-none');
                    $('#dialog').load('_dialog.php?id=' + $(this).attr('data-id'), function() {
                        $('#dialog').scrollTop($('#dialog_content').height());
                        clearInterval(intervalDialogID);
                        intervalDialogID = setInterval(function() {
                            user_id_to = $('#user_id_to').val();
                            $('#dialog').load('_dialog.php?id=' + user_id_to, function() {
                                $('#dialog').scrollTop(scrollTop);
                            });
                            CheckViewed();
                        }, 2000);
                    });
                    
                    $('#input').removeClass('d-none');
                    $('#user_id_to').val($(this).attr('data-id'));
                    $('#message').focus();
                });
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
        </script>
    </body>
</html>