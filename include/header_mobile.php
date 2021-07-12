<div class="d-none d-lg-flex">
    <?php
    include 'left_bar.php';
    ?>
</div>
<div class="container-fluid header">
    <div class="d-flex d-lg-none">
        <?php if(empty(filter_input(INPUT_COOKIE, USERNAME))): ?>
        <form class="my-2 my-lg-0" method="post">
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
        <?php endif; ?>
    </div>
    <nav class="navbar navbar-expand-sm justify-content-end d-none d-lg-flex">
        <?php 
        include 'header_right.php';
        ?>
    </nav>
    <nav class="navbar navbar-expand-sm justify-content-between d-flex d-lg-none pr-0">
        <ul class="navbar-nav">
            <?php if(IsInRole(array('electrocarist'))): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?=APPLICATION ?>/car/">Склад</a>
            </li>
            <?php elseif(IsInRole(array('cutter'))): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?=APPLICATION ?>/cut/">Склад</a>
            </li>
            <?php elseif(!empty(filter_input(INPUT_COOKIE, USERNAME))): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Меню</a>
                <div class="dropdown-menu" style="position: absolute;">
                    <?php if(IsInRole(array('technologist', 'dev'))): ?>
                    <a class="btn btn-link dropdown-item" href="<?=APPLICATION ?>/calculation/">Заказы</a>
                    <?php
                    endif;
                    if(IsInRole(array('technologist', 'storekeeper', 'dev', 'manager'))):
                    ?>
                    <a class="btn btn-link dropdown-item" href="<?=APPLICATION ?>/pallet/">Склад</a>
                    <a class="btn btn-link dropdown-item" href="<?=APPLICATION ?>/grafik/comiflex.php">График</a>
                    <?php
                    endif;
                    if(IsInRole(array('technologist', 'dev'))): ?>
                    <a class="btn btn-link dropdown-item" href="<?=APPLICATION ?>/user/">Админка</a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>
        </ul>
        <?php if(!empty(filter_input(INPUT_COOKIE, USERNAME))): ?>
        <ul class="navbar-nav">
            <li class="nav-item dropdown no-dropdown-arrow-after">
                <a class="nav-link mr-0" href="<?=APPLICATION ?>/user_mobile.php?link=<?=$_SERVER['REQUEST_URI'] ?>"><i class="fa fa-cog" aria-hidden="true""></i></a>
            </li>
        </ul>
        <?php endif; ?>
    </nav>
</div>
<div id="topmost"></div>
<div class="d-flex d-lg-none">
    <?php if(empty(filter_input(INPUT_COOKIE, USERNAME))): ?>
    <div style="height: 10rem;"></div>
    <?php endif; ?>
</div>
