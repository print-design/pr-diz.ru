<div class="container-fluid">
    <nav class="navbar navbar-expand-sm">
        <a class="navbar-brand" href="<?=APPLICATION ?>/">
            <i class="fas fa-home"></i>
        </a>
        <ul class="navbar-nav mr-auto">
            <?php
            $comiflex_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/comiflex/index.php' ? ' disabled' : '';
            $zbs1_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/zbs1/index.php' ? ' disabled' : '';
            $zbs2_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/zbs2/index.php' ? ' disabled' : '';
            $zbs3_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/zbs3/index.php' ? ' disabled' : '';
            $atlas_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/atlas/index.php' ? ' disabled' : '';
            $laminators1_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/laminators1/index.php' ? ' disabled' : '';
            $laminators2_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/laminators2/index.php' ? ' disabled' : '';
            $cutters1_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/cutters1/index.php' ? ' disabled' : '';
            $cutters2_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/cutters2/index.php' ? ' disabled' : '';
            $cutters3_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/cutters3/index.php' ? ' disabled' : '';
            $cutters4_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/cutters4/index.php' ? ' disabled' : '';
            $cutters_atlas_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/cutters_atlas/index.php' ? ' disabled' : '';
            $cutters_soma_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/cutters_soma/index.php' ? ' disabled' : '';
            $machine_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/machine/index.php' ? ' disabled' : '';
            $lamination_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/lamination/index.php' ? ' disabled' : '';
            $user_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/user/index.php' ? ' disabled' : '';
            $personal_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/personal/index.php' ? ' disabled' : '';
            
            $query_string = '';
            $period = array();
            
            $from = filter_input(INPUT_GET, 'from');
            if($from !== null)
                $period['from'] = $from;
            
            $to = filter_input(INPUT_POST, 'to');
            if($to !== null)
                $period['to'] = $to;
            
            if(count($period) > 0)
                $query_string = '?'.http_build_query($period);
            
            if(LoggedIn()):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$comiflex_status ?>" href="<?=APPLICATION ?>/comiflex/<?=$query_string ?>">Comiflex</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs1_status ?>" href="<?=APPLICATION ?>/zbs1/<?=$query_string ?>">ZBS-1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs2_status ?>" href="<?=APPLICATION ?>/zbs2/<?=$query_string ?>">ZBS-2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs3_status ?>" href="<?=APPLICATION ?>/zbs3/<?=$query_string ?>">ZBS-3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$atlas_status ?>" href="<?=APPLICATION ?>/atlas/<?=$query_string ?>">Атлас</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$laminators1_status ?>" href="<?=APPLICATION ?>/laminators1/<?=$query_string ?>">Ламинатор 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$laminators2_status ?>" href="<?=APPLICATION ?>/laminators2/<?=$query_string ?>">Ламинатор 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters1_status ?>" href="<?=APPLICATION ?>/cutters1/<?=$query_string ?>">Резка 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters2_status ?>" href="<?=APPLICATION ?>/cutters2/<?=$query_string ?>">Резка 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters3_status ?>" href="<?=APPLICATION ?>/cutters3/<?=$query_string ?>">Резка 3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters4_status ?>" href="<?=APPLICATION ?>/cutters4/<?=$query_string ?>">Резка 4</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters_atlas_status ?>" href="<?=APPLICATION ?>/cutters_atlas/<?=$query_string ?>">Резка &laquo;Атлас&raquo;</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters_soma_status ?>" href="<?=APPLICATION ?>/cutters_soma/<?=$query_string ?>">Резка &laquo;Сома&raquo;</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$personal_status ?>" href="<?=APPLICATION ?>/personal/">Мои настройки</a>
            </li>
            <?php
            endif;
            if(IsInRole('admin')):
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                    Администратор
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item<?=$user_status ?>" href="<?=APPLICATION ?>/user/">Пользователи</a>
                    <a class="dropdown-item<?=$machine_status ?>" href="<?=APPLICATION ?>/machine/">Машины</a>
                </div>
            </li>
            <?php
            endif;
            ?>
        </ul>
        <?php
        $user_name = filter_input(INPUT_COOKIE, USERNAME);
        if($user_name !== null):
        ?>
        <form class="form-inline" method="post">
            <label>
                <?php
                echo filter_input(INPUT_COOKIE, FIO);
                ?>
                &nbsp;
            </label>
            <button type="submit" class="btn btn-outline-dark" id="logout_submit" name="logout_submit">Выход&nbsp;<i class="fas fa-sign-out-alt"></i></button>
        </form>
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
<hr />