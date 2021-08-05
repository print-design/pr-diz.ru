<div class="container-fluid">
    <nav class="navbar navbar-expand-sm">
        <a class="navbar-brand" href="<?=APPLICATION ?>/">
            <i class="fas fa-home"></i>
        </a>
        <ul class="navbar-nav mr-auto">
            <?php
            $comiflex_status = filter_input(INPUT_GET, 'id') == 1 ? ' disabled' : '';
            $zbs1_status = filter_input(INPUT_GET, 'id') == 2 ? ' disabled' : '';
            $zbs2_status = filter_input(INPUT_GET, 'id') == 3 ? ' disabled' : '';
            $zbs3_status = filter_input(INPUT_GET, 'id') == 4 ? ' disabled' : '';
            $atlas_status = filter_input(INPUT_GET, 'id') == 5 ? ' disabled' : '';
            $laminators1_status = filter_input(INPUT_GET, 'id') == 6 ? ' disabled' : '';
            $laminators2_status = filter_input(INPUT_GET, 'id') == 13 ? ' disabled' : '';
            $cutters1_status = filter_input(INPUT_GET, 'id') == 7 ? ' disabled' : '';
            $cutters2_status = filter_input(INPUT_GET, 'id') == 9 ? ' disabled' : '';
            $cutters3_status = filter_input(INPUT_GET, 'id') == 10 ? ' disabled' : '';
            $cutters4_status = filter_input(INPUT_GET, 'id') == 14 ? ' disabled' : '';
            $cutters_atlas_status = filter_input(INPUT_GET, 'id') == 11 ? ' disabled' : '';
            $cutters_soma_status = filter_input(INPUT_GET, 'id') == 12 ? ' disabled' : '';
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
                <a class="nav-link<?=$comiflex_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 1) ?><?=$query_string ?>">Comiflex</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs1_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 2) ?><?=$query_string ?>">ZBS-1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs2_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 3) ?><?=$query_string ?>">ZBS-2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs3_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 4) ?><?=$query_string ?>">ZBS-3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$atlas_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 5) ?><?=$query_string ?>">Атлас</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$laminators1_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 6) ?><?=$query_string ?>">Ламинатор 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$laminators2_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 13) ?><?=$query_string ?>">Ламинатор 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters1_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 7) ?><?=$query_string ?>">Резка 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters2_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 9) ?><?=$query_string ?>">Резка 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters3_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 10) ?><?=$query_string ?>">Резка 3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters4_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 14) ?><?=$query_string ?>">Резка 4</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters_atlas_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 11) ?><?=$query_string ?>">Резка &laquo;Атлас&raquo;</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters_soma_status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', 12) ?><?=$query_string ?>">Резка &laquo;Сома&raquo;</a>
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
<div class="modal fade" id="move_shifts_form">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Сдвиг тиражей</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <input type="hidden" id="move_shifts_machine_id" name="move_shifts_machine_id" />
                    <input type="hidden" id="move_shifts_date_from" name="move_shifts_date_from" />
                    <input type="hidden" id="move_shifts_shift_from" name="move_shifts_shift_from" />
                    <div class="form-group form-inline">
                        <label for="move_shifts_count">На сколько смен сдвинуть?&nbsp;</label>
                        <input type="number" id="move_shifts_count" name="move_shifts_count" min="1" max="99" class="form-control" value="1" required="required" />
                    </div>
                    <input type="hidden" id="scroll" name="scroll" />
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" id="move-shift-up-button" class="btn" onclick="javascript: MoveShiftsUp($(this))" data-dismiss="modal">Назад&nbsp;<i class="fas fa-arrow-up"></i></button>
                    <button type="button" id="move-shift-down-button" class="btn ml-1" onclick="javascript: MoveShiftsDown($(this))" data-dismiss="modal">Вперёд&nbsp;<i class="fas fa-arrow-down"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>