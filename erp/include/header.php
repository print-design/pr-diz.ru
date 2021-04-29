<?php
function Initials() {
    $last_name = filter_input(INPUT_COOKIE, LAST_NAME);
    $first_name = filter_input(INPUT_COOKIE, FIRST_NAME);
    $result = '';
    
    if(mb_strlen($last_name) > 1) {
        $result .= mb_substr($last_name, 0, 1);
    }
    else {
        $result .= $last_name;
    }
    
    if(mb_strlen($first_name) > 1) {
        $result .= mb_substr($first_name, 0, 1);
    }
    else {
        $result .= $first_name;
    }
    
    return $result;
}
?>
<div id="left_bar">
    <a href="<?=APPLICATION ?>/"><img src="<?=APPLICATION ?>/images/logo.svg" /></a>
</div>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm">
        <ul class="navbar-nav">
            <?php
            $pallets_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/pallet')) == APPLICATION.'/pallet' ? ' disabled' : '';
            $rolls_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/roll')) == APPLICATION.'/roll' ? ' disabled' : '';
            $cut_requests_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/cut_request')) == APPLICATION.'/cut_request' ? ' disabled' : '';
            $utilized_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/utilized')) == APPLICATION.'/utilized' ? ' disabled' : '';
            $user_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/user/index.php' ? ' disabled' : '';
            $personal_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/personal/index.php' ? ' disabled' : '';
            if(IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$pallets_status ?>" href="<?=APPLICATION ?>/pallet/">Паллеты</a>
            </li>
            <li class='nav-item'>
                <a class="nav-link<?=$rolls_status ?>" href="<?=APPLICATION ?>/roll/">Рулоны</a>
            </li>
            <?php
            endif;
            if(IsInRole(array('technologist', 'dev', 'cutter'))):
            ?>
            <li class="nav-item d-none">
                <a class="nav-link<?=$cut_requests_status ?>" href="<?=APPLICATION ?>/cut_request/">Заявки</a>
            </li>
            <?php
            endif;
            if(IsInRole(array('technologist', 'dev'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$utilized_status ?> text-nowrap" href="<?=APPLICATION ?>/utilized/">Сработанная пленка</a>
            </li>
            <?php
            endif;
            
            if($_SERVER['HTTP_HOST'] == "pr-diz-test.ru") {
                echo "<li style='font-weight: bold; font-size: large; margin-left: 50px;'>ТЕСТОВАЯ</li>";
            }
            else if($_SERVER['HTTP_HOST'] == "pr-diz-develop.ru") {
                echo "<li style='font-weight: bold; font-size: large; margin-left: 50px;'>РАЗРАБОТКА</li>";
            }
            ?>
        </ul>
        <?php
        if(file_exists('find.php')) {
            include 'find.php';
        }
        else {
            echo "<div class='ml-auto'></div>";
        }
        
        if(IsInRole('cutter')) {
            echo 'Автовыход через&nbsp;';
            echo '<div id="autologout">';
            echo filter_input(INPUT_COOKIE, LOGIN_TIME);
            echo '</div>';
            echo '&nbsp;&nbsp;';
        }
        if(!empty(filter_input(INPUT_COOKIE, USERNAME))):
        ?>
        <ul class="navbar-nav">
            <?php
            if(IsInRole(array('technologist', 'dev'))):
            ?>
            <li class="nav-item">
                <a class="nav-link admin" href="<?=APPLICATION ?>/user/">Админка</a>
            </li>
            <?php
            endif;
            ?>
            <li class="nav-item dropdown" id="nav-user" style="padding-left: 7px;">
                <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown"><?= Initials() ?></a>
                <div class="dropdown-menu" id="user-dropdown">
                    <a href="<?=APPLICATION ?>/personal/" class="btn btn-link dropdown-item"><i class="fas fa-user"></i>&nbsp;Мои настройки</a>
                    <form method="post">
                        <button type="submit" class="btn btn-link dropdown-item" id="logout_submit" name="logout_submit"><i class="fas fa-sign-out-alt"></i>&nbsp;Выход</button>
                    </form>
                </div>
            </li>
        </ul>
        <?php
        else:
        ?>
        <form class="form-inline my-2 my-lg-0" method="post">
            <div class="form-group">
                <input class="form-control mr-sm-2<?=$login_username_valid ?>" type="text" id="login_username" name="login_username" placeholder="Логин" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_username']) ? $_POST['login_username'] : '' ?>" required="required" autocomplete="on" />
                <div class="invalid-feedback">*</div>
            </div>
            <div class="form-group">
                <input class="form-control mr-sm-2<?=$login_password_valid ?>" type="password" id="login_password" name="login_password" placeholder="Пароль" required="required" />
                <div class="invalid-feedback">*</div>
            </div>
            <button type="submit" class="btn btn-outline-dark my-2 my-sm-2" id="login_submit" name="login_submit">Войти&nbsp;<i class="fas fa-sign-in-alt"></i></button>
        </form>
        <?php
        endif;
        ?>
    </nav>
</div>
<div id="topmost"></div>