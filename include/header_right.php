<?php
if(!empty(filter_input(INPUT_COOKIE, USERNAME))):
?>
<ul class="navbar-nav">
    <li class="nav-item dropdown" id="nav-user">
        <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown" style="padding: 0; padding-bottom: 0 !important;"><span class="text-nowrap" id="top_user_name" style="color: #EC3A7A;"><?= filter_input(INPUT_COOKIE, LAST_NAME).' '.filter_input(INPUT_COOKIE, FIRST_NAME) ?></span></a>
        <div id="nav-role" class="text-nowrap"><?= filter_input(INPUT_COOKIE, ROLE_LOCAL) ?></div>
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
        <input class="form-control mr-sm-2<?=$login_username_valid ?>" type="text" id="login_username" name="login_username" placeholder="Логин" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_username']) ? $_POST['login_username'] : '' ?>" required="required" autocomplete="off" />
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

<?php
if(!empty(filter_input(INPUT_COOKIE, USERNAME))):
?>
<div class="flexim-user-chip">
    <span class="flexim-avatar flexim-avatar--m flexim-avatar--initials" title="Пономарёв Сергей"><?=(mb_strlen(filter_input(INPUT_COOKIE, LAST_NAME)) == 0 ? '' : mb_substr(filter_input(INPUT_COOKIE, LAST_NAME), 0, 1)).(mb_strlen(filter_input(INPUT_COOKIE, FIRST_NAME)) == 0 ? '' : mb_substr(filter_input(INPUT_COOKIE, FIRST_NAME), 0, 1)) ?></span>
    <span class="flexim-user-chip__name">
        <a href="#" class="flexim-user-chip__title"><?= filter_input(INPUT_COOKIE, LAST_NAME).' '.filter_input(INPUT_COOKIE, FIRST_NAME) ?></a>
        <span class="flexim-user-chip__role"><?= filter_input(INPUT_COOKIE, ROLE_LOCAL) ?></span>
    </span>
</div>
<?php
endif;
?>