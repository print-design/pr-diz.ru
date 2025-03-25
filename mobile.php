<?php
include_once 'include/topscripts.php';

if(LoggedIn()) {
    header('Location: '.APPLICATION.'/');
}

// Логин по графическому ключу
if(null !== filter_input(INPUT_POST, 'graph_key_id')) {
    $graph_key_id = filter_input(INPUT_POST, 'graph_key_id');
    $graph_key = filter_input(INPUT_POST, 'graph_key');
    
    $sql = "select id, username, password, last_name, first_name, email, code, role_id from user where graph_key = '$graph_key'";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $user_id = $row['id'];
        $username = $row['username'];
        $password = $row['password'];
        if(strlen($password) > 5) {
            $password5 = substr($password, 1, 5);
        }
        $last_name = $row['last_name'];
        $first_name = $row['first_name'];
        $role = ROLE_NAMES[$row['role_id']];
        $role_local = ROLE_LOCAL_NAMES[$row['role_id']];
        $email = $row['email'];
        
        setcookie(USER_ID, $user_id, time() + 60 * 60 * 24 * 100000, "/");
        setcookie(USERNAME, $username, time() + 60 * 60 * 24 * 100000, "/");
        setcookie(PASSWORD5, $password5, time() + 60 * 60 * 24 * 100000, "/");
        setcookie(LAST_NAME, $last_name, time() + 60 * 60 * 24 * 100000, "/");
        setcookie(FIRST_NAME, $first_name, time() + 60 * 60 * 24 * 100000, "/");
        setcookie(ROLE, $role, time() + 60 * 60 * 24 * 100000, '/');
        setcookie(ROLE_LOCAL, $role_local, time() + 60 * 60 * 24 * 100000, '/');
        setcookie(LOGIN_TIME, (new DateTime())->getTimestamp(), time() + 60 * 60 * 24 * 100000, "/");
        header("Refresh:0");
    }
    
    $error_message = "Неверный графический ключ";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include 'include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include 'include/style_mobile.php';
        ?>
        <script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
        <script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script src="<?=APPLICATION ?>/js/popper.min.js"></script>
        <script src="<?=APPLICATION ?>/js/jquery.maskedinput.js"></script>
        <script src="<?=APPLICATION ?>/js/calculation.js?version=100"></script>
        <style>
            .figure-point {
                border: solid .2rem blue;
                height: 5.2rem;
                width: 5.2rem;
                position: absolute;
                text-align: center;
            }
            
            .figure-line {
                background-color: green;
                border-radius: .5rem;
            }
            
            #figure-area {
                position: relative;
                height: 16rem;
                width: 16rem;
            }
            
            #fp1 { top: 0rem; left: 0rem; }
            
            #fp2 { top: 0rem; left: 5rem; }
            
            #fp3 { top: 0rem; left: 10rem; }
            
            #fp4 { top: 5rem; left: 0rem; }
            
            #fp5 { top: 5rem; left: 5rem; }
            
            #fp6 { top: 5rem; left: 10rem; }
            
            #fp7 { top: 10rem; left: 0rem; }
            
            #fp8 { top: 10rem; left: 5rem; }
            
            #fp9 { top: 10rem; left: 10rem; }
        </style>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APPLICATION ?>/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Войдите, используя графический ключ</h1>
            <div id="figure-area" class="mt-3">
                <div class="figure-point" id="fp1"><div class="figure-drag" data-number="1" style="width: 100%; height: 100%;"></div></div>
                <div class="figure-point" id="fp2"><div class="figure-drag" data-number="2" style="width: 100%; height: 100%;"></div></div>
                <div class="figure-point" id="fp3"><div class="figure-drag" data-number="3" style="width: 100%; height: 100%;"></div></div>
                <div class="figure-point" id="fp4"><div class="figure-drag" data-number="4" style="width: 100%; height: 100%;"></div></div>
                <div class="figure-point" id="fp5"><div class="figure-drag" data-number="5" style="width: 100%; height: 100%;"></div></div>
                <div class="figure-point" id="fp6"><div class="figure-drag" data-number="6" style="width: 100%; height: 100%;"></div></div>
                <div class="figure-point" id="fp7"><div class="figure-drag" data-number="7" style="width: 100%; height: 100%;"></div></div>
                <div class="figure-point" id="fp8"><div class="figure-drag" data-number="8" style="width: 100%; height: 100%;"></div></div>
                <div class="figure-point" id="fp9"><div class="figure-drag" data-number="9" style="width: 100%; height: 100%;"></div></div>
            </div>
            <form method="post" id="graph_key_form">
                <input type="hidden" id="graph_key_id" name="graph_key_id" value="<?= filter_input(INPUT_POST, 'graph_key_id') ?>" />
                <input type="hidden" name="graph_key" id="graph_key" />
            </form>
        </div>
    </body>
    <script>
    previous_point = 0;
            
            
    function AddPoint(sender) {
        let number = sender.attr('data-number');
                
        if(number !== previous_point) {
            let figure_val = $('input#graph_key').val();
            $('input#graph_key').val(figure_val + sender.attr('data-number'));
                    
            let figure_area_top = $('#figure-area').offset().top;
            let figure_area_left = $('#figure-area').offset().left;
                    
            let current_width = $('#fp' + number).width();
            let current_height = $('#fp' + number).height();
                    
            if(previous_point > 0) {
                previous_top = $('#fp' + previous_point).offset().top - figure_area_top + (current_height / 2) - (current_height / 8);
                current_top = $('#fp' + number).offset().top - figure_area_top + (current_height / 2) - (current_height / 8);
                previous_left = $('#fp' + previous_point).offset().left - figure_area_left + (current_width / 2) - (current_width / 8);
                current_left = $('#fp' + number).offset().left - figure_area_left + (current_width / 2) - (current_width / 8);
                        
                line_top = previous_top < current_top ? previous_top : current_top;
                line_left = previous_left < current_left ? previous_left : current_left;
                line_width = Math.abs(previous_point - number) > 2 ? current_width / 4 : current_width + (current_width / 4);
                line_height = Math.abs(previous_point - number) > 2 ? current_height + (current_height / 4) : current_height / 4;
                        
                $('#figure-area').append($("<div class='figure-line' style='position: absolute; " + 
                        "top: " + line_top + "px;" + 
                        "left: " + line_left + "px;" + 
                        "width: " + line_width + "px;" + 
                        "height: " + line_height + "px;'>"));
            }
            
            previous_point = sender.attr('data-number');
        }
    }
            
        $(document).ready(function(){
            $('.figure-drag').on('mousedown', function() {
                if(event.which === 1) {
                    AddPoint($(this));
                }
            });
                
            $('.figure-drag').on('mouseenter', function(event) {
                if(event.which === 1) {
                    AddPoint($(this));
                }
            });
                
            $('.figure-drag').on('mouseup', function() {
                if(event.which === 1) {
                    $('form#graph_key_form').submit();
                }
            });
                
            $('.modal-body').on('mouseup', function() {
                if(event.which === 1 && $('form#graph_key_form').length) {
                    $('form#graph_key_form').submit();
                }
            });
                
            current_point = 0;
            
            $('.figure-drag').on('touchmove', function(event) {
                target = document.elementFromPoint(event.originalEvent.changedTouches[0].clientX, event.originalEvent.changedTouches[0].clientY);
                if($(target).attr('data-number') !== current_point && $(target).attr('data-number') !== undefined) {
                    AddPoint($(target));
                    current_point = $(target).attr('data-number');
                }
            });
                
            $('.figure-drag').on('touchend', function() {
                $('form#graph_key_form').submit();
            });
        });
    </script>
</html>