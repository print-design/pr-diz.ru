<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'electrocarist'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body {
                padding-left: 0;
            }
            
            .container-fluid {
                padding-left: 15px;
            }
            
            @media (min-width: 768px) {
                body {
                    padding-left: 60px;
                }
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_mobile.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="get">
                        <div class="form-group">
                            <label for="id">Введите ID</label>
                            <div class="d-flex">
                                <input type="text"
                                       id="id"
                                       name="id"
                                       class="form-control int-only"
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                       onmouseup="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" 
                                       onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                       onkeyup="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" 
                                       onfocusout="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" />
                                <button type="submit" class="btn btn-info ml-1 d-none" id="car-submit">Найти</button>
                            </div>
                            <div class="invalid-feedback">ID обязательно</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            function SetSubmitVisibility(input) {
                val = input.val();
                if(val == '') {
                    $('#car-submit').addClass('d-none');
                }
                else {
                    $('#car-submit').removeClass('d-none');
                }
            }
            
            $('input#id').keypress(function (){ SetSubmitVisibility($(this)) });
            $('input#id').keyup(function (){ SetSubmitVisibility($(this)) });
            $('input#id').change(function (){ SetSubmitVisibility($(this)) });
        </script>
    </body>
</html>