<?php
/*if(IsInRole('cutter1')) {
    echo 'Автовыход через&nbsp;';
    echo '<div id="autologout">';
    echo filter_input(INPUT_COOKIE, LOGIN_TIME);
    echo '</div>';
    echo '&nbsp;&nbsp;';
}*/
if(!empty(filter_input(INPUT_COOKIE, USERNAME))):
?>
<ul class="navbar-nav">
    <?php
    if(IsInRole(array('technologist', 'dev'))):
    ?>
    <li class="nav-item">
        <a class="nav-link" href="<?=APPLICATION ?>/user/">Админка</a>
    </li>
    <?php
    endif;
    ?>
    <li class="nav-item dropdown" id="nav-user" style="padding-left: 10px;">
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
    <div class="form-group">
        <button type="submit" class="btn btn-outline-dark my-2 my-sm-2" id="login_submit" name="login_submit">Войти&nbsp;<i class="fas fa-sign-in-alt"></i></button>
    </div>
</form>
<?php
endif;
?>