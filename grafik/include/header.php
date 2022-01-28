<div class="container-fluid">
    <nav class="navbar navbar-expand-sm">
        <a class="navbar-brand" href="<?=APPLICATION ?>/">
            <i class="fas fa-home"></i>
        </a>
        <ul class="navbar-nav mr-auto">
            <?php
            if(LoggedIn()):
            $sql = "select id, name from machine order by name";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()):
                $status = filter_input(INPUT_GET, 'id') == $row['id'] ? ' disabled' : '';
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$status ?>" href="<?=APPLICATION ?>/machine.php<?= BuildQuery('id', $row['id']) ?>"><?=$row['name'] ?></a>
            </li>
            <?php
            endwhile;
            $personal_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/personal/index.php' ? ' disabled' : '';
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$personal_status ?>" href="<?=APPLICATION ?>/personal/">Мои настройки</a>
            </li>
            <?php
            if(IsInRole('admin')):
            $user_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/user/index.php' ? ' disabled' : '';
            $machine_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/machine/index.php' ? ' disabled' : '';
            $lamination_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/lamination/index.php' ? ' disabled' : '';
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
                    <div class="form-group">
                        <div id="move_shifts_title"></div>
                    </div>
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